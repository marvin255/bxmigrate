<?php

declare(strict_types=1);

namespace Marvin255\Bxmigrate\Repository;

use Iterator;

/**
 * Интерфейс для объекта, который предоставляет доступ к миграциям.
 */
interface RepositoryInterface extends Iterator
{
    /**
     * Проверяет существует ли миграция с указанным именем.
     */
    public function isMigrationExists(string $migrationName): bool;
}
