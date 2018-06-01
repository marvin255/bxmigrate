<?php

namespace marvin255\bxmigrate\cli;

use marvin255\bxmigrate\IMigrateManager;
use marvin255\bxmigrate\repo\Files;
use marvin255\bxmigrate\checker\HighLoadIb;
use marvin255\bxmigrate\manager\Simple;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Абстрактная команда с общими методами для тех команд,
 * которые использую менеджер миграций.
 */
abstract class AbstractManagerCommand extends Command
{
    /**
     * @var string
     */
    protected $migrationPath = '';
    /**
     * @var \marvin255\bxmigrate\IMigrateManager
     */
    protected $migrateManager;

    /**
     * Задает путь к папке с миграциями.
     *
     * @param string $migrationPath
     *
     * @return self
     */
    public function setMigrationPath($migrationPath)
    {
        $this->migrationPath = $migrationPath;

        return $this;
    }

    /**
     * Возвращает путь к папке с миграциями.
     *
     * @return string
     */
    public function getMigrationPath()
    {
        return $this->migrationPath;
    }

    /**
     * Задает объект менеджера миграций.
     *
     * @param \marvin255\bxmigrate\IMigrateManager $manager
     *
     * @return self
     */
    public function setMigrateManager(IMigrateManager $manager)
    {
        $this->migrateManager = $manager;

        return $this;
    }

    /**
     * Возвращает объект менеджера миграций. Пробует создать дефолтный, если
     * явно не указан объект менеджера.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \marvin255\bxmigrate\IMigrateManager
     */
    protected function getOrCreateMigrateManager(InputInterface $input, OutputInterface $output)
    {
        if ($this->migrateManager === null) {
            $repo = new Files($this->migrationPath);
            $checker = new HighLoadIb;
            $notifier = new Notifier($output);
            $this->migrateManager = new Simple($repo, $checker, $notifier);
        }

        return $this->migrateManager;
    }
}
