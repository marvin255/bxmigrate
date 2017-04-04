<?php

namespace marvin255\bxmigrate\cli;

use marvin255\bxmigrate\IMigrateNotifier;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Выводит сообщения о результатах миграций в консоль, через symfony console.
 */
class Notifier implements IMigrateNotifier
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output = null;

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
    public function success($messages)
    {
        if (!empty($messages)) {
            $messages = is_array($messages) ? $messages : [$messages];
            $this->writeln($messages, 'info');
        }
    }

    /**
     * @inheritdoc
     */
    public function error($messages)
    {
        if (!empty($messages)) {
            $messages = is_array($messages) ? $messages : [$messages];
            $this->writeln($messages, 'error');
        }
    }

    /**
     * Выводит сообщения в консоль.
     *
     * @param array $messages
     * @param string $type
     */
    protected function writeln(array $messages, $type)
    {
        foreach ($messages as $message) {
            $this->output->writeln("<{$type}>{$message}</{$type}>");
        }
    }
}
