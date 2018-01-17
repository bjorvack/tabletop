<?php

namespace App\Command\Console;

use App\Command\CreateDesigner as CreateDesignerCommand;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDesigner extends Command
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
            ->setName('app:create-designer')
            ->setDescription('Creates a new designer')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the designer')
            ->addArgument(
                'description',
                InputArgument::OPTIONAL,
                'The description of the designer',
                null
            )
            ->addArgument(
                'website',
                InputArgument::OPTIONAL,
                'The website of the designer',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $createDesigner = new CreateDesignerCommand(
            $input->getArgument('name'),
            $input->getArgument('description'),
            $input->getArgument('website'),
            null
        );

        $this->commandBus->handle($createDesigner);
    }
}
