<?php

namespace marvin255\bxmigrate\migrate;

use Bitrix\Main\Loader;

Loader::includeModule('iblock');
Loader::includeModule('highloadblock');

abstract class Coded implements \marvin255\bxmigrate\IMigrate
{
	/**
	 * @return mixed
	 */
	public function getName()
	{
		return get_class($this);
	}



	/**
	 * @param array $params
	 */
	protected function IblockSectionCreate(array $params)
	{
		$bs = new \CIBlockSection;
		$res = $bs->Add($params);
		if (!$res) {
			throw new \Exception($bs->LAST_ERROR);
		} else {
			return $res;
		}
	}



	/**
	 * @param string $entity
	 * @param array $data
	 * @var bool $deleteIfExists
	 */
	protected function UFCreate(array $data, $deleteIfExists = false)
	{
		global $USER_FIELD_MANAGER;
		if (empty($data['FIELD_NAME'])) throw new \Exception('You must set group FIELD_NAME');
		if (empty($data['ENTITY_ID'])) throw new \Exception('You must set group ENTITY_ID');
		$fire = false;
		if ($this->UFGetIdByCode($data['ENTITY_ID'], $data['FIELD_NAME'])) {
			if ($deleteIfExists) {
				$this->UFDelete($data['ENTITY_ID'], $data['FIELD_NAME']);
				$fire = true;
			}
		} else {
			$fire = true;
		}
		if ($fire) {
			$ib = new \CUserTypeEntity;
			$id = $ib->Add(array_merge([
				'USER_TYPE_ID' => 'string',
			], $data));
			if ($id) {
				echo "Add {$data['FIELD_NAME']} user field\r\n";
				if (
					!empty($data['LIST'])
					&& ($arType = $USER_FIELD_MANAGER->GetUserType($data['USER_TYPE_ID']))
					&& $arType['BASE_TYPE'] == 'enum'
				){
					$obEnum = new \CUserFieldEnum;
					$res = $obEnum->SetEnumValues($id, $data['LIST']);
					echo "Add {$data['FIELD_NAME']} user field list\r\n";
				}
			} else {
				throw new \Exception("Can't create {$data['FIELD_NAME']} user field");
			}
		}
	}

	/**
	 * @param string $entity
	 * @param array $data
	 * @var bool $deleteIfExists
	 */
	protected function UFUpdate(array $data)
	{
		global $USER_FIELD_MANAGER;
		if (empty($data['FIELD_NAME'])) throw new \Exception('You must set group FIELD_NAME');
		if (empty($data['ENTITY_ID'])) throw new \Exception('You must set group ENTITY_ID');
		$fire = false;
		if ($id = $this->UFGetIdByCode($data['ENTITY_ID'], $data['FIELD_NAME'])) {
			$ib = new \CUserTypeEntity;
			$id = $ib->Update($id, $data);
			if ($id) {
				echo "Update {$data['FIELD_NAME']} user field\r\n";
				if (
					!empty($data['LIST'])
					&& ($arType = $USER_FIELD_MANAGER->GetUserType($data['USER_TYPE_ID']))
					&& $arType['BASE_TYPE'] == 'enum'
				){
					$obEnum = new \CUserFieldEnum;
					$res = $obEnum->SetEnumValues($id, $data['LIST']);
					echo "Update {$data['FIELD_NAME']} user field list\r\n";
				}
			}
		} else {
			throw new \Exception("Can't update {$data['FIELD_NAME']} user field");
		}
	}

	/**
	 * @var string $entity
	 * @var string $code
	 */
	protected function UFDelete($entity, $code)
	{
		$id = $this->UFGetIdByCode($entity, $code);
		if ($id) {
			global $DB;
			$group = new \CUserTypeEntity;
			$DB->StartTransaction();
			if (!$group->Delete($id)) {
				$DB->Rollback();
				$error = "Can't delete user field {$code}";
			}
			$DB->Commit();
			echo "Delete user field {$code}\r\n";
			if (isset($error)) throw new \Exception($error);
		}
	}

