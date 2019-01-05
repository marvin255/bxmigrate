<?php

declare(strict_types=1);

namespace Marvin255\Bxmigrate\Repository;

/**
 * Интерфейс для объекта, который хранит в себе единичную запись о миграции
 * в репозитории.
 */
interface EntityInterface
{
    /**
     * Возвращает строковое имя данной миграции.
     */
    public function getName(): string;

    /**
     * Возвращает текстовое содержимое миграции.
     */
    public function getContent(): string;
}
