<?php
/**
 *    Settings
 *
 * @mail support@local.online
 * @link local.online
 */

namespace Local\Fwink;

use Bitrix\Main,
    Bitrix\Main\DB\Exception,
    Bitrix\Main\Config\Option;

class Settings
{
    const MODULE_ID = 'local.fwink';

	var $options;

	public static function get($name, $serialized=false) {
		$value = false;
		if ($name) {
			$value = Option::get(self::MODULE_ID, $name);
		}
		if ($serialized && $value) {
			$value = unserialize($value);
		}
		return $value;
	}

	public static function hasRecord($name) {
		$value = false;
		if ($name) {
			$value = (Option::getRealValue(self::MODULE_ID, $name) === NULL) ? false : true;
		}
		return $value;
	}

	public static function save($name, $value, $serialized=false) {
		$result = true;
		if ($serialized) {
			$value = serialize($value);
		}
		Option::set(self::MODULE_ID, $name, $value);
		return $result;
	}
}
