<?php

namespace marvin255\bxmigrate;

/**
 * Объект, который выводит сообщения о результатах миграций пользователю.
 */
interface IMigrateNotifier
{
    /**
     * Выводит сообщения об успешном завершении.
     *
     * @param array|string $messages
     */
    public function success($messages);

    /**
     * Выводит сообщения об ошибке.
     *
     * @param array|string $messages
     */
    public function error($messages);
}
