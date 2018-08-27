<?php

namespace marvin255\bxmigrate\migrate;

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Exception;

/**
 * Базовая миграция для битрикса. Изменения задаются через использование функций стандартного API битрикса.
 */
abstract class Coded implements \marvin255\bxmigrate\IMigrate
{
    use traits\Module;
    use traits\HlBlock;
    use traits\UserField;
    use traits\Group;
    use traits\Iblock;
    use traits\IblockProperty;
    use traits\IblockType;
    use traits\EmailEvent;
    use traits\EmailTemplate;

    /**
     * В конструкторе подключаем все модули битрикса, которые будем использовать в миграции.
     */
    public function __construct()
    {
        Loader::includeModule('iblock');
        Loader::includeModule('highloadblock');
    }

    /**
     * {@inheritdoc}
     */
    public function managerUp()
    {
        $result = null;

        $this->clearCache();
        $conn = $this->getConnection();

        if (method_exists($this, 'unsafeUp')) {
            $result = $this->unsafeUp();
        } else {
            global $DB;
            $conn->startTransaction();
            try {
                $result = $this->up();
                $conn->commitTransaction();
            } catch (Exception $e) {
                $conn->rollbackTransaction();
                throw $e;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function managerDown()
    {
        $result = null;

        $this->clearCache();
        $conn = $this->getConnection();

        if (method_exists($this, 'unsafeDown')) {
            $result = $this->unsafeDown();
        } else {
            global $DB;
            $conn->startTransaction();
            try {
                $result = $this->down();
                $conn->commitTransaction();
            } catch (Exception $e) {
                $conn->rollbackTransaction();
                throw $e;
            }
        }

        return $result;
    }

    /**
     * Очищает все виды кэша.
     */
    protected function clearCache()
    {
        global $USER_FIELD_MANAGER;
        if ($USER_FIELD_MANAGER) {
            $USER_FIELD_MANAGER->CleanCache();
        }

        BXClearCache(true, '/');
    }

    /**
     * Возвращает объект для соединения с бд.
     *
     * @return \Bitrix\Main\DB\Connection
     */
    protected function getConnection()
    {
        $connection = Application::getConnection();
        $connection->clearCaches();

        return $connection;
    }
}
