<?php

namespace marvin255\bxmigrate;

interface IMigrateChecker
{
    /**
     * @param string $migration
     *
     * @return bool
     */
    public function isChecked($migration);

    /**
     * @param string $migration
     */
    public function check($migration);
}
