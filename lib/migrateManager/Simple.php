<?php

namespace marvin255\bxmigrate\migrateManager;

class Simple implements \marvin255\bxmigrate\IMigrateManager
{
	/**
	 * @var \marvin255\bxmigrate\IMigrateRepo
	 */
	protected $_repo = null;
	/**
	 * @var \marvin255\bxmigrate\IMigrateRepo
	 */
	protected $_checker = null;


	/**
	 * @param \marvin255\bxmigrate\IMigrateRepo $repo
	 * @param \marvin255\bxmigrate\IMigrateChecker $checker
	 */
	public function __construct(\marvin255\bxmigrate\IMigrateRepo $repo, \marvin255\bxmigrate\IMigrateChecker $checker)
	{
		$this->setRepo($repo);
		$this->setChecker($checker);
	}


	/**
	 * @param int $count
	 * @return mixed
	 */
	public function up($count = null)
	{
		$migrations = $this->getRepo()->getMigrations();
		$checker = $this->getChecker();
		$total = count($migrations);
		$upped = 0;
		for ($i = 0; $i < $total; $i++) {
			if ($checker->isChecked($migrations[$i]->getName())) continue;
			$migrations[$i]->up();
			$checker->check($migrations[$i]->getName());
			$upped++;
			if ($count && $upped === $count) break;
		}
	}

	/**
	 * @param int $count
	 * @return mixed
	 */
	public function down($count = null)
	{
		$migrations = $this->getRepo()->getMigrations();
		$checker = $this->getChecker();
		$total = count($migrations);
		$upped = 0;
		for ($i = $total - 1; $i >= 0; $i--) {
			if (!$checker->isChecked($migrations[$i]->getName())) continue;
			$migrations[$i]->down();
			$checker->uncheck($migrations[$i]->getName());
			$upped++;
			if ($count && $upped === $count) break;
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function create($name)
	{
		return $this->getRepo()->create($name);
	}


	/**
	 * @param \marvin255\bxmigrate\IMigrateRepo $item
	 * @return \marvin255\bxmigrate\IMigrateManager 
	 */
	public function setRepo(\marvin255\bxmigrate\IMigrateRepo $item)
	{
		$this->_repo = $item;
	}

	/**
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function getRepo()
	{
		return $this->_repo;
	}

	/**
	 * @param \marvin255\bxmigrate\IMigrateChecker $item
	 * @return \marvin255\bxmigrate\IMigrateManager 
	 */
	public function setChecker(\marvin255\bxmigrate\IMigrateChecker $item)
	{
		$this->_checker = $item;
	}

	/**
	 * @return \marvin255\bxmigrate\IMigrateChecker
	 */
	public function getChecker()
	{
		return $this->_checker;
	}
}