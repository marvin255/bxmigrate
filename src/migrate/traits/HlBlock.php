<?php

namespace marvin255\bxmigrate\migrate\traits;

use marvin255\bxmigrate\migrate\Exception;
use Bitrix\Highloadblock\HighloadBlockTable;

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
        $result = HighloadBlockTable::add($data);
        if ($result->isSuccess()) {
            $return[] = "Add {$data['NAME']} (".$result->getId().') highload block';
        } else {
            throw new Exception("Can't create {$data['NAME']} highload block: ".implode(', ', $result->getErrorMessages()));
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
            unset($data['NAME']);
            $result = HighloadBlockTable::update($id, $data);
            if ($res->isSuccess()) {
                $return[] = "Update {$data['NAME']} ({$id}) highload block";
            } else {
                throw new Exception("Can't update {$data['NAME']} ({$id}) highload block: ".implode(', ', $result->getErrorMessages()));
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
                throw new Exception("Can't delete {$entity} ({$id}) highload block: ".implode(', ', $result->getErrorMessages()));
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
}
