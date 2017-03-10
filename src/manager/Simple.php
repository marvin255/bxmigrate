<?php

namespace marvin255\bxmigrate\manager;

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
     * @param \marvin255\bxmigrate\IMigrateRepo    $repo
     * @param \marvin255\bxmigrate\IMigrateChecker $checker
     */
    public function __construct(\marvin255\bxmigrate\IMigrateRepo $repo, \marvin255\bxmigrate\IMigrateChecker $checker)
    {
        $this->repo = $repo;
        $this->checker = $checker;
    }

    /**
     * @param int $count
     *
     * @return array
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
     * @param int $count
     *
     * @return array
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
     * @param string $name
     *
     * @return bool
     */
    public function create($name)
    {
        return $this->repo->create($name);
    }
}
