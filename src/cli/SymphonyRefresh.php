<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Консольная команда для Symfony console, которая обновляет миграцию с укзанным именем.
 */
class SymphonyRefresh extends AbstractManagerCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('bxmigrate:refresh')
            ->setDescription('Refresh migration')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Refresh migration with name in parameter'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $manager = $this->getOrCreateMigrateManager($input, $output);

        $manager->refresh($name);
    }
}
