<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SymphonyDown extends Command
{
    protected function configure()
    {
        $this
            ->setName('bxmigrate:down')
            ->setDescription('Set down migrations')
            ->addArgument(
                'count',
                InputArgument::OPTIONAL,
                'Count of migrations to set down'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = (int) $input->getArgument('count');
        $count = $count ? $count : null;

        if (!defined('CLI_MIGRATIONS_PATH') || empty(CLI_MIGRATIONS_PATH)) {
            $output->writeln('<error>Please set up CLI_MIGRATIONS_PATH constant</error>');
        } else {
            $repo = new \marvin255\bxmigrate\migrateRepo\Files([
                'folder' => CLI_MIGRATIONS_PATH,
            ]);
            $checker = new \marvin255\bxmigrate\migrateChecker\HighLoadIb();
            $manager = new \marvin255\bxmigrate\migrateManager\Simple($repo, $checker);
            $manager->down($count);
            $output->writeln('<info>Migrations set down</info>');
        }
    }
}
