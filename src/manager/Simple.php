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
        $this->notifier->info('Running up migrations:', true);
        try {
            $migrations = $this->repo->getMigrations();
            $upped = 0;
            foreach ($migrations as $migrationName) {
                if (
                    $this->checker->isChecked($migrationName)
                    || $count && !is_numeric($count) && $count !== $migrationName
                ){
                    continue;
                }
                $this->notifier->info("Processing {$migrationName}");
                $result = $this->repo->instantiateMigration($migrationName)->managerUp();
                $this->checker->check($migrationName);
                $this->notifier->success($result, true);
                ++$upped;
                if ($count && ($upped === $count || !is_numeric($count))) {
                    break;
                }
            }
            if ($upped === 0) {
                $this->notifier->info('There are no migrations for up', true);
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down($count = null)
    {
        $this->notifier->info('Running down migrations:', true);
        try {
            $count = $count === null ? 1 : $count;
            $migrations = array_reverse($this->repo->getMigrations());
            $upped = 0;
            foreach ($migrations as $migrationName) {
                if (
                    !$this->checker->isChecked($migrationName)
                    || $count && !is_numeric($count) && $count !== $migrationName
                ){
                    continue;
                }
                $this->notifier->info("Processing {$migrationName}");
                $result = $this->repo->instantiateMigration($migrationName)->managerDown();
                $this->checker->uncheck($migrationName);
                $this->notifier->success($result, true);
                ++$upped;
                if ($count && ($upped === $count || !is_numeric($count))) {
                    break;
                }
            }
            if ($upped === 0) {
                $this->notifier->info('There are no migrations for down', true);
            }
        } catch (\Exception $e) {
            $this->handleException($e);
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
            $this->handleException($e);
        }
    }

    /**
     * Обрабатывает исключение.
     *
     * @param \Exception $e
     */
    protected function handleException(\Exception $e)
    {
        $errors = [];
        $errors[] = $e->getMessage();
        $showException = $e->getPrevious() ?: $e;
        $errors[] = 'In '.$showException->getFile().' on line '.$showException->getLine();
        $this->notifier->error($errors);
    }
}
