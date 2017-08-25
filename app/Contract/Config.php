<?php
namespace App\Contract;

use App\Definition\Contract;
use App\Service\Config AS ConfigService;

class Config
{
	use Contract;

	protected static function newInstance()
	{
		return new ConfigService();
	}
	public static function get($name)
	{
		$instance = self::getInstance();
		return $instance->get($name);
	}
	public static function set($name, $value)
	{
		$instance = self::getInstance();
		return $instance->set($name, $value);
	}
}
?>
