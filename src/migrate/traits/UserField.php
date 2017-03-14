<?php

namespace marvin255\bxmigrate\migrate\traits;

use marvin255\bxmigrate\migrate\Exception;
use CUserTypeEntity;
use CUserFieldEnum;

trait UserField
{
    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
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
        $ib = new CUserTypeEntity();
        $id = $ib->Add(array_merge(['USER_TYPE_ID' => 'string'], $data));
        if ($id) {
            $return[] = "Add {$data['FIELD_NAME']} user field";
            if (
                !empty($data['LIST'])
                && ($arType = $USER_FIELD_MANAGER->GetUserType($data['USER_TYPE_ID']))
                && $arType['BASE_TYPE'] == 'enum'
            ) {
                $obEnum = new CUserFieldEnum();
                $res = $obEnum->SetEnumValues($id, $data['LIST']);
                $return[] = "Add {$data['FIELD_NAME']} user field list";
            }
        } else {
            throw new Exception("Can't create {$data['FIELD_NAME']} user field");
        }

        return $return;
    }

    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
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
            $ib = new CUserTypeEntity();
            $id = $ib->Update($id, $data);
            if ($id) {
                $return[] = "Update {$data['FIELD_NAME']} user field";
                if (
                    !empty($data['LIST'])
                    && ($arType = $USER_FIELD_MANAGER->GetUserType($data['USER_TYPE_ID']))
                    && $arType['BASE_TYPE'] == 'enum'
                ) {
                    $obEnum = new CUserFieldEnum();
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
     * @var string
     * @var string $code
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function UFDelete($entity, $code)
    {
        $return = [];
        $id = $this->UFGetIdByCode($entity, $code);
        if ($id) {
            $group = new CUserTypeEntity();
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
     * @var string
     * @var string $code
     *
     * @return int|null
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function UFGetIdByCode($entity, $code)
    {
        $rsData = CUserTypeEntity::GetList([], [
            'ENTITY_ID' => $entity,
            'FIELD_NAME' => $code,
        ]);
        if ($ob = $rsData->GetNext()) {
            return $ob['ID'];
        } else {
            return null;
        }
    }
}
