<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5+
 *
 * @package Drone
 * @version 0.2.2
 * @copyright 2015 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @link <https://github.com/shayanderson/drone>
 */
namespace Drone;

/**
 * Registry class - handle global variables and objects
 *
 * @author Shay Anderson 02.15 <http://www.shayanderson.com/contact>
 */
class Registry
{
	/**
	 * Registry
	 *
	 * @var array
	 */
	private static $__reg = [];

	/**
	 * Clear/unset key
	 *
	 * @param string $key
	 * @return void
	 */
	public function clear($key)
	{
		if(self::has($key))
		{
			unset(self::$__reg[$key]);
			Core::getInstance()->log->trace('Registry unset key \'' . $key . '\'', Logger::CATEGORY_DRONE);
		}
	}

	/**
	 * Value getter (reference)
	 *
	 * @param string $key
	 * @return mixed (null if key does not exist)
	 */
	public function &get($key)
	{
		if(self::has($key))
		{
			return self::$__reg[$key];
		}
		else
		{
			$val = null;
			return $val;
		}
	}

	/**
	 * Registry array getter
	 *
	 * @return array
	 */
	public function getAll()
	{
		return self::$__reg;
	}

	/**
	 * Key exists flag getter
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function has($key)
	{
		return isset(self::$__reg[$key]) || array_key_exists($key, self::$__reg);
	}

	/**
	 * Key value setter
	 *
	 * @param mixed $key (string|array, array ex: ['k1' => 'v1', ...])
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value = null)
	{
		if(!is_array($key))
		{
			if(!self::has($key)) // log initial setter
			{
				Core::getInstance()->log->trace('Registry set key \'' . $key . '\'', Logger::CATEGORY_DRONE);
			}

			self::$__reg[$key] = $value;
		}
		else // set using array
		{
			foreach($key as $k => $v)
			{
				self::set($k, $v);
			}
		}
	}
}