<?php

namespace App\Command\Console;

use App\Command\CreateArtist as CreateArtistCommand;
use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportArtist extends Command
{
    private const ENDPOINT = 'https://boardgamegeek.com/xmlapi/boardgameartist/';

    /** @var CommandBus */
    private $commandBus;

    /** @var ArtistRepository */
    private $artistRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param CommandBus             $commandBus
     * @param ArtistRepository       $artistRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CommandBus $commandBus,
        ArtistRepository $artistRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->artistRepository = $artistRepository;
        $this->entityManager = $entityManager;

        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    protected function configure()
    {
        $this
            ->setName('app:import-artists')
            ->setDescription('Imports artists from board game geek')
            ->addArgument(
                'artists',
                InputArgument::IS_ARRAY,
                "The id's of the artists to import",
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
        foreach ($input->getArgument('artists') as $key => $artist) {
            if (0 === $key % 10) {
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

            if ($this->artistRepository->findByBoardGameGeekId($artist) instanceof Artist) {
                $output->writeln("<comment>Artist with id $artist already imported</comment>");
                continue;
            }

            try {
                $data = $this->getArtistInfo(
                    intval($artist)
                );
            } catch (Exception $e) {
                $output->writeln("<error>Artist with id $artist returned an error</error>");
                continue;
            }

            if ($data) {
                try {
                    $createArtist = $this->createCommandFromSimpleXMLElement($data, $artist);
                } catch (Exception $e) {
                    $output->writeln("<error>Artist with id $artist has invalid data</error>");
                    continue;
                }

                $this->commandBus->handle($createArtist);
                $output->writeln(
                    "<info>Artist with id $artist imported as ".$createArtist->getName().'</info>'
                );
            } else {
                $output->writeln("<comment>Artist with id $artist not found</comment>");
            }
        }
    }

    /**
     * @param SimpleXMLElement $data
     * @param int              $id
     *
     * @return CreateArtistCommand
     */
    private function createCommandFromSimpleXMLElement(SimpleXMLElement $data, int $id)
    {
        $person = $data->children()[0];

        return new CreateArtistCommand(
            (string) $person->name,
            (string) $person->description,
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
    private function getArtistInfo(int $id): ?SimpleXMLElement
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
