<?php

namespace marvin255\bxmigrate\tests\bxmigrate\manager;

use marvin255\bxmigrate\manager\Simple;
use marvin255\bxmigrate\tests\BaseCase;

class SimpleTest extends BaseCase
{
    /**
     * @test
     */
    public function testUp()
    {
        $migration = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration->expects($this->once())->method('managerUp');

        $migration2 = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration2->expects($this->once())->method('managerUp');

        $migration3 = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration3->expects($this->never())->method('managerUp');

        $migrations = [
            'migration_to_up' => $migration,
            'migration1' => null,
            'migration_to_up_2' => $migration2,
            'migration2' => null,
            'migration3' => null,
            'migration_to_up_3' => $migration3,
        ];
        $checked = [];

        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')->getMock();
        $repo->method('getMigrations')->will($this->returnValue(array_keys($migrations)));
        $repo->method('instantiateMigration')->will($this->returnCallback(function ($name) use ($migrations) {
            return $migrations[$name];
        }));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();
        $checker->method('isChecked')->will($this->returnCallback(function ($name) use ($migrations) {
            return $migrations[$name] === null;
        }));
        $checker->method('check')->will($this->returnCallback(function ($name) use (&$checked) {
            $checked[] = $name;
        }));

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('info');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->up(2);

        $this->assertSame(['migration_to_up', 'migration_to_up_2'], $checked);
    }

    /**
     * @test
     */
    public function testUpException()
    {
        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')
            ->getMock();
        $repo->method('getMigrations')->will($this->throwException(new \Exception('test exception')));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('error');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->up();
    }

    /**
     * @test
     */
    public function testUpByName()
    {
        $migration = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration->expects($this->never())->method('managerUp');

        $migration2 = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration2->expects($this->once())->method('managerUp');

        $migration3 = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration3->expects($this->never())->method('managerUp');

        $migrationToUp = 'migration_to_up_2';
        $migrationToUpAlreadySet = 'migration_to_up_3';
        $migrations = [
            'migration_to_up' => $migration,
            'migration1' => null,
            'migration_to_up_2' => $migration2,
            'migration2' => null,
            'migration3' => null,
            'migration_to_up_3' => $migration3,
        ];

        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')->getMock();
        $repo->method('getMigrations')->will($this->returnValue(array_keys($migrations)));
        $repo->method('instantiateMigration')->will($this->returnCallback(function ($name) use ($migrations) {
            return $migrations[$name];
        }));
        $repo->method('isMigrationExists')->will($this->returnCallback(function ($name) use ($migrations) {
            return array_key_exists($name, $migrations);
        }));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();
        $checker->method('isChecked')->will($this->returnCallback(function ($name) use ($migrations, $migrationToUpAlreadySet) {
            return $migrations[$name] === null || $name === $migrationToUpAlreadySet;
        }));
        $checker->expects($this->once())->method('check')->with($this->equalTo($migrationToUp));

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('info');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->upByName($migrationToUp);
        $manager->upByName($migrationToUpAlreadySet);
        $manager->upByName('unexisted_name');
    }

    /**
     * @test
     */
    public function testUpByNameException()
    {
        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')
            ->getMock();
        $repo->method('isMigrationExists')->will($this->throwException(new \Exception('test exception')));
        $repo->method('isChecked')->will($this->throwException(new \Exception('test exception')));
        $repo->method('instantiateMigration')->will($this->throwException(new \Exception('test exception')));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('error');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->upByName('test');
    }

    /**
     * @test
     */
    public function testDown()
    {
        $migration = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration->expects($this->never())->method('managerDown');

        $migration2 = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration2->expects($this->never())->method('managerDown');

        $migration3 = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration3->expects($this->once())->method('managerDown');

        $migrations = [
            'migration_to_up' => $migration,
            'migration1' => null,
            'migration_to_up_2' => $migration2,
            'migration2' => null,
            'migration3' => null,
            'migration_to_up_3' => $migration3,
        ];
        $checked = [];

        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')->getMock();
        $repo->method('getMigrations')->will($this->returnValue(array_keys($migrations)));
        $repo->method('instantiateMigration')->will($this->returnCallback(function ($name) use ($migrations) {
            return $migrations[$name];
        }));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();
        $checker->method('isChecked')->will($this->returnCallback(function ($name) use ($migrations) {
            return $migrations[$name] !== null;
        }));
        $checker->method('uncheck')->will($this->returnCallback(function ($name) use (&$checked) {
            $checked[] = $name;
        }));

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('info');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->down();

        $this->assertSame(['migration_to_up_3'], $checked);
    }

