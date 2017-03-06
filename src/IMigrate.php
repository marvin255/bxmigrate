<?php

namespace marvin255\bxmigrate;

interface IMigrate
{
    /**
     * @return mixed
     */
    public function up();

    /**
     * @return mixed
     */
    public function down();

    /**
     * @return mixed
     */
    public function managerUp();

    /**
     * @return mixed
     */
    public function managerDown();

    /**
     * @return mixed
     */
    public function getName();
}
