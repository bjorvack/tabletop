<?php

namespace App\Command\Console;

use App\Command\CreateGame as CreateGameCommand;
use App\Entity\Game;
use App\Exception\ImportException;
use App\Repository\GameRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

    /**
     * @param CommandBus             $commandBus
     * @param GameRepository         $gameRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CommandBus $commandBus,
        GameRepository $gameRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->gameRepository = $gameRepository;
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
        foreach ($input->getArgument('games') as $key => $game) {
            if (0 === $key % 10) {
                $this->clearMemory($output);
            }

            if ($this->gameRepository->findByBoardGameGeekId($game) instanceof Game) {
                $output->writeln("<comment>Game with id $game already imported</comment>");
                continue;
            }

            try {
                $data = $this->getGameInfo(
                    intval($game)
                );
            } catch (Exception $e) {
                $output->writeln("<error>Game with id $game returned an error</error>");
                continue;
            }

            if ($data) {
                try {
                    /** @var CreateGameCommand $createGame */
                    $createGame = $this->createCommandFromSimpleXMLElement($data, $game);
                } catch (Exception $e) {
                    $output->writeln("<error>Game with id $game has invalid data</error>");
                    continue;
                }

                $this->commandBus->handle($createGame);
                $output->writeln(
                    "<info>Game with id $game imported as ".$createGame->getTitle().'</info>'
                );
            } else {
                $output->writeln("<comment>Game with id $game not found</comment>");
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
     * @param SimpleXMLElement $data
     * @param int              $id
     *
     * @throws ImportException
     *
     * @return CreateGameCommand
     */
    private function createCommandFromSimpleXMLElement(SimpleXMLElement $data, int $id)
    {
        $game = $data->children()[0];

        if ($game->error) {
            throw ImportException::create();
        }

        $title = null;
        foreach ($game->name as $name) {
            if ((bool) $name->attributes()->primary) {
                $title = (string) $name;
            }
        }

        return new CreateGameCommand(
            $title,
            (string) $game->description,
            DateTimeImmutable::createFromFormat('Y', (string) $game->yearpublished),
            (string) $game->image,
            null,
            null,
            null,
            $id
        );
    }

    /**
     * @param int $id
     *
     * @return null|SimpleXMLElement
     */
    private function getGameInfo(int $id): ?SimpleXMLElement
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::ENDPOINT.$id);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = simplexml_load_string(
            curl_exec($curl)
        );

        curl_close($curl);

        return false !== $result ? $result : null;
    }
}