    /**
     * @test
     */
    public function testDownWithCount()
    {
        $migration = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration->expects($this->never())->method('managerDown');

        $migration2 = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration2->expects($this->once())->method('managerDown');

        $migration3 = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration3->expects($this->once())->method('managerDown');

        $migrations = [
            'migration_to_up' => $migration,
            'migration1' => null,
            'migration_to_up_2' => $migration2,
            'migration2' => null,
            'migration3' => null,
            'migration_to_up_3' => $migration3,
        ];
        $checked = [];

        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')->getMock();
        $repo->method('getMigrations')->will($this->returnValue(array_keys($migrations)));
        $repo->method('instantiateMigration')->will($this->returnCallback(function ($name) use ($migrations) {
            return $migrations[$name];
        }));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();
        $checker->method('isChecked')->will($this->returnCallback(function ($name) use ($migrations) {
            return $migrations[$name] !== null;
        }));
        $checker->method('uncheck')->will($this->returnCallback(function ($name) use (&$checked) {
            $checked[] = $name;
        }));

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('info');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->down(2);

        $this->assertSame(['migration_to_up_3', 'migration_to_up_2'], $checked);
    }

    /**
     * @test
     */
    public function testDownException()
    {
        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')
            ->getMock();
        $repo->method('getMigrations')->will($this->throwException(new \Exception('test exception')));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('error');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->down();
    }

    /**
     * @test
     */
    public function testDownByName()
    {
        $migration = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration->expects($this->never())->method('managerDown');

        $migration2 = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration2->expects($this->once())->method('managerDown');

        $migration3 = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrate')->getMock();
        $migration3->expects($this->never())->method('managerDown');

        $migrationToDown = 'migration_to_down_2';
        $migrationToDownAlreadyUnset = 'migration_to_down_3';
        $migrations = [
            'migration_to_down' => $migration,
            'migration1' => null,
            'migration_to_down_2' => $migration2,
            'migration2' => null,
            'migration3' => null,
            'migration_to_down_3' => $migration3,
        ];

        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')->getMock();
        $repo->method('getMigrations')->will($this->returnValue(array_keys($migrations)));
        $repo->method('instantiateMigration')->will($this->returnCallback(function ($name) use ($migrations) {
            return $migrations[$name];
        }));
        $repo->method('isMigrationExists')->will($this->returnCallback(function ($name) use ($migrations) {
            return array_key_exists($name, $migrations);
        }));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();
        $checker->method('isChecked')->will($this->returnCallback(function ($name) use ($migrations, $migrationToDownAlreadyUnset) {
            return $migrations[$name] !== null && $name !== $migrationToDownAlreadyUnset;
        }));
        $checker->expects($this->once())->method('uncheck')->with($this->equalTo($migrationToDown));

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('info');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->downByName($migrationToDown);
        $manager->downByName($migrationToDownAlreadyUnset);
        $manager->downByName('unexisted_name');
    }

    /**
     * @test
     */
    public function testDownByNameException()
    {
        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')
            ->getMock();
        $repo->method('isMigrationExists')->will($this->throwException(new \Exception('test exception')));
        $repo->method('isChecked')->will($this->throwException(new \Exception('test exception')));
        $repo->method('instantiateMigration')->will($this->throwException(new \Exception('test exception')));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('error');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->downByName('test');
    }

    /**
     * @test
     */
    public function testCreate()
    {
        $name = 'migration_' . mt_rand();

        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')
            ->getMock();
        $repo->expects($this->once())->method('create')->with($this->equalTo($name));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('info');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->create($name);
    }

    /**
     * @test
     */
    public function testCreateException()
    {
        $name = 'migration_' . mt_rand();

        $repo = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateRepo')
            ->getMock();
        $repo->expects($this->once())
            ->method('create')
            ->with($this->equalTo($name))
            ->will($this->throwException(new \Exception('test exception')));

        $checker = $this->getMockBuilder('\\marvin255\\bxmigrate\\IMigrateChecker')->getMock();

        $notifier = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')->getMock();
        $notifier->expects($this->atLeastOnce())->method('error');

        $manager = new Simple($repo, $checker, $notifier);
        $manager->create($name);
    }
}
