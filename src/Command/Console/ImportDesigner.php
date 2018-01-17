<?php

namespace App\Command\Console;

use App\Command\CreateDesigner as CreateDesignerCommand;
use App\Entity\Designer;
use App\Repository\DesignerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportDesigner extends Command
{
    private const ENDPOINT = 'https://boardgamegeek.com/xmlapi/boardgamedesigner/';

    /** @var CommandBus */
    private $commandBus;

    /** @var DesignerRepository */
    private $designerRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param CommandBus             $commandBus
     * @param DesignerRepository       $designerRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CommandBus $commandBus,
        DesignerRepository $designerRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->designerRepository = $designerRepository;
        $this->entityManager = $entityManager;

        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
    }

    protected function configure()
    {
        $this
            ->setName('app:import-designers')
            ->setDescription('Imports designers from board game geek')
            ->addArgument(
                'designers',
                InputArgument::IS_ARRAY,
                "The id's of the designers to import",
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
        foreach ($input->getArgument('designers') as $key => $designer) {
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

            if ($this->designerRepository->findByBoardGameGeekId($designer) instanceof Designer) {
                $output->writeln("<comment>Designer with id $designer already imported</comment>");
                continue;
            }

            try {
                $data = $this->getDesignerInfo(
                    intval($designer)
                );
            } catch (Exception $e) {
                $output->writeln("<error>Designer with id $designer returned an error</error>");
                continue;
            }

            if ($data) {
                try {
                    $createDesigner = $this->createCommandFromSimpleXMLElement($data, $designer);
                } catch (Exception $e) {
                    $output->writeln("<error>Designer with id $designer has invalid data</error>");
                    continue;
                }

                $this->commandBus->handle($createDesigner);
                $output->writeln(
                    "<info>Designer with id $designer imported as ".$createDesigner->getName().'</info>'
                );
            } else {
                $output->writeln("<comment>Designer with id $designer not found</comment>");
            }
        }
    }

    /**
     * @param SimpleXMLElement $data
     * @param int              $id
     *
     * @return CreateDesignerCommand
     */
    private function createCommandFromSimpleXMLElement(SimpleXMLElement $data, int $id)
    {
        $person = $data->children()[0];

        return new CreateDesignerCommand(
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
    private function getDesignerInfo(int $id): ?SimpleXMLElement
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
