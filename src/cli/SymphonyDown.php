<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use marvin255\bxmigrate\repo\Files;
use marvin255\bxmigrate\checker\HighLoadIb;
use marvin255\bxmigrate\manager\Simple;

/**
 * Консольная команда для Symfony console, которая откатывает миграции.
 */
class SymphonyDown extends Command
{
    /**
     * @var string
     */
    protected $migrationPath;

    /**
     * Задает путь к папке с миграциями.
     *
     * @param string $migrationPath
     *
     * @return self
     */
    public function setMigrationPath($migrationPath)
    {
        $this->migrationPath = $migrationPath;

        return $this;
    }

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
        $repo = new Files($this->migrationPath);
        $checker = new HighLoadIb;
        $notifier = new Notifier($output);
        $manager = new Simple($repo, $checker, $notifier);
        $manager->down($count);
    }
}
