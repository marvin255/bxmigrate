<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\tests\BaseCase;
use marvin255\bxmigrate\IMigrateManager;
use marvin255\bxmigrate\cli\SymphonyDown;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SymphonyDownTest extends BaseCase
{
    /**
     * @test
     */
    public function testExecute()
    {
        $count = mt_rand();

        $manager = $this->getMockBuilder(IMigrateManager::class)->getMock();
        $manager->expects($this->once())->method('down')->with($this->equalTo($count));

        $input = $this->getMockBuilder(InputInterface::class)->getMock();
        $input->method('getArgument')->with($this->equalTo('count'))->will($this->returnValue($count));

        $output = $this->getMockBuilder(OutputInterface::class)->getMock();

        $command = new SymphonyDown;
        $command->setMigrateManager($manager)->run($input, $output);
    }

    /**
     * @test
     */
    public function testSetMigrationPath()
    {
        $path = 'migration_name_' . mt_rand();

        $command = new SymphonyDown;

        $this->assertSame($command, $command->setMigrationPath($path));
        $this->assertSame($path, $command->getMigrationPath());
    }
}
