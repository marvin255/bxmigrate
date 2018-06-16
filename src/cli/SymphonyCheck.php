<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Консольная команда для Symfony console, которая создает новую миграцию.
 */
class SymphonyCheck extends AbstractManagerCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('bxmigrate:check')
            ->setDescription('Checks migration without running')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of migration to check'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $manager = $this->getOrCreateMigrateManager($input, $output);

        $manager->check($name);
    }
}
