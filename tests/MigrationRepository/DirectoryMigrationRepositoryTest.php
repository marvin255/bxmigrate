<?php

namespace Marvin255\Bxmigrate\Tests\MigrationRepository;

use Marvin255\Bxmigrate\MigrationRepository\DirectoryMigrationRepository;
use Marvin255\Bxmigrate\MigrationEntity\MigrationEntityInterface;
use Marvin255\Bxmigrate\Tests\BaseCase;
use InvalidArgumentException;
use OutOfRangeException;

/**
 * Тест для проверки объекта, который хранит миграции в папке.
 */
class DirectoryMigrationRepositoryTest extends BaseCase
{
    /**
     * Каталог с тестовыми миграциями.
     *
     * @var string
     */
    protected $dir = '';
    /**
     * Префикс для миграций.
     *
     * @var string
     */
    protected $prefix = '';
    /**
     * Расширений для файлов миграций.
     *
     * @var string
     */
    protected $ext = '';
    /**
     * Существующие файлы миграций.
     *
     * @var string
     */
    protected $migrations = [];

    /**
     * Проверяет, что объект выбросит исключение, если базовой папки не существует.
     */
    public function testConstructDirectoryDoesNotExistException()
    {
        $dir = "{$this->dir}/doesnot/exists";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/' . preg_quote($dir, '/') . '/');

        new DirectoryMigrationRepository($dir);
    }

    /**
     * Проверяет, что объект выбросит исключение, если разрешение файлов
     * задано неверно.
     */
    public function testConstructWrongExtensionException()
    {
        $ext = $this->createFakeData()->uuid;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/' . preg_quote($ext, '/') . '/');

        new DirectoryMigrationRepository($this->dir, $this->prefix, $ext);
    }

    /**
     * Проверяет, что объект работает в качестве итератора.
     */
    public function testIterator()
    {
        $migrations = array_combine($this->migrations, $this->migrations);

        $repository = $this->createRepo();

        $migrationsToTest = [];
        foreach ($repository as $key => $migration) {
            $migrationsToTest[$key] = $migration->getName();
        }

        $migrationsToTest = [];
        foreach ($repository as $key => $migration) {
            $migrationsToTest[$key] = $migration->getName();
        }

        ksort($migrations);
        ksort($migrationsToTest);

        $this->assertSame($migrations, $migrationsToTest);
    }

    /**
     * Проверяет, что объект работает в качестве ArrayAccess.
     */
    public function testArrayAccess()
    {
        $migrationName = reset($this->migrations);

        $repository = $this->createRepo();

        $this->assertTrue(isset($repository[$migrationName]));
        $this->assertFalse(isset($repository['Foo']));
        $this->assertSame($migrationName, $repository[$migrationName]->getName());
    }

    /**
     * Проверяет, что объект выбросит исключение при попытке обратиться по
     * индексу к несуществующей миграции.
     */
    public function testArrayAccessOutOfRangeException()
    {
        $migrationName = $this->createFakeData()->word;

        $repository = $this->createRepo();

        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessageRegExp('/' . preg_quote($migrationName, '/') . '/');

        $migration = $repository[$migrationName];
    }

    /**
     * Проверяет, что объект создает и удаляет файл миграции по указанному
     * индексу.
     */
    public function testArrayAccessSetAndUnset()
    {
        $migrationName = "{$this->prefix}N" . $this->createFakeData()->word;
        $migrationFileName = "{$this->dir}/{$migrationName}.{$this->ext}";
        $migrationContent = $this->createFakeData()->word;

        $entity = $this->getMockBuilder(MigrationEntityInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entity->method('getName')->will($this->returnValue($migrationName));
        $entity->method('getContent')->will($this->returnValue($migrationContent));

        $repository = $this->createRepo();

        $repository[$migrationName] = $entity;
        $this->assertFileExists($migrationFileName);
        $this->assertSame($migrationContent, file_get_contents($migrationFileName));

        unset($repository[$migrationName]);
        $this->assertFileNotExists($migrationFileName);
    }

    /**
     * Задаем папку с миграциями для тестов.
     *
     * @throws RuntimeException
     */
    public function setUp()
    {
        $this->dir = $this->getTempDir();
        $this->prefix = 'P' . $this->createFakeData()->word;
        $this->ext = 'e' . $this->createFakeData()->word;
        $this->migrations = [];

        for ($i = 0; $i < 3; ++$i) {
            $migrationName = "{$this->prefix}N{$i}" . $this->createFakeData()->word;
            $migrationPath = "{$this->dir}/{$migrationName}.{$this->ext}";
            $this->migrations[] = $migrationName;
            file_put_contents($migrationPath, $this->createFakeData()->word . "\r\n");
        }

        return parent::setUp();
    }

    /**
     * Возвращает настроенный объект репозитория.
     */
    protected function createRepo(): DirectoryMigrationRepository
    {
        return new DirectoryMigrationRepository($this->dir, $this->prefix, $this->ext);
    }
}
