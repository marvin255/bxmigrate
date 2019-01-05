<?php

declare(strict_types=1);

namespace Marvin255\Bxmigrate\Repository;

use SplFileInfo;

/**
 * Объект, который хранит в себе информации о миграции из php класса в файле.
 */
class PhpClassEntity implements EntityInterface
{
    /**
     * Файл, в котором хранится класс миграции.
     *
     * @var SplFileInfo
     */
    protected $fileInfo;

    public function __construct(SplFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return pathinfo($this->fileInfo->getPathname(), PATHINFO_FILENAME);
    }

    /**
     * @inheritdoc
     */
    public function getContent(): string
    {
        return file_get_contents($this->fileInfo->getPathname());
    }

    /**
     * Возвращает полный путь к файлу.
     */
    public function getPathname(): string
    {
        return $this->fileInfo->getPathname();
    }
}
