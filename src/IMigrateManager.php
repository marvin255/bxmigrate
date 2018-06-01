<?php

namespace marvin255\bxmigrate;

/**
 * Объект для управления миграциями. Получает ссылки на хранилище миграций и объект для проверки статуса миграций.
 * Умеет все то же самое, что оба объекта по раздельности: применять миграции, откатывать, создавать новые.
 * Просто передает управление нужному объекту.
 */
interface IMigrateManager
{
    /**
     * Применяет все миграции, если $count пустой, или указанное в $count количество миграций,
     * которые не значатся в качестве примененных.
     *
     * @param int $count
     */
    public function up($count = null);

    /**
     * Пробует найти и применить миграцию с указанным именем.
     *
     * @param string $name
     */
    public function upByName($name);

    /**
     * Откатывает последнюю миграцию, если $count пустой, или указанное в $count количество  последних миграций,
     * которые значатся в качестве примененных.
     *
     * @param int $count
     */
    public function down($count = null);

    /**
     * Пробует найти и отменить миграцию с указанным именем.
     *
     * @param string $name
     */
    public function downByName($name);

    /**
     * Удаляет миграцию с указанным именем и устанавливает ее заново.
     *
     * @param string $name
     */
    public function refresh($name);

    /**
     * Помечает миграцию с указанным именем пройденной без запускаса миграции.
     *
     * @param string $name
     */
    public function check($name);

    /**
     * Создает новую миграцию с указанным именем.
     *
     * @param string $name
     */
    public function create($name);
}
