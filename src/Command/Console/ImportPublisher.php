<?php

namespace App\Command\Console;

use App\Command\CreatePublisher as CreatePublisherCommand;
use App\Entity\Publisher;
use App\Repository\PublisherRepository;
use App\Utils\StringUtils;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPublisher extends Command
{
    private const ENDPOINT = 'https://boardgamegeek.com/xmlapi/publisher/';

    /** @var CommandBus */
    private $commandBus;

    /** @var PublisherRepository */
    private $publisherRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param CommandBus             $commandBus
     * @param PublisherRepository    $publisherRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CommandBus $commandBus,
        PublisherRepository $publisherRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->publisherRepository = $publisherRepository;
        $this->entityManager = $entityManager;

        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    protected function configure()
    {
        $this
            ->setName('app:import-publishers')
            ->setDescription('Imports publishers from board game geek')
            ->addArgument(
                'publishers',
                InputArgument::IS_ARRAY,
                "The id's of the publishers to import",
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
        $publishers = $input->getArgument('publishers');

        $importedPublishers = [];

        foreach (array_chunk($publishers, 100) as $chunk) {
            $importedChunk = $this->publisherRepository->findByBoardGameGeekIds($chunk);
            array_walk($importedChunk, function (Publisher &$item) {
                $item = $item->getBoardGameGeekId();
            });

            $importedPublishers = array_merge($importedPublishers, $importedChunk);
            unset($importedChunk);
            $this->clearMemory(null);
        }

        foreach (array_diff($publishers, $importedPublishers) as $key => $publisher) {
            if (0 === $key % 10) {
                $this->clearMemory($output);
            }

            if ($this->publisherRepository->findByBoardGameGeekId($publisher) instanceof Publisher) {
                $output->writeln("<comment>Publisher with id $publisher already imported</comment>");
                continue;
            }

            try {
                $data = $this->getPublisherInfo(
                    intval($publisher)
                );
            } catch (Exception $e) {
                $output->writeln("<error>Publisher with id $publisher returned an error</error>");
                continue;
            }

            if ($data) {
                try {
                    $createPublisher = $this->createCommandFromSimpleXMLElement($data, $publisher);
                } catch (Exception $e) {
                    $output->writeln("<info>Publisher with id $publisher has invalid data</info>");
                    continue;
                }

                $this->commandBus->handle($createPublisher);
                $output->writeln(
                    "<info>Publisher with id $publisher imported as ".$createPublisher->getName().'</info>'
                );
            } else {
                $output->writeln("<error>Publisher with id $publisher not found</error>");
            }
        }
    }

    /**
     * @param OutputInterface|null $output
     */
    private function clearMemory(?OutputInterface $output): void
    {
        if ($output instanceof OutputInterface) {
            $output->writeln(
                '<comment>'.
                sprintf(
                    'Memory usage (currently) %dKB/ (max) %dKB',
                    round(memory_get_usage(true) / 1024),
                    memory_get_peak_usage(true) / 1024
                ).
                '</comment>'
            );
        }

        $this->entityManager->clear();
        gc_collect_cycles();
    }

    /**
     * @param SimpleXMLElement $data
     * @param int              $id
     *
     * @return CreatePublisherCommand
     */
    private function createCommandFromSimpleXMLElement(SimpleXMLElement $data, int $id)
    {
        $publisher = $data->children()[0];

        return new CreatePublisherCommand(
            StringUtils::cleanup((string) $publisher->name),
            StringUtils::cleanup((string) $publisher->description),
            null,
            $id
        );
    }

    /**
     * @param int $id
     *
     * @return null|SimpleXMLElement
     */
    private function getPublisherInfo(int $id): ?SimpleXMLElement
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
