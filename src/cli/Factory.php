<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Application;

/**
 * Фабрика, которая создает консольные команды.
 */
class Factory
{
    /**
     * Регистрирует консольные команды в обхекте приложения Symfony console.
     *
     * @param \Symfony\Component\Console\Application $app
     * @param string                                 $pathToMigrations
     *
     * @return \Symfony\Component\Console\Application
     */
    public static function registerCommands(Application $app, $pathToMigrations)
    {
        $app->add((new SymphonyUp)->setMigrationPath($pathToMigrations));
        $app->add((new SymphonyDown)->setMigrationPath($pathToMigrations));
        $app->add((new SymphonyCreate)->setMigrationPath($pathToMigrations));
        $app->add((new SymphonyRefresh)->setMigrationPath($pathToMigrations));
        $app->add((new SymphonyCheck)->setMigrationPath($pathToMigrations));

        return $app;
    }
}
