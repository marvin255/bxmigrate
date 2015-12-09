<?php

namespace marvin255\bxmigrate\migrateChecker;

class File implements \marvin255\bxmigrate\IMigrateChecker
{
	/**
	 * @var string
	 */
	protected $_file = null;



	/**
	 * @param array $config
	 */
	public function __construct(array $config = null)
	{
		if ($config) $this->config($config);
	}


	/**
	 * @param string $migration
	 * @return bool
	 */
	public function isChecked($migration)
	{
		$checked = $this->getChecked();
		return in_array($migration, $checked);
	}

	/**
	 * @param string $migration
	 */
	public function check($migration)
	{
		$checked = $this->getChecked();
		if (!in_array($migration, $checked)) {
			$checked[] = $migration;
			file_put_contents($this->getFile(), implode("\r\n", $checked));
		}
	}

	/**
	 * @param string $migration
	 */
	public function uncheck($migration)
	{
		$checked = $this->getChecked();
		$new = [];
		foreach ($checked as $value) {
			if ($value === $migration) continue;
			$new[] = $value;
		}
		file_put_contents($this->getFile(), implode("\r\n", $new));
	}


	/**
	 * @param array $config
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function config(array $config)
	{
		if (isset($config['file'])) $this->setFile($config['file']);
		return $this;
	}


	/**
	 * @return array
	 */
	protected function getChecked()
	{
		$return = null;
		$file = $this->getFile();
		if (file_exists($file)) {
			$return = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			if ($return === false) {
				throw new \marvin255\bxmigrate\Exception('Can not read checker file');
			}
		}
		return is_array($return) ? $return : [];
	}


	/**
	 * @param string $file
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function setFile($file)
	{
		$this->_file = trim($file);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return isset($this->_file) ? $this->_file : null;
	}
}