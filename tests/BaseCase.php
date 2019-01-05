<?php

namespace Marvin255\Bxmigrate\Tests;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

/**
 * Базовый тест, от которого унаследуются все остальные тесты.
 */
abstract class BaseCase extends TestCase
{
    /**
     * @var \Faker\Generator|null
     */
    private $faker;
    /**
     * @var string
     */
    private $tempDir = '';

    /**
     * Возвращает объект php faker для генерации случайных данных.
     *
     * Использует ленивую инициализацию и создает объект faker только при первом
     * запросе, для всех последующих заново возвращает тот же самый инстанс,
     * который был создан в первый раз.
     *
     * @return \Faker\Generator
     */
    public function createFakeData(): \Faker\Generator
    {
        if ($this->faker === null) {
            $this->faker = \Faker\Factory::create();
        }

        return $this->faker;
    }

    /**
     * Возвращает путь до базовой папки для тестов.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    protected function getTempDir(): string
    {
        if ($this->tempDir === '') {
            $this->tempDir = sys_get_temp_dir();
            if (!$this->tempDir || !is_writable($this->tempDir)) {
                throw new RuntimeException(
                    "Can't find or write temporary folder: {$this->tempDir}"
                );
            }
            $this->tempDir .= DIRECTORY_SEPARATOR . 'bxmigrate';
            $this->removeDir($this->tempDir);
            if (!mkdir($this->tempDir, 0777, true)) {
                throw new RuntimeException(
                    "Can't create temporary folder: {$this->tempDir}"
                );
            }
        }

        return $this->tempDir;
    }

    /**
     * Удаляет содержимое папки.
     *
     * @param string $folderPath
     */
    protected function removeDir(string $folderPath)
    {
        if (is_dir($folderPath)) {
            $it = new RecursiveDirectoryIterator(
                $folderPath,
                RecursiveDirectoryIterator::SKIP_DOTS
            );
            $files = new RecursiveIteratorIterator(
                $it,
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } elseif ($file->isFile()) {
                    unlink($file->getRealPath());
                }
            }
            rmdir($folderPath);
        }
    }

    /**
     * Удаляет тестовую директорию и все ее содержимое.
     */
    protected function tearDown()
    {
        if ($this->tempDir) {
            $this->removeDir($this->tempDir);
        }

        return parent::tearDown();
    }
}
