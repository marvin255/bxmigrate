<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\tests\BaseCase;
use marvin255\bxmigrate\cli\SymphonyCheck;

class SymphonyCheckTest extends BaseCase
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
        $manager->expects($this->once())->method('check')->with($this->equalTo($name));

        $input = $this->getMockBuilder('\\Symfony\\Component\\Console\\Input\\InputInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $input->method('getArgument')->with($this->equalTo('name'))->will($this->returnValue($name));

        $output = $this->getMockBuilder('\\Symfony\\Component\\Console\\Output\\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $command = new SymphonyCheck;
        $command->setMigrateManager($manager)->run($input, $output);
    }

    /**
     * @test
     */
    public function testSetMigrationPath()
    {
        $path = 'migration_name_' . mt_rand();

        $command = new SymphonyCheck;

        $this->assertSame($command, $command->setMigrationPath($path));
        $this->assertSame($path, $command->getMigrationPath());
    }
}
