<?php

namespace marvin255\bxmigrate\migrate;

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