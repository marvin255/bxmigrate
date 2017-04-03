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
     * @param array $data
     * @param array $fields
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

        $sites = !empty($data['LID']) ? $data['LID'] : null;
        if ($sites === null) {
            $rsSite = CSite::GetList($by = 'sort', $order = 'desc', ['DEFAULT' => 'Y']);
            if ($obSite = $rsSite->Fetch()) {
                $sites[] = $obSite['LID'];
            } else {
                throw new Exception('Can not find default site to create iblock');
            }
        }

        $iblockId = $this->IblockGetIdByCode($data['CODE'], $sites);
        if ($iblockId) {
            throw new Exception("Iblock {$data['CODE']} ($iblockId) already exists");
        }

        $ib = new CIBlock();
        $id = $ib->Add(array_merge([
            'ACTIVE' => 'Y',
            'CODE' => $data['CODE'],
            'XML_ID' => $data['CODE'],
            'SITE_ID' => $sites,
            'LIST_PAGE_URL' => '',
            'DETAIL_PAGE_URL' => '',
            'SECTION_PAGE_URL' => '',
            'CANONICAL_PAGE_URL' => '',
            'SORT' => 500,
            'INDEX_ELEMENT' => 'N',
            'INDEX_SECTION' => 'N',
        ], $data));
        if ($id) {
            $return[] = "Add {$data['CODE']} ({$id}) iblock";
            if ($id && $fields) {
                $return = array_merge($return, $this->IblockSetFields($id, $fields));
            }
        } else {
            throw new Exception("Can't create {$data['CODE']} iblock: {$ib->LAST_ERROR}");
        }

        return $return;
    }

    /**
     * @param array $data
     * @param array $fields
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
        $iblock = $this->IblockGetIdByCode(
            $name,
            !empty($fields['SITE_ID']) ? $fields['SITE_ID'] : []
        );
        if ($iblock) {
            $ib = new CIBlock();
            $res = $ib->Update($iblock, $data);
            if ($res) {
                $return[] = "Update {$data['CODE']} ({$iblock}) iblock";
                if ($fields) {
                    $return = array_merge($return, $this->IblockSetFields($iblock, $fields));
                }
            } else {
                throw new Exception("Can't update {$data['CODE']} ({$iblock}) iblock: {$ib->LAST_ERROR}");
            }
        } else {
            throw new Exception("Iblock {$data['CODE']} doesn't exists");
        }

        return $return;
    }

    /**
     * @param string $id
     * @param array  $fields
     *
     * @return array
     */
    protected function IblockSetFields($id, array $fields)
    {
        $old_fields = CIBlock::getFields($id);
        $fields = array_merge($old_fields, $fields);
        CIBlock::setFields($id, $fields);
        
        return ['Set fields for iblock with id = ' . $id];
    }

    /**
     * @param string $code
     * @param array  $sites
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockDelete($name, array $sites = array())
    {
        $return = [];
        if (empty($name)) {
            throw new Exception('You must set iblock CODE');
        }
        $iblock = $this->IblockGetIdByCode($name, $sites);
        if ($iblock) {
            if (CIBlock::Delete($iblock)) {
                $return[] = "Delete {$name} ({$iblock}) iblock";
            } else {
                throw new Exception("Can't delete {$name} ({$iblock}) iblock");
            }
        } else {
            throw new Exception("Iblock {$name} doesn't exists");
        }

        return $return;
    }

    /**
     * @param string $code
     * @param array  $siteId
     *
     * @return int|string
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockGetIdByCode($code, array $siteId = array())
    {
        if (empty($siteId)) {
            $rsSite = CSite::GetList($by = 'sort', $order = 'desc', ['DEFAULT' => 'Y']);
            if ($ob = $rsSite->fetch()) {
                $siteId[] = $ob['LID'];
            } else {
                throw new Exception('Can not find default site to search iblock');
            }
        }
        $res = CIBlock::GetList([], [
            'CODE' => $code,
            'CHECK_PERMISSIONS' => 'N',
            'SITE_ID' => $siteId,
        ]);
        $ob = $res->Fetch();

        return !empty($ob['ID']) ? $ob['ID'] : null;
    }
}
