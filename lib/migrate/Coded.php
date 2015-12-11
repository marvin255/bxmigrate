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
	 * @var string $code
	 */
	protected function IblockCreate(array $data, $deleteIfExists = true)
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
			} else {
				throw new \Exception("Can't create {$name} iblock type");
			}
		}

		return $id;
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
				throw new \Exception("Can't delete {$name} iblock");
			} else {
				$DB->Commit();
				echo "Delete {$name} iblock\r\n";
			}
		}
	}



	/**
	 * @param array $data
	 * @param bool $deleteIfExists
	 */
	protected function IblockTypeCreate(array $data, $deleteIfExists = true)
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
				throw new \Exception("Can't create {$name} iblock type");
			} else {
				$DB->Commit();
				echo "Add {$name} iblock type\r\n";
			}
		}

		return $name;
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
			throw new \Exception("Can't delete {$name} iblock type");
		} else {
			echo "Delete {$name} iblock type\r\n";
			$DB->Commit();
		}
	}
}