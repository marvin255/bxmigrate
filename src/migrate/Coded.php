<?php

namespace marvin255\bxmigrate\migrate;

use Bitrix\Main\Loader;

/**
 * Базовая миграция для битрикса. Изменения задаются через использование функций стандартного API битрикса.
 */
abstract class Coded implements \marvin255\bxmigrate\IMigrate
{
    use Module;
    use HlBlock;
    use UserField;
    use Group;
    use Iblock;
    use IblockProperty;
    use IblockType;

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
    public function getName()
    {
        return get_class($this);
    }

    /**
     * {@inheritdoc}
     */
    public function managerUp()
    {
        $result = null;
        BXClearCache(true, '/');
        if (method_exists($this, 'unsafeUp')) {
            $result = $this->unsafeUp();
        } else {
            global $DB;
            $DB->StartTransaction();
            try {
                $result = $this->up();
                $DB->Commit();
            } catch (\Exception $e) {
                $DB->Rollback();
                throw new Exception($e->getMessage());
            }
        }
        BXClearCache(true, '/');

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function managerDown()
    {
        $result = null;
        BXClearCache(true, '/');
        if (method_exists($this, 'unsafeDown')) {
            $result = $this->unsafeDown();
        } else {
            global $DB;
            $DB->StartTransaction();
            try {
                $result = $this->down();
                $DB->Commit();
            } catch (\Exception $e) {
                $DB->Rollback();
                throw new Exception($e->getMessage());
            }
        }
        BXClearCache(true, '/');

        return $result;
    }
}
