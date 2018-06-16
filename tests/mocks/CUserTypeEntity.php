<?php

/**
 * Мок для класса полей HL блока.
 */
class CUserTypeEntity
{
    /**
     * @var string|null
     */
    public static $errorOnAdd;
    /**
     * @var array
     */
    protected static $entities = [];

    /**
     * Проксит поиск сущности hl блока.
     *
     * @param array $order
     * @param array $filter
     *
     * @return QueryResult
     */
    public static function getList(array $order, array $filter = [])
    {
        if (empty($filter)) {
            $return = self::$entities;
        } else {
            $return = [];
            foreach (self::$entities as $entity) {
                $isSuites = true;
                foreach ($filter as $filterItem => $filterValue) {
                    if (!preg_match('/^\=?([^\=]+)$/', $filterItem, $matches)) {
                        continue;
                    }
                    if (!isset($entity[$matches[1]]) || $entity[$matches[1]] != $filterValue) {
                        $isSuites = false;
                        break;
                    }
                }
                if ($isSuites) {
                    $return[] = $entity;
                }
            }
        }

        return new QueryResult($return);
    }

    /**
     * Проксит создание поля hl сущности.
     *
     * @param array $add
     *
     * @return int|bool
     */
    public function add(array $add)
    {
        if (!empty($add['USER_TYPE_ID']) && !empty($add['ENTITY_ID']) && !empty($add['FIELD_NAME'])) {
            $add['ID'] = mt_rand();
            self::$entities[] = $add;
            $return = $add['ID'];
        } else {
            $return = false;
        }

        return self::$errorOnAdd ? false : $return;
    }

    /**
     * Очищает мок для нового теста.
     */
    public static function reset()
    {
        self::$entities = [];
        self::$errorOnAdd = null;
    }
}
