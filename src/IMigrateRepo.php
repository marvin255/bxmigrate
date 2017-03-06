<?php

namespace marvin255\bxmigrate;

interface IMigrateRepo
{
    /**
     * @return array
     */
    public function getMigrations();

    /**
     * @param string $name
     *
     * @return string
     */
    public function create($mName);
}
