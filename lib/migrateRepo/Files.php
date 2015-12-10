<?php

namespace marvin255\bxmigrate\migrateRepo;

class Files implements \marvin255\bxmigrate\IMigrateRepo
{
	/**
	 * @var string
	 */
	protected $_folder = null;
	/**
	 * @var string
	 */
	protected $_filePrefix = null;
	/**
	 * @var string
	 */
	protected $_viewFile = null;
	/**
	 * @var string
	 */
	protected $_parentClass = '\\marvin255\\bxmigrate\\migrate\\Coded';



	/**
	 * @param array $config
	 */
	public function __construct(array $config = null)
	{
		if ($config) $this->config($config);
	}


	/**
	 * @return array
	 */
	public function getMigrations()
	{
		$return = [];
		$prefix = $this->getFilePrefix();
		$folder = $this->getFolder();
		$files = scandir($folder);
		foreach ($files as $file) {
			if (!preg_match('/^(' . $prefix . '\S+)\.php$/', $file, $matches)) continue;
			require_once($folder . DIRECTORY_SEPARATOR . $file);
			$class = $matches[1];
			if (!is_subclass_of($class, '\marvin255\bxmigrate\IMigrate')) continue;
			$return[] = new $class;
		}
		return $return;
	}


	/**
	 * @param array $config
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function config(array $config)
	{
		if (isset($config['folder'])) $this->setFolder($config['folder']);
		if (isset($config['filePrefix'])) $this->setFilePrefix($config['filePrefix']);
		if (isset($config['viewFile'])) $this->setViewFile($config['viewFile']);
		return $this;
	}


	/**
	 * @param string $name
	 * @return string
	 */
	public function create($mName)
	{
		$mName = str_replace(['.', '/', '\\'], '_', trim($mName));
		$name = $mName;
		if ($name !== '') {
			$name = $this->getFilePrefix() . time() . '_' . $name;
			$fileName = $this->getFolder() . DIRECTORY_SEPARATOR . "{$name}.php";
			if (file_exists($fileName)) {
				throw new \marvin255\bxmigrate\Exception('Migration already exists');
			} elseif (!is_writable($this->getFolder())) {
				throw new \marvin255\bxmigrate\Exception('Can\'t create migration file');
			}
			$migrationText = $this->renderMigration($this->getViewFile(), [
				'name' => $name,
				'parentClass' => $this->getParentClass(),
			]);
			file_put_contents($fileName, $migrationText);
		} else {
			throw new \marvin255\bxmigrate\Exception('Wrong migration name');
		}
		return $mName;
	}


	/**
	 * @param string $folder
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function setFolder($folder)
	{
		$folder = trim($folder);
		if (is_dir($folder)) {
			$this->_folder = $folder;
		} else {
			throw new \marvin255\bxmigrate\Exception('Migrations folder doesn\'t exist');			
		}
		return $this;
	}


	/**
	 * @param string $___view___
	 * @param array $___data___
	 */
	protected function renderMigration($___view___, array $___data___ = null)
	{
		if (!$___view___ || !file_exists($___view___)) {
			$___view___ = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'default.php';
		}
		ob_start();
		ob_implicit_flush(false);
		if ($___data___) extract($___data___);
		require($___view___);
		return ob_get_clean();
	}


	/**
	 * @return string
	 */
	public function getFolder()
	{
		return isset($this->_folder) ? $this->_folder : null;
	}

	/**
	 * @param string $prefix
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function setFilePrefix($prefix)
	{
		$this->_filePrefix = $prefix;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFilePrefix()
	{
		return isset($this->_filePrefix) ? $this->_filePrefix : 'migrate_';
	}

	/**
	 * @param string $file
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function setViewFile($file)
	{
		$this->_viewFile = $file;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getViewFile()
	{
		return $this->_viewFile;
	}

	/**
	 * @param string $class
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function setParentClass($class)
	{
		$this->_parentClass = $class;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getParentClass()
	{
		return $this->_parentClass;
	}
}