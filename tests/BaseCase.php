<?php

namespace Marvin255\Bxmigrate\Tests;

use PHPUnit\Framework\TestCase;

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
}
