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

		if(count($route) !== count($request)) // not equal parts
		{
			return false;
		}

		$params = []; // tmp params

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