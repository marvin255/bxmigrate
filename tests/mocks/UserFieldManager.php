<?php

/**
 * Мок менеджера для управления пользовательскими полями.
 */
class UserFieldManager
{
    /**
     * Очищает кэш менеджера.
     */
    public function cleanCache()
    {
    }
}

global $USER_FIELD_MANAGER;
$USER_FIELD_MANAGER = new UserFieldManager;
