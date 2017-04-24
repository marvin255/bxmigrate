<?php

namespace marvin255\bxmigrate\migrate\traits;

use Bitrix\Main\SiteTable;
use marvin255\bxmigrate\migrate\Exception;
use CIBlock;

/**
 * Трэйт с функциями для инфоблоков.
 */
trait Iblock
{
    /**
     * Обновляет указанный инфоблок.
     *
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

        if (!empty($data['ID'])) {
            $iblock = $this->iblockLocate($data['ID']);
            unset($data['ID']);
        } elseif (!empty($data['CODE'])) {
            $iblock = $this->iblockLocate($data['CODE']);
            unset($data['CODE']);
        } else {
            throw new Exception('You must set iblock CODE param or iblock ID param');
        }

        $ib = new CIBlock();
        $res = $ib->Update($iblock['ID'], $data);
        if ($res) {
            $return[] = "Iblock {$iblock['CODE']}({$iblock['ID']}) updated";
            if ($fields) {
                $return = array_merge(
                    $return,
                    $this->IblockSetFields($iblock['ID'], $fields)
                );
            }
        } else {
            throw new Exception("Can't update {$iblock['CODE']}({$iblock['ID']}) iblock: {$ib->LAST_ERROR}");
        }

        return $return;
    }

    /**
     * Удаляет информационный блок по его идентификатору или коду.
     *
     * @param string $id
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function IblockDelete($id)
    {
        $return = [];
        $iblock = $this->iblockLocate($id);
        if (CIBlock::Delete($iblock['ID'])) {
            $return[] = "Iblock {$iblock['CODE']}({$iblock['ID']}) deleted";
        } else {
            throw new Exception("Can't delete {$iblock['CODE']}({$iblock['ID']}) iblock");
        }

        return $return;
    }

    /**
     * Создает новый инфоблок с настройками из первого параметра.
     * Для создания нового инфоблока обязательно должен быть указан уникальный буквенный код.
     * Если указан второй параметр, то устанавливает настройки с помощью CIBlock::getFields.
     *
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

        if (empty($data['CODE'])) {
            throw new Exception('CODE param is required for iblock creation');
        } elseif (is_numeric($data['CODE'])) {
            throw new Exception('CODE param must contain at least one string char');
        }

        if (empty($data['SITE_ID'])) {
            $data['SITE_ID'] = [$this->iblockGetDefaultSiteId()];
        }

        foreach ($data['SITE_ID'] as $siteId) {
            $id = $this->iblockGetIdByCode($data['CODE'], $siteId);
            if ($id === null) {
                continue;
            }
            throw new Exception("Can't create iblock {$data['CODE']}, same iblock ID = {$id} already exists.");
        }

        $ib = new CIBlock();
        $id = $ib->add(array_merge([
           'ACTIVE' => 'Y',
           'XML_ID' => $data['CODE'],
           'LIST_PAGE_URL' => '',
           'DETAIL_PAGE_URL' => '',
           'SECTION_PAGE_URL' => '',
           'CANONICAL_PAGE_URL' => '',
           'SORT' => 500,
           'INDEX_ELEMENT' => 'N',
           'INDEX_SECTION' => 'N',
        ], $data));

        if ($id) {
            $return[] = "Iblock {$data['CODE']}({$id}) created";
            if ($id && $fields) {
                $return = array_merge($return, $this->iblockSetFields($id, $fields));
            }
        } else {
            throw new Exception("Can't create {$data['CODE']} iblock: {$ib->LAST_ERROR}");
        }

        return $return;
    }

    /**
     * Задает поля для инфоблока с помощью CIBlock::setFields.
     *
     * @param string $id
     * @param array  $fields
     *
     * @return array
     */
    protected function IblockSetFields($id, array $fields)
    {
        $iblock = $this->iblockLocate($id);
        $oldFields = CIBlock::getFields($iblock['ID']);
        $fields = array_merge($oldFields, $fields);
        CIBlock::setFields($iblock['ID'], $fields);

        return ["Set fields for iblock {$iblock['CODE']}({$iblock['ID']})"];
    }

    /**
     * Умный поиск инфоблока. Если в параметр переданы цифры, то ищет по идентификатору,
     * если цифры и буквы, то по коду. Возвращает массив полей инфоблока.
     *
     * @param string $id
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function iblockLocate($id)
    {
        if (empty($id)) {
            throw new Exception('Id parameter must not be empty');
        } elseif (!is_numeric($id)) {
            $findByCode = $this->iblockGetIdByCode($id);
            if ($findByCode === null) {
                throw new Exception("Can't find iblock by code: {$id}");
            }
            $id = $findByCode;
        }

        $iblock = $this->iblockGetById($id);
        if ($iblock === null) {
            throw new Exception("Can't find iblock by id: {$id}");
        }

        return $iblock;
    }

    /**
     * Возвращает информацию об инфоблоке по его идентификатору.
     *
     * @param int $id
     *
     * @return array|null
     */
    protected function iblockGetById($id)
    {
        $res = CIBlock::getList([], [
            'ID' => (int) $id,
            'CHECK_PERMISSIONS' => 'N',
        ]);

        return $res->fetch() ?: null;
    }

    /**
     * Возвращает идентификатор инфоблока по его коду.
     *
     * @param string $code
     * @param string $siteId
     *
     * @return string|null
     */
    protected function iblockGetIdByCode($code, $siteId = null)
    {
        $siteId = $siteId ?: $this->iblockGetDefaultSiteId();
        $res = CIBlock::getList([], [
            'CODE' => $code,
            'CHECK_PERMISSIONS' => 'N',
            'SITE_ID' => $siteId,
        ]);
        $iblock = $res->fetch();

        return !empty($iblock['ID']) ? $iblock['ID'] : null;
    }

    /**
     * Возвращает идентификатор для сайта по умолчанию.
     *
     * @return string
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function iblockGetDefaultSiteId()
    {
        $return = null;
        $res = SiteTable::getRow(['filter' => ['DEF' => 'Y']]);
        if (!$res) {
            $res = SiteTable::getRow(['order' => ['SORT' => 'asc']]);
            if (!$res) {
                throw new Exception('Can not find default site for iblock');
            }
        }

        return $res['LID'];
    }
}
