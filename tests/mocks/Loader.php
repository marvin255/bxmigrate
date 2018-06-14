<?php

namespace Bitrix\Main;

/**
 * Мок для загрузчика модулей битрикса.
 */
class Loader
{
    /**
     * Проверяет, что указано верное имя модуля для загрузки.
     * Тестируемые классы загружают только highloadblock.
     */
    public static function includeModule($name)
    {
        return $name === 'highloadblock';
    }
}
