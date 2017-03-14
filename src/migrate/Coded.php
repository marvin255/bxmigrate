<?php

namespace marvin255\bxmigrate\migrate;

use Bitrix\Main\Loader;

abstract class Coded implements \marvin255\bxmigrate\IMigrate
{
    use Module;
    use HlBlock;
    use UserField;
    use Group;
    use Iblock;
    use IblockProperty;
    use IblockType;

    public function __construct()
    {
        Loader::includeModule('iblock');
        Loader::includeModule('highloadblock');
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return get_class($this);
    }

    /**
     * @return mixed
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
     * @return mixed
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
