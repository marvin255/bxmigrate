Консольные миграции для 1С Битрикс
==================================

[![Latest Stable Version](https://poser.pugx.org/marvin255/bxmigrate/v/stable.png)](https://packagist.org/packages/marvin255/bxmigrate)
[![License](https://poser.pugx.org/marvin255/bxmigrate/license.svg)](https://packagist.org/packages/marvin255/bxmigrate)
[![Build Status](https://travis-ci.org/marvin255/bxmigrate.svg?branch=master)](https://travis-ci.org/marvin255/bxmigrate)

Реализация консольных миграций для 1С Битрикс.



Установка
---------

Устанавливается с помощью [Composer](https://getcomposer.org/doc/00-intro.md).

Добавьте в ваш composer.json в раздел `require`:

```javascript
"require": {
    "marvin255/bxmigrate": "~2.0"
}
```



Описание
--------

Миграции фиксируют какое-либо изменение в базе данных в файл. Это позволяет передавать изменения базы данных через репозиторий проекта.

Миграции состоят из 4 элементов:

1. Объект миграции - объект с уникальным именем класса, который имеет метод up для применения миграции в базе данных и метод down для отмены миграции в базе данных, эти методы реализует тот, кто хочет внести изменения в базу данных.

2. Объект хранилища миграций - возвращает массив с объектами всех доступных миграций, позволяет создать новую миграцию.

3. Объект статуса миграций - позволяет проверить применена ли миграция и пометить миграцию примененной или отмененной.

4. Объект менеджера миграций - связывает работу всех предыдущих объектов воедино, позволяет применить миграции, отменить, создать новую, для этого он передает контроль одному из описанных выше объектов.

5. Объект-нотификатор - выводит сообщения о результатах выполнения миграций пользователю.



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

//Подключаем Symfony console
use Symfony\Component\Console\Application;
$application = new Application();

//Определяем команды для миграций.
$application->add((new \marvin255\bxmigrate\cli\SymphonyUp)->setMigrationPath(CLI_MIGRATIONS_PATH));
$application->add((new \marvin255\bxmigrate\cli\SymphonyDown)->setMigrationPath(CLI_MIGRATIONS_PATH));
$application->add((new \marvin255\bxmigrate\cli\SymphonyCreate)->setMigrationPath(CLI_MIGRATIONS_PATH));

//Запускаем команду на исполнение.
$application->run();
```

После того как скрипт был создан, то алгоритм ничем не отличается от любых иных миграций на php:

1. создать миграцию с помощью команды `php cli.php bxmigrate:create new_migration`,

2. в каталоге `migrations` найти миграцию с именем `migrate_{TIMESTAMP_СОЗДАНИЯ_МИГРАЦИИ}_new_migration.php`,

3. реализовать методы up и down для данной миграции, например:

    ```php
    <?php

    /**
     * Миграция для создания типа инфоблока 'Structure'.
     */
    class migrate_/*TIMESTAMP_СОЗДАНИЯ_МИГРАЦИИ*/_new_migration extends \marvin255\bxmigrate\migrate\Coded
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

5. В случае необходимости отменить последнюю миграцию можно с помощью команды `php cli.php bxmigrate:down`.



Команды, предоставляемые вместе с библиотекой
---------------------------------------------

1. `bxmigrate:create my_awesome_migration` - создать файл для миграции с именем `my_awesome_migration`,

2. `bxmigrate:up` - применить все непримененые миграции,

3. `bxmigrate:up 3` - применить 3 первых по порядку не примененных миграции,

4. `bxmigrate:up my_awesome_migration` - применить только миграцию с именем `my_awesome_migration`,

5. `bxmigrate:down` - отменить одну, последнюю по порядку, миграцию,

6. `bxmigrate:down 3` - отменить 3 последних по порядку примененных миграции,

7. `bxmigrate:down my_awesome_migration` - отменить только миграцию с именем `my_awesome_migration`,



Особенности реализации
----------------------

1. Кэш Битрикса очищается каждый раз до миграции.

2. По умолчанию все действия внутри методов `up` и `down` обернуты в транзакцию. Для выполнения миграции без транзакций нужно использовать методы `unsafeUp` и `unsafeDown` соответственно.

3. "Быстрое" создание миграций. Если название миграции будет удовлетворять определенному регулярному выражению, то файл миграции будет сформирован сразу с реализованными методами `up` и `down`, в которые будут переданы те значения, которые получит регулярное выражение из названия миграции. Список "быстрых" названий:

    * `/^create_iblock_(.+)_uf_(.+)$/i` (`create_iblock_news_uf_author`) - Создает пользовательское свойство с указанным во втором параметре кодом для разделов инфоблока с кодом, указанным в первом параметре.
    * `/^update_iblock_(.+)_uf_(.+)$/i` (`update_iblock_news_uf_author`) - Обновляет пользовательское свойство с указанным во втором параметре кодом для разделов инфоблока с кодом, указанным в первом параметре.
    * `/^delete_iblock_(.+)_uf_(.+)$/i` (`delete_iblock_news_uf_author`) - Удаляет пользовательское свойство с указанным во втором параметре кодом для разделов инфоблока с кодом, указанным в первом параметре.
    * `/^create_iblock_(.+)_property_(.+)$/i` (`create_iblock_news_property_author`) - Создает свойство элемента инфоблока, указанного в первом параметре, с  кодом, указанным во втором параметре.
    * `/^update_iblock_(.+)_property_(.+)$/i` (`update_iblock_news_property_author`) - Обновляет свойство элемента инфоблока, указанного в первом параметре, с кодом, указанным во втором параметре.
    * `/^delete_iblock_(.+)_property_(.+)$/i` (`delete_iblock_news_property_author`) - Удаляет свойство элемента инфоблока, указанного в первом параметре, с кодом, указанным во втором параметре.
    * `/^create_iblock_type_(.+)$/i` (`create_iblock_type_content`) - Создает тип инфоблоков с кодом, указанным в первом параметре.
    * `/^delete_iblock_type_(.+)$/i` (`delete_iblock_type_content`) - Удаляет тип инфоблоков с кодом, указанным в первом параметре.
    * `/^create_iblock_(.+)$/i` (`create_iblock_news`) - Создает инфоблок с кодом, указанным в первом параметре.
    * `/^update_iblock_(.+)$/i` (`update_iblock_news`) - Обновляет инфоблок с кодом, указанным в первом параметре.
    * `/^create_iblock_(.+)$/i` (`create_iblock_news`) - Создает инфоблок с кодом, указанным в первом параметре.
    * `/^install_module_(.+)$/i` (`install_module_iblock`) - Устанавливает модуль с кодом, указанным в первом параметре.
    * `/^delete_module_(.+)$/i` (`delete_module_iblock`) - Удаляет модуль с кодом, указанным в первом параметре.
    * `/^create_user_uf_(.+)$/i` (`create_user_uf_field`) - Создает пользовательское поле (UF_*) для сущности пользователя с кодом, указанным в первом параметре.
    * `/^update_user_uf_(.+)$/i` (`update_user_uf_field`) - Обновляет пользовательское поле (UF_*) для сущности пользователя с кодом, указанным в первом параметре.
    * `/^delete_user_uf_(.+)$/i` (`delete_user_uf_field`) - Удаляет пользовательское поле (UF_*) для сущности пользователя с кодом, указанным в первом параметре.
    * `/^create_hlblock_(.+)_property_(.+)$/i` (`create_hlblock_OrderForm_property_number`) - Создает пользовательское свойство с указанным во втором параметре кодом для сущности highload инфоблока с кодом, указанным в первом параметре.
    * `/^update_hlblock_(.+)_property_(.+)$/i` (`update_hlblock_OrderForm_property_number`) - Обновляет пользовательское свойство с указанным во втором параметре кодом для сущности highload инфоблока с кодом, указанным в первом параметре.
    * `/^delete_hlblock_(.+)_property_(.+)$/i` (`delete_hlblock_OrderForm_property_number`) - Удаляет пользовательское свойство с указанным во втором параметре кодом для сущности highload инфоблока с кодом, указанным в первом параметре.
    * `/^create_hlblock_(.+)$/i` (`create_hlblock_OrderForm`) - Создает сущность highload инфоблока с названием, указанным в первом параметре.
    * `/^update_hlblock_(.+)$/i` (`update_hlblock_OrderForm`) - Обновляет сущность highload инфоблока с названием, указанным в первом параметре.
    * `/^delete_hlblock_(.+)$/i` (`delete_hlblock_OrderForm`) - Удаляет сущность highload инфоблока с названием, указанным в первом параметре.
    * `/^create_email_event_type_(.+)_lid_([^_]+)$/i` (`create_email_event_type_TEST_MESSAGE_ru`) - Создает тип почтовых событий с названием, указанным в первом параметре, и привязанным к языку из второго параметра.
    * `/^update_email_event_type_(.+)_lid_([^_]+)$/i` (`update_email_event_type_TEST_MESSAGE_ru`) - Обновляет тип почтовых событий с названием, указанным в первом параметре, и привязанным к языку из второго параметра.
    * `/^delete_email_event_type_(.+)_lid_([^_]+)$/i` (`delete_email_event_type_TEST_MESSAGE_ru`) - Удаляет тип почтовых событий с названием, указанным в первом параметре, и привязанным к языку из второго параметра.
    * `/^create_email_template_(.+)$/i` (`create_email_template_TEST_MESSAGE`) - Создает шаблон почтового сообщения для события с типом, указанном в первом параметре.
    * `/^update_email_template_(.+)$/i` (`update_email_template_TEST_MESSAGE`) - Обновляет шаблон почтового сообщения для события с типом, указанном в первом параметре.
    * `/^delete_email_template_(.+)$/i` (`delete_email_template_TEST_MESSAGE`) - Удаляет шаблон почтового сообщения для события с типом, указанном в первом параметре.
