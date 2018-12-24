<?php

namespace Marvin255\Bxmigrate\Tests\Repository;

use Marvin255\Bxmigrate\Repository\DirectoryRepository;
use Marvin255\Bxmigrate\Tests\BaseCase;
use InvalidArgumentException;

/**
 * Тест для проверки объекта, который хранит миграции в папке.
 */
class DirectoryRepositoryTest extends BaseCase
{
    /**
     * Проверяет, что объект выбросит исключение, если базовой папки не существует.
     */
    public function testConstructDirectoryDoesNotExist()
    {
        $dir = __DIR__ . '/doesnot/exists';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/' . preg_quote($dir) . '/');

        new DirectoryRepository($dir);
    }

    /**
     * Проверяет, что объект работает в качестве итератора.
     */
    public function testIterator()
    {
        $migrations = [
            'MigrationFoo',
            'MigrationBar',
        ];

        $repository = new DirectoryRepository(
            __DIR__ . '/_fixture_directory_repository',
            '/^Migration.*\.php$/i'
        );

        $migrationsToTest = [];
        foreach ($repository as $migration) {
            $migrationsToTest[] = $migration;
        }

        $this->assertSame($migrations, $migrationsToTest);
    }

    /**
     * Проверяет метод, который проверяет существует ли миграция.
     */
    public function testIsMigrationExists()
    {
        $repository = new DirectoryRepository(
            __DIR__ . '/_fixture_directory_repository',
            '/^Migration.*\.php$/i'
        );

        $this->assertTrue($repository->isMigrationExists('MigrationFoo'));
        $this->assertFalse($repository->isMigrationExists('Bar'));
    }
}
