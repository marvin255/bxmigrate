<?php

namespace marvin255\bxmigrate\checker;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use CUserTypeEntity;

/**
 * Объект, который проверяет статус миграции для данной базы данных.
 * Создает высоконагруженный инфоблок, в который записывает название миграции и дату применения миграции.
 * При отмене миграции - удаляет запись о миграции из списка.
 * Инфоблок создается автоматичекси, при применении каждой новой миграции проверяется его существование.
 */
class HighLoadIb implements \marvin255\bxmigrate\IMigrateChecker
{
    /**
     * @var string
     */
    protected $tableName = null;
    /**
     * @var string
     */
    protected $compiledEntity = null;

    /**
     * В конструкторе задем название таблицы базы данных, в которой будут сохранены записи о миграциях.
     *
     * @param string $tableName
     *
     * @throws \marvin255\bxmigrate\checker\Exception
     */
    public function __construct($tableName = 'bx_db_migrations')
    {
        if (empty($tableName)) {
            throw new Exception('Table name can not be empty');
        }
        $this->tableName = $tableName;
        Loader::includeModule('highloadblock');
    }

    /**
     * {@inheritdoc}
     */
    public function isChecked($migration)
    {
        $checked = $this->getChecked();

        return isset($checked[$migration]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxmigrate\checker\Exception
     */
    public function check($migration)
    {
        $checked = $this->getChecked();
        if (!isset($checked[$migration])) {
            $hlblock = $this->infrastructureCheck();
            $class = $this->compileEntity($hlblock);
            $result = $class::add([
                'UF_MIGRATION_NAME' => $migration,
                'UF_MIGRATION_DATE' => date('d.m.Y'),
            ]);
            if (!$result->isSuccess()) {
                throw new Exception('Can\'t check migration in HL: '.implode(', ', $result->getErrorMessages()));
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxmigrate\checker\Exception
     */
    public function uncheck($migration)
    {
        $checked = $this->getChecked();
        if (isset($checked[$migration])) {
            $hlblock = $this->infrastructureCheck();
            $class = $this->compileEntity($hlblock);
            $result = $class::delete($checked[$migration]['ID']);
            if (!$result->isSuccess()) {
                throw new Exception('Can\'t delete migration in HL '.implode(', ', $result->getErrorMessages()));
            }
        }
    }

    /**
     * Возвращает список всех примененных миграций.
     *
     * @return array
     */
    protected function getChecked()
    {
        $return = [];
        $hlblock = $this->infrastructureCheck();
        $class = $this->compileEntity($hlblock);
        $res = $class::getList([
            'select' => ['*'],
        ])->fetchAll();
        foreach ($res as $key => $value) {
            $return[$value['UF_MIGRATION_NAME']] = $value;
        }

        return $return;
    }

    /**
     * Битриксовая магия. Прежде, чем использовать модель hl инфоблока, ее нужно собрать.
     *
     * @param array $hlblock
     *
     * @return string
     */
    protected function compileEntity(array $hlblock)
    {
        if ($this->compiledEntity === null) {
            global $USER_FIELD_MANAGER;
            $USER_FIELD_MANAGER->CleanCache();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $this->compiledEntity = $entity->getDataClass();
        }

        return $this->compiledEntity;
    }

    /**
     * Проверяет существовани таблицы в базе данных и соответствующей ей сущности hl инфоблока.
     * Если что-либо не найдено, то создает.
     * Возвращает массив с описанием сущности hl инфоблока, которая используется для обработки миграций.
     *
     * @return array
     *
     * @throws \marvin255\bxmigrate\checker\Exception
     */
    protected function infrastructureCheck()
    {
        $modelName = $this->getModelName();
        //проверяем существует ли таблица миграций
        $filter = array(
            'select' => array('ID', 'NAME', 'TABLE_NAME'),
            'filter' => array('=TABLE_NAME' => $this->tableName),
        );
        $hlblock = HighloadBlockTable::getList($filter)->fetch();
        //создаем таблицу, если она не существует
        if (empty($hlblock['ID'])) {
            $result = HighloadBlockTable::add([
                'NAME' => $modelName,
                'TABLE_NAME' => $this->tableName,
            ]);
            $id = $result->getId();
            if (!$id) {
                throw new Exception('Can\'t create HL table '.implode(', ', $result->getErrorMessages()));
            }
        } else {
            $id = $hlblock['ID'];
        }
        //проверяем поля таблицы, чтобы были все
        $fields = [];
        $rsData = CUserTypeEntity::GetList([], [
            'ENTITY_ID' => "HLBLOCK_{$id}",
        ]);
        while ($ob = $rsData->GetNext()) {
            $fields[$ob['FIELD_NAME']] = $ob['ID'];
        }
        //название миграции
        if (empty($fields['UF_MIGRATION_NAME'])) {
            $obUserField = new CUserTypeEntity();
            $idRes = $obUserField->Add([
                'USER_TYPE_ID' => 'string',
                'ENTITY_ID' => "HLBLOCK_{$id}",
                'FIELD_NAME' => 'UF_MIGRATION_NAME',
                'EDIT_FORM_LABEL' => [
                    'ru' => 'Название миграции',
                ],
            ]);
            if (!$idRes) {
                throw new Exception('Can\'t create UF_MIGRATION_NAME property');
            }
        }
        //дата миграции
        if (empty($fields['UF_MIGRATION_DATE'])) {
            $obUserField = new CUserTypeEntity();
            $idRes = $obUserField->Add([
                'USER_TYPE_ID' => 'string',
                'ENTITY_ID' => "HLBLOCK_{$id}",
                'FIELD_NAME' => 'UF_MIGRATION_DATE',
                'EDIT_FORM_LABEL' => [
                    'ru' => 'Дата миграции',
                ],
            ]);
            if (!$idRes) {
                throw new Exception('Can\'t create UF_MIGRATION_DATE property');
            }
        }

        return [
            'ID' => $id,
            'NAME' => $modelName,
            'TABLE_NAME' => $this->tableName,
        ];
    }

    /**
     * Преобразовывает имя таблицы в базе данных в имя сущности hl инфоблока.
     *
     * @return string
     */
    protected function getModelName()
    {
        return ucfirst(str_replace(['_'], '', $this->tableName));
    }
}
