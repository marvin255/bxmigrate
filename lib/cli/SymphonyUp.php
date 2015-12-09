<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
		
		//

		$output->writeln('<info>Migrations set up</info>');
	}
}