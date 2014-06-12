<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 1.0.b - Jun 12, 2014
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone\Core;

/**
 * Drone Route class
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class Route
{
	/**
	 * Mapped route action separator
	 */
	const ACTION_SEPARATOR = '->';

	/**
	 * Route parameter wildcard character
	 */
	const PARAM_WILDCARD_CHARACTER = '*';

	/**
	 * Route action
	 *
	 * @var string
	 */
	private $__action;

	/**
	 * Route controller
	 *
	 * @var string
	 */
	private $__controller;

	/**
	 * Route params
	 *
	 * @var array
	 */
	private $__params = [];

	/**
	 * Route path
	 *
	 * @var string
	 */
	private $__path;

	/**
	 * Init
	 *
	 * @param string $path
	 * @param string $controller
	 */
	public function __construct($path, $controller)
	{
		$this->__path = $path;

		// parse action, ex: 'controller->action'
		if(($pos = strpos($controller, self::ACTION_SEPARATOR)) !== false)
		{
			list($this->__controller, $this->__action) = explode(self::ACTION_SEPARATOR, $controller);
		}
		else
		{
			$this->__controller = $controller;
		}
	}

	/**
	 * Search array values for substring
	 *
	 * @param array $arr
	 * @param string $substr
	 * @return int (the array key, or false on no substring)
	 */
	public static function __arraySearchSubstring(array $arr, $substr)
	{
		foreach($arr as $k => $v)
		{
			if(strpos($v, $substr) !== false)
			{
				return $k;
			}
		}

		return false;
	}

	/**
	 * Route action getter
	 *
	 * @return string
	 */
	public function getAction()
	{
		return $this->__action;
	}

	/**
	 * Route controller getter
	 *
	 * @return string
	 */
	public function getController()
	{
		return $this->__controller;
	}

	/**
	 * Route params getter
	 *
	 * @return array
	 */
	public function &getParams()
	{
		return $this->__params;
	}

	/**
	 * Route has action flag getter
	 *
	 * @return boolean
	 */
	public function isAction()
	{
		return strlen($this->__action) > 0;
	}

	/**
	 * Test if route matches request path
	 *
	 * @param string $request_path
	 * @return boolean
	 */
	public function match($request_path)
	{
		$route = explode('/', $this->__path);
		$request = explode('/', $request_path);

		// test wildcard params + wildcard route key
		$route_key_wildcard = strpos($this->__path, self::PARAM_WILDCARD_CHARACTER) !== false
			? self::__arraySearchSubstring($route, self::PARAM_WILDCARD_CHARACTER) : false;

		if($route_key_wildcard !== false) // wildcard params
		{
			// get wildcard param key, ex: '/:parts*' => 'parts'
			$key_wildcard = str_replace([':', self::PARAM_WILDCARD_CHARACTER], '', $route[$route_key_wildcard]);
			if(strlen($key_wildcard) < 1) // set default wildcard param key
			{
				$key_wildcard = 'params';
			}

			$route = array_slice($route, 0, $route_key_wildcard); // rm wildcard
			$params_wildcard = array_slice($request, $route_key_wildcard); // set wildcard params
			if(substr($request_path, -1) === '/') // last param is empty
			{
				array_pop($params_wildcard);
			}
			$request = array_slice($request, 0, $route_key_wildcard); // rm wildcard param values

			if(count($params_wildcard) < 1)
			{
				// route with wildcard params must end in '/' when using no params (dup content protection)
				// ex: '/route/', not allowed: '/route.htm'
				if(substr($request_path, -1) !== '/')
				{
					return false;
				}
			}
			// route with wildcard params using the params must not end in '/' (dup content protection)
			// ex: '/route/p1/p2.htm', not allowed: '/route/p1/p2/'
			else if(substr($request_path, -1) === '/')
			{
				return false;
			}
		}

		if(count($route) !== count($request)) // not equal parts (+ not wildcard params)
		{
			return false;
		}

		$params = []; // tmp params

		if(isset($params_wildcard))
		{
			$params[$key_wildcard] = $params_wildcard;
		}

		foreach($request as $k => $v)
		{
			if($v !== $route[$k]) // part does not match
			{
				if($route[$k][0] !== ':') // param check
				{
					return false; // not param
				}

				$params[substr($route[$k], 1)] = $v;
			}
		}

		// set perm params
		$this->__params = &$params;

		return true; // all parts match
	}
}