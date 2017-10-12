<?php

namespace marvin255\bxmigrate\migrate\traits;

use marvin255\bxmigrate\migrate\Exception;
use CIBlockType;

/**
 * Трэйт с функциями для типов инфоблоков.
 */
trait IblockType
{
    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockTypeCreate(array $data)
    {
        $return = [];
        if (empty($data['ID'])) {
            throw new Exception('You must set iblock type ID');
        }
        $res = CIBlockType::GetByID($data['ID']);
        if ($ob = $res->Fetch()) {
            throw new Exception("Iblock type {$data['ID']} already exists");
        }
        $obBlocktype = new CIBlockType();
        $res = $obBlocktype->Add(array_merge([
            'ID' => $data['ID'],
            'SECTIONS' => 'Y',
            'IN_RSS' => 'N',
            'SORT' => 500,
        ], $data));
        if ($res) {
            $return[] = "Add {$data['ID']} iblock type";
        } else {
            throw new Exception("Can't create {$data['ID']} iblock type");
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
    protected function IblockTypeUpdate($data)
    {
        $return = [];
        if (empty($data['ID'])) {
            throw new Exception('You must set iblock type ID');
        }
        $res = CIBlockType::GetList([], [
            '=ID' => $data['ID'],
        ]);
        if ($ob = $res->Fetch()) {
            $ib = new CIBlockType();
            $id = $ib->Update($ob['ID'], $data);
            if ($id) {
                $return[] = "Update {$data['ID']} iblock type";
            } else {
                throw new Exception("Can't create {$data['ID']} iblock type");
            }
        } else {
            throw new Exception("Iblock type {$data['ID']} doesn't exists");
        }

        return $return;
    }

    /**
     * @param string $name
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockTypeDelete($name)
    {
        $return = [];
        $name = trim($name);
        if (empty($name)) {
            throw new Exception('You must set iblock type ID');
        }
        if (CIBlockType::Delete($name)) {
            $return[] = "Delete {$name} iblock type";
        } else {
            throw new Exception("Can't delete {$name} iblock type");
        }

        return $return;
    }
}
