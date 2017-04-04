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
     * @param bool $setSpace
     */
    public function success($messages, $setSpace = false);

    /**
     * Выводит информационное сообщение.
     *
     * @param array|string $messages
     * @param bool $setSpace
     */
    public function info($messages, $setSpace = false);

    /**
     * Выводит сообщения об ошибке.
     *
     * @param array|string $messages
     * @param bool $setSpace
     */
    public function error($messages, $setSpace = false);
}
