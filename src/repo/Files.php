<?php

namespace marvin255\bxmigrate\repo;

use marvin255\bxmigrate\IMigrateRepo;

class Files implements IMigrateRepo
{
    /**
     * @var string
     */
    protected $folder = null;
    /**
     * @var string
     */
    protected $fileNamePrefix = null;
    /**
     * @var string
     */
    protected $templatesFolder = null;
    /**
     * @var string
     */
    protected $parentClass = null;

    /**
     * @param array $config
     *
     * @throws \marvin255\bxmigrate\repo\Exception
     */
    public function __construct(
        $folder,
        $templatesFolder,
        $parentClass = '\\marvin255\\bxmigrate\\migrate\\Coded',
        $fileNamePrefix = 'migrate'
    ) {
        if (empty($folder) || !is_dir($folder) || !is_writable($folder)) {
            throw new Exception(
                'Migration folder does not exist: '.(empty($folder) ? 'null' : $folder)
            );
        } else {
            $this->folder = $folder;
        }
        $templatesFolder = $templatesFolder ? $templatesFolder : __DIR__.'/../../views';
        if (!is_dir($templatesFolder)) {
            throw new Exception(
                'Migration template folder does not exist: '.(empty($templatesFolder) ? 'null' : $templatesFolder)
            );
        } else {
            $this->templatesFolder = $templatesFolder;
        }
        if (empty($parentClass)) {
            throw new Exception('Migration parent class is empty');
        } else {
            $this->parentClass = $parentClass;
        }
        $this->fileNamePrefix = $fileNamePrefix;
    }

    /**
     * @return array
     *
     * @throws \marvin255\bxmigrate\repo\Exception
     */
    public function getMigrations()
    {
        $return = [];
        $regexp = $this->fileNamePrefix ? '/^('.$this->fileNamePrefix.'\S+)\.php$/' : '/^(\S+)\.php$/';
        foreach (scandir($this->folder) as $file) {
            if (!preg_match($regexp, $file, $matches)) continue;
            require_once($this->folder.'/'.$file);
            $class = $matches[1];
            if (!is_subclass_of($class, '\marvin255\bxmigrate\IMigrate')) {
                throw new Exception('File '.$file.' has no migration class');
            } else {
                $return[$matches[1]] = new $class();
            }
        }

        return $return;
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \marvin255\bxmigrate\repo\Exception
     */
    public function create($mName)
    {
        $name = $this->clearMigartionName($mName);
        if ($name === '') {
            throw new Exception('Can not create migration file for name: '.$mName);
        }
        $name = $this->fileNamePrefix.'_'.time().'_'.$name;
        $fileName = "{$this->folder}/{$name}.php";
        if (file_exists($fileName)) {
            throw new Exception('Migration already exists: '.$mName);
        }
        list($viewFile, $viewData) = $this->getView($mName);
        if (!file_exists($viewFile)) {
            throw new Exception('Can\'t find migration template file for migration: '.$mName);
        }
        $viewData['name'] = $name;
        $viewData['parentClass'] = $this->parentClass;
        $migration = $this->renderMigration($viewFile, $viewData);
        file_put_contents($fileName, $migration);

        return $mName;
    }

    /**
     * @param string $___view___
     * @param array  $___data___
     */
    protected function renderMigration($___view___, array $___data___ = null)
    {
        ob_start();
        ob_implicit_flush(false);
        if ($___data___) {
            extract($___data___);
        }
        include($___view___);

        return ob_get_clean();
    }

    /**
     * @param string $name
     *
     * @return array
     */
    protected function getView($name)
    {
        $smartViews = [
            'iblock_uf_property_create' => '/^create_iblock_(.+)_uf_(.+)$/i',
            'iblock_uf_property_update' => '/^update_iblock_(.+)_uf_(.+)$/i',
            'iblock_uf_property_delete' => '/^delete_iblock_(.+)_uf_(.+)$/i',
            'iblock_property_create' => '/^create_iblock_(.+)_property_(.+)$/i',
            'iblock_property_update' => '/^update_iblock_(.+)_property_(.+)$/i',
            'iblock_property_delete' => '/^delete_iblock_(.+)_property_(.+)$/i',
            'iblock_type_create' => '/^create_iblock_type_(.+)$/i',
            'iblock_type_delete' => '/^delete_iblock_type_(.+)$/i',
            'iblock_create' => '/^create_iblock_(.+)$/i',
            'iblock_update' => '/^update_iblock_(.+)$/i',
            'iblock_delete' => '/^create_iblock_(.+)$/i',

            'hlblock_property_create' => '/^create_hlblock_(.+)_property_(.+)$/i',
            'hlblock_property_update' => '/^update_hlblock_(.+)_property_(.+)$/i',
            'hlblock_property_delete' => '/^delete_hlblock_(.+)_property_(.+)$/i',
            'hlblock_create' => '/^create_hlblock_(.+)$/i',
            'hlblock_update' => '/^update_hlblock_(.+)$/i',
            'hlblock_delete' => '/^create_hlblock_(.+)$/i',

            'module_install' => '/^install_module_(.+)$/i',
            'module_delete' => '/^delete_module_(.+)$/i',
        ];

        $view = null;
        $params = [];
        foreach ($smartViews as $file => $regular) {
            if (!preg_match($regular, $name, $matches)) continue;
            $view = "{$file}.php";
            foreach ($matches as $key => $val) {
                if ($key === 0) continue;
                $params["smart_param_{$key}"] = $val;
            }
            break;
        }
        if (!$view) {
            $view = 'default.php';
        }

        return [$this->templatesFolder.'/'.$view, $params];
    }

    /**
     * @param string
     *
     * @return string
     */
    protected function clearMigartionName($name)
    {
        return trim(preg_replace('/[^0-9a-z_]/i', '_', str_replace([' ', '.', '/', '\\'], '', $name)));
    }
}
