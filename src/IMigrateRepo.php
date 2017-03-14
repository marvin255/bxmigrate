<?php

namespace marvin255\bxmigrate;

/**
 * Хранилище миграций. Здесь хранятся все миграции: как уже примененные, так и еще нет.
 * Хранилище возвращает массив объектов миграций.
 */
interface IMigrateRepo
{
    /**
     * Возвращает массив объектов всех миграций, которые есть в хранилище.
     *
     * @return array
     */
    public function getMigrations();

    /**
     * Создает в хранилище новую миграцию с указанным именем.
     *
     * @param string $name
     *
     * @return string
     */
    public function create($mName);
}
