<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\tests\BaseCase;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Highloadblock\CheckerTable;
use CUserTypeEntity;
use marvin255\bxmigrate\checker\HighLoadIb;

class HighLoadIbTest extends BaseCase
{
    /**
     * @test
     */
    public function testWrongTableNameInConstructorException()
    {
        $this->setExpectedException('\\marvin255\\bxmigrate\\checker\\Exception');
        $checker = new HighLoadIb(true);
    }

    /**
     * @test
     */
    public function testCreateTableException()
    {
        $migration = 'migration_name_' . mt_rand();

        $checker = new HighLoadIb;

        HighloadBlockTable::$errorOnAdd = 'error while adding HighloadBlockTable';

        $this->setExpectedException('\\marvin255\\bxmigrate\\checker\\Exception');
        $checker->check($migration);
    }

    /**
     * @test
     */
    public function testCreateFieldException()
    {
        $migration = 'migration_name_' . mt_rand();

        $checker = new HighLoadIb;

        CUserTypeEntity::$errorOnAdd = 'error while adding CUserTypeEntity';

        $this->setExpectedException('\\marvin255\\bxmigrate\\checker\\Exception');
        $checker->check($migration);
    }

    /**
     * @test
     */
    public function testCheck()
    {
        $migration = 'migration_name_' . mt_rand();

        $checker = new HighLoadIb;

        $this->assertFalse($checker->isChecked($migration));
        $checker->check($migration);
        $this->assertTrue($checker->isChecked($migration));
    }

    /**
     * @test
     */
    public function testCheckException()
    {
        $migration = 'migration_name_' . mt_rand();

        $checker = new HighLoadIb;

        CheckerTable::$errorOnAdd = 'error while adding CheckerTable';

        $this->setExpectedException('\\marvin255\\bxmigrate\\checker\\Exception');
        $checker->check($migration);
    }

    /**
     * @test
     */
    public function testUncheck()
    {
        $migration = 'migration_name_' . mt_rand();

        $checkerToCheck = new HighLoadIb;
        $checkerToCheck->check($migration);
        $checkerToUncheck = new HighLoadIb;

        $this->assertTrue($checkerToUncheck->isChecked($migration));
        $checkerToUncheck->uncheck($migration);
        $this->assertFalse($checkerToUncheck->isChecked($migration));
    }

    /**
     * @test
     */
    public function testUncheckException()
    {
        $migration = 'migration_name_' . mt_rand();

        $checker = new HighLoadIb;

        $checker->check($migration);
        CheckerTable::$errorOnDelete = 'error while deleting CheckerTable';

        $this->setExpectedException('\\marvin255\\bxmigrate\\checker\\Exception');
        $checker->uncheck($migration);
    }

    /**
     * Очишаем классы с моками битриксовых сущностей.
     */
    public function setUp()
    {
        CUserTypeEntity::reset();
        HighloadBlockTable::reset();
        CheckerTable::reset();
    }
}
