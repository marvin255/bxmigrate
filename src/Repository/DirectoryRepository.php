<?php

declare(strict_types=1);

namespace Marvin255\Bxmigrate\Repository;

use Iterator;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use SplFileInfo;
use CallbackFilterIterator;
use InvalidArgumentException;
use RuntimeException;
use OutOfRangeException;

/**
 * Объект, который хранит миграции в указанной папке.
 */
class DirectoryRepository implements RepositoryInterface
{
    /**
     * Путь к директории, в которой хранятся файлы миграций.
     *
     * @var string
     */
    private $dir;
    /**
     * Префикс для имени файла миграции, по которому будут отфильтрованы все файлы.
     *
     * @var string
     */
    private $prefix;
    /**
     * Расширение файлов миграций.
     *
     * @var string
     */
    private $ext;
    /**
     * Регулярное выражение для фильтрации файлов миграций.
     *
     * @var string
     */
    private $regexp;
    /**
     * Внутренний объект итератора.
     *
     * @var Iterator|null
     */
    private $iterator;

    /**
     * @param string $dir    Каталог, в котором рекурсивно будут искаться файлы миграций
     * @param string $prefix Префикс для имени файла миграции, по которому будут отфильтрованы все файлы
     * @param string $ext    Расширение файлов миграций
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $dir, string $prefix = 'migrate_', string $ext = 'php')
    {
        if (!is_dir($dir) || !file_exists($dir) || !is_writable($dir)) {
            throw new InvalidArgumentException(
                "Directory {$dir} for repository doesn't exist or isn't writable."
            );
        }

        if (!preg_match('/^[a-z0-9]+$/i', $ext)) {
            throw new InvalidArgumentException(
                "Wrong migration file extension: {$ext}."
            );
        }

        $this->dir = rtrim($dir, '/\\ ');
        $this->prefix = $prefix;
        $this->ext = $ext;
        $this->regexp = '/^' . preg_quote($prefix, '/') . '.+\.' . preg_quote($ext, '/') . '$/';
    }

    /**
     * @see ArrayAccess
     *
     * @throws OutOfRangeException
     */
    public function offsetGet($offset): PhpClassEntity
    {
        $migration = null;

        foreach ($this as $iteratedMigration) {
            if ($iteratedMigration->getName() === $offset) {
                $migration = $iteratedMigration;
                break;
            }
        }

        if ($migration === null) {
            throw new OutOfRangeException("Migration {$offset} not found.");
        }

        return $migration;
    }

    /**
     * @see ArrayAccess
     */
    public function offsetExists($offset): bool
    {
        $isMigrationExists = false;

        foreach ($this as $iteratedMigration) {
            if ($iteratedMigration->getName() === $offset) {
                $isMigrationExists = true;
                break;
            }
        }

        return $isMigrationExists;
    }

    /**
     * @see ArrayAccess
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function offsetSet($offset, $entity)
    {
        if ($entity instanceof EntityInterface === false) {
            throw new InvalidArgumentException(
                'Value must be an ' . EntityInterface::class . ' instance.'
            );
        }

        if (strpos($entity->getName(), $this->prefix) !== 0) {
            throw new InvalidArgumentException(
                'Emtity name ' . $entity->getName() . " must have {$this->prefix} prefix."
            );
        }

        $fileName = $this->dir . '/' . $entity->getName() . '.' . $this->ext;

        if (file_exists($fileName)) {
            throw new RuntimeException(
                "Migration {$fileName} already exists"
            );
        }

        if (!file_put_contents($fileName, $entity->getContent())) {
            throw new RuntimeException(
                "Can't put content to {$fileName} file"
            );
        }
    }

    /**
     * @see ArrayAccess
     *
     * @throws OutOfRangeException
     * @throws RuntimeException
     */
    public function offsetUnset($offset)
    {
        $pathToFile = $this->offsetGet($offset)->getPathname();

        if (!unlink($pathToFile)) {
            throw new RuntimeException(
                "Can't unlink {$pathToFile} for {$offset} migration"
            );
        }
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
    public function current(): PhpClassEntity
    {
        $fileInfo = $this->getOrCreateIterator()->current();

        return new PhpClassEntity($fileInfo);
    }

    /**
     * @see Iterator
     */
    public function key()
    {
        return $this->current()->getName();
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
            $callbackFilter = function (SplFileInfo $current, string $key, Iterator $iterator): bool {
                return $current->isFile() && preg_match($this->regexp, $current->getBasename());
            };
            $this->iterator = new CallbackFilterIterator($dirIterator, $callbackFilter);
        }

        return $this->iterator;
    }
}
