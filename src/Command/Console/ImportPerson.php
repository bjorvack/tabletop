<?php

namespace App\Command\Console;

use App\Command\CreatePerson as CreatePersonCommand;
use App\Entity\Person;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPerson extends Command
{
    private const ENDPOINT = 'https://boardgamegeek.com/xmlapi/person/';

    /** @var CommandBus */
    private $commandBus;

    /** @var PersonRepository */
    private $personRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param CommandBus             $commandBus
     * @param PersonRepository       $personRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CommandBus $commandBus,
        PersonRepository $personRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->personRepository = $personRepository;
        $this->entityManager = $entityManager;

        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    protected function configure()
    {
        $this
            ->setName('app:import-persons')
            ->setDescription('Imports persons from board game geek')
            ->addArgument(
                'persons',
                InputArgument::IS_ARRAY,
                "The id's of the persons to import",
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
        foreach ($input->getArgument('persons') as $key => $person) {
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

            if ($this->personRepository->findByBoardGameGeekId($person) instanceof Person) {
                $output->writeln("<comment>Person with id $person already imported</comment>");
                continue;
            }

            try {
                $data = $this->getPersonInfo(
                    intval($person)
                );
            } catch (Exception $e) {
                $output->writeln("<error>Person with id $person returned an error</error>");
                continue;
            }

            if ($data) {
                try {
                    $createPerson = $this->createCommandFromSimpleXMLElement($data, $person);
                } catch (Exception $e) {
                    $output->writeln("<error>Person with id $person has invalid data</error>");
                    continue;
                }

                $this->commandBus->handle($createPerson);
                $output->writeln(
                    "<info>Person with id $person imported as ".$createPerson->getName().'</info>'
                );
            } else {
                $output->writeln("<comment>Person with id $person not found</comment>");
            }
        }
    }

    /**
     * @param SimpleXMLElement $data
     * @param int              $id
     *
     * @return CreatePersonCommand
     */
    private function createCommandFromSimpleXMLElement(SimpleXMLElement $data, int $id)
    {
        $person = $data->children()[0];

        return new CreatePersonCommand(
            (string) $person->name,
            (string) $person->description,
            null,
            $id
        );
    }

    /**
     * @param int $id
     *
     * @return null|SimpleXMLElement
     */
    private function getPersonInfo(int $id): ?SimpleXMLElement
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
