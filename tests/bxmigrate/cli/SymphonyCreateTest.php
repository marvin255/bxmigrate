<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\tests\BaseCase;
use marvin255\bxmigrate\cli\SymphonyCreate;

class SymphonyCreateTest extends BaseCase
{
    /**
     * @test
     */
    public function testExecute()
    {
        $name = 'migration_name_' . mt_rand();

        $manager = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateManager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager->expects($this->once())->method('create')->with($this->equalTo($name));

        $input = $this->getMockBuilder('\\Symfony\\Component\\Console\\Input\\InputInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $input->method('getArgument')->with($this->equalTo('name'))->will($this->returnValue($name));

        $output = $this->getMockBuilder('\\Symfony\\Component\\Console\\Output\\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $command = new SymphonyCreate;
        $command->setMigrateManager($manager)->run($input, $output);
    }

    /**
     * @test
     */
    public function testSetMigrationPath()
    {
        $path = 'migration_name_' . mt_rand();

        $command = new SymphonyCreate;

        $this->assertSame($command, $command->setMigrationPath($path));
        $this->assertSame($path, $command->getMigrationPath());
    }
}
