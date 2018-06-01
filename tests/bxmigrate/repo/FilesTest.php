<?php

namespace marvin255\bxmigrate\tests\bxmigrate\repo;

use marvin255\bxmigrate\repo\Files;
use marvin255\bxmigrate\tests\BaseCase;
use RuntimeException;

class FilesTest extends BaseCase
{
    /**
     * @var string
     */
    protected $migrationsFolder;
    /**
     * @var string
     */
    protected $templatesFolder;

    /**
     * @test
     */
    public function testConstructorEmptyFolderParameterException()
    {
        $this->setExpectedException('\\marvin255\\bxmigrate\\repo\\Exception');

        $repo = new Files('');
    }

    /**
     * @test
     */
    public function testConstructorUnexistedFolderException()
    {
        $unexistedFolder = 'unexisted_' . mt_rand();
        $this->setExpectedException('\\marvin255\\bxmigrate\\repo\\Exception', $unexistedFolder);

        $repo = new Files($unexistedFolder);
    }

    /**
     * @test
     */
    public function testConstructorEmptyFilePrefixParameterException()
    {
        $this->setExpectedException('\\marvin255\\bxmigrate\\repo\\Exception');

        $repo = new Files($this->migrationsFolder, false);
    }

    /**
     * @test
     */
    public function testConstructorEmptyTemplatesFolderParameterException()
    {
        $this->setExpectedException('\\marvin255\\bxmigrate\\repo\\Exception');

        $repo = new Files($this->migrationsFolder, 'test', '');
    }

    /**
     * @test
     */
    public function testConstructorUnexistedTemplatesFolderException()
    {
        $unexistedFolder = 'unexisted_' . mt_rand();
        $this->setExpectedException('\\marvin255\\bxmigrate\\repo\\Exception', $unexistedFolder);

        $repo = new Files($this->migrationsFolder, 'test', $unexistedFolder);
    }

    /**
     * @test
     */
    public function testGetMigrations()
    {
        $repo = new Files($this->migrationsFolder, 'test', $this->templatesFolder);

        $migrations = $repo->getMigrations();
        $migrations = $repo->getMigrations();

        $this->assertSame(
            ['test_migration_1', 'test_migration_2'],
            $migrations
        );
    }

    /**
     * @test
     */
    public function testInstantiateMigration()
    {
        $repo = new Files($this->migrationsFolder, 'test', $this->templatesFolder);

        $migrationObject = $repo->instantiateMigration('test_migration_2');

        $this->assertInstanceOf('test_migration_2', $migrationObject);
        $this->assertSame('test 2', $migrationObject->getTestParam());
    }

    /**
     * @test
     */
    public function testInstantiateMigrationNoMigrationFileException()
    {
        $repo = new Files($this->migrationsFolder, 'test', $this->templatesFolder);
        $migration = 'cant_find_this_migration';

        $this->setExpectedException('\\marvin255\\bxmigrate\\repo\\Exception', $migration);
        $migrationObject = $repo->instantiateMigration($migration);
    }

    /**
     * @test
     */
    public function testInstantiateMigrationNoMigrationClassException()
    {
        $repo = new Files($this->migrationsFolder, 'test', $this->templatesFolder);
        $migration = 'test_migration_1';

        $this->setExpectedException('\\marvin255\\bxmigrate\\repo\\Exception', $migration);
        $migrationObject = $repo->instantiateMigration($migration);
    }

    /**
     * @test
     */
    public function testCreate()
    {
        $repo = new Files($this->migrationsFolder, 'test', $this->templatesFolder);
        $migrationName = 'created_migration_' . mt_rand();
        $testParameter = 'test_parameter_' . mt_rand();

        $createdMigrationName = $repo->create(
            $migrationName,
            $this->templatesFolder . '/default.php',
            ['testParameter' => $testParameter]
        );
        $migrationObject = $repo->instantiateMigration($createdMigrationName);

        $this->assertSame($testParameter, $migrationObject->getTestParam());
    }

