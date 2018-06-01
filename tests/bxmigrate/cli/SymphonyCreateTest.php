<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\tests\BaseCase;
use marvin255\bxmigrate\IMigrateManager;
use marvin255\bxmigrate\cli\SymphonyCreate;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SymphonyCreateTest extends BaseCase
{
    /**
     * @test
     */
    public function testExecute()
    {
        $name = 'migration_name_' . mt_rand();

        $manager = $this->getMockBuilder(IMigrateManager::class)->getMock();
        $manager->expects($this->once())->method('create')->with($this->equalTo($name));

        $input = $this->getMockBuilder(InputInterface::class)->getMock();
        $input->method('getArgument')->with($this->equalTo('name'))->will($this->returnValue($name));

        $output = $this->getMockBuilder(OutputInterface::class)->getMock();

        $command = new SymphonyCreate;
        $command->setMigrateManager($manager)->run($input, $output);
    }
}
