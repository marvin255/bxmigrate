<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Консольная команда для Symfony console, которая откатывает миграции.
 */
class SymphonyDown extends AbstractManagerCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('bxmigrate:down')
            ->setDescription('Set down migrations')
            ->addArgument(
                'count',
                InputArgument::OPTIONAL,
                'Count of migrations to set down'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getArgument('count') ?: null;
        $manager = $this->getOrCreateMigrateManager($input, $output);

        $manager->down($count);
    }
}
