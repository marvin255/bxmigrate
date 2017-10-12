<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\repo\Files;

class FilesTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorFolderException()
    {
        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'Migration folder does not exist'
        );
        new Files('test_fails', 'test_fails');
    }

    public function testConstructorTemplatesFolderException()
    {
        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'Migration template folder does not exist'
        );
        new Files(sys_get_temp_dir(), 'test_fails');
    }

    public function testConstructorParentClassException()
    {
        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'Migration parent class is empty'
        );
        new Files(sys_get_temp_dir(), sys_get_temp_dir(), null);
    }

    public function testGetMigrations()
    {
        $folder = $this->getTestFolder();
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');
        $repo->create('test_1');
        $repo->create('test_2');
        $repo->create('test_3');
        $migrations = $repo->getMigrations();
        $this->assertCount(3, $migrations, 'getMigrations method must return list of all migrations.');
        foreach ($migrations as $migration) {
            $this->assertRegExp(
                '/prefix_\d+_(test_1|test_2|test_3)/',
                $migration,
                'All migrations must have properly file names'
            );
        }
    }

    public function testInstatntiateMigration()
    {
        $folder = $this->getTestFolder();
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');
        $repo->create('test_1');

        $migrations = $repo->getMigrations();
        $this->assertCount(1, $migrations);

        $this->assertInstanceOf(
            get_class($migrationMock),
            $repo->instantiateMigration($migrations[0])
        );
    }

    public function testInstatntiateMigrationWithWrongFileName()
    {
        $folder = $this->getTestFolder();
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');
        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'Can\'t find file for migration with name test_1'
        );
        $repo->instantiateMigration('test_1');
    }

    public function testInstatntiateMigrationWithWrongMigrationClass()
    {
        $folder = $this->getTestFolder();
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');

        file_put_contents(
            $folder . '/prefix_test_migration.php',
            "<?php class prefix_test_migration {}\r\n;"
        );
        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'File prefix_test_migration has no migration class'
        );
        $repo->instantiateMigration('prefix_test_migration');
    }

    public function testCreateWithWrongName()
    {
        $folder = $this->getTestFolder();
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');
        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'Can not create migration file for name:     ~~~~~~  '
        );
        $repo->create('    ~~~~~~  ');
    }

    public function testCreateWithAlreadyExistedName()
    {
        $folder = $this->getTestFolder();
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');

        file_put_contents(
            $folder . '/prefix_' . time() . '_test_migration.php',
            "<?php class prefix_test_migration {}\r\n;"
        );

        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'Migration already exists: test_migration'
        );
        $repo->create('test_migration');
    }

    public function testCreateWithEmptyMigrationTemplate()
    {
        $folder = $this->getTestFolder();
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');

        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'Can\'t find migration template file for migration: create_iblock_type_test'
        );
        $repo->create('create_iblock_type_test');
    }

    public function setUp()
    {
        $folder = $this->getTestFolder();
        if (!is_dir($folder)) {
            mkdir($folder);
        }
        file_put_contents(
            $folder . '/default.php',
            '<?php echo "<?php class {$name} extends {$parentClass} {}\r\n";'
        );
    }

    public function tearDown()
    {
        $folder = $this->getTestFolder();
        if (is_dir($folder)) {
            $files = scandir($folder);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                unlink("{$folder}/{$file}");
            }
            rmdir($folder);
        }
    }

    protected function getTestFolder()
    {
        return __DIR__ . '/bxmigrateunit';
    }
}
