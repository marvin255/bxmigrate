<?php

namespace marvin255\bxmigrate\migrate\traits;

use marvin255\bxmigrate\migrate\Exception;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Highloadblock\HighloadBlockLangTable;

/**
 * Трэйт с функциями для высоконагруженных инфоблоков.
 */
trait HlBlock
{
    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
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
        if ($id = $this->HLGetIdByCode($data['NAME'])) {
            throw new Exception("Hl entity with name {$data['NAME']} ({$id}) already exists");
        }
        $arLoad = $data;
        unset($arLoad['LANGS']);
        $result = HighloadBlockTable::add($arLoad);
        if ($result->isSuccess()) {
            if (!empty($data['LANGS'])) {
                $this->HLSetLangs($result->getId(), $data['LANGS']);
            }
            $return[] = "Add {$data['NAME']} (" . $result->getId() . ') highload block';
        } else {
            throw new Exception("Can't create {$data['NAME']} highload block: " . implode(', ', $result->getErrorMessages()));
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
    protected function HLUpdate(array $data)
    {
        $return = [];
        if (empty($data['NAME'])) {
            throw new Exception('You must set NAME');
        }
        if ($id = $this->HLGetIdByCode($data['NAME'])) {
            $arLoad = $data;
            unset($arLoad['LANGS'], $arLoad['NAME']);
            $result = HighloadBlockTable::update($id, $arLoad);
            if ($res->isSuccess()) {
                if (!empty($data['LANGS'])) {
                    $this->HLSetLangs($id, $data['LANGS']);
                }
                $return[] = "Update {$data['NAME']} ({$id}) highload block";
            } else {
                throw new Exception("Can't update {$data['NAME']} ({$id}) highload block: " . implode(', ', $result->getErrorMessages()));
            }
        } else {
            throw new Exception("Hl entity with name {$data['NAME']} does not exist");
        }

        return $return;
    }

    /**
     * @param string $entity
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function HLDelete($entity)
    {
        $return = [];
        $id = $this->HLGetIdByCode($entity);
        if ($id) {
            $res = HighloadBlockTable::delete($id);
            if ($res->isSuccess()) {
                $return[] = "Delete highload block {$entity} ({$id})";
            } else {
                throw new Exception("Can't delete {$entity} ({$id}) highload block: " . implode(', ', $result->getErrorMessages()));
            }
        } else {
            throw new Exception("Hl entity with name {$entity} does not exist");
        }

        return $return;
    }

    /**
     * @param string $entity
     *
     * @return mixed
     */
    protected function HLGetIdByCode($entity)
    {
        $filter = [
            'filter' => ['=NAME' => $entity],
        ];
        $hlblock = HighloadBlockTable::getRow($filter);

        return !empty($hlblock['ID']) ? $hlblock['ID'] : null;
    }

    /**
     * Задает языковые параметры для hl блока.
     *
     * @param int   $hlId  Идентификатор блока
     * @param array $langs Массив переводов
     *
     * @throws \marvin255\bxmigrate\migrate\Exception
     */
    protected function HLSetLangs($hlId, array $langs)
    {
        $hlId = (int) $hlId;
        if (!$hlId) {
            throw new Exception('Empty Hl id for langs');
        }

        $res = HighloadBlockLangTable::getList([
            'filter' => ['ID' => $hlId],
        ]);
        while ($loc = $res->fetch()) {
            HighloadBlockLangTable::delete($loc['ID']);
        }

        foreach ($langs as $langId => $langValue) {
            $langRes = HighloadBlockLangTable::add([
                'ID' => $hlId,
                'LID' => $langId,
                'NAME' => $langValue,
            ]);
            if (!$langRes->isSuccess()) {
                throw new Exception("Can't create lang {$langId} for {$hlId} highload block: " . implode(', ', $langRes->getErrorMessages()));
            }
        }
    }
}
