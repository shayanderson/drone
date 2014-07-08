<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 0.1.3
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */

/**
 * Drone core functions
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */

/**
 * Class autoloading
 *
 * @param array $autoload_paths
 * @return void
 */
function autoload(array $autoload_paths)
{
	set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $autoload_paths));

	function __autoload($class)
	{
		require_once str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	}
}

/**
 * Drone Core instance getter
 *
 * @return \Drone\Core
 */
function &drone()
{
	return Drone\Core::getInstance();
}