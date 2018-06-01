<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\tests\BaseCase;
use marvin255\bxmigrate\IMigrateManager;
use marvin255\bxmigrate\cli\SymphonyUp;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SymphonyUpTest extends BaseCase
{
    /**
     * @test
     */
    public function testExecute()
    {
        $count = mt_rand();

        $manager = $this->getMockBuilder(IMigrateManager::class)->getMock();
        $manager->expects($this->once())->method('up')->with($this->equalTo($count));

        $input = $this->getMockBuilder(InputInterface::class)->getMock();
        $input->method('getArgument')->with($this->equalTo('count'))->will($this->returnValue($count));

        $output = $this->getMockBuilder(OutputInterface::class)->getMock();

        $command = new SymphonyUp;
        $command->setMigrateManager($manager)->run($input, $output);
    }
}
