<?php

namespace marvin255\bxmigrate\migrate\traits;

use marvin255\bxmigrate\migrate\Exception;
use CIBlock;
use CSite;

/**
 * Трэйт с функциями для инфоблоков.
 */
trait Iblock
{
    /**
     * @var array $data
     * @var array $fields
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockCreate(array $data, array $fields = null)
    {
        $return = [];
        if (empty(trim($data['CODE']))) {
            throw new Exception('You must set iblock CODE');
        }
        $res = CIBlock::GetList([], ['CODE' => $data['CODE'], 'CHECK_PERMISSIONS' => 'N']);
        if ($ob = $res->Fetch()) {
            throw new Exception('Iblock '.$data['CODE'].' already exists');
        }
        $rsSite = CSite::GetList($by = 'sort', $order = 'desc', ['DEFAULT' => 'Y']);
        $sites = [];
        while ($obSite = $rsSite->Fetch()) {
            $sites[] = $obSite['LID'];
        }
        $ib = new CIBlock();
        $id = $ib->Add(array_merge([
            'ACTIVE' => 'Y',
            'CODE' => $data['CODE'],
            'XML_ID' => $data['CODE'],
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
            $return[] = "Add {$data['CODE']} iblock";
            if ($id && $fields) {
                $return = array_merge($return, $this->IblockSetFields($data['CODE'], $fields));
            }
        } else {
            throw new Exception("Can't create {$data['CODE']} iblock type");
        }

        return $return;
    }

    /**
     * @var array $data
     * @var array $fields
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockUpdate(array $data, array $fields = null)
    {
        $return = [];
        if (empty(trim($data['CODE']))) {
            throw new Exception('You must set iblock CODE');
        }
        $res = CIBlock::GetList([], ['CODE' => $data['CODE'], 'CHECK_PERMISSIONS' => 'N']);
        if ($ob = $res->Fetch()) {
            $ib = new CIBlock();
            $id = $ib->Update($ob['ID'], $data);
            if ($id) {
                $return[] = "Update {$data['CODE']} iblock";
                if ($id && $fields) {
                    $return = array_merge($return, $this->IblockSetFields($data['CODE'], $fields));
                }
            } else {
                throw new Exception("Can't create {$data['CODE']} iblock type");
            }
        } else {
            throw new Exception("Iblock {$data['CODE']} doesn't exists");
        }

        return $return;
    }

    /**
     * @param string $code
     * @param array  $fields
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockSetFields($code, array $fields)
    {
        $return = [];
        $id = $this->IblockGetIdByCode($code);
        if ($id) {
            $old_fields = CIBlock::getFields($id);
            $fields = array_merge($old_fields, $fields);
            CIBlock::setFields($id, $fields);
            $return[] = "Set fields for {$code} iblock";
        } else {
            throw new Exception("Can't set fields for {$code} iblock");
        }

        return $return;
    }

    /**
     * @var string $code
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockDelete($name)
    {
        $return = [];
        if (empty($name)) {
            throw new Exception('You must set iblock CODE');
        }
        $res = CIBlock::GetList([], [
            'CODE' => $name,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        if ($ob = $res->Fetch()) {
            if (CIBlock::Delete($ob['ID'])) {
                $return[] = "Delete {$name} iblock";
            } else {
                throw new Exception("Can't delete {$name} iblock");
            }
        } else {
            throw new Exception("Iblock {$name} doesn't exists");
        }

        return $return;
    }

    /**
     * @var string $code
     *
     * @return int|string
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockGetIdByCode($code)
    {
        $res = CIBlock::GetList([], [
            'CODE' => $code,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        $ob = $res->Fetch();

        return !empty($ob['ID']) ? $ob['ID'] : null;
    }
}
