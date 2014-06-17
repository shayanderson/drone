<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 1.0.b
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */

/**
 * Drone helper functions
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */

/**
 * Clear/unset param key/value pair (\Drone\Core->clear() alias)
 *
 * @param string $key
 * @return void
 */
function clear($key)
{
	drone()->clear($key);
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
 * Filter value (\Drone\Core->data->filter() alias)
 *
 * @param mixed $value
 * @param mixed $_ (flags or strings)
 * @return mixed
 */
function filter($value, $_ = null)
{
	return call_user_func_array([drone()->data, 'filter'], func_get_args());
}

/**
 * Flash message getter/setter (or \Drone\Core\Flash instance getter (drone()->flash alias))
 *
 * @param mixed $key
 * @param mixed $value
 * @return \Drone\Core\Flash (or mixed on getter/setter)
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
 * Format value (\Drone\Core->data->format() alias)
 *
 * @param mixed $value
 * @param mixed $_ (flags or strings)
 * @return mixed
 */
function format($value, $_ = null)
{
	return call_user_func_array([drone()->data, 'format'], func_get_args());
}

/**
 * Param value getter (\Drone\Core->get() alias)
 *
 * @param string $key
 * @return mixed
 */
function get($key)
{
	return drone()->get($key);
}

/**
 * Param exists flag getter (\Drone\Core->has() alias)
 *
 * @param string $key
 * @return boolean
 */
function has($key)
{
	return drone()->has($key);
}

/**
 * Load common file
 *
 * @param string $file (ex: 'my_common_file')
 * @param boolean $load_once (only load file once)
 * @param string $ext (file extension)
 * @return void
 */
function load_com($file, $load_once = true, $ext = '.php')
{
	if(substr($file, -4) !== $ext)
	{
		$file .= $ext;
	}

	$file = PATH_ROOT . '_app' . DIRECTORY_SEPARATOR . 'com' . DIRECTORY_SEPARATOR . $file;

	$load_once ? require_once $file : require $file;
}

/**
 * \Drone\Core\Logger instance getter (drone()->log alias)
 *
 * @return \Drone\Core\Logger
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
	echo is_scalar($v) ? $v . ( PHP_SAPI === 'cli' ? PHP_EOL : '<br />' )
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
 * \Drone\Core\Request instance getter (drone()->request alias)
 *
 * @return \Drone\Core\Request
 */
function request()
{
	return drone()->request;
}

/**
 * \Drone\Core\Session instance getter (drone()->session alias)
 *
 * @return \Drone\Core\Session
 */
function session()
{
	return drone()->session;
}

/**
 * Param value setter (\Drone\Core->set() alias)
 *
 * @param array|string $key (array ex: ['k1' => 'v1', ...])
 * @param mixed $value
 * @return void
 */
function set($key, $value = null)
{
	drone()->set($key, $value);
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
 * Validate value (\Drone\Core->data->validate() alias)
 *
 * @param mixed $value
 * @param mixed $_ (flags or strings)
 * @return boolean
 */
function validate($value, $_ = null)
{
	return call_user_func_array([drone()->data, 'validate'], func_get_args());
}

/**
 * \Drone\Core\View instance getter (drone()->view alias)
 *
 * @return \Drone\Core\View
 */
function view()
{
	return drone()->view;
}