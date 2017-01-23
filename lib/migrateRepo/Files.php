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
        $mName = str_replace(['.', '/', '\\', ' '], '_', trim($mName));
        $name = preg_replace('/[^0-9a-z_]/i', '_', $mName);
        if ($name !== '') {
            $name = $this->getFilePrefix() . time() . '_' . $name;
            $fileName = $this->getFolder() . DIRECTORY_SEPARATOR . "{$name}.php";
            if (file_exists($fileName)) {
                throw new \marvin255\bxmigrate\Exception('Migration already exists');
            } elseif (!is_writable($this->getFolder())) {
                throw new \marvin255\bxmigrate\Exception('Can\'t create migration file');
            }
            $migrationData = [
                'name' => $name,
                'parentClass' => $this->getParentClass(),
            ];
            $view = $this->getViewFile();
            if (!$view && ($smartView = $this->getSmartView($mName))) {
                $view = $smartView[0];
                $migrationData = array_merge($migrationData, $smartView[1]);
            }
            $migrationText = $this->renderMigration($view, $migrationData);
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
     * @param string $name
     *
     * @return array
     */
    protected function getSmartView($name)
    {
        $return = null;
        $viewsDir = dirname(dirname(__DIR__)).'/views';
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
        foreach ($smartViews as $file => $regular) {
            if (!preg_match($regular, $name, $matches)) continue;
            $params = [];
            foreach ($matches as $key => $val) {
                if ($key === 0) continue;
                $params["smart_param_{$key}"] = $val;
            }
            $return = [
                "{$viewsDir}/{$file}.php",
                $params
            ];
            break;
        }
        return $return;
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
