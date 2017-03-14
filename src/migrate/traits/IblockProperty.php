<?php

namespace marvin255\bxmigrate\migrate\traits;

use marvin255\bxmigrate\migrate\Exception;
use CIBlockProperty;

trait IblockProperty
{
    /**
     * @var string
     * @var array  $data
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
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
        $res = CIBlockProperty::GetList([], [
            'CODE' => $data['CODE'],
            'IBLOCK_ID' => $ibId,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            throw new Exception("Property with code {$data['CODE']} already exists");
        }
        $ib = new CIBlockProperty();
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
     * @var string
     * @var array  $data
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
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
        $res = CIBlockProperty::GetList([], [
            'CODE' => $data['CODE'],
            'IBLOCK_ID' => $ibId,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            $ib = new CIBlockProperty();
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
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockPropertyDelete($iblock, $code)
    {
        $return = [];
        $ibId = $this->IblockGetIdByCode($iblock);
        if (!$ibId) {
            throw new Exception("Can't find iblock {$iblock}");
        }
        $res = CIBlockProperty::GetList([], [
            'CODE' => $code,
            'IBLOCK_ID' => $ibId,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            if (CIBlockProperty::Delete($ob['ID'])) {
                $return[] = "Delete {$code} iblock property";
            } else {
                throw new Exception("Can't delete iblock property {$code}");
            }
        }

        return $return;
    }
}
