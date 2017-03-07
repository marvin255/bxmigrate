<?php

namespace marvin255\bxmigrate;

interface IMigrateManager
{
    /**
     * @param \marvin255\bxmigrate\IMigrateRepo    $repo
     * @param \marvin255\bxmigrate\IMigrateChecker $checker
     */
    public function __construct(\marvin255\bxmigrate\IMigrateRepo $repo, \marvin255\bxmigrate\IMigrateChecker $checker);

    /**
     * @param int $count
     *
     * @return array
     */
    public function up($count = null);

    /**
     * @param int $count
     *
     * @return array
     */
    public function down($count = null);
}
