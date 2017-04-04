<?php

namespace marvin255\bxmigrate\migrate\traits;

use marvin255\bxmigrate\migrate\Exception;
use CIBlockProperty;

/**
 * Трэйт с функциями для свойств инфоблоков.
 */
trait IblockProperty
{
    /**
     * Создает новое пользовательское свойство инфоблока.
     *
     * @param string $iblock
     * @param array  $data
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockPropertyCreate($iblock, array $data)
    {
        $return = [];

        $iblock = $this->iblockLocate($iblock);

        if (empty($data['CODE'])) {
            throw new Exception('You must set property CODE');
        }
        $res = CIBlockProperty::GetList([], [
            'CODE' => $data['CODE'],
            'IBLOCK_ID' => $iblock['ID'],
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            throw new Exception("Property {$data['CODE']}({$ob['ID']}) for iblock {$iblock['CODE']}({$iblock['ID']}) already exists");
        }

        $ib = new CIBlockProperty();
        $id = $ib->Add(array_merge([
            'IBLOCK_ID' => $iblock['ID'],
            'CODE' => $data['CODE'],
            'XML_ID' => $data['CODE'],
            'ACTIVE' => 'Y',
        ], $data));

        if ($id) {
            $return[] = "Property {$data['CODE']}($id) for iblock {$iblock['CODE']}({$iblock['ID']}) added";
        } else {
            throw new Exception("Can't create property {$data['CODE']} for iblock {$iblock['CODE']}({$iblock['ID']})");
        }

        return $return;
    }

    /**
     * Обновляет указанное пользовательское свойство инфоблока.
     *
     * @param string $iblock
     * @param array  $data
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockPropertyUpdate($iblock, array $data)
    {
        $return = [];

        $iblock = $this->iblockLocate($iblock);

        if (empty($data['CODE'])) {
            throw new Exception('You must set property CODE');
        }

        $res = CIBlockProperty::GetList([], [
            'CODE' => $data['CODE'],
            'IBLOCK_ID' => $iblock['ID'],
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            $ib = new CIBlockProperty();
            $id = $ib->Update($ob['ID'], $data);
            if ($id) {
                $return[] = "Property {$data['CODE']}({$ob['ID']}) for iblock {$iblock['CODE']}({$iblock['ID']}) updated";
            } else {
                throw new Exception("Can't update {$data['CODE']} property for iblock {$iblock['CODE']}({$iblock['ID']})");
            }
        } else {
            throw new Exception("Can't find {$data['CODE']} property for iblock {$iblock['CODE']}({$iblock['ID']})");
        }

        return $return;
    }

    /**
     * Удаляет указанное пользовательское свойство инфоблока.
     *
     * @param string $iblock
     * @param string $code
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockPropertyDelete($iblock, $code)
    {
        $return = [];

        $iblock = $this->iblockLocate($iblock);

        $res = CIBlockProperty::GetList([], [
            'CODE' => $code,
            'IBLOCK_ID' => $iblock['ID'],
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            if (CIBlockProperty::Delete($ob['ID'])) {
                $return[] = "Property {$code} for iblock {$iblock['CODE']}({$iblock['ID']}) deleted";
            } else {
                throw new Exception("Can't delete iblock property {$code} for iblock {$iblock['CODE']}({$iblock['ID']})");
            }
        } else {
            throw new Exception("Can't find property {$code} for iblock {$iblock['CODE']}({$iblock['ID']})");
        }

        return $return;
    }
}
