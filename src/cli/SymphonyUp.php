<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use marvin255\bxmigrate\repo\Files;
use marvin255\bxmigrate\checker\HighLoadIb;
use marvin255\bxmigrate\manager\Simple;
use InvalidArgumentException;

/**
 * Консольная команда для Symfony console, которая применяет миграции.
 */
class SymphonyUp extends Command
{
    /**
     * @var string
     */
    protected $migrationPath = null;

    public function __construct($migrationPath)
    {
        if (empty($migrationPath)) {
            throw new InvalidArgumentException('Migration path can not be empty');
        }
        $this->migrationPath = $migrationPath;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('bxmigrate:up')
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
        $repo = new Files($this->migrationPath);
        $checker = new HighLoadIb();
        $notifier = new Notifier($output);
        $manager = new Simple($repo, $checker, $notifier);
        $manager->up($count);
    }
}
