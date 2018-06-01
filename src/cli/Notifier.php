<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Выводит сообщения о результатах миграций в консоль, через symfony console.
 */
class Notifier extends AbstractLogger
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Задаем объект для вывода в symfony console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = [])
    {
        $errors = [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
        ];
        $type = in_array($level, $errors) ? 'error' : 'info';

        $this->output->writeln("<{$type}>{$message}</{$type}>");
    }
}
