<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\tests\BaseCase;
use marvin255\bxmigrate\cli\Notifier;

class NotifierTest extends BaseCase
{
    /**
     * @test
     */
    public function testInfo()
    {
        $message = 'message_' . mt_rand();

        $output = $this->getMockBuilder('\\Symfony\\Component\\Console\\Output\\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $output->expects($this->once())
            ->method('writeln')
            ->with($this->equalTo("<info>{$message}</info>"));

        $notifier = new Notifier($output);
        $notifier->info($message);
    }

    /**
     * @test
     */
    public function testError()
    {
        $message = 'message_' . mt_rand();

        $output = $this->getMockBuilder('\\Symfony\\Component\\Console\\Output\\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $output->expects($this->once())
            ->method('writeln')
            ->with($this->equalTo("<error>{$message}</error>"));

        $notifier = new Notifier($output);
        $notifier->error($message);
    }
}
