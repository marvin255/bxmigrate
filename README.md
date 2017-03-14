Консольные миграции для 1С Битрикс
==================================

Реализация консольных миграций для 1С Битрикс. 



Установка
---------

Устанавливается с помощью [Composer](https://getcomposer.org/doc/00-intro.md).

Добавьте в ваш composer.json в раздел `require`:

```javascript
"require": {
    "marvin255/bxmigrate": "dev-master"
}
```

И в раздел `repositories`:

```javascript
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/marvin255/bxmigrate"
    }
]
```



Описание
--------

Миграции фиксируют какое-либо изменение в базе данных в файл. Это позволяет передавать изменения базы данных через репозиторий проекта.

Миграции состоят из 4 элементов:

1. Объект миграции - объект с уникальным именем класса, который имеет метод up для применения миграции в базе данных и метод down для отмены миграции в базе данных, эти методы реализует тот, кто хочет внести изменения в базу данных.

2. Объект хранилища миграций - возвращает массив с объектами всех доступных миграций, позволяет создать новую миграцию.

3. Объект статуса миграций - позволяет проверить применена ли миграция и пометить миграцию примененной или отмененной.

4. Объект менеджера миграций - связывает работу всех предыдущих объектов воедино, позволяет применить миграции, отменить, создать новую, для этого он передает контроль одному из описанных выше объектов.



Алгоритм работы с миграциями
----------------------------

Прежде всего, нужно создать консольный скрипт, который позволит использовать 1С Битрикс в консоли. Пример реализации такого скрипта с использованием [Symfony console](https://github.com/symfony/console):

```php
#!/usr/bin/env php
<?php

//Данный файл называется cli.php и расположен на уровень выше document root веб-сервера (папка web).
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/web');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

//Папка с миграциями должна быть создана и находиться рядом с этим скриптом (папка migrations).
define('CLI_MIGRATIONS_PATH', __DIR__.'/migrations');

//Отключаем сбор статистики и проверку событий и агентов.
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('CHK_EVENT', true);

//Подключаем ядро битрикса.
require_once(__DIR__.'/vendor/marvin255/bxmigrate/src/Autoloader.php');
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

//подключаем Symfony console
use Symfony\Component\Console\Application;
$application = new Application();

//Определяем команды для миграций.
$application->add(new \marvin255\bxmigrate\cli\SymphonyUp(CLI_MIGRATIONS_PATH));
$application->add(new \marvin255\bxmigrate\cli\SymphonyDown(CLI_MIGRATIONS_PATH));
$application->add(new \marvin255\bxmigrate\cli\SymphonyCreate(CLI_MIGRATIONS_PATH));

//Запускаем команду на исполнение.
$application->run();
```

После того как скрипт был создан, то алгоритм ничем не отличается от любых иных миграций на php:

1. создать миграцию с помощью команды `php cli.php bxmigrate:create new_migration`,

2. в каталоге `migrations` найти миграцию с именем `migrate_{TIMESTAMP СОЗДАНИЯ МИГРАЦИИ}_new_migration.php`,

3. реализовать методы up и down для данной миграции, например:

```php
<?php

/**
 * Миграция для создания типа инфоблока 'Structure'.
 */
class migrate_{TIMESTAMP СОЗДАНИЯ МИГРАЦИИ}_new_migration extends \marvin255\bxmigrate\migrate\Coded
{
    public function up()
    {
        return $this->IblockTypeCreate([
            'ID' => 'structure',
            'SECTIONS' => 'Y',
            'IN_RSS' => 'N',
            'SORT' => 500,
            'EDIT_FILE_BEFORE' => '',
            'EDIT_FILE_AFTER' => '',
            'LANG' => [
                'en' => [
                    'NAME' => 'Structure',
                    'SECTION_NAME' => '',
                    'ELEMENT_NAME' => '',
                ],
                'ru' => [
                    'NAME' => 'Структура сайта',
                    'SECTION_NAME' => '',
                    'ELEMENT_NAME' => '',
                ],
            ],
        ]);
    }

    public function down()
    {
        return $this->IblockTypeDelete('structure');
    }
}
```

4. Применить миграцию в базе данных с помощью команды `php cli.php bxmigrate:up`,

5. В случае необходимости отменить последню миграцию можно с помощью команды ``php cli.php bxmigrate:down`.