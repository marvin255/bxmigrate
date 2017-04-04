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
    public function success($messages, $setSpace = false)
    {
        $this->info($messages, $setSpace);
    }

    /**
     * @inheritdoc
     */
    public function info($messages, $setSpace = false)
    {
        $this->writeln($messages, 'info', $setSpace);
    }

    /**
     * @inheritdoc
     */
    public function error($messages, $setSpace = false)
    {
        $this->writeln($messages, 'error', $setSpace);
    }

    /**
     * Выводит сообщения в консоль.
     *
     * @param array|string  $messages
     * @param string        $type
     * @param bool          $setSpace
     */
    protected function writeln($messages, $type, $setSpace = false)
    {
        $messages = is_array($messages) ? $messages : [$messages];
        if ($setSpace) {
            $messages[] = '';
        }
        foreach ($messages as $message) {
            $this->output->writeln("<{$type}>{$message}</{$type}>");
        }
    }
}
