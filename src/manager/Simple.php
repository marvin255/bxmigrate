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
                $this->notify("Processing {$migrationName}");
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
                ) {
                    continue;
                }
                $this->notifier->info("Processing {$migrationName}");
                $result = $this->repo->instantiateMigration($migrationName)->managerDown();
                $this->checker->uncheck($migrationName);
                $this->notifier->success($result, true);
                ++$upped;
                if ($count && ((int) $upped === (int) $count || !is_numeric($count))) {
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
            $scannedException = $e->getPrevious();
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
