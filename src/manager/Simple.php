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
     * @var \marvin255\bxmigrate\IMigrateNotifier
     */
    protected $notifier = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(\marvin255\bxmigrate\IMigrateRepo $repo, \marvin255\bxmigrate\IMigrateChecker $checker, \marvin255\bxmigrate\IMigrateNotifier $notifier)
    {
        $this->repo = $repo;
        $this->checker = $checker;
        $this->notifier = $notifier;
    }

    /**
     * {@inheritdoc}
     */
    public function up($count = null)
    {
        $migrations = $this->repo->getMigrations();
        $upped = 0;
        try {
            foreach ($migrations as $migration) {
                if ($this->checker->isChecked($migration->getName())) {
                    continue;
                }
                $result = $migration->managerUp();
                $this->notifier->success($result);
                $this->checker->check($migration->getName());
                ++$upped;
                if ($count && $upped === $count) {
                    break;
                }
            }
        } catch (\Exception $e) {
            $errors = [];
            $errors[] = $e->getMessage();
            $showException = $e->getPrevious() ?: $e;
            $errors[] = 'In ' . $showException->getFile() . ' on line ' . $showException->getLine();
            $this->notifier->error($errors);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down($count = null)
    {
        $count = $count === null ? 1 : $count;
        $migrations = array_reverse($this->repo->getMigrations());
        $upped = 0;
        try {
            foreach ($migrations as $migration) {
                if (!$this->checker->isChecked($migration->getName())) {
                    continue;
                }
                $result = $migration->managerDown();
                $this->notifier->success($result);
                $this->checker->uncheck($migration->getName());
                ++$upped;
                if ($count && $upped === $count) {
                    break;
                }
            }
        } catch (\Exception $e) {
            $errors = [];
            $errors[] = $e->getMessage();
            $showException = $e->getPrevious() ?: $e;
            $errors[] = 'In ' . $showException->getFile() . ' on line ' . $showException->getLine();
            $this->notifier->error($errors);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        try {
            $res = $this->repo->create($name);
            $this->notifier->success($res);
        } catch (\Exception $e) {
            $errors = [];
            $errors[] = $e->getMessage();
            $showException = $e->getPrevious() ?: $e;
            $errors[] = 'In ' . $showException->getFile() . ' on line ' . $showException->getLine();
            $this->notifier->error($errors);
        }
    }
}
