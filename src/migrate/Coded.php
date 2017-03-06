<?php

namespace marvin255\bxmigrate\migrate;

use Bitrix\Main\Loader;

abstract class Coded implements \marvin255\bxmigrate\IMigrate
{
    /**
     * @return null
     */
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
        if (method_exists($this, 'unsafeUp')) {
            $result = $this->unsafeUp();
        } else {
            global $DB;
            BXClearCache(true, '/');
            $DB->StartTransaction();
            try {
                $result = $this->up();
                $DB->Commit();
                BXClearCache(true, '/');
            } catch (\Exception $e) {
                $DB->Rollback();
                throw new Exception($e->getMessage());
            }
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function managerDown()
    {
        $result = null;
        if (method_exists($this, 'unsafeDown')) {
            $result = $this->unsafeDown();
        } else {
            global $DB;
            BXClearCache(true, '/');
            $DB->StartTransaction();
            try {
                $result = $this->down();
                $DB->Commit();
                BXClearCache(true, '/');
            } catch (\Exception $e) {
                $DB->Rollback();
                throw new Exception($e->getMessage());
            }
        }
        return $result;
    }


    /**
     * @param array $params
     */
    protected function IblockSectionCreate(array $params)
    {
        $return = [];
        $bs = new \CIBlockSection();
        $res = $bs->Add($params);
        if (!$res) {
            throw new Exception($bs->LAST_ERROR);
        } else {
            $return[] = 'Sction with id = '.$res.' created';
        }
        return $return;
    }

    /**
     * @param array $data
     */
    protected function HLCreate(array $data)
    {
        $return = [];
        if (empty($data['NAME'])) {
            throw new Exception('You must set hl NAME');
        }
        if (empty($data['TABLE_NAME'])) {
            throw new Exception('You must set hl TABLE_NAME');
        }
        if ($this->HLGetIdByCode($data['NAME'])) {
            throw new Exception('Hl entity with name '.$data['NAME'].' already exists');
        }
        $result = \Bitrix\Highloadblock\HighloadBlockTable::add($data);
        if ($result->isSuccess()) {
            $return[] = "Add {$data['NAME']} highload block";
        } else {
            throw new Exception("Can't create {$data['NAME']} highload block: ".implode(', ', $result->getErrorMessages()));
        }
        return $return;
    }

    /**
     * @param array  $data
     */
    protected function HLUpdate(array $data)
    {
        $return = [];
        if (empty($data['NAME'])) {
            throw new Exception('You must set NAME');
        }
        if ($id = $this->HLGetIdByCode($data['NAME'])) {
            unset($data['NAME']);
            $result = \Bitrix\Highloadblock\HighloadBlockTable::update($id, $data);
            if ($res->isSuccess()) {
                $return[] = "Update {$data['NAME']} highload block";
            } else {
                throw new Exception("Can't update {$data['NAME']} highload block: ".implode(', ', $result->getErrorMessages()));
            }
        } else {
            throw new Exception("Hl entity with name '.$data['NAME'].' does not exist");
        }
        return $return;
    }

    /**
     * @param string $entity
     *
     * @return mixed
     */
    protected function HLDelete($entity)
    {
        $return = [];
        $id = $this->HLGetIdByCode($entity);
        if ($id) {
            $res = \Bitrix\Highloadblock\HighloadBlockTable::delete($id);
            if ($res->isSuccess()) {
                $return[] = "Delete highload block {$entity}";
            } else {
                throw new Exception("Can't delete {$entity} highload block");
            }
        } else {
            throw new Exception("Hl entity with name '.$entity.' does not exist");
        }
        return $return;
    }

    /**
     * @param array $entity
     *
     * @return mixed
     */
    protected function HLGetIdByCode($entity)
    {
        $filter = [
            'filter' => ['=NAME' => $entity],
        ];
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getRow($filter);

        return !empty($hlblock['ID']) ? $hlblock['ID'] : null;
    }

    /**
     * @param array $data
     */
    protected function UFCreate(array $data)
    {
        $return = [];
        global $USER_FIELD_MANAGER;
        if (empty($data['FIELD_NAME'])) {
            throw new Exception('You must set group FIELD_NAME');
        }
        if (empty($data['ENTITY_ID'])) {
            throw new Exception('You must set group ENTITY_ID');
        }
        if ($this->UFGetIdByCode($data['ENTITY_ID'], $data['FIELD_NAME'])) {
            throw new Exception('UF with code '.$data['FIELD_NAME'].' already exists');
        }
        $ib = new \CUserTypeEntity();
        $id = $ib->Add(array_merge(['USER_TYPE_ID' => 'string'], $data));
        if ($id) {
            $return[] = "Add {$data['FIELD_NAME']} user field";
            if (
                !empty($data['LIST'])
                && ($arType = $USER_FIELD_MANAGER->GetUserType($data['USER_TYPE_ID']))
                && $arType['BASE_TYPE'] == 'enum'
            ) {
                $obEnum = new \CUserFieldEnum();
                $res = $obEnum->SetEnumValues($id, $data['LIST']);
                $return[] = "Add {$data['FIELD_NAME']} user field list";
            }
        } else {
            throw new Exception("Can't create {$data['FIELD_NAME']} user field");
        }
        return $return;
    }

    /**
     * @param string $entity
     * @param array  $data
     *
     * @var bool $deleteIfExists
     */
    protected function UFUpdate(array $data)
    {
        $return = [];
        global $USER_FIELD_MANAGER;
        if (empty($data['FIELD_NAME'])) {
            throw new Exception('You must set group FIELD_NAME');
        }
        if (empty($data['ENTITY_ID'])) {
            throw new Exception('You must set group ENTITY_ID');
        }
        if ($id = $this->UFGetIdByCode($data['ENTITY_ID'], $data['FIELD_NAME'])) {
            $ib = new \CUserTypeEntity();
            $id = $ib->Update($id, $data);
            if ($id) {
                $return[] = "Update {$data['FIELD_NAME']} user field";
                if (
                    !empty($data['LIST'])
                    && ($arType = $USER_FIELD_MANAGER->GetUserType($data['USER_TYPE_ID']))
                    && $arType['BASE_TYPE'] == 'enum'
                ) {
                    $obEnum = new \CUserFieldEnum();
                    $res = $obEnum->SetEnumValues($id, $data['LIST']);
                    $return[] = "Update {$data['FIELD_NAME']} user field list";
                }
            } else {
                throw new Exception("Can't update {$data['FIELD_NAME']} user field");
            }
        } else {
            throw new Exception("Can't find {$data['FIELD_NAME']} user field");
        }
        return $return;
    }

    /**
     * @var string $entity
     * @var string $code
     */
    protected function UFDelete($entity, $code)
    {
        $return = [];
        $id = $this->UFGetIdByCode($entity, $code);
        if ($id) {
            $group = new \CUserTypeEntity();
            if ($group->Delete($id)) {
                $return[] = "Delete user field {$code}";
            } else {
                throw new Exception("Can't delete {$code} user field");
            }
        } else {
            throw new Exception("Can't find {$code} user field");
        }
        return $return;
    }

    /**
     * @var string $entity
     * @var string $code
     */
    protected function UFGetIdByCode($entity, $code)
    {
        $rsData = \CUserTypeEntity::GetList([], [
            'ENTITY_ID' => $entity,
            'FIELD_NAME' => $code,
        ]);
        if ($ob = $rsData->GetNext()) {
            return $ob['ID'];
        } else {
            return null;
        }
    }

    /**
     * @var array $data
     */
    protected function UserGroupCreate(array $data)
    {
        $return = [];
        if (empty($data['STRING_ID'])) {
            throw new Exception('You must set group STRING_ID');
        }
        if ($this->UserGetGroupIdByCode($data['STRING_ID'])) {
            throw new Exception('Group with STRING_ID '.$data['STRING_ID'].' already exists');
        }
        $ib = new \CGroup();
        $id = $ib->Add(array_merge(['ACTIVE' => 'Y'], $data));
        if ($id) {
            $return[] = "Add {$data['STRING_ID']} users group";
        } else {
            throw new Exception("Can't create {$data['STRING_ID']} users group");
        }
        return $return;
    }

    /**
     * @var string
     */
    protected function UserGroupDelete($groupName)
    {
        $return = [];
        $id = $this->UserGetGroupIdByCode($groupName);
        if ($id) {
            $group = new \CGroup();
            if ($group->Delete($id)) {
                $return[] = "Delete group {$groupName}";
            } else {
                throw new Exception("Can't delete group {$groupName}");
            }
        } else {
            throw new Exception("Group {$groupName} does not exist");
        }
        return $return;
    }

    /**
     * @var string
     *
     * @return int
     */
    protected function UserGetGroupIdByCode($code)
    {
        $rsGroups = \CGroup::GetList(($by = 'c_sort'), ($order = 'desc'), [
            'STRING_ID' => $code,
        ]);
        if ($ob = $rsGroups->Fetch()) {
            return $ob['ID'];
        } else {
            return null;
        }
    }

    /**
     * @var string $iblock
     * @var array  $data
     */
    protected function IblockPropertyCreate($iblock, array $data)
    {
        $return = [];
        $ibId = $this->IblockGetIdByCode($iblock);
        if (empty($ibId)) {
            throw new Exception('Can not find iblock '.$iblock);
        }
        if (empty($data['CODE'])) {
            throw new Exception('You must set property CODE');
        }
        $res = \CIBlockProperty::GetList([], [
            'CODE' => $data['CODE'],
            'IBLOCK_ID' => $ibId,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            throw new Exception("Property with code {$data['CODE']} already exists");
        }
        $ib = new \CIBlockProperty();
        $id = $ib->Add(array_merge([
            'IBLOCK_ID' => $ibId,
            'CODE' => $data['CODE'],
            'XML_ID' => $data['CODE'],
            'ACTIVE' => 'Y',
        ], $data));
        if ($id) {
            $return[] = "Add {$data['CODE']} iblock property";
        } else {
            throw new Exception("Can't create {$data['CODE']} iblock property");
        }
        return $return;
    }

    /**
     * @var string $iblock
     * @var array  $data
     */
    protected function IblockPropertyUpdate($iblock, array $data)
    {
        $return = [];
        $ibId = $this->IblockGetIdByCode($iblock);
        if (empty($ibId)) {
            throw new Exception('Can not find iblock '.$iblock);
        }
        if (empty($data['CODE'])) {
            throw new Exception('You must set property CODE');
        }
        $res = \CIBlockProperty::GetList([], [
            'CODE' => $data['CODE'],
            'IBLOCK_ID' => $ibId,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            $ib = new \CIBlockProperty();
            $id = $ib->Update($ob['ID'], $data);
            if ($id) {
                $return[] = "Update {$data['CODE']} iblock property";
            } else {
                throw new Exception("Can't update {$data['CODE']} iblock property");
            }
        } else {
            throw new Exception("Can't find {$data['CODE']} iblock property");
        }
        return $return;
    }

    /**
     * @param string $iblock
     * @param string $code
     */
    protected function IblockPropertyDelete($iblock, $code)
    {
        $return = [];
        $ibId = $this->IblockGetIdByCode($iblock);
        if (!$ibId) {
            throw new Exception("Can't find iblock {$iblock}");
        }
        $res = \CIBlockProperty::GetList([], [
            'CODE' => $code,
            'IBLOCK_ID' => $ibId,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            if (\CIBlockProperty::Delete($ob['ID'])) {
                $return[] = "Delete {$code} iblock property";
            } else {
                throw new Exception("Can't delete iblock property {$code}");
            }
        }
        return $return;
    }

    /**
     * @var array
     * @var array $fields
     * @var bool  $deleteIfExists
     */
    protected function IblockCreate(array $data, array $fields = null, $deleteIfExists = false)
    {
        global $DB;

        if (empty(trim($data['CODE']))) {
            throw new Exception('You must set iblock CODE');
        }
        $name = trim($data['CODE']);

        $fire = false;
        $res = \CIBlock::GetList([], [
            'CODE' => $name,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            if ($deleteIfExists) {
                $this->IblockDelete($name);
                $fire = true;
            } else {
                $id = $ob['ID'];
            }
        } else {
            $fire = true;
        }

        if ($fire) {
            $rsSite = \CSite::GetList($by = 'sort', $order = 'desc', ['DEFAULT' => 'Y']);
            $sites = [];
            while ($obSite = $rsSite->Fetch()) {
                $sites[] = $obSite['LID'];
            }
            $ib = new \CIBlock();
            $id = $ib->Add(array_merge([
                'ACTIVE' => 'Y',
                'CODE' => $name,
                'XML_ID' => $name,
                'LID' => $sites[0],
                'LIST_PAGE_URL' => '',
                'DETAIL_PAGE_URL' => '',
                'SECTION_PAGE_URL' => '',
                'CANONICAL_PAGE_URL' => '',
                'SORT' => 500,
                'INDEX_ELEMENT' => 'N',
                'INDEX_SECTION' => 'N',
            ], $data));
            if ($id) {
                echo "Add {$name} iblock";
                if ($id && $fields) {
                    $this->IblockSetFields($name, $fields);
                }
            } else {
                throw new Exception("Can't create {$name} iblock type");
            }
        }

        return $id;
    }

    /**
     * @var array
     * @var array $fields
     * @var bool  $deleteIfExists
     */
    protected function IblockUpdate(array $data, array $fields = null)
    {
        global $DB;

        if (empty(trim($data['CODE']))) {
            throw new Exception('You must set iblock CODE');
        }
        $name = trim($data['CODE']);

        $fire = false;
        $res = \CIBlock::GetList([], [
            'CODE' => $name,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            $ib = new \CIBlock();
            $id = $ib->Update($ob['ID'], $data);
            if ($id) {
                echo "Update {$name} iblock";
                if ($id && $fields) {
                    $this->IblockSetFields($name, $fields);
                }
            } else {
                throw new Exception("Can't create {$name} iblock type");
            }
        } else {
            throw new Exception("Iblock don't exists");
        }

        return $id;
    }

    /**
     * @param string $code
     * @param array  $fields
     */
    protected function IblockSetFields($code, array $fields)
    {
        $id = $this->IblockGetIdByCode($code);
        if ($id) {
            $old_fields = \CIBlock::getFields($id);
            $fields = array_merge($old_fields, $fields);
            \CIBlock::setFields($id, $fields);
        } else {
            throw new Exception("Can't set fields for {$code} iblock");
        }
    }

    /**
     * @var string
     */
    protected function IblockDelete($name)
    {
        $name = trim($name);
        if (empty($name)) {
            throw new Exception('You must set iblock CODE');
        }
        global $DB;
        $res = \CIBlock::GetList([], [
            'CODE' => $name,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            $DB->StartTransaction();
            if (!\CIBlock::Delete($ob['ID'])) {
                $DB->Rollback();
                $error = "Can't delete {$name} iblock";
            } else {
                echo "Delete {$name} iblock";
            }
            $DB->Commit();
            if (isset($error)) {
                throw new Exception($error);
            }
        }
    }

    /**
     * @var string
     *
     * @return int
     */
    protected function IblockGetIdByCode($code)
    {
        $res = \CIBlock::GetList([], [
            'CODE' => $code,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            return $ob['ID'];
        } else {
            return null;
        }
    }

    /**
     * @param array $data
     * @param bool  $deleteIfExists
     */
    protected function IblockTypeCreate(array $data, $deleteIfExists = false)
    {
        global $DB;

        if (empty(trim($data['ID']))) {
            throw new Exception('You must set iblock type ID');
        }
        $name = trim($data['ID']);

        $fire = false;
        $res = \CIBlockType::GetByID($name);
        if ($ob = $res->Fetch()) {
            if ($deleteIfExists) {
                $this->IblockTypeDelete($name);
                $fire = true;
            }
        } else {
            $fire = true;
        }

        if ($fire) {
            $obBlocktype = new \CIBlockType();
            $DB->StartTransaction();
            $res = $obBlocktype->Add(array_merge([
                'ID' => $name,
                'SECTIONS' => 'Y',
                'IN_RSS' => 'N',
                'SORT' => 500,
            ], $data));
            if (!$res) {
                $DB->Rollback();
                $error = "Can't create {$name} iblock type";
            } else {
                echo "Add {$name} iblock type";
            }
            $DB->Commit();
            if (isset($error)) {
                throw new Exception($error);
            }
        }

        return $name;
    }

    /**
     * @param string $name
     */
    protected function IblockTypeUpdate($data)
    {
        global $DB;

        if (empty(trim($data['ID']))) {
            throw new Exception('You must set iblock type ID');
        }
        $name = trim($data['ID']);

        $res = \CIBlockType::GetList([], [
            '=ID' => $name,
        ]);
        if ($ob = $res->Fetch()) {
            $ib = new \CIBlockType();
            $DB->StartTransaction();
            $id = $ib->Update($ob['ID'], $data);
            if ($id) {
                $DB->Commit();
                echo "Update {$name} iblock type";
            } else {
                $DB->Rollback();
                throw new Exception("Can't create {$name} iblock type");
            }
        } else {
            throw new Exception("Iblock type don't exists");
        }

        return $id;
    }

    /**
     * @param string $name
     * @param bool   $deleteIfExists
     */
    protected function IblockTypeDelete($name)
    {
        $name = trim($name);
        if (empty($name)) {
            throw new Exception('You must set iblock CODE');
        }
        global $DB;
        $DB->StartTransaction();
        if (!\CIBlockType::Delete($name)) {
            $DB->Rollback();
            $error = "Can't delete {$name} iblock type";
        } else {
            echo "Delete {$name} iblock type";
        }
        $DB->Commit();
        if (isset($error)) {
            throw new Exception($error);
        }
    }
}
