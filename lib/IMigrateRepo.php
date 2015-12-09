<?php

namespace marvin255\bxmigrate;

interface IMigrateRepo
{
	/**
	 * @param array $config
	 */
	public function __construct(array $config = null);
	
	/**
	 * @return array
	 */
	public function getMigrations();

	/**
	 * @param array $config
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function config(array $config);

	/**
	 * @param string $name
	 * @return string
	 */
	public function create($mName);
}