<?php

declare(strict_types=1);

namespace Marvin255\Bxmigrate\MigrationRepository;

use Iterator;
use ArrayAccess;

/**
 * Интерфейс для объекта, который хранит список миграций и предоставляет к ним
 * доступ.
 */
interface MigrationRepositoryInterface extends Iterator, ArrayAccess
{
}