	/**
	 * @var string $entity
	 * @var string $code
	 */
	protected function UFGetIdByCode($entity, $code)
	{
		$rsData = \CUserTypeEntity::GetList([], [
			'ENTITY_ID' => $entity,
			'FIELD_NAME' => $code,
		]);
		if ($ob = $rsData->GetNext()) {
			return $ob['ID'];
		} else {
			return null;
		}
	}



	/**
	 * @var array $data
	 * @var bool $deleteIfExists
	 */
	protected function UserGroupCreate(array $data, $deleteIfExists = false)
	{
		if (empty($data['STRING_ID'])) throw new \Exception('You must set group STRING_ID');
		$fire = false;
		if ($this->UserGetGroupIdByCode($data['STRING_ID'])) {
			if ($deleteIfExists) {
				$this->UserGroupDelete($data['STRING_ID']);
				$fire = true;
			}
		} else {
			$fire = true;
		}
		if ($fire) {
			$ib = new \CGroup;
			$id = $ib->Add(array_merge([
				'ACTIVE' => 'Y',
			], $data));
			if ($id) {
				echo "Add {$data['STRING_ID']} users group\r\n";
			} else {
				throw new \Exception("Can't create {$data['STRING_ID']} users group");
			}
		}
	}

	/**
	 * @var string $group
	 */
	protected function UserGroupDelete($groupName)
	{
		$id = $this->UserGetGroupIdByCode($groupName);
		if ($id) {
			global $DB;
			$group = new \CGroup;
			$DB->StartTransaction();
			if (!$group->Delete($id)) {
				$DB->Rollback();
				$error = "Can't delete group {$groupName}";
			}
			$DB->Commit();
			echo "Delete group {$groupName}";
			if (isset($error)) throw new \Exception($error);
		}
	}

	/**
	 * @var string $param
	 * @return int
	 */
	protected function UserGetGroupIdByCode($code)
	{
		$rsGroups = \CGroup::GetList(($by = 'c_sort'), ($order = 'desc'), [
			'STRING_ID' => $code,
		]);
		if ($ob = $rsGroups->Fetch()) {
			return $ob['ID'];
		} else {
			return null;
		}
	}



	/**
	 * @var string $iblock
	 * @var array $data
	 * @var bool $deleteIfExists
	 */
	protected function IblockPropertyCreate($iblock, array $data, $deleteIfExists = false)
	{
		$ibId = $this->IblockGetIdByCode($iblock);

		if (empty($data['CODE'])) throw new \Exception('You must set property CODE');

		if ($ibId) {
			$fire = false;
			$res = \CIBlockProperty::GetList([], [
				'CODE' => $data['CODE'],
				'IBLOCK_ID' => $ibId,
				'CHECK_PERMISSIONS' => 'N',
			]);
			if ($ob = $res->Fetch()) {
				if ($deleteIfExists) {
					$this->IblockPropertyDelete($iblock, $data['CODE']);
					$fire = true;
				}
			} else {
				$fire = true;
			}
			if ($fire) {
				$ib = new \CIBlockProperty;
				$id = $ib->Add(array_merge([
					'IBLOCK_ID' => $ibId,
					'CODE' => $data['CODE'],
					'XML_ID' => $data['CODE'],
					'ACTIVE' => 'Y',
				], $data));
				if ($id) {
					echo "Add {$data['CODE']} iblock property\r\n";
				} else {
					throw new \Exception("Can't create {$data['CODE']} iblock property");
				}
			}
		} else {
			throw new \Exception("Can't find iblock {$iblock}");
		}
	}

