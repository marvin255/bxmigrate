<?php

namespace marvin255\bxmigrate\tests\bxmigrate\manager;

class SimpleTest extends \PHPUnit_Framework_TestCase
{
    public function testUp()
    {
        $migration1 = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $migration1->method('getName')->will($this->returnValue('migration1'));

        $migration2 = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $migration2->method('getName')->will($this->returnValue('migration2'));
        $migration2->expects($this->once())
            ->method('managerUp')
            ->will($this->returnValue(['test1', 'test2']));

        $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
            ->getMock();
        $repo->method('getMigrations')->will($this->returnValue([$migration1, $migration2]));

        $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')
            ->getMock();
        $checker->expects($this->at(0))
            ->method('isChecked')
            ->with($this->equalTo('migration1'))
            ->will($this->returnValue(true));
        $checker->expects($this->at(1))
            ->method('isChecked')
            ->with($this->equalTo('migration2'))
            ->will($this->returnValue(false));
        $checker->expects($this->once())
            ->method('check')
            ->with($this->equalTo('migration2'));

        $manager = new \marvin255\bxmigrate\manager\Simple($repo, $checker);

        $this->assertSame(
            ['test1', 'test2'],
            $manager->up(),
            'Manager must check all migration and set up those which are not checked'
        );
    }

    public function testDown()
    {
        $migration1 = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $migration1->method('getName')->will($this->returnValue('migration1'));

        $migration2 = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $migration2->method('getName')->will($this->returnValue('migration2'));
        $migration2->expects($this->once())
            ->method('managerDown')
            ->will($this->returnValue(['test1', 'test2']));

        $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
            ->getMock();
        $repo->method('getMigrations')->will($this->returnValue([$migration1, $migration2]));

        $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')
            ->getMock();
        $checker->expects($this->at(0))
            ->method('isChecked')
            ->with($this->equalTo('migration2'))
            ->will($this->returnValue(true));
        $checker->expects($this->once())
            ->method('uncheck')
            ->with($this->equalTo('migration2'));

        $manager = new \marvin255\bxmigrate\manager\Simple($repo, $checker);

        $this->assertSame(
            ['test1', 'test2'],
            $manager->down(1),
            'Manager must check all migration and set down those which are checked'
        );
    }

    public function testCreate()
    {
        $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
            ->getMock();
        $repo->expects($this->once())
            ->method('create')
            ->with($this->equalTo('test_migration'));

        $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')
            ->getMock();

        $manager = new \marvin255\bxmigrate\manager\Simple($repo, $checker);
        $manager->create('test_migration');
    }
}
