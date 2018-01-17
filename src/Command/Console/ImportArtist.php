<?php

namespace App\Command\Console;

use App\Command\CreateArtist as CreateArtistCommand;
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

    /**
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
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
        foreach ($input->getArgument('artists') as $artist) {
            $data = $this->getArtistInfo(
                intval($artist)
            );

            if ($data) {
                $createArtist = $this->createCommandFromSimpleXMLElement($data);

                $this->commandBus->handle($createArtist);
                $output->writeln(
                    "<info>Artist with id $artist imported as ".$createArtist->getName().'</info>'
                );
            } else {
                $output->writeln("<error>Artist with id $artist not found</error>");
            }
        }
    }

    /**
     * @param SimpleXMLElement $data
     *
     * @return CreateArtistCommand
     */
    private function createCommandFromSimpleXMLElement(SimpleXMLElement $data)
    {
        $person = $data->children()[0];

        return new CreateArtistCommand(
            (string) $person->name,
            (string) $person->description,
            null,
            null
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
