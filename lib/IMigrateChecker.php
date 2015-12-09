<?php

namespace marvin255\bxmigrate;

interface IMigrateChecker
{
	/**
	 * @param array $config
	 */
	public function __construct(array $config = null);
	
	/**
	 * @param string $migration
	 * @return bool
	 */
	public function isChecked($migration);

	/**
	 * @param string $migration
	 */
	public function check($migration);

	/**
	 * @param array $config
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function config(array $config);
}