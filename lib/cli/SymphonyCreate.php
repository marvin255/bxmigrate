<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SymphonyCreate extends Command
{
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
		
		if (!defined('CLI_MIGRATIONS_PATH') || empty(CLI_MIGRATIONS_PATH)) {
			$output->writeln('<error>Please set up CLI_MIGRATIONS_PATH constant</error>');
		} else {
			$repo = new \marvin255\bxmigrate\migrateRepo\Files([
				'folder' => CLI_MIGRATIONS_PATH,
			]);
			$checker = new \marvin255\bxmigrate\migrateChecker\File([
				'file' => CLI_MIGRATIONS_PATH . '/migrations_checker.txt',
			]);
			$manager = new \marvin255\bxmigrate\migrateManager\Simple($repo, $checker);
			$manager->create($name);
			$output->writeln('<info>Migration created</info>');
		}

	}
}