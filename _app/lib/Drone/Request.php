<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5+
 *
 * @package Drone
 * @version 0.1.9
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone;

/**
 * Request data handler
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class Request
{
	/**
	 * Request types
	 */
	const
		TYPE_COOKIE = 0x1,
		TYPE_FILE = 0x2,
		TYPE_GET = 0x4,
		TYPE_POST = 0x8,
		TYPE_REQUEST = 0x10;

	/**
	 * Request type callable filters
	 *
	 * @var array
	 */
	private static $__filters = [];

	/**
	 * Request value getter
	 *
	 * @param array $arr
	 * @param string $key
	 * @param mixed $default
	 * @param mixed $type
	 * @return array
	 */
	private static function __fetch(array $arr, $key, $default = null, $type = null)
	{
		if(is_null($key)) // get all
		{
			return is_int($type) && isset(self::$__filters[$type]) // apply filter
				? array_map(self::$__filters[$type], $arr) : $arr;
		}
		else if(is_array($key))
		{
			$ret = [];

			foreach($key as $k)
			{
				$ret[$k] = self::__fetch($arr, $k, $default, $type);
			}

			return $ret;
		}

		if(self::__has($arr, $key))
		{
			if(is_int($type) && isset(self::$__filters[$type])) // apply filter check
			{
				return call_user_func(self::$__filters[$type], $arr[$key]); // filter value
			}

			return $arr[$key];
		}

		return $default;
	}

	/**
	 * Request key exists flag getter
	 *
	 * @param array $arr
	 * @param mixed $key (string|array)
	 * @return boolean
	 */
	private static function __has(array $arr, $key)
	{
		if(is_array($key)) // check multiple
		{
			foreach($key as $v)
			{
				if(!self::__has($arr, $v))
				{
					return false;
				}
			}
			return true;
		}

		return isset($arr[$key]) || array_key_exists($key, $arr);
	}

	/**
	 * Request cookie ($_COOKIE) value getter
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function cookie($key, $default = null)
	{
		return self::__fetch($_COOKIE, $key, $default);
	}

	/**
	 * Request environment ($_ENV) value getter
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function env($key, $default = null)
	{
		return self::__fetch($_ENV, $key, $default);
	}

	/**
	 * Request file ($_FILES) value getter
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function file($key, $default = null)
	{
		return self::__fetch($_FILES, $key, $default);
	}

	/**
	 * Callable filter setter for request type
	 *
	 * @param int $types
	 * @param callable $filter
	 * @return void
	 */
	public function filter($types, callable $filter)
	{
		if($types & self::TYPE_COOKIE)
		{
			self::$__filters[self::TYPE_COOKIE] = $filter;
		}
		if($types & self::TYPE_FILE)
		{
			self::$__filters[self::TYPE_FILE] = $filter;
		}
		if($types & self::TYPE_GET)
		{
			self::$__filters[self::TYPE_GET] = $filter;
		}
		if($types & self::TYPE_POST)
		{
			self::$__filters[self::TYPE_POST] = $filter;
		}
		if($types & self::TYPE_REQUEST)
		{
			self::$__filters[self::TYPE_REQUEST] = $filter;
		}
	}

	/**
	 * Request get ($_GET) value getter
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		return self::__fetch($_GET, $key, $default, self::TYPE_GET);
	}

	/**
	 * Request host getter
	 *
	 * @return string
	 */
	public function getHost()
	{
		return !is_null($this->server('HTTP_HOST')) ? $this->server('HTTP_HOST') : $this->server('SERVER_NAME');
	}

	/**
	 * Request IP address getter
	 *
	 * @return string
	 */
	public function getIpAddress()
	{
		return $this->server('REMOTE_ADDR');
	}

	/**
	 * Request method getter
	 *
	 * @return string
	 */
	public function getMethod()
	{
		return strtoupper($this->server('REQUEST_METHOD'));
	}

	/**
	 * Request port getter
	 *
	 * @return string
	 */
	public function getPort()
	{
		return $this->server('SERVER_PORT');
	}

	/**
	 * Request protocol getter
	 *
	 * @return string
	 */
	public function getProtocol()
	{
		return $this->server('SERVER_PROTOCOL');
	}

	/**
	 * Request query string getter
	 *
	 * @return string
	 */
	public function getQueryString()
	{
		$uri = $this->getUri();

		if(($pos = strpos($uri, '?')) !== false) // check for query string
		{
			return substr($uri, $pos + 1);
		}
	}

	/**
	 * Request referrer getter
	 *
	 * @return string
	 */
	public function getReferrer()
	{
		return $this->server('HTTP_REFERRER');
	}

	/**
	 * Request schema getter
	 *
	 * @return string
	 */
	public function getSchema()
	{
		return $this->isSecure() ? 'https' : 'http';
	}

	/**
	 * Request URI getter
	 *
	 * @param boolean $query_string
	 * @return string
	 */
	public function getUri($query_string = true)
	{
		if($query_string)
		{
			return $this->server('REQUEST_URI');
		}
		else
		{
			$uri = $this->getUri();

			if(($pos = strpos($uri, '?')) !== false) // rm query string
			{
				$uri = substr($uri, 0, $pos);
			}

			return $uri;
		}
	}

	/**
	 * Request cookie ($_COOKIE) key exists flag getter
	 *
	 * @param mixed $key (string|array)
	 * @return boolean
	 */
	public function hasCookie($key)
	{
		return self::__has($_COOKIE, $key);
	}

	/**
	 * Request file ($_FILES) key exists flag getter
	 *
	 * @param mixed $key (string|array)
	 * @return boolean
	 */
	public function hasFile($key)
	{
		return self::__has($_FILES, $key);
	}

	/**
	 * Request get ($_GET) key exists flag getter
	 *
	 * @param mixed $key (string|array)
	 * @return boolean
	 */
	public function hasGet($key)
	{
		return self::__has($_GET, $key);
	}

	/**
	 * Request post ($_POST) key exists flag getter
	 *
	 * @param mixed $key (string|array)
	 * @return boolean
	 */
	public function hasPost($key)
	{
		return self::__has($_POST, $key);
	}

	/**
	 * Request ($_REQUEST) key exists flag getter
	 *
	 * @param mixed $key (string|array)
	 * @return boolean
	 */
	public function hasRequest($key)
	{
		return self::__has($_REQUEST, $key);
	}

	/**
	 * Ajax request flag getter
	 *
	 * @return boolean
	 */
	public function isAjax()
	{
		return strtoupper($this->server('HTTP_X_REQUESTED_WITH')) === 'XMLHTTPREQUEST';
	}

	/**
	 * Post request method flag getter
	 *
	 * @return boolean
	 */
	public function isPost()
	{
		return strtoupper($this->server('REQUEST_METHOD')) === 'POST';
	}

	/**
	 * Secure request flag getter
	 *
	 * @return boolean
	 */
	public function isSecure()
	{
		return strtoupper($this->server('HTTPS')) === 'ON';
	}

	/**
	 * Request post ($_POST) value getter
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function post($key, $default = null)
	{
		return self::__fetch($_POST, $key, $default);
	}

	/**
	 * Remove cookie ($_COOKIE) key/value pair
	 *
	 * @param string $key
	 * @param string $path (required if path used to set cookie, ex: '/account')
	 * @return boolean (true on actual cookie set expired)
	 */
	public function removeCookie($key, $path = '/')
	{
		if($this->hasCookie($key))
		{
			unset($_COOKIE[$key]);
			return $this->setCookie($key, null, time() - 3600, $path); // expire cookie to remove
		}

		return false;
	}

	/**
	 * Remove get ($_GET) key/value pair
	 *
	 * @param string $key
	 * @return void
	 */
	public function removeGet($key)
	{
		if($this->hasGet($key))
		{
			unset($_GET[$key]);
		}
	}

	/**
	 * Remove post ($_POST) key/value pair
	 *
	 * @param string $key
	 * @return void
	 */
	public function removePost($key)
	{
		if($this->hasPost($key))
		{
			unset($_POST[$key]);
		}
	}

	/**
	 * Remove request ($_REQUEST) key/value pair
	 *
	 * @param string $key
	 * @return void
	 */
	public function removeRequest($key)
	{
		if($this->hasRequest($key))
		{
			unset($_REQUEST[$key]);
		}
	}

	/**
	 * Request ($_REQUEST) value getter
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function request($key, $default = null)
	{
		return self::__fetch($_REQUEST, $key, $default);
	}

	/**
	 * Request server ($_SERVER) value getter
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function server($key, $default = null)
	{
		return self::__fetch($_SERVER, $key, $default);
	}

	/**
	 * Cookie sender (see more docs at <http://www.php.net/manual/en/function.setcookie.php>)
	 *
	 * @param string $name (ex: 'my_id')
	 * @param mixed $value (cookie value)
	 * @param mixed $expire (string ex: '+30 days', int ex: time() + 3600 (expire in 1 hour))
	 * @param string $path (optional, ex: '/account' (only accessible in /account directory + subdirectories))
	 * @param string $domain (optional, ex: 'www.example.com' (accessible in www subdomain + higher))
	 * @param boolean $only_secure (transmit cookie only over HTTPS connection)
	 * @param boolean $http_only (accessible only in HTTP protocol)
	 * @return boolean (false on fail, true on send but cannot tell if client accepted cookie)
	 */
	function setCookie($name, $value, $expire = '+1 day', $path = '/', $domain = null, $only_secure = false,
		$http_only = false)
	{
		if(headers_sent())
		{
			return false;
		}

		return setcookie($name, $value, is_string($expire) ? strtotime($expire) : $expire, $path, $domain,
			$only_secure, $http_only);
	}
}