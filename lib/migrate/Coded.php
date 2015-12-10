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
}