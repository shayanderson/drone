<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5+
 *
 * @package Drone
 * @version 0.2.3
 * @copyright 2015 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @link <https://github.com/shayanderson/drone>
 */
namespace Drone;

/**
 * Configuration class - config file array to object (multidimensional array to object supported)
 *
 * @author Shay Anderson 02.15 <http://www.shayanderson.com/contact>
 */
class Conf
{
	/**
	 * Array to object (multidimensional array supported)
	 *
	 * @param array $arr
	 * @return \stdClass
	 */
	private static function __arrToObj(&$arr)
	{
		if(is_array($arr))
		{
			return (object)array_map(__METHOD__, $arr);
		}
		else
		{
			return $arr;
		}
	}

	/**
	 * Get file config (must return array) to object
	 *
	 * @param string $file_path (ex: PATH_ROOT . '_app/com/conf.php')
	 * @return \stdClass
	 */
	public static function file($file_path)
	{
		return self::__arrToObj(require $file_path);
	}

	/**
	 * Array to config object (multidimensional array supported)
	 *
	 * @param array $conf
	 * @return \stdClass
	 */
	public static function get(array $conf)
	{
		return self::__arrToObj($conf);
	}
}