<?php

namespace App\Command\Console;

use App\Command\CreateArtist as CreateArtistCommand;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateArtist extends Command
{
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
            ->setName('app:create-artist')
            ->setDescription('Creates a new artist')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the artist')
            ->addArgument(
                'description',
                InputArgument::OPTIONAL,
                'The description of the artist',
                null
            )
            ->addArgument(
                'website',
                InputArgument::OPTIONAL,
                'The website of the artist',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $createArtist = new CreateArtistCommand(
            $input->getArgument('name'),
            $input->getArgument('description'),
            $input->getArgument('website'),
            null
        );

        $this->commandBus->handle($createArtist);
    }
}
