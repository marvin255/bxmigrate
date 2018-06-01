<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Консольная команда для Symfony console, которая создает новую миграцию.
 */
class SymphonyCreate extends AbstractManagerCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('bxmigrate:create')
            ->setDescription('Create new migration')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Create new migration'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $manager = $this->getOrCreateMigrateManager($input, $output);

        $manager->create($name);
    }
}
