<?php

namespace App\Command\Console;

use App\Command\CreateGame as CreateGameCommand;
use App\Entity\Game;
use App\Exception\ImportException;
use App\Repository\PersonRepository;
use App\Repository\GameRepository;
use App\Repository\PublisherRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportGame extends Command
{
    private const ENDPOINT = 'https://boardgamegeek.com/xmlapi/boardgame/';

    /** @var CommandBus */
    private $commandBus;

    /** @var GameRepository */
    private $gameRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PersonRepository */
    private $personRepository;

    /** @var PublisherRepository */
    private $publisherRepository;

    /**
     * @param CommandBus             $commandBus
     * @param GameRepository         $gameRepository
     * @param PersonRepository       $personRepository
     * @param PublisherRepository    $publisherRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CommandBus $commandBus,
        GameRepository $gameRepository,
        PersonRepository $personRepository,
        PublisherRepository $publisherRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->gameRepository = $gameRepository;
        $this->personRepository = $personRepository;
        $this->publisherRepository = $publisherRepository;
        $this->entityManager = $entityManager;

        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    protected function configure()
    {
        $this
            ->setName('app:import-games')
            ->setDescription('Imports games from board game geek')
            ->addArgument(
                'games',
                InputArgument::IS_ARRAY,
                "The id's of the games to import",
                range(1, 100000)
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $games = $input->getArgument('games');

        $importedGames = [];

        foreach (array_chunk($games, 100) as $chunk) {
            $importedChunk = $this->gameRepository->findByBoardGameGeekIds($chunk);
            array_walk($importedChunk, function (Game &$item) {
                $item = $item->getBoardGameGeekId();
            });

            $importedGames = array_merge($importedGames, $importedGames);
        }

        foreach (array_chunk(array_diff($games, $importedGames), 200) as $key => $chunk) {
            if (0 === $key % 10) {
                $this->clearMemory($output);
            }

            $this->importGames($chunk, $output);
        }
    }

    /**
     * @param array           $ids
     * @param OutputInterface $output
     */
    private function importGames(array $ids, OutputInterface $output): void
    {
        try {
            $gamesInfo = $this->getGamesInfo($ids);

            foreach ($gamesInfo->children() as $gameInfo) {
                $command = $this->createCommandFromSimpleXMLElement($gameInfo);

                $this->commandBus->handle($command);

                $output->writeln(
                    '<info>Game with id '.$command->getBoardGameGeekId().' imported as '.$command->getTitle().'</info>'
                );
            }
        } catch (ImportException $e) {
            if (count($ids) > 1) {
                foreach (array_chunk($ids, (int) count($ids) / 2) as $chunk) {
                    $this->importGames($chunk, $output);
                    $this->clearMemory($output);
                }
            }
        }
    }

    /**
     * @param OutputInterface $output
     */
    private function clearMemory(OutputInterface $output): void
    {
        $output->writeln(
            '<comment>'.
            sprintf(
                'Memory usage (currently) %dKB/ (max) %dKB',
                round(memory_get_usage(true) / 1024),
                memory_get_peak_usage(true) / 1024
            ).
            '</comment>'
        );
        $this->entityManager->clear();
        gc_collect_cycles();
    }

    /**
     * @param SimpleXMLElement $game
     *
     * @throws ImportException
     *
     * @return CreateGameCommand
     */
    private function createCommandFromSimpleXMLElement(SimpleXMLElement $game)
    {
        if ($game->error) {
            throw ImportException::create();
        }

        $title = null;
        foreach ($game->name as $name) {
            if ((bool) $name->attributes()->primary) {
                $title = (string) $name;
            }
        }

        $artists = null;
        if ($game->boardgameartist) {
            $ids = [];

            /** @var SimpleXMLElement $artist */
            foreach ($game->boardgameartist as $artist) {
                $ids[] = (int) $artist->attributes()['objectid'];
            }

            $artists = new ArrayCollection($this->personRepository->findByBoardGameGeekIds($ids));
        }

        $designers = null;
        if ($game->boardgamedesigner) {
            $ids = [];

            /** @var SimpleXMLElement $designer */
            foreach ($game->boardgamedesigner as $designer) {
                $ids[] = (int) $designer->attributes()['objectid'];
            }

            $designers = new ArrayCollection($this->personRepository->findByBoardGameGeekIds($ids));
        }

        $publishers = null;
        if ($game->boardgamepublisher) {
            $ids = [];

            /** @var SimpleXMLElement $publisher */
            foreach ($game->boardgamepublisher as $publisher) {
                $ids[] = (int) $publisher->attributes()['objectid'];
            }

            $publishers = new ArrayCollection($this->publisherRepository->findByBoardGameGeekIds($ids));
        }

        return new CreateGameCommand(
            $title,
            (string) $game->description,
            DateTimeImmutable::createFromFormat('Y', (string) $game->yearpublished),
            (string) $game->image,
            $artists,
            $designers,
            $publishers,
            (int) $game->attributes()['objectid']
        );
    }

    /**
     * @param array $ids
     *
     * @return null|SimpleXMLElement
     */
    private function getGamesInfo(array $ids): ?SimpleXMLElement
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::ENDPOINT.implode(',', $ids));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = simplexml_load_string(
            curl_exec($curl)
        );

        curl_close($curl);

        return false !== $result ? $result : null;
    }
}
