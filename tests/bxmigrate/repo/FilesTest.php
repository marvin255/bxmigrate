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
        foreach ($migrations as $key => $migration) {
            $this->assertInstanceOf(
                get_class($migrationMock),
                $migration,
                'All migrations must implements class from parentClass param'
            );
            $this->assertRegExp(
                '/prefix_\d+_(test_1|test_2|test_3)/',
                $key,
                'All migrations must have properly file names'
            );
        }
    }

    public function testGetMigartionsWrongInterfaceException()
    {
        $folder = $this->getTestFolder();
        file_put_contents($folder.'/prefix_123_test.php', '<?php class prefix_123_test {}');
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');
        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'File prefix_123_test.php has no migration class'
        );
        $repo->getMigrations();
    }

    public function testCreateEmptyNameException()
    {
        $folder = $this->getTestFolder();
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');
        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'Can not create migration file for name:  /// .. \\'
        );
        $repo->create(' /// .. \\');
    }

    public function testCreateWrongTemplateException()
    {
        $folder = $this->getTestFolder();
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');
        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'Can\'t find migration template file for migration: create_iblock_test'
        );
        $repo->create('create_iblock_test');
    }

    public function testCreateMigartionExistException()
    {
        $folder = $this->getTestFolder();
        file_put_contents($folder.'/prefix_'.time().'_create_iblock_test.php', '');
        $migrationMock = $this->getMockBuilder('\marvin255\bxmigrate\IMigrate')
            ->getMock();
        $repo = new Files($folder, $folder, get_class($migrationMock), 'prefix');
        $this->setExpectedException(
            '\marvin255\bxmigrate\repo\Exception',
            'Migration already exists: create_iblock_test'
        );
        $repo->create('create_iblock_test');
    }

    public function setUp()
    {
        $folder = $this->getTestFolder();
        if (!is_dir($folder)) {
            mkdir($folder);
        }
        file_put_contents(
            $folder.'/default.php',
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
        return __DIR__.'/bxmigrateunit';
    }
}
