<?php

namespace marvin255\bxmigrate;

/**
 * Объект, который проверяет была ли миграция примемена к текущей базе данных.
 * Помечает миграции примененнными или наоборот непримененными.
 */
interface IMigrateChecker
{
    /**
     * Проверяет по названию миграции была ли она применена к текущей базе данных.
     *
     * @param string $migration
     *
     * @return bool
     */
    public function isChecked($migration);

    /**
     * Помечает миграцию примененной в текущей базе данных по ее названию.
     *
     * @param string $migration
     */
    public function check($migration);

    /**
     * Помечает миграцию непримененной в текущей базе данных по ее названию.
     *
     * @param string $migration
     */
    public function uncheck($migration);
}