	/**
	 * @var string $iblock
	 * @var array $data
	 * @var bool $deleteIfExists
	 */
	protected function IblockPropertyUpdate($iblock, array $data)
	{
		$ibId = $this->IblockGetIdByCode($iblock);

		if (empty($data['CODE'])) throw new \Exception('You must set property CODE');

		if ($ibId) {
			$fire = false;
			$res = \CIBlockProperty::GetList([], [
				'CODE' => $data['CODE'],
				'IBLOCK_ID' => $ibId,
				'CHECK_PERMISSIONS' => 'N',
			]);
			if ($ob = $res->Fetch()) {
				$ib = new \CIBlockProperty;
				$id = $ib->Update($ob['ID'], $data);
				if ($id) {
					echo "Update {$data['CODE']} iblock property\r\n";
				} else {
					throw new \Exception("Can't update {$data['CODE']} iblock property");
				}
			} else {
				throw new \Exception("Can't find {$data['CODE']} iblock property");
			}
		} else {
			throw new \Exception("Can't find iblock {$iblock}");
		}
	}

	/**
	 * @param string $iblock
	 * @param string $code
	 */
	protected function IblockPropertyDelete($iblock, $code)
	{
		$ibId = $this->IblockGetIdByCode($iblock);
		if (!$ibId) throw new \Exception("Can't find iblock {$iblock}");
		$res = \CIBlockProperty::GetList([], [
			'CODE' => $code,
			'IBLOCK_ID' => $ibId,
			'CHECK_PERMISSIONS' => 'N',
		]);
		if ($ob = $res->Fetch()) {
			if (!\CIBlockProperty::Delete($ob['ID'])) {
				throw new \Exception("Can't delete iblock property {$code}");
			}
		}
	}



	/**
	 * @var array $data
	 * @var array $fields
	 * @var bool $deleteIfExists
	 */
	protected function IblockCreate(array $data, array $fields = null, $deleteIfExists = false)
	{
		global $DB;

		if (empty(trim($data['CODE']))) throw new \Exception('You must set iblock CODE');
		$name = trim($data['CODE']);

		$fire = false;
		$res = \CIBlock::GetList([], [
			'CODE' => $name,
			'CHECK_PERMISSIONS' => 'N',
		]);
		if ($ob = $res->Fetch()) {
			if ($deleteIfExists) {
				$this->IblockDelete($name);
				$fire = true;
			} else {
				$id = $ob['ID'];
			}
		} else {
			$fire = true;
		}

		if ($fire) {
			$rsSite = \CSite::GetList($by = 'sort', $order = 'desc', ['DEFAULT' => 'Y']);
			$sites = [];
			while ($obSite = $rsSite->Fetch()) {
				$sites[] = $obSite['LID'];
			}
			$ib = new \CIBlock;
			$id = $ib->Add(array_merge([
				'ACTIVE' => 'Y',
				'CODE' => $name,
				'XML_ID' => $name,
				'LID' => $sites[0],
				'LIST_PAGE_URL' => '',
				'DETAIL_PAGE_URL' => '',
				'SECTION_PAGE_URL' => '',
				'CANONICAL_PAGE_URL' => '',
				'SORT' => 500,
				'INDEX_ELEMENT' => 'N',
				'INDEX_SECTION' => 'N',
			], $data));
			if ($id) {
				echo "Add {$name} iblock\r\n";
				if ($id && $fields) $this->IblockSetFields($name, $fields);
			} else {
				throw new \Exception("Can't create {$name} iblock type");
			}
		}

		return $id;
	}

	/**
	 * @var array $data
	 * @var array $fields
	 * @var bool $deleteIfExists
	 */
	protected function IblockUpdate(array $data, array $fields = null)
	{
		global $DB;

		if (empty(trim($data['CODE']))) throw new \Exception('You must set iblock CODE');
		$name = trim($data['CODE']);

		$fire = false;
		$res = \CIBlock::GetList([], [
			'CODE' => $name,
			'CHECK_PERMISSIONS' => 'N',
		]);
		if ($ob = $res->Fetch()) {
			$ib = new \CIBlock;
			$id = $ib->Update($ob['ID'], $data);
			if ($id) {
				echo "Update {$name} iblock\r\n";
				if ($id && $fields) $this->IblockSetFields($name, $fields);
			} else {
				throw new \Exception("Can't create {$name} iblock type");
			}
		} else {
			throw new \Exception("Iblock don't exists");
		}

		return $id;
	}

