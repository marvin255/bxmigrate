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
     * Создает объект миграции по указанному имени.
     *
     * @param string $name
     *
     * @return \marvin255\bxmigrate\IMigrate
     */
    public function instantiateMigration($name);

    /**
     * Проверяет существует ли миграция с указанным именем в репозитории.
     *
     * @param string $migrationName
     *
     * @return bool
     */
    public function isMigrationExists($migrationName);

    /**
     * Создает в хранилище новую миграцию с указанным именем. В результате вернет
     * имя файла созданной миграции.
     *
     * Если не указан путь к фйлу с шаблоном, то будет поключен шаблон по умолчанию.
     * С помощью третьего массива можно передать данные, которые попадут в шаблон
     * миграции.
     *
     * @param string $name
     * @param string $template
     * @param array  $data
     *
     * @return string
     */
    public function create($name, $template = null, array $data = []);

    /**
     * Возвращает путь до файлас миграцией по ее имени.
     *
     * @param string $migrationName
     *
     * @return string
     */
    public function getPathToMigrationFile($migrationName);
}
