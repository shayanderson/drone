<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 1.0.0.b
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
	 * Route parameter special characters
	 */
	const
		PARAM_OPTIONAL_CHARACTER = '?',
		PARAM_WILDCARD_CHARACTER = '*';

	/**
	 * Route action
	 *
	 * @var string
	 */
	private $__action;

	/**
	 * Route callable (called before controller load)
	 *
	 * @var mixed
	 */
	private $__callable;

	/**
	 * Route controller class name
	 *
	 * @var string
	 */
	private $__class = '\Controller';

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
	 * @param mixed $controller (string or array for controller with callable)
	 * @param mixed $callable
	 */
	public function __construct($path, $controller, $callable = null)
	{
		$this->__path = $path;

		if(is_array($controller))
		{
			$callable = $controller[1];
			$controller = $controller[0];
		}

		// parse action, ex: 'controller->action'
		if(($pos = strpos($controller, self::ACTION_SEPARATOR)) !== false)
		{
			$parts = explode(self::ACTION_SEPARATOR, $controller);

			if(count($parts) === 3) // class name
			{
				list($this->__controller, $this->__class, $this->__action) = $parts;
			}
			else
			{
				list($this->__controller, $this->__action) = $parts;
			}
		}
		else
		{
			$this->__controller = $controller;
		}

		$this->__callable = $callable;
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
	 * Route callable getter
	 *
	 * @return callable (or null on no callable)
	 */
	public function getCallable()
	{
		return $this->__callable;
	}

	/**
	 * Route controller class name getter
	 *
	 * @return string
	 */
	public function getClass()
	{
		return $this->__class;
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
	 * Route path getter
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->__path;
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
	 * Route has callable flag getter
	 *
	 * @return boolean
	 */
	public function isCallable()
	{
		return is_callable($this->__callable);
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
		$is_protect = false; // dup content protection flag

		if(substr($request_path, -1) === '/') // rm last empty element if request like '/route/'
		{
			array_pop($request);
		}

		// test for wildcard params, ex: '/route/*'
		if(($pos = strpos($this->__path, self::PARAM_WILDCARD_CHARACTER)) !== false)
		{
			$is_protect = true;

			// set wildcard location (array key)
			$key = self::__arraySearchSubstring($route, self::PARAM_WILDCARD_CHARACTER);

			if($key > 0)
			{
				$params = $labels = [];

				// test for wildcard param labels, ex: '/route/*(:param1/:param2)'
				if(substr($this->__path, $pos + 1, 1) === '(' && substr($this->__path, -1) === ')')
				{
					$labels = explode('/', substr(rtrim($this->__path, ')'), $pos + 2));
				}

				foreach(array_slice($request, $key) as $k => $v)
				{
					$params[$k] = $v;

					if(isset($labels[$k]))
					{
						$params[str_replace(':', '', $labels[$k])] = $v;
					}
				}

				$route = array_slice($route, 0, $key); // rm wildcard params from route
				$request = array_slice($request, 0, $key); // rm wildcard param values from request
				unset($labels); // cleanup
			}
		}
		// test for optional params, ex: '/route/:param1/:param2?)'
		else if(strpos($this->__path, self::PARAM_OPTIONAL_CHARACTER) !== false)
		{
			$is_protect = true;

			if(count($route) !== count($request)) // rm optional params from route to match request
			{
				foreach($route as $k => $v)
				{
					if(!isset($request[$k]) && strpos($v, self::PARAM_OPTIONAL_CHARACTER) !== false)
					{
						unset($route[$k]);
					}
				}
			}

			// rm optional character
			$route = array_map(function($v) { return str_replace(self::PARAM_OPTIONAL_CHARACTER, '', $v); },
				$route);
		}

		if(count($route) !== count($request)) // not equal parts (+ not wildcard params)
		{
			return false;
		}

		if(!isset($params))
		{
			$params = []; // route params
		}
		else
		{
			$params = array_map(function($v) { return urldecode($v); }, $params); // filter
		}

		foreach($request as $k => $v)
		{
			if($v !== $route[$k]) // part does not match
			{
				if($route[$k][0] !== ':') // param check
				{
					return false; // not param
				}

				$params[substr($route[$k], 1)] = urldecode($v);
			}
		}

		// dup content protection
		if($is_protect)
		{
			if(count($params) < 1)
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

		// set route params
		$this->__params = &$params;

		return true; // all parts match
	}
}