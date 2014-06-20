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

use Drone\Core\Logger;

/**
 * Session handler class
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class Session
{
	/**
	 * Init session
	 *
	 * @staticvar boolean $is_init
	 * @return void
	 */
	private static function __init()
	{
		static $is_init = false;

		if(!self::__isSession()) // auto start session
		{
			session_cache_limiter(false);
			session_start();

			if(!$is_init)
			{
				drone()->log->trace('Session started', Logger::CATEGORY_DRONE);
			}

			$is_init = true;
		}
	}

	/**
	 * Session exists flag getter
	 *
	 * @return boolean
	 */
	private static function __isSession()
	{
		return session_status() !== PHP_SESSION_NONE;
	}

	/**
	 * Add array element to session key array
	 *
	 * @param string $key
	 * @param string $array_key
	 * @param mixed $value
	 * @return void
	 */
	public function add($key, $array_key, $value)
	{
		self::__init();

		if(is_array($array_key))
		{
			foreach($array_key as $k => $v)
			{
				$this->add($key, $k, $v);
			}
			return;
		}

		if(!$this->has($key) || !is_array($this->get($key)))
		{
			$_SESSION[$key] = [];
		}

		$_SESSION[$key][$array_key] = $value;
	}

	/**
	 * Clear session key (or session array)
	 *
	 * @param string $key
	 * @param mixed $array_key (string when clearing session array)
	 * @return void
	 */
	public function clear($key, $array_key = null)
	{
		self::__init();

		if(is_array($key))
		{
			foreach($key as $v)
			{
				$this->clear($v);
			}
			return;
		}
		else if(is_array($array_key))
		{
			foreach($array_key as $v)
			{
				$this->clear($key, $v);
			}
			return;
		}

		if(is_null($array_key))
		{
			if($this->has($key))
			{
				unset($_SESSION[$key]);
			}
		}
		else if($this->has($key, $array_key))
		{
			unset($_SESSION[$key][$array_key]);
		}
	}

	/**
	 * Count elements in session array
	 *
	 * @param string $key
	 * @return int (will return 0 if session key is not array)
	 */
	public function count($key)
	{
		if($this->isArray($key))
		{
			return count($this->get($key));
		}

		return 0;
	}

	/**
	 * Destroy session
	 *
	 * @return void
	 */
	public function destroy()
	{
		self::__init();
		$this->flush();

		if(ini_get('session.use_cookies')) // delete session cookie
		{
			$c = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$c['path'], $c['domain'], $c['secure'], $c['httponly']);
		}

		session_destroy();

		// init session + regenerate ID
		$this->newId();
	}

	/**
	 * Flush all session variables
	 *
	 * @return void
	 */
	public function flush()
	{
		self::__init();
		$_SESSION = [];
	}

	/**
	 * Session key value (or array) getter
	 *
	 * @param string $key
	 * @param mixed $array_key (string when getting session array element)
	 * @return mixed
	 */
	public function get($key, $array_key = null)
	{
		self::__init();

		if(is_null($key)) // get all
		{
			return $_SESSION;
		}
		else if(is_array($key))
		{
			$out = [];
			foreach($key as $v)
			{
				$out[$v] = $this->get($v);
			}
			return $out;
		}

		if(is_null($array_key))
		{
			if($this->has($key))
			{
				return $_SESSION[$key];
			}
		}
		else if($this->has($key, $array_key))
		{
			return $_SESSION[$key][$array_key];
		}

		return null;
	}

	/**
	 * Session ID getter
	 *
	 * @return string
	 */
	public function getId()
	{
		self::__init();
		return session_id();
	}

	/**
	 * Session key (or array key) exists
	 *
	 * @param string $key
	 * @param mixed $array_key (string when checking if session array key exists)
	 * @return boolean
	 */
	public function has($key, $array_key = null)
	{
		self::__init();

		if(is_null($array_key))
		{
			return isset($_SESSION[$key]) || array_key_exists($key, $_SESSION);
		}
		else if(isset($_SESSION[$key]) && is_array($_SESSION[$key])) // array key
		{
			return isset($_SESSION[$key][$array_key]) || array_key_exists($array_key, $_SESSION[$key]);
		}

		return false;
	}

	/**
	 * Session key value is array flag getter
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function isArray($key)
	{
		return $this->has($key) && is_array($_SESSION[$key]);
	}

	/**
	 * Session exists flag getter
	 *
	 * @return boolean
	 */
	public function isSession()
	{
		return self::__isSession();
	}

	/**
	 * Regenerate session ID
	 *
	 * @return void
	 */
	public function newId()
	{
		self::__init();
		session_regenerate_id();
	}

	/**
	 * Session key value setter
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value)
	{
		self::__init();

		if(is_array($key))
		{
			foreach($key as $k => $v)
			{
				$this->set($k, $v);
			}
			return;
		}

		$_SESSION[$key] = $value;
	}
}