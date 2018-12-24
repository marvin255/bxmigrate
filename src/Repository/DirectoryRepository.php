<?php

declare(strict_types=1);

namespace Marvin255\Bxmigrate\Repository;

use Iterator;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use CallbackFilterIterator;
use InvalidArgumentException;

/**
 * Объект, который хранит миграции в указанной папке.
 */
class DirectoryRepository implements RepositoryInterface
{
    /**
     * @var string
     */
    private $dir;
    /**
     * @var string
     */
    private $regexp;
    /**
     * @var Iterator|null
     */
    private $iterator;

    /**
     * @param string $dir    Каталог, в котором рекурсивно будут искаться файлы миграций
     * @param string $regexp Регулярное выражение, по которому буду фильтроваться файлы миграций
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $dir, string $regexp = null)
    {
        if (!is_dir($dir) || !file_exists($dir) || !is_writable($dir)) {
            throw new InvalidArgumentException(
                "Directory {$dir} for repository doesn't exist or isn't writable"
            );
        }

        $this->dir = $dir;
        $this->regexp = $regexp ?: '/^migrate_.*\.php$/i';
    }

    /**
     * Проверяет существует ли миграция с указанным именем.
     */
    public function isMigrationExists(string $migrationName): bool
    {
        $isMigrationExists = false;

        foreach ($this as $iteratedMigrationName) {
            if ($iteratedMigrationName === $migrationName) {
                $isMigrationExists = true;
                break;
            }
        }

        return $isMigrationExists;
    }

    /**
     * Загружает класс миграции по ее имени.
     */
    public function loadMigrationClass(string $migrationName): string
    {
    }

    /**
     * @see Iterator
     */
    public function rewind()
    {
        $this->getOrCreateIterator()->rewind();
    }

    /**
     * @see Iterator
     */
    public function current()
    {
        return pathinfo($this->getOrCreateIterator()->current()->getPathname(), PATHINFO_FILENAME);
    }

    /**
     * @see Iterator
     */
    public function key()
    {
        return $this->getOrCreateIterator()->key();
    }

    /**
     * @see Iterator
     */
    public function next()
    {
        $this->getOrCreateIterator()->next();
    }

    /**
     * @see Iterator
     */
    public function valid()
    {
        return $this->getOrCreateIterator()->valid();
    }

    /**
     * Возвращает объект итератора для поиска файлов миграций.
     */
    protected function getOrCreateIterator(): Iterator
    {
        if ($this->iterator instanceof Iterator === false) {
            $dirIterator = new RecursiveDirectoryIterator(
                $this->dir,
                FilesystemIterator::SKIP_DOTS | FilesystemIterator::CURRENT_AS_FILEINFO
            );
            $iteratorIterator = new RecursiveIteratorIterator(
                $dirIterator,
                RecursiveIteratorIterator::SELF_FIRST
            );
            $this->iterator = new CallbackFilterIterator(
                $iteratorIterator,
                function ($current, $key, $iterator) {
                    return $current->isFile() && preg_match($this->regexp, $current->getBasename());
                }
            );
        }

        return $this->iterator;
    }
}
