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
    protected $tableName;
    /**
     * @var string
     */
    protected $compiledEntity;

    /**
     * В конструкторе задем название таблицы базы данных, в которой будут сохранены записи о миграциях.
     *
     * @param string $tableName
     *
     * @throws \marvin255\bxmigrate\checker\Exception
     */
    public function __construct($tableName = 'bx_db_migrations')
    {
        if (!preg_match('/^[0-9a-zA-Z_]{3,}$/i', $tableName)) {
            throw new Exception(
                'Table name can contains only letters, numbers and _, and must be more than 3 symbols'
            );
        }
        $this->tableName = $tableName;
    }

    /**
     * @inheritdoc
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
        if (!$this->isChecked($migration)) {
            $class = $this->compileEntity();
            $result = $class::add([
                'UF_MIGRATION_NAME' => $migration,
                'UF_MIGRATION_DATE' => date('d.m.Y H:i'),
            ]);
            if (!$result->isSuccess()) {
                throw new Exception(
                    "Can't check migration in HL: "
                    . implode(', ', $result->getErrorMessages())
                );
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
        if ($this->isChecked($migration)) {
            $checked = $this->getChecked();
            $class = $this->compileEntity();
            $result = $class::delete($checked[$migration]['ID']);
            if (!$result->isSuccess()) {
                throw new Exception(
                    "Can't delete migration in HL "
                    . implode(', ', $result->getErrorMessages())
                );
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

        $class = $this->compileEntity();
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
     * @return string
     */
    protected function compileEntity()
    {
        if ($this->compiledEntity === null) {
            Loader::includeModule('highloadblock');
            global $USER_FIELD_MANAGER;
            $USER_FIELD_MANAGER->CleanCache();
            $hlblock = $this->infrastructureCheck();
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
        $filter = [
            'select' => ['ID', 'NAME', 'TABLE_NAME'],
            'filter' => ['=TABLE_NAME' => $this->tableName],
        ];
        $hlblock = HighloadBlockTable::getList($filter)->fetch();

        //создаем таблицу, если она не существует
        if (empty($hlblock['ID'])) {
            $result = HighloadBlockTable::add([
                'NAME' => $modelName,
                'TABLE_NAME' => $this->tableName,
            ]);
            $id = $result->getId();
            if (!$id) {
                throw new Exception(
                    "Can't create HL table "
                    . implode(', ', $result->getErrorMessages())
                );
            }
        } else {
            $id = $hlblock['ID'];
        }

        //проверяем поля таблицы
        $fields = [];
        $rsData = CUserTypeEntity::getList([], [
            'ENTITY_ID' => "HLBLOCK_{$id}",
        ]);
        while ($ob = $rsData->getNext()) {
            $fields[$ob['FIELD_NAME']] = $ob['ID'];
        }

        $requiredFields = [
            'UF_MIGRATION_NAME' => 'Название миграции',
            'UF_MIGRATION_DATE' => 'Дата миграции',
        ];
        foreach ($requiredFields as $requiredFieldName => $requiredFieldLabel) {
            if (!isset($fields[$requiredFieldName])) {
                $obUserField = new CUserTypeEntity;
                $idRes = $obUserField->add([
                    'USER_TYPE_ID' => 'string',
                    'ENTITY_ID' => "HLBLOCK_{$id}",
                    'FIELD_NAME' => $requiredFieldName,
                    'EDIT_FORM_LABEL' => [
                        'ru' => $requiredFieldLabel,
                    ],
                ]);
                if (!$idRes) {
                    throw new Exception(
                        "Can't create {$requiredFieldName} property"
                    );
                }
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
