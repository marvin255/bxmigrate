<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

class SymphonyCreate extends Command
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
    }

    protected function configure()
    {
        $this
            ->setName('bxmigrate:create')
            ->setDescription('Create new migration')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Create new migration'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        try {
            $repo = new \marvin255\bxmigrate\migrateRepo\Files($this->migrationPath);
            $checker = new \marvin255\bxmigrate\migrateChecker\HighLoadIb();
            $manager = new \marvin255\bxmigrate\migrateManager\Simple($repo, $checker);
            $manager->create($name);
            $output->writeln('<info>Migration created</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
        }
    }
}
