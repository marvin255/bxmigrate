<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\tests\BaseCase;
use marvin255\bxmigrate\cli\SymphonyDown;

class SymphonyDownTest extends BaseCase
{
    /**
     * @test
     */
    public function testExecuteNumeric()
    {
        $count = mt_rand();

        $manager = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateManager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager->expects($this->once())->method('down')->with($this->equalTo($count));

        $input = $this->getMockBuilder('\\Symfony\\Component\\Console\\Input\\InputInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $input->method('getArgument')->with($this->equalTo('count'))->will($this->returnValue($count));

        $output = $this->getMockBuilder('\\Symfony\\Component\\Console\\Output\\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $command = new SymphonyDown;
        $command->setMigrateManager($manager)->run($input, $output);
    }

    /**
     * @test
     */
    public function testExecuteName()
    {
        $count = mt_rand() . '_migrate';

        $manager = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateManager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager->expects($this->once())->method('downByName')->with($this->equalTo($count));

        $input = $this->getMockBuilder('\\Symfony\\Component\\Console\\Input\\InputInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $input->method('getArgument')->with($this->equalTo('count'))->will($this->returnValue($count));

        $output = $this->getMockBuilder('\\Symfony\\Component\\Console\\Output\\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock();

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
