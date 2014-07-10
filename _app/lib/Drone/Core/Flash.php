<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 0.1.3
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone\Core;

/**
 * Session flash messages class
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class Flash
{
	/**
	 * Key for session data
	 */
	const KEY_SESSION = '__DRONE__.flash';

	/**
	 * Template message placeholder
	 */
	const TEMPLATE_MESSAGE_PLACEHOLDER = '{$message}';

	/**
	 * Session object
	 *
	 * @var \Drone\Core\Session
	 */
	private $__session;

	/**
	 * Group templates
	 *
	 * @var array
	 */
	private static $__templates = [];

	/**
	 * Init
	 */
	public function __construct()
	{
		$this->__session = &drone()->session;
	}

	/**
	 * Cleanup
	 */
	public function __destruct() // clear flash keys
	{
		if($this->__session->has(self::KEY_SESSION) && $this->__session->count(self::KEY_SESSION) < 1)
		{
			$this->__session->clear(self::KEY_SESSION); // cleanup empty flash array
		}
	}

	/**
	 * Clear flash key
	 *
	 * @param string $key
	 * @return void
	 */
	public function clear($key)
	{
		if(is_array($key))
		{
			foreach($key as $v)
			{
				$this->clear($v);
			}
			return;
		}

		$this->__session->clear(self::KEY_SESSION, $key);
	}

	/**
	 * Flush flash
	 *
	 * @param string $key
	 * @return void
	 */
	public function flush($key)
	{
		$this->__session->clear(self::KEY_SESSION);
	}

	/**
	 * Flash key value getter
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		$out = '';

		// check for group template
		if(($pos = strpos($key, '.')) !== false && isset(self::$__templates[($group = substr($key, 0, $pos))]))
		{
			if(substr($key, $pos + 1, 1) === '*') // fetch all group messages + apply template
			{
				if($this->__session->has(self::KEY_SESSION))
				{
					foreach($this->__session->get(self::KEY_SESSION) as $k => $v)
					{
						if(substr($k, 0, $pos + 1) === $group . '.')
						{
							$out .= str_replace(self::TEMPLATE_MESSAGE_PLACEHOLDER,
								$this->__session->get(self::KEY_SESSION, $k), self::$__templates[$group]);
							$this->clear($k);
						}
					}
				}
			}
			else if($this->has($key)) // apply group template
			{
				$out = str_replace(self::TEMPLATE_MESSAGE_PLACEHOLDER,
					$this->__session->get(self::KEY_SESSION, $key), self::$__templates[$group]);
				$this->clear($key); // flush message
			}
		}
		else if($this->has($key))
		{
			$out = $this->__session->get(self::KEY_SESSION, $key);
			$this->clear($key); // flush message
		}

		return $out;
	}

	/**
	 * Flash key exists flag getter
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function has($key)
	{
		return $this->__session->has(self::KEY_SESSION, $key);
	}

	/**
	 * Flash key value setter
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value)
	{
		if(empty($key)) // require key
		{
			return;
		}

		$this->__session->add(self::KEY_SESSION, $key, $value);
	}

	/**
	 * Flash message template setter
	 *
	 * @param string $group (ex: 'error', or array for multiple load ex: ['error' => x, 'alert' => y])
	 * @param string $template (ex: '<div class="error">{$message}</div>')
	 * @return void
	 */
	public static function template($group, $template)
	{
		if(is_array($group)) // multiple load
		{
			foreach($group as $k => $v)
			{
				self::template($k, $v);
			}
			return;
		}
		self::$__templates[$group] = $template;
	}
}