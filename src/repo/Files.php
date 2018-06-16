<?php

namespace marvin255\bxmigrate\repo;

use marvin255\bxmigrate\IMigrateRepo;
use DirectoryIterator;

/**
 * Хранилище миграций, которое использует файлы php
 * и классы с соответствующими именами для получения миграций.
 */
class Files implements IMigrateRepo
{
    /**
     * @var string
     */
    protected $folder;
    /**
     * @var string
     */
    protected $fileNamePrefix;
    /**
     * @var string
     */
    protected $templatesFolder;
    /**
     * @var array
     */
    protected $migrations;
    /**
     * @var array
     */
    protected $smartViews = [
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
        'iblock_delete' => '/^delete_iblock_(.+)$/i',

        'hlblock_property_create' => '/^create_hlblock_(.+)_property_(.+)$/i',
        'hlblock_property_update' => '/^update_hlblock_(.+)_property_(.+)$/i',
        'hlblock_property_delete' => '/^delete_hlblock_(.+)_property_(.+)$/i',
        'hlblock_create' => '/^create_hlblock_(.+)$/i',
        'hlblock_update' => '/^update_hlblock_(.+)$/i',
        'hlblock_delete' => '/^delete_hlblock_(.+)$/i',

        'user_uf_property_create' => '/^create_user_uf_(.+)$/i',
        'user_uf_property_update' => '/^update_user_uf_(.+)$/i',
        'user_uf_property_delete' => '/^delete_user_uf_(.+)$/i',

        'module_install' => '/^install_module_(.+)$/i',
        'module_delete' => '/^delete_module_(.+)$/i',

        'email_event_type_create' => '/^create_email_event_type_(.+)_lid_([^_]+)$/i',
        'email_event_type_update' => '/^update_email_event_type_(.+)_lid_([^_]+)$/i',
        'email_event_type_delete' => '/^delete_email_event_type_(.+)_lid_([^_]+)$/i',

        'email_template_create' => '/^create_email_template_(.+)$/i',
        'email_template_update' => '/^update_email_template_(.+)$/i',
        'email_template_delete' => '/^delete_email_template_(.+)$/i',
    ];

    /**
     * Задаем в конструкторе настройки хранилища.
     *
     * @param string $folder          Путь до папки, в которой хранятся миграции
     * @param string $fileNamePrefix  Префикс, который будет использован в имени файла миграции
     * @param string $templatesFolder Путь до папки, в которой хранятся шаблоны для создания миграций
     *
     * @throws \marvin255\bxmigrate\repo\Exception
     */
    public function __construct($folder, $fileNamePrefix = 'migrate', $templatesFolder = null)
    {
        if (trim($folder) === '') {
            throw new Exception("Folder parameter can't be empty");
        } elseif (!is_dir($folder)) {
            throw new Exception("Folder {$folder} doesn't exist");
        } elseif (!is_writable($folder)) {
            throw new Exception("Folder {$folder} isn't writable");
        }
        $this->folder = $folder;

        if (trim($fileNamePrefix) === '') {
            throw new Exception("FileNamePrefix parameter can't be empty");
        }
        $this->fileNamePrefix = trim($fileNamePrefix);

        if ($templatesFolder === null) {
            $templatesFolder = __DIR__ . '/../../views';
        }
        if (trim($templatesFolder) === '') {
            throw new Exception("TemplatesFolder parameter can't be empty");
        } elseif (!is_dir($templatesFolder)) {
            throw new Exception("Folder {$templatesFolder} doesn't exist");
        }
        $this->templatesFolder = $templatesFolder;
    }

    /**
     * @inheritdoc
     */
    public function getMigrations()
    {
        if ($this->migrations === null) {
            $this->migrations = [];
            $regexp = '/^(' . preg_quote($this->fileNamePrefix) . '[a-zA-Z0-9_]+)\.php$/';
            $iterator = new DirectoryIterator($this->folder);
            foreach ($iterator as $item) {
                if ($item->isFile() && preg_match($regexp, $item->getBasename(), $matches)) {
                    $this->migrations[] = $matches[1];
                }
            }
            sort($this->migrations);
        }

        return $this->migrations;
    }

    /**
     * @inheritdoc
     */
    public function isMigrationExists($migrationName)
    {
        $migrations = $this->getMigrations();

        return in_array($migrationName, $migrations);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxmigrate\repo\Exception
     */
    public function instantiateMigration($name)
    {
        if (!$this->isMigrationExists($name)) {
            throw new Exception("Can't find file for migration with name {$name}");
        }

        if (!class_exists($name, false)) {
            require_once "{$this->folder}/{$name}.php";
            if (!class_exists($name, false)) {
                throw new Exception(
                    "File {$this->folder}/{$name}.php has no {$name} class"
                );
            }
        }

        return new $name;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxmigrate\repo\Exception
     */
    public function create($name, $template = null, array $data = [])
    {
        $migrationName = $this->clearMigartionName($name);
        if ($migrationName === '') {
            throw new Exception("Empty or wrong migration name: {$name}");
        }

        $ts = time();
        $migrationName = "{$this->fileNamePrefix}_{$ts}_{$migrationName}";
        $pathToFile = $this->getPathToMigrationFile($migrationName);
        $migrationData = [
            'name' => $migrationName,
            'parentClass' => '\\marvin255\\bxmigrate\\migrate\\Coded',
        ];
        $migrationData = array_merge($migrationData, $data);

        if ($template === null) {
            list($template, $defaultData) = $this->getDefaultView($name);
            $migrationData = array_merge($defaultData, $migrationData);
        }

        if (!file_exists($template)) {
            throw new Exception(
                "Can't find migration template file {$template} for migration {$name}"
            );
        }

        $migrationText = $this->renderMigration($template, $migrationData);
        file_put_contents($pathToFile, $migrationText);

        return $migrationName;
    }

    /**
     * @inheritdoc
     */
    public function getPathToMigrationFile($migrationName)
    {
        return "{$this->folder}/{$migrationName}.php";
    }

    /**
     * Рендерит файл миграции на основании указанного шаблона миграции и данных,
     * которые были получены от пользователя.
     *
     * @param string $___view___
     * @param array  $___data___
     *
     * @return string
     */
    protected function renderMigration($___view___, array $___data___ = null)
    {
        ob_start();
        ob_implicit_flush(false);

        if ($___data___) {
            extract($___data___);
        }
        include $___view___;

        return ob_get_clean();
    }

    /**
     * Пробует распарсить имя миграции, чтобы на его основании подобрать правильный шаблон миграции.
     * Если найден шаблон по регулрному выражению, то возвращает путь к данному шаблону и те параметры,
     * что удалось извлечь регулярному выражению. В противном случае возвращает путь до шаблона default.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getDefaultView($name)
    {
        $view = 'default.php';
        $params = [];
        foreach ($this->smartViews as $file => $regular) {
            if (!preg_match($regular, $name, $matches)) {
                continue;
            }
            $view = "{$file}.php";
            foreach ($matches as $key => $val) {
                if ($key === 0) {
                    continue;
                }
                $params["smart_param_{$key}"] = $val;
            }
            break;
        }

        return [$this->templatesFolder . '/' . $view, $params];
    }

    /**
     * Очищает название миграции от невалидных символов,
     * для использования его в качестве имени файла и класса.
     *
     * @param string $name
     *
     * @return string
     */
    protected function clearMigartionName($name)
    {
        $return = preg_replace('/[^0-9a-z_]/i', '_', $name);
        $return = trim($return, " \t\n\r\0\x0B_");

        return $return;
    }
}
