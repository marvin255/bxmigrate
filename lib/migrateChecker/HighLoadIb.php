<?php

namespace marvin255\bxmigrate\migrateChecker;

use Bitrix\Main\Loader;
use CUserTypeEntity;

class HighLoadIb implements \marvin255\bxmigrate\IMigrateChecker
{
	/**
	 * @var string
	 */
	protected $_tableName = null;
	/**
	 * @var string
	 */
	protected $_compiledEntity = null;



	/**
	 * @param array $config
	 */
	public function __construct(array $config = null)
	{
		Loader::includeModule('highloadblock');
		if ($config) $this->config($config);
	}



	/**
	 * @param string $migration
	 * @return bool
	 */
	public function isChecked($migration)
	{
		$checked = $this->getChecked();
		return isset($checked[$migration]);
	}

	/**
	 * @param string $migration
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
				throw new \marvin255\bxmigrate\Exception('Can\'t check migration in HL');
			}
		}
	}

	/**
	 * @param string $migration
	 */
	public function uncheck($migration)
	{
		$checked = $this->getChecked();
		if (isset($checked[$migration])) {
			$hlblock = $this->infrastructureCheck();
			$class = $this->compileEntity($hlblock);
			$result = $class::delete($checked[$migration]['ID']);
			if (!$result->isSuccess()) {
				throw new \marvin255\bxmigrate\Exception('Can\'t delete migration in HL');
			}
		}
	}

	/**
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

		if (is_array($res)) {
			foreach ($res as $key => $value) {
				$return[$value['UF_MIGRATION_NAME']] = $value;
			}
		}

		return $return;
	}

	/**
	 * @param array $hlblock
	 * @return string
	 */
	protected function compileEntity(array $hlblock)
	{
		if ($this->_compiledEntity === null) {
			global $USER_FIELD_MANAGER;
			$USER_FIELD_MANAGER->CleanCache();
			$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
			$this->_compiledEntity = $entity->getDataClass();
		}
		return $this->_compiledEntity;
	}

	/**
	 * @return array
	 */
	protected function infrastructureCheck()
	{
		$table = $this->getTableName();
		$modelName = $this->getModelName();

		//проверяем существует ли таблица миграций
		$filter = array(
			'select' => array('ID', 'NAME', 'TABLE_NAME'),
			'filter' => array('=TABLE_NAME' => $table),
		);
		$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList($filter)->fetch();
		//создаем таблицу, если она не существует
		if (empty($hlblock['ID'])) {
			$result = \Bitrix\Highloadblock\HighloadBlockTable::add([
				'NAME' => $modelName,
				'TABLE_NAME' => $table,
			]);
			$id = $result->getId();
			if (!$id) throw new \marvin255\bxmigrate\Exception('Can\'t create HL table');
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
			$obUserField = new CUserTypeEntity;
			$idRes = $obUserField->Add([
				'USER_TYPE_ID' => 'string',
				'ENTITY_ID' => "HLBLOCK_{$id}",
				'FIELD_NAME' => 'UF_MIGRATION_NAME',
				'EDIT_FORM_LABEL' => [
					'ru' => 'Название миграции',
				]
			]);
			if (!$idRes) throw new \marvin255\bxmigrate\Exception('Can\'t create UF_MIGRATION_NAME property');
		}

		//дата миграции
		if (empty($fields['UF_MIGRATION_DATE'])) {
			$obUserField = new CUserTypeEntity;
			$idRes = $obUserField->Add([
				'USER_TYPE_ID' => 'string',
				'ENTITY_ID' => "HLBLOCK_{$id}",
				'FIELD_NAME' => 'UF_MIGRATION_DATE',
				'EDIT_FORM_LABEL' => [
					'ru' => 'Дата миграции',
				]
			]);
			if (!$idRes) throw new \marvin255\bxmigrate\Exception('Can\'t create UF_MIGRATION_DATE property');
		}

		return ['ID' => $id, 'NAME' => $modelName, 'TABLE_NAME' => $table];
	}


	/**
	 * @param array $config
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function config(array $config)
	{
		if (isset($config['tableName'])) $this->setTableName($config['tableName']);
		return $this;
	}


	/**
	 * @param string $table
	 * @return \marvin255\bxmigrate\IMigrateRepo
	 */
	public function setTableName($table)
	{
		$this->_tableName = trim($table);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTableName()
	{
		return isset($this->_tableName) ? $this->_tableName : 'bx_db_migrations';
	}

	/**
	 * @return string
	 */
	public function getModelName()
	{
		return ucfirst(str_replace(['_'], '', $this->getTableName()));
	}
}