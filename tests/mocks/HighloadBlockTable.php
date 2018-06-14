<?php

namespace Bitrix\Highloadblock;

use QueryResult;
use ExecuteResult;

/**
 * Мок для ORM таблицы hl блоков.
 */
class HighloadBlockTable
{
    /**
     * @var string|null
     */
    public static $errorOnAdd;
    /**
     * @var array
     */
    protected static $hlblocks = [];

    /**
     * Проксит поиск сущности hl блока.
     *
     * @param array $query
     *
     * @return QueryResult
     */
    public static function getList(array $query)
    {
        if (empty($query['filter'])) {
            $return = self::$hlblocks;
        } else {
            $return = [];
            foreach (self::$hlblocks as $hlblock) {
                $isSuites = true;
                foreach ($query['filter'] as $filterItem => $filterValue) {
                    if (!preg_match('/^\=?([^\=]+)$/', $filterItem, $matches)) {
                        continue;
                    }
                    if (!isset($hlblock[$matches[1]]) || $hlblock[$matches[1]] != $filterValue) {
                        $isSuites = false;
                        break;
                    }
                }
                if ($isSuites) {
                    $return[] = $hlblock;
                }
            }
        }

        return new QueryResult($return);
    }

    /**
     * Проксит создание сущности hl.
     *
     * @param array $add
     *
     * @return ExecuteResult
     */
    public static function add(array $add)
    {
        if (self::$errorOnAdd) {
            $return = new ExecuteResult(null, [self::$errorOnAdd]);
        } elseif (!empty($add['NAME']) && !empty($add['TABLE_NAME'])) {
            $add['ID'] = mt_rand();
            self::$hlblocks[] = $add;
            $return = new ExecuteResult($add['ID']);
        } else {
            $return = new ExecuteResult(null, ['need NAME and TABLE_NAME']);
        }

        return $return;
    }

    /**
     * Возвращает скомпилированный класс сущности.
     *
     * @param array $entity
     *
     * @return Entity|null
     */
    public function compileEntity(array $entity)
    {
        $return = null;
        if (!empty($entity['ID'])) {
            foreach (self::$hlblocks as $hlblock) {
                if ((int) $hlblock['ID'] === (int) $entity['ID']) {
                    $return = new Entity;
                    break;
                }
            }
        }

        return $return;
    }

    /**
     * Очищает мок для нового теста.
     */
    public static function reset()
    {
        self::$hlblocks = [];
        self::$errorOnAdd = null;
    }
}

/**
 * Мок для сущности таблицы.
 */
class Entity
{
    /**
     * Возвращает имя класса сущности hl блока.
     *
     * @return string
     */
    public function getDataClass()
    {
        return '\\Bitrix\\Highloadblock\\CheckerTable';
    }
}

/**
 * Мок для таблицы с примененными миграциями.
 */
class CheckerTable
{
    /**
     * @var string|null
     */
    public static $errorOnAdd;
    /**
     * @var string|null
     */
    public static $errorOnDelete;
    /**
     * @var array
     */
    protected static $migrations = [];

    /**
     * Проксит поиск по таблице с примененными миграциями.
     *
     * @param array $query
     *
     * @return QueryResult
     */
    public static function getList(array $query)
    {
        if (empty($query['filter'])) {
            $return = self::$migrations;
        } else {
            $return = [];
            foreach (self::$migrations as $migration) {
                $isSuites = true;
                foreach ($query['filter'] as $filterItem => $filterValue) {
                    if (!preg_match('/^\=?([^\=]+)$/', $filterItem, $matches)) {
                        continue;
                    }
                    if (!isset($migration[$matches[1]]) || $migration[$matches[1]] != $filterValue) {
                        $isSuites = false;
                        break;
                    }
                }
                if ($isSuites) {
                    $return[] = $migration;
                }
            }
        }

        return new QueryResult($return);
    }

    /**
     * Проксит создание сущности hl.
     *
     * @param array $add
     *
     * @return ExecuteResult
     */
    public static function add(array $add)
    {
        if (self::$errorOnAdd) {
            $return = new ExecuteResult(null, [self::$errorOnAdd]);
        } elseif (!empty($add['UF_MIGRATION_NAME']) && !empty($add['UF_MIGRATION_DATE'])) {
            $add['ID'] = mt_rand();
            self::$migrations[] = $add;
            $return = new ExecuteResult($add['ID']);
        } else {
            $return = new ExecuteResult(null, ['need UF_MIGRATION_NAME and UF_MIGRATION_DATE']);
        }

        return $return;
    }

    /**
     * Проксит удаление сущности hl.
     *
     * @param int $id
     *
     * @return ExecuteResult
     */
    public static function delete($id)
    {
        $return = null;
        foreach (self::$migrations as $key => $migration) {
            if ((int) $migration['ID'] === (int) $id) {
                unset(self::$migrations[$key]);
                $return = $id;
            }
        }

        return self::$errorOnDelete
            ? new ExecuteResult(null, [self::$errorOnDelete])
            : new ExecuteResult($return);
    }

    /**
     * Очищает мок для нового теста.
     */
    public static function reset()
    {
        self::$migrations = [];
        self::$errorOnAdd = null;
        self::$errorOnDelete = null;
    }
}
