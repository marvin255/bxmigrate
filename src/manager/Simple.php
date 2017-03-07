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
        $total = count($migrations);
        $upped = 0;
        for ($i = 0; $i < $total; ++$i) {
            if ($this->checker->isChecked($migrations[$i]->getName())) {
                continue;
            }
            $result = $migrations[$i]->managerUp();
            $this->checker->check($migrations[$i]->getName());
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
        $migrations = $this->repo->getMigrations();
        $total = count($migrations);
        $upped = 0;
        for ($i = $total - 1; $i >= 0; --$i) {
            if (!$this->checker->isChecked($migrations[$i]->getName())) {
                continue;
            }
            $result = $migrations[$i]->managerDown();
            $this->checker->uncheck($migrations[$i]->getName());
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