	/**
	 * @param string $code
	 * @param array $fields
	 */
	protected function IblockSetFields($code, array $fields)
	{
		$id = $this->IblockGetIdByCode($code);
		if ($id) {
			$old_fields = \CIBlock::getFields($id);
			$fields = array_merge($old_fields, $fields);
			\CIBlock::setFields($id, $fields);
		} else {
			throw new \Exception("Can't set fields for {$code} iblock");
		}
	}

	/**
	 * @var string $code
	 */
	protected function IblockDelete($name)
	{
		$name = trim($name);
		if (empty($name)) throw new \Exception('You must set iblock CODE');
		global $DB;
		$res = \CIBlock::GetList([], [
			'CODE' => $name,
			'CHECK_PERMISSIONS' => 'N',
		]);
		if ($ob = $res->Fetch()) {
			$DB->StartTransaction();
			if (!\CIBlock::Delete($ob['ID'])) {
				$DB->Rollback();
				$error = "Can't delete {$name} iblock";
			} else {
				echo "Delete {$name} iblock\r\n";
			}
			$DB->Commit();
			if (isset($error)) throw new \Exception($error);
		}
	}

	/**
	 * @var string $param
	 * @return int
	 */
	protected function IblockGetIdByCode($code)
	{
		$res = \CIBlock::GetList([], [
			'CODE' => $code,
			'CHECK_PERMISSIONS' => 'N',
		]);
		if ($ob = $res->Fetch()) {
			return $ob['ID'];
		} else {
			return null;
		}
	}



	/**
	 * @param array $data
	 * @param bool $deleteIfExists
	 */
	protected function IblockTypeCreate(array $data, $deleteIfExists = false)
	{
		global $DB;

		if (empty(trim($data['ID']))) throw new \Exception('You must set iblock type ID');
		$name = trim($data['ID']);

		$fire = false;
		$res = \CIBlockType::GetByID($name);
		if ($ob = $res->Fetch()) {
			if ($deleteIfExists) {
				$this->IblockTypeDelete($name);
				$fire = true;
			}
		} else {
			$fire = true;
		}

		if ($fire) {
			$obBlocktype = new \CIBlockType;
			$DB->StartTransaction();
			$res = $obBlocktype->Add(array_merge([
				'ID' => $name,
				'SECTIONS' => 'Y',
				'IN_RSS' => 'N',
				'SORT' => 500,
			], $data));
			if (!$res) {
				$DB->Rollback();
				$error = "Can't create {$name} iblock type";
			} else {
				echo "Add {$name} iblock type\r\n";
			}
			$DB->Commit();
			if (isset($error)) throw new \Exception($error);
		}

		return $name;
	}

	/**
	 * @param string $name
	 */
	protected function IblockTypeUpdate($data)
	{
		global $DB;

		if (empty(trim($data['ID']))) throw new \Exception('You must set iblock type ID');
		$name = trim($data['ID']);

		$res = \CIBlockType::GetList([], [
			'=ID' => $name,
		]);
		if ($ob = $res->Fetch()) {
			$ib = new \CIBlockType;
			$DB->StartTransaction();
			$id = $ib->Update($ob['ID'], $data);
			if ($id) {
				$DB->Commit();
				echo "Update {$name} iblock type\r\n";
			} else {
				$DB->Rollback();
				throw new \Exception("Can't create {$name} iblock type");
			}
		} else {
			throw new \Exception("Iblock type don't exists");
		}

		return $id;
	}

	/**
	 * @param string $name
	 * @param bool $deleteIfExists
	 */
	protected function IblockTypeDelete($name)
	{
		$name = trim($name);
		if (empty($name)) throw new \Exception('You must set iblock CODE');
		global $DB;
		$DB->StartTransaction();
		if (!\CIBlockType::Delete($name)) {
			$DB->Rollback();
			$error = "Can't delete {$name} iblock type";
		} else {
			echo "Delete {$name} iblock type\r\n";
		}
		$DB->Commit();
		if (isset($error)) throw new \Exception($error);
	}
}
