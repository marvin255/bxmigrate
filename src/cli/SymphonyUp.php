<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SymphonyUp extends Command
{
    protected function configure()
    {
        $this
            ->setName('bxmigrate:up')
            ->setDescription('Set up migrations')
            ->addArgument(
                'count',
                InputArgument::OPTIONAL,
                'Count of migrations to set up'
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
            $manager->up($count);
            $output->writeln('<info>Migrations set up</info>');
        }
    }
}
