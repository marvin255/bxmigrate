<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\tests\BaseCase;
use marvin255\bxmigrate\cli\Factory;

class FactoryTest extends BaseCase
{
    public function testRegisterCommands()
    {
        $app = $this->getMockBuilder('\\Symfony\\Component\\Console\\Application')
            ->disableOriginalConstructor()
            ->getMock();

        $app->expects($this->at(0))
            ->method('add')
            ->with($this->isInstanceOf('\\marvin255\\bxmigrate\\cli\\SymphonyUp'));
        $app->expects($this->at(1))
            ->method('add')
            ->with($this->isInstanceOf('\\marvin255\\bxmigrate\\cli\\SymphonyDown'));
        $app->expects($this->at(2))
            ->method('add')
            ->with($this->isInstanceOf('\\marvin255\\bxmigrate\\cli\\SymphonyCreate'));
        $app->expects($this->at(3))
            ->method('add')
            ->with($this->isInstanceOf('\\marvin255\\bxmigrate\\cli\\SymphonyRefresh'));
        $app->expects($this->at(4))
            ->method('add')
            ->with($this->isInstanceOf('\\marvin255\\bxmigrate\\cli\\SymphonyCheck'));

        $res = Factory::registerCommands($app, sys_get_temp_dir());

        $this->assertInstanceOf('\\Symfony\\Component\\Console\\Application', $app);
    }
}
