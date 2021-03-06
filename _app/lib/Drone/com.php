<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 0.2.3
 * @copyright 2015 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @link <https://github.com/shayanderson/drone>
 */

/**
 * Drone common functions
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
 * \Drone\Data instance getter (drone()->data alias)
 *
 * @return \Drone\Data
 */
function data()
{
	return drone()->data;
}

/**
 * Drone Core instance getter
 *
 * @return \Drone\Core
 */
function &drone()
{
	return \Drone\Core::getInstance();
}

/**
 * Trigger error or register error handler for error code (\Drone\Core->error() alias)
 *
 * @param mixed $code
 * @param mixed $message (optional)
 * @return void
 */
function error($code = null, $message = null)
{
	drone()->error($code, $message);
}

/**
 * Last error message getter (\Drone\Core->errorLast() alias)
 *
 * @return string (or null on no last error message)
 */
function error_last()
{
	return drone()->errorLast();
}

/**
 * Flash message getter/setter (or \Drone\Flash instance getter (drone()->flash alias))
 *
 * @param mixed $key
 * @param mixed $value
 * @return \Drone\Flash (or mixed on getter/setter)
 */
function flash($key = null, $value = null)
{
	if(!is_null($key)) // getter/setter
	{
		if(!is_null($value)) // setter
		{
			drone()->flash->set($key, $value);
			return;
		}

		return drone()->flash->get($key); // getter
	}
	return drone()->flash;
}

/**
 * \Drone\Logger instance getter (drone()->log alias)
 *
 * @return \Drone\Logger
 */
function logger()
{
	return drone()->log;
}

/**
 * String/array printer for debugging
 *
 * @var mixed $v
 * @return void
 */
function pa($v = null)
{
	if(count(func_get_args()) > 1)
	{
		foreach(func_get_args() as $arg) pa($arg);
		return;
	}
	echo is_scalar($v) || $v === null ? $v . ( PHP_SAPI === 'cli' ? PHP_EOL : '<br />' )
		: ( PHP_SAPI === 'cli' ? print_r($v, true) : '<pre>' . print_r($v, true) . '</pre>' );
}

/**
 * Route param getter (\Drone\Core->view->param() alias)
 *
 * @param mixed $key (string for getter, null for get all)
 * @param mixed $_ (optional, for getting multiple)
 * @return mixed (false on param does not exist, array on multiple get)
 */
function param($key, $_ = null)
{
	if($_ !== null)
	{
		return drone()->view->param(func_get_args());
	}

	return drone()->view->param($key);
}

/**
 * Force redirect to URL (\Drone\Core->redirect() alias)
 *
 * @param string $location
 * @param boolean $use_301 (permanent 301 flag)
 * @return void
 */
function redirect($location, $use_301 = false)
{
	drone()->redirect($location, $use_301);
}

/**
 * \Drone\Request instance getter (drone()->request alias)
 *
 * @return \Drone\Request
 */
function request()
{
	return drone()->request;
}

/**
 * \Drone\Session instance getter (drone()->session alias)
 *
 * @return \Drone\Session
 */
function session()
{
	return drone()->session;
}

/**
 * Template to formatted path getter (drone()->view->template() alias)
 *
 * @param string $template (ex: 'my_template')
 * @return string (ex: '/var/www/proj/.../my_template.tpl')
 */
function template($template)
{
	return drone()->view->template($template);
}

/**
 * Template to formatted path getter using global template path (drone()->view->templateGlobal() alias)
 *
 * @param string $template (ex: 'my_template')
 * @return string (ex: '/var/www/proj/.../_global/my_template.tpl')
 */
function template_global($template)
{
	return drone()->view->templateGlobal($template);
}

/**
 * \Drone\View instance getter (drone()->view alias)
 *
 * @return \Drone\View
 */
function view()
{
	return drone()->view;
}