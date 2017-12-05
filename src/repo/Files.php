<?php

namespace marvin255\bxmigrate\repo;

use marvin255\bxmigrate\IMigrateRepo;

/**
 * Хранилище миграций, которое использует файлы php и классы с соответствующими именами для получения миграций.
 */
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
     * Задаем в конструкторе настройки хранилища.
     *
     * @param string $folder          Путь до папки, в которой хранятся миграции
     * @param string $templatesFolder Путь до папки, в которой хранятся шаблоны для создания миграций
     * @param string $parentClass     Класс, от которого будут унаследованы создаваемые миграции
     * @param string $fileNamePrefix  Префикс, который будет использован в имени файла миграции
     *
     * @throws \marvin255\bxmigrate\repo\Exception
     */
    public function __construct(
        $folder,
        $templatesFolder = null,
        $parentClass = '\\marvin255\\bxmigrate\\migrate\\Coded',
        $fileNamePrefix = 'migrate'
    ) {
        if (empty($folder) || !is_dir($folder) || !is_writable($folder)) {
            throw new Exception(
                'Migration folder does not exist: ' . (empty($folder) ? 'null' : $folder)
            );
        } else {
            $this->folder = $folder;
        }
        $templatesFolder = $templatesFolder ? $templatesFolder : __DIR__ . '/../../views';
        if (!is_dir($templatesFolder)) {
            throw new Exception(
                'Migration template folder does not exist: ' . (empty($templatesFolder) ? 'null' : $templatesFolder)
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
     * @var array
     */
    protected $migrations = null;

    /**
     * {@inheritdoc}
     */
    public function getMigrations()
    {
        if ($this->migrations === null) {
            $this->migrations = [];
            $regexp = $this->fileNamePrefix ? '/^(' . $this->fileNamePrefix . '\S+)\.php$/' : '/^(\S+)\.php$/';
            foreach (scandir($this->folder) as $file) {
                if (!preg_match($regexp, $file, $matches)) {
                    continue;
                }
                $this->migrations[] = $matches[1];
            }
        }

        return $this->migrations;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxmigrate\repo\Exception
     */
    public function instantiateMigration($name)
    {
        $migrations = $this->getMigrations();
        if (!in_array($name, $migrations)) {
            throw new Exception("Can't find file for migration with name {$name}");
        }
        require_once "{$this->folder}/{$name}.php";
        if (!is_subclass_of($name, '\marvin255\bxmigrate\IMigrate')) {
            throw new Exception("File {$name} has no migration class");
        }

        return new $name();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxmigrate\repo\Exception
     */
    public function create($mName)
    {
        $name = $this->clearMigartionName($mName);
        if ($name === '') {
            throw new Exception('Can not create migration file for name: ' . $mName);
        }
        $name = $this->fileNamePrefix . '_' . time() . '_' . $name;
        $fileName = "{$this->folder}/{$name}.php";
        if (file_exists($fileName)) {
            throw new Exception('Migration already exists: ' . $mName);
        }
        list($viewFile, $viewData) = $this->getView($mName);
        if (!file_exists($viewFile)) {
            throw new Exception('Can\'t find migration template file for migration: ' . $mName);
        }
        $viewData['name'] = $name;
        $viewData['parentClass'] = $this->parentClass;
        $migration = $this->renderMigration($viewFile, $viewData);
        file_put_contents($fileName, $migration);

        return ["Migration {$mName}($fileName) created"];
    }

    /**
     * Рендерит файл миграции на основании указанного шаблона миграции и данных, которые были получены от пользователя.
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

        $view = null;
        $params = [];
        foreach ($smartViews as $file => $regular) {
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
        if (!$view) {
            $view = 'default.php';
        }

        return [$this->templatesFolder . '/' . $view, $params];
    }

    /**
     * Очищает название миграции от невалидных символов, для использования его в качестве имени файла и класса.
     *
     * @param string
     *
     * @return string
     */
    protected function clearMigartionName($name)
    {
        return trim(preg_replace('/[^0-9a-z_]/i', '_', str_replace([' ', '.', '/', '\\'], '', $name)), '_');
    }
}
