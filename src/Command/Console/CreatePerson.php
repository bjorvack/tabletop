<?php

namespace App\Command\Console;

use App\Command\CreatePerson as CreatePersonCommand;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePerson extends Command
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
            ->setName('app:create-person')
            ->setDescription('Creates a new person')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the person')
            ->addArgument(
                'description',
                InputArgument::OPTIONAL,
                'The description of the person',
                null
            )
            ->addArgument(
                'website',
                InputArgument::OPTIONAL,
                'The website of the person',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $createPerson = new CreatePersonCommand(
            $input->getArgument('name'),
            $input->getArgument('description'),
            $input->getArgument('website')
        );

        $this->commandBus->handle($createPerson);
    }
}
