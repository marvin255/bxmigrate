<?php

namespace marvin255\bxmigrate\manager;

/**
 * Простой менеджер миграций. Получает классы миграций из хранилища, переданного в конструкторе.
 * Проверяет применена ли миграция с помощью объекта для проверки миграций.
 * В зависимости от метода, создает, накатывает или отменяет миграции.
 */
class Simple implements \marvin255\bxmigrate\IMigrateManager
{
    /**
     * @var \marvin255\bxmigrate\IMigrateRepo
     */
    protected $repo = null;
    /**
     * @var \marvin255\bxmigrate\IMigrateRepo
     */
    protected $checker = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(\marvin255\bxmigrate\IMigrateRepo $repo, \marvin255\bxmigrate\IMigrateChecker $checker)
    {
        $this->repo = $repo;
        $this->checker = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function up($count = null)
    {
        $return = [];
        $migrations = $this->repo->getMigrations();
        $upped = 0;
        foreach ($migrations as $migration) {
            if ($this->checker->isChecked($migration->getName())) {
                continue;
            }
            $result = $migration->managerUp();
            $this->checker->check($migration->getName());
            if ($result) {
                $return = array_merge($return, $result);
            }
            ++$upped;
            if ($count && $upped === $count) {
                break;
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function down($count = null)
    {
        $count = $count === null ? 1 : $count;
        $return = [];
        $migrations = array_reverse($this->repo->getMigrations());
        $upped = 0;
        foreach ($migrations as $migration) {
            if (!$this->checker->isChecked($migration->getName())) {
                continue;
            }
            $result = $migration->managerDown();
            $this->checker->uncheck($migration->getName());
            if ($result) {
                $return = array_merge($return, $result);
            }
            ++$upped;
            if ($count && $upped === $count) {
                break;
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        return $this->repo->create($name);
    }
}
