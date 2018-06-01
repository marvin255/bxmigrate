<?php

namespace marvin255\bxmigrate\tests\bxmigrate\manager;

use marvin255\bxmigrate\manager\Simple;
use marvin255\bxmigrate\tests\BaseCase;

class SimpleTest extends BaseCase
{
    // public function testUp()
    // {
    //     $migration = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
    //         ->getMock();
    //     $migration->expects($this->once())
    //         ->method('managerUp')
    //         ->will($this->returnValue(['test1', 'test2']));
    //
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
    //         ->getMock();
    //     $repo->method('getMigrations')->will($this->returnValue(['migration3', 'migration2', 'migration1']));
    //     $repo->expects($this->once())
    //         ->method('instantiateMigration')
    //         ->with($this->equalTo('migration1'))
    //         ->will($this->returnValue($migration));
    //
    //     $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')->getMock();
    //     $checker->method('isChecked')->will($this->returnCallback(function ($name) {
    //         return $name !== 'migration1';
    //     }));
    //     $checker->expects($this->once())->method('check')->with($this->equalTo('migration1'));
    //
    //     $notifications = [];
    //     $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     $notifier->method('info')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['info'][] = $message;
    //     }));
    //     $notifier->method('success')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['success'] = $message;
    //     }));
    //
    //     $this->getManager($repo, $checker, $notifier)->up();
    //
    //     $this->assertSame(
    //         ['info' => ['Running up migrations:', 'Processing migration1'], 'success' => ['test1', 'test2']],
    //         $notifications
    //     );
    // }
    //
    // public function testUpWithCountParam()
    // {
    //     $migration = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')->getMock();
    //
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')->getMock();
    //     $repo->method('getMigrations')->will($this->returnValue(['migration3', 'migration2', 'migration1']));
    //     $repo->expects($this->once())
    //         ->method('instantiateMigration')
    //         ->with($this->equalTo('migration2'))
    //         ->will($this->returnValue($migration));
    //
    //     $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')
    //         ->getMock();
    //     $checker->expects($this->at(0))
    //         ->method('isChecked')
    //         ->with($this->equalTo('migration3'))
    //         ->will($this->returnValue(true));
    //     $checker->expects($this->at(1))
    //         ->method('isChecked')
    //         ->with($this->equalTo('migration2'))
    //         ->will($this->returnValue(false));
    //
    //     $manager = $this->getManager($repo, $checker)->up(1);
    // }
    //
    // public function testUpWithName()
    // {
    //     $migration = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
    //         ->getMock();
    //     $migration->expects($this->once())
    //         ->method('managerUp')
    //         ->will($this->returnValue(['test1', 'test2']));
    //
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
    //         ->getMock();
    //     $repo->method('getMigrations')->will($this->returnValue(['migration3', 'migration2', 'migration1']));
    //     $repo->expects($this->once())
    //         ->method('instantiateMigration')
    //         ->with($this->equalTo('migration2'))
    //         ->will($this->returnValue($migration));
    //
    //     $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')->getMock();
    //     $checker->method('isChecked')->will($this->returnValue(false));
    //     $checker->expects($this->once())->method('check')->with($this->equalTo('migration2'));
    //
    //     $notifications = [];
    //     $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     $notifier->method('info')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['info'][] = $message;
    //     }));
    //     $notifier->method('success')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['success'] = $message;
    //     }));
    //
    //     $this->getManager($repo, $checker, $notifier)->up('migration2');
    //
    //     $this->assertSame(
    //         ['info' => ['Running up migrations:', 'Processing migration2'], 'success' => ['test1', 'test2']],
    //         $notifications
    //     );
    // }
    //
    // public function testUpWithEmptyMigrationsList()
    // {
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
    //         ->getMock();
    //     $repo->method('getMigrations')->will($this->returnValue(['migration3', 'migration2', 'migration1']));
    //     $repo->expects($this->never())->method('instantiateMigration');
    //
    //     $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')->getMock();
    //     $checker->method('isChecked')->will($this->returnCallback(function ($name) {
    //         return true;
    //     }));
    //     $checker->expects($this->never())->method('check');
    //
    //     $notifications = [];
    //     $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     $notifier->method('info')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['info'][] = $message;
    //     }));
    //     $notifier->method('success')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['success'] = $message;
    //     }));
    //
    //     $this->getManager($repo, $checker, $notifier)->up();
    //
    //     $this->assertSame(
    //         ['info' => ['Running up migrations:', 'There are no migrations for up']],
    //         $notifications
    //     );
    // }
    //
    // public function testUpWithexception()
    // {
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
    //         ->getMock();
    //     $repo->method('getMigrations')->will($this->throwException(new \Exception('test exception')));
    //
    //     $notifications = [];
    //     $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     $notifier->method('error')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications = $message;
    //     }));
    //
    //     $this->getManager($repo, null, $notifier)->up();
    //
    //     $this->assertContains('test exception', $notifications);
    // }
    //
    // public function testDown()
    // {
    //     $migration = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
    //         ->getMock();
    //     $migration->expects($this->once())
    //         ->method('managerDown')
    //         ->will($this->returnValue(['test1', 'test2']));
    //
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
    //         ->getMock();
    //     $repo->method('getMigrations')->will($this->returnValue(['migration3', 'migration2', 'migration1']));
    //     $repo->expects($this->once())
    //         ->method('instantiateMigration')
    //         ->with($this->equalTo('migration2'))
    //         ->will($this->returnValue($migration));
    //
    //     $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')->getMock();
    //     $checker->method('isChecked')->will($this->returnCallback(function ($name) {
    //         return $name === 'migration2';
    //     }));
    //     $checker->expects($this->once())->method('uncheck')->with($this->equalTo('migration2'));
    //
    //     $notifications = [];
    //     $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     $notifier->method('info')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['info'][] = $message;
    //     }));
    //     $notifier->method('success')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['success'] = $message;
    //     }));
    //
    //     $this->getManager($repo, $checker, $notifier)->down();
    //
    //     $this->assertSame(
    //         ['info' => ['Running down migrations:', 'Processing migration2'], 'success' => ['test1', 'test2']],
    //         $notifications
    //     );
    // }
    //
    // public function testDownWithCountParam()
    // {
    //     $migration = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')->getMock();
    //
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')->getMock();
    //     $repo->method('getMigrations')->will($this->returnValue(['migration3', 'migration2', 'migration1']));
    //     $repo->expects($this->once())
    //         ->method('instantiateMigration')
    //         ->with($this->equalTo('migration2'))
    //         ->will($this->returnValue($migration));
    //
    //     $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')
    //         ->getMock();
    //     $checker->expects($this->at(0))
    //         ->method('isChecked')
    //         ->with($this->equalTo('migration1'))
    //         ->will($this->returnValue(false));
    //     $checker->expects($this->at(1))
    //         ->method('isChecked')
    //         ->with($this->equalTo('migration2'))
    //         ->will($this->returnValue(true));
    //
    //     $manager = $this->getManager($repo, $checker)->down(1);
    // }
    //
    // public function testDownWithName()
    // {
    //     $migration = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
    //         ->getMock();
    //     $migration->expects($this->once())
    //         ->method('managerDown')
    //         ->will($this->returnValue(['test1', 'test2']));
    //
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
    //         ->getMock();
    //     $repo->method('getMigrations')->will($this->returnValue(['migration3', 'migration2', 'migration1']));
    //     $repo->expects($this->once())
    //         ->method('instantiateMigration')
    //         ->with($this->equalTo('migration2'))
    //         ->will($this->returnValue($migration));
    //
    //     $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')->getMock();
    //     $checker->method('isChecked')->will($this->returnValue(true));
    //     $checker->expects($this->once())->method('uncheck')->with($this->equalTo('migration2'));
    //
    //     $notifications = [];
    //     $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     $notifier->method('info')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['info'][] = $message;
    //     }));
    //     $notifier->method('success')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['success'] = $message;
    //     }));
    //
    //     $this->getManager($repo, $checker, $notifier)->down('migration2');
    //
    //     $this->assertSame(
    //         ['info' => ['Running down migrations:', 'Processing migration2'], 'success' => ['test1', 'test2']],
    //         $notifications
    //     );
    // }
    //
    // public function testDownWithEmptyMigrationsList()
    // {
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
    //         ->getMock();
    //     $repo->method('getMigrations')->will($this->returnValue(['migration3', 'migration2', 'migration1']));
    //     $repo->expects($this->never())->method('instantiateMigration');
    //
    //     $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')->getMock();
    //     $checker->method('isChecked')->will($this->returnCallback(function ($name) {
    //         return false;
    //     }));
    //     $checker->expects($this->never())->method('check');
    //
    //     $notifications = [];
    //     $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     $notifier->method('info')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications['info'][] = $message;
    //     }));
    //
    //     $this->getManager($repo, $checker, $notifier)->down();
    //
    //     $this->assertSame(
    //         ['info' => ['Running down migrations:', 'There are no migrations for down']],
    //         $notifications
    //     );
    // }
    //
    // public function testDownWithexception()
    // {
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
    //         ->getMock();
    //     $repo->method('getMigrations')->will($this->throwException(new \Exception('test exception')));
    //
    //     $notifications = [];
    //     $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     $notifier->method('error')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications = $message;
    //     }));
    //
    //     $this->getManager($repo, null, $notifier)->down();
    //
    //     $this->assertContains('test exception', $notifications);
    // }
    //
    // public function testCreate()
    // {
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
    //         ->getMock();
    //     $repo->expects($this->once())
    //         ->method('create')
    //         ->with($this->equalTo('test_migration'))
    //         ->will($this->returnValue('test create'));
    //
    //     $notifications = null;
    //     $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     $notifier->method('success')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications = $message;
    //     }));
    //
    //     $this->getManager($repo, null, $notifier)->create('test_migration');
    //
    //     $this->assertSame('test create', $notifications);
    // }
    //
    // public function testCreateWithException()
    // {
    //     $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')
    //         ->getMock();
    //     $repo->expects($this->once())
    //         ->method('create')
    //         ->will($this->throwException(new \Exception('test exception')));
    //
    //     $notifications = null;
    //     $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     $notifier->method('error')->will($this->returnCallback(function ($message) use (&$notifications) {
    //         $notifications = $message;
    //     }));
    //
    //     $this->getManager($repo, null, $notifier)->create('test_migration');
    //
    //     $this->assertContains('test exception', $notifications);
    // }
    //
    // protected function getManager($repo = null, $checker = null, $notifier = null)
    // {
    //     if ($repo === null) {
    //         $repo = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateRepo')->getMock();
    //     }
    //     if ($checker === null) {
    //         $checker = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateChecker')->getMock();
    //     }
    //     if ($notifier === null) {
    //         $notifier = $this->getMockBuilder('\marvin255\bxmigrate\IMigrateNotifier')->getMock();
    //     }
    //
    //     return new Simple($repo, $checker, $notifier);
    // }
}
