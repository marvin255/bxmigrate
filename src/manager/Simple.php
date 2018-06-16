<?php

namespace marvin255\bxmigrate\manager;

use marvin255\bxmigrate\IMigrateRepo;
use marvin255\bxmigrate\IMigrateChecker;
use Psr\Log\LoggerInterface;

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
    protected $repo;
    /**
     * @var \marvin255\bxmigrate\IMigrateChecker
     */
    protected $checker;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $notifier;

    /**
     * @inheritdoc
     */
    public function __construct(IMigrateRepo $repo, IMigrateChecker $checker, LoggerInterface $notifier = null)
    {
        $this->repo = $repo;
        $this->checker = $checker;
        $this->notifier = $notifier;
    }

    /**
     * @inheritdoc
     */
    public function up($count = null)
    {
        try {
            $this->notify('Running up migrations:');

            $migrations = $this->repo->getMigrations();
            $count = intval($count) ?: count($migrations);
            $upped = 0;

            foreach ($migrations as $migrationName) {
                if ($this->checker->isChecked($migrationName)) {
                    continue;
                }
                $this->notify("Processing '{$migrationName}':");
                $result = $this->repo->instantiateMigration($migrationName)->managerUp();
                $this->checker->check($migrationName);
                $this->notify($result);
                ++$upped;
                if ($upped === $count) {
                    break;
                }
            }

            if ($upped === 0) {
                $this->notify('There are no migrations for up');
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function upByName($name)
    {
        try {
            $this->notify("Running up '{$name}' migration:");
            if (!$this->repo->isMigrationExists($name)) {
                $this->notify("There is no '{$name}' migration");
            } elseif ($this->checker->isChecked($name)) {
                $this->notify('Migration already set');
            } else {
                $result = $this->repo->instantiateMigration($name)->managerUp();
                $this->checker->check($name);
                $this->notify($result);
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function down($count = null)
    {
        try {
            $this->notify('Running down migrations:');

            $migrations = array_reverse($this->repo->getMigrations());
            $count = intval($count) ?: 1;
            $downed = 0;

            foreach ($migrations as $migrationName) {
                if (!$this->checker->isChecked($migrationName)) {
                    continue;
                }
                $this->notify("Processing '{$migrationName}':");
                $result = $this->repo->instantiateMigration($migrationName)->managerDown();
                $this->checker->uncheck($migrationName);
                $this->notify($result);
                ++$downed;
                if ($downed === $count) {
                    break;
                }
            }

            if ($downed === 0) {
                $this->notify('There are no migrations for down');
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function downByName($name)
    {
        try {
            $this->notify("Running down '{$name}' migration:");
            if (!$this->repo->isMigrationExists($name)) {
                $this->notify("There is no '{$name}' migration");
            } elseif (!$this->checker->isChecked($name)) {
                $this->notify('Migration already unset');
            } else {
                $result = $this->repo->instantiateMigration($name)->managerDown();
                $this->checker->uncheck($name);
                $this->notify($result);
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function refresh($name)
    {
        try {
            $this->notify("Refreshing '{$name}' migration:");
            if (!$this->repo->isMigrationExists($name)) {
                $this->notify("There is no '{$name}' migration");
            } elseif (!$this->checker->isChecked($name)) {
                $this->notify('Migration is unset');
            } else {
                $migration = $this->repo->instantiateMigration($name);
                $migration->managerDown();
                $migration->managerUp();
                $this->notify('Migration refreshed');
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function check($name)
    {
        try {
            $this->notify("Checking '{$name}' migration:");
            if (!$this->repo->isMigrationExists($name)) {
                $this->notify("There is no '{$name}' migration");
            } elseif ($this->checker->isChecked($name)) {
                $this->notify('Migration is already checked');
            } else {
                $this->checker->check($name);
                $this->notify('Migration checked');
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * @inheritdoc
     */
    public function create($name)
    {
        try {
            $migrationName = $this->repo->create($name);
            $migrationPath = $this->repo->getPathToMigrationFile($migrationName);
            $this->notify("Migration {$name}($migrationPath) created");
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Обрабатывает исключение.
     *
     * @param \Exception $e
     *
     * @return self
     */
    protected function handleException(\Exception $e)
    {
        $errors = [];

        $scannedException = $e;
        while ($scannedException) {
            $errors[] = $scannedException->getMessage();
            $errors[] = 'In ' . $scannedException->getFile() . ' on line ' . $scannedException->getLine();
            $scannedException = $scannedException->getPrevious();
        }

        return $this->notify($errors, 'error');
    }

    /**
     * Выводит в лог информацию.
     *
     * @param string|array $message
     * @param string       $type
     *
     * @return self
     */
    protected function notify($message, $type = 'info')
    {
        if ($this->notifier) {
            $messages = is_array($message) ? $message : [$message];
            foreach ($messages as $text) {
                if ($type === 'error') {
                    $this->notifier->error($text);
                } else {
                    $this->notifier->info($text);
                }
            }
        }

        return $this;
    }
}
