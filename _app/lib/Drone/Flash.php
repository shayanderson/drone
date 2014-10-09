<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5+
 *
 * @package Drone
 * @version 0.2.0
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone;

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
	 * Keys for templates
	 */
	const
		KEY_TEMPLATE_GROUP = 0,
		KEY_TEMPLATE_MESSAGE = 1;

	/**
	 * Template message placeholder
	 */
	const TEMPLATE_MESSAGE_PLACEHOLDER = '{$message}';

	/**
	 * Session object
	 *
	 * @var \Drone\Session
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

		if($this->has($key) && is_array($_SESSION[self::KEY_SESSION][$key]))
		{
			foreach($this->__session->get(self::KEY_SESSION, $key) as $k => $v)
			{
				if(isset(self::$__templates[$key][self::KEY_TEMPLATE_MESSAGE])
					&& !empty(self::$__templates[$key][self::KEY_TEMPLATE_MESSAGE])) // apply message template
				{
					$v = str_replace(self::TEMPLATE_MESSAGE_PLACEHOLDER, $v,
						self::$__templates[$key][self::KEY_TEMPLATE_MESSAGE]);
				}

				$out .= $v;
			}

			if(isset(self::$__templates[$key][self::KEY_TEMPLATE_GROUP])
				&& !empty(self::$__templates[$key][self::KEY_TEMPLATE_GROUP])) // apply group template
			{
				$out = str_replace(self::TEMPLATE_MESSAGE_PLACEHOLDER, $out,
					self::$__templates[$key][self::KEY_TEMPLATE_GROUP]);
			}

			$this->clear($key); // flush message(s)
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

		if(!$this->has($key))
		{
			$this->__session->add(self::KEY_SESSION, $key, []);
		}

		$_SESSION[self::KEY_SESSION][$key][] = $value;
	}

	/**
	 * Flash message template setter
	 *
	 * @param string $group (ex: 'error')
	 * @param string $group_template (ex: '<div class="error">{$message}</div>')
	 * @param string $message_template (optional, put multiple messages in group template, ex: '{$message}<br />')
	 * @return void
	 */
	public static function template($group, $group_template, $message_template = null)
	{
		self::$__templates[$group] = [self::KEY_TEMPLATE_GROUP => $group_template,
			self::KEY_TEMPLATE_MESSAGE => $message_template];
	}
}