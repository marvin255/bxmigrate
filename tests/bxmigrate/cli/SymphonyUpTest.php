<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\tests\BaseCase;
use marvin255\bxmigrate\cli\SymphonyUp;

class SymphonyUpTest extends BaseCase
{
    /**
     * @test
     */
    public function testExecute()
    {
        $count = mt_rand();

        $manager = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateManager')
            ->disableOriginalConstructor()
            ->getMock();
        $manager->expects($this->once())->method('up')->with($this->equalTo($count));

        $input = $this->getMockBuilder('\\Symfony\\Component\\Console\\Input\\InputInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $input->method('getArgument')->with($this->equalTo('count'))->will($this->returnValue($count));

        $output = $this->getMockBuilder('\\Symfony\\Component\\Console\\Output\\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $command = new SymphonyUp;
        $command->setMigrateManager($manager)->run($input, $output);
    }

    /**
     * @test
     */
    public function testSetMigrationPath()
    {
        $path = 'migration_name_' . mt_rand();

        $command = new SymphonyUp;

        $this->assertSame($command, $command->setMigrationPath($path));
        $this->assertSame($path, $command->getMigrationPath());
    }
}