    /**
     * @test
     */
    public function testCreateSmartTemplate()
    {
        $repo = new Files($this->migrationsFolder, 'test', $this->templatesFolder);
        $moduleName = 'test.test';
        $migrationName = "install_module_{$moduleName}";

        $createdMigrationName = $repo->create($migrationName);
        $migrationObject = $repo->instantiateMigration($createdMigrationName);

        $this->assertSame($moduleName, $migrationObject->getTestParam());
    }

    /**
     * @test
     */
    public function testCreateEmptyNameException()
    {
        $repo = new Files($this->migrationsFolder, 'test', $this->templatesFolder);
        $migration = './~\\';

        $this->setExpectedException('\\marvin255\\bxmigrate\\repo\\Exception', $migration);
        $migrationName = $repo->create($migration);
    }

    /**
     * @test
     */
    public function testCreateUnexistedTemplateException()
    {
        $repo = new Files($this->migrationsFolder, 'test', $this->templatesFolder);
        $template = 'unexisted_template';
        $migration = 'test_migration';

        $this->setExpectedException('\\marvin255\\bxmigrate\\repo\\Exception', $template);
        $migrationName = $repo->create($migration, $template);
    }

    /**
     * Создаем всевременные папки и файлы для тетосв перед тестированием.
     *
     * @throws \RuntimeException
     */
    public function setUp()
    {
        $this->migrationsFolder = sys_get_temp_dir() . '/bxmigrate_migrations';
        self::rrmdir($this->migrationsFolder);
        if (!mkdir($this->migrationsFolder, 0777, true)) {
            throw new RuntimeException(
                "Can't create {$this->migrationsFolder} folder for testing"
            );
        }

        file_put_contents(
            $this->migrationsFolder . '/test_migration_1.php',
            '<?php class test_migration_fails { public function getTestParam() { return "test 1"; } }'
        );
        file_put_contents(
            $this->migrationsFolder . '/test_migration_2.php',
            '<?php class test_migration_2 { public function getTestParam() { return "test 2"; } }'
        );
        file_put_contents(
            $this->migrationsFolder . '/cant_find_this_migration.php',
            '<?php class cant_find_this_migration { public function getTestParam() { return "cant_find_this_migration"; } }'
        );

        $this->templatesFolder = sys_get_temp_dir() . '/bxmigrate_templates';
        self::rrmdir($this->templatesFolder);
        if (!mkdir($this->templatesFolder, 0777, true)) {
            throw new RuntimeException(
                "Can't create {$this->templatesFolder} folder for testing"
            );
        }

        file_put_contents(
            $this->templatesFolder . '/default.php',
            '<?php echo "<?php\n"; ?>'
            . 'class <?php echo $name; ?> {'
            . ' public function getTestParam() { return "<?php echo $testParameter; ?>"; }'
            . ' }'
        );
        file_put_contents(
            $this->templatesFolder . '/module_install.php',
            '<?php echo "<?php\n"; ?>'
            . 'class <?php echo $name; ?> {'
            . ' public function getTestParam() { return "<?php echo $smart_param_1; ?>"; }'
            . ' }'
        );
    }

    /**
     * Удаляем временные папки и файлы после тестирования.
     */
    public function tearDown()
    {
        self::rrmdir($this->migrationsFolder);
        self::rrmdir($this->templatesFolder);
    }

    /**
     * Удаляет папку вместе со всем содержимым.
     *
     * @param string $dir
     *
     * @return false
     */
    protected static function rrmdir($dir)
    {
        $return = false;

        if (is_dir($dir)) {
            $items = scandir($dir);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                $path = "{$dir}/{$item}";
                if (is_dir($path)) {
                    self::rrmdir($path);
                } else {
                    unlink($path);
                }
            }
            $return = rmdir($dir);
        }

        return $return;
    }
}
