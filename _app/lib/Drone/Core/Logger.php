<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 0.1.8
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone\Core;

/**
 * Message logging class with levels
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class Logger
{
	/**
	 * Log categories
	 */
	const
		CATEGORY_DEFAULT = 'app',
		CATEGORY_DRONE = 'drone';

	/**
	 * Log levels
	 */
	const
		LEVEL_TRACE = 0,
		LEVEL_DEBUG = 1,
		LEVEL_WARN = 2,
		LEVEL_ERROR = 3,
		LEVEL_FATAL = 4;

	/**
	 * Log message data
	 *
	 * @var array
	 */
	private static $__data = [];

	/**
	 * Log message date format
	 *
	 * @var string
	 */
	private static $__date_format = 'Y-m-d H:i:s';

	/**
	 * Log file path
	 *
	 * @var string
	 */
	private static $__file_path;

	/**
	 * Log file path use root path flag
	 *
	 * @var boolean
	 */
	private static $__file_use_root_path = true;

	/**
	 * Custom user defined log handler
	 *
	 * @var callable
	 */
	private static $__handler;

	/**
	 * Current log message ID
	 *
	 * @var int
	 */
	private static $__id = 0;

	/**
	 * Log level
	 *
	 * @var int
	 */
	private static $__level = self::LEVEL_TRACE;

	/**
	 * Log level strings
	 *
	 * @var array
	 */
	private static $__levels = [
		self::LEVEL_TRACE => 'TRACE',
		self::LEVEL_DEBUG => 'DEBUG',
		self::LEVEL_WARN => 'WARNING',
		self::LEVEL_ERROR => 'ERROR',
		self::LEVEL_FATAL => 'FATAL'
	];

	/**
	 * Log
	 *
	 * @var array
	 */
	private static $__log = [];

	/**
	 * Finalize log
	 */
	public function __destruct()
	{
		if(!is_null(self::$__file_path)) // write log sep
		{
			self::__write('');
		}
	}

	/**
	 * Array to string for log message
	 *
	 * @param array $arr
	 * @return string
	 */
	public static function __arrayToString(array $arr)
	{
		$str = '';

		foreach($arr as $k => $v)
		{
			$str .= $k . ':';

			if(is_string($v))
			{
				$str .= $v . ', ';
			}
			else if(is_array($v))
			{
				$str .= '[' . self::__arrayToString($v) . '], ';
			}
		}

		$str = rtrim($str, ', ');

		return $str;
	}

	/**
	 * Format log message
	 *
	 * @param string $message
	 * @param int $level
	 * @param string $category
	 * @return string
	 */
	public static function __formatMessage($message, $level, $category)
	{
		return '[' . date(self::$__date_format) . ' +' . drone()->timer() . '] [' . $category . '] ['
			. self::$__levels[$level] . '] ' . $message
			. ( count(self::$__data) > 0 ? ' (' . self::__arrayToString(self::$__data) . ')' : '' );
	}

	/**
	 * Write message to log file
	 *
	 * @staticvar \Drone\Filesystem\File $file
	 * @param string $message
	 * @return void
	 */
	public static function __write($message)
	{
		static $file = null;

		if(is_null($file))
		{
			$file = new \Drone\Filesystem\File(self::$__file_path, self::$__file_use_root_path);
		}

		if(!$file->write($message . PHP_EOL, true))
		{
			throw new \Exception('Failed to write to log file: \'' . $file->getPath() . '\' ('
				. $file->error . ')');
		}
	}

	/**
	 * Log message data setter
	 *
	 * @param array $data
	 * @return void
	 */
	public function data(array $data)
	{
		self::$__data = array_merge(self::$__data, $data);
	}

	/**
	 * Log debug message
	 *
	 * @param string $log_message
	 * @param string $category
	 * @return void
	 */
	public function debug($log_message, $category = self::CATEGORY_DEFAULT)
	{
		$this->log($log_message, self::LEVEL_DEBUG, $category);
	}

	/**
	 * Log error message
	 *
	 * @param string $log_message
	 * @param string $category
	 * @return void
	 */
	public function error($log_message, $category = self::CATEGORY_DEFAULT)
	{
		$this->log($log_message, self::LEVEL_ERROR, $category);
	}

	/**
	 * Log fatal message
	 *
	 * @param string $log_message
	 * @param string $category
	 * @return void
	 */
	public function fatal($log_message, $category = self::CATEGORY_DEFAULT)
	{
		$this->log($log_message, self::LEVEL_FATAL, $category);
	}

	/**
	 * Log getter, get all categories or specific category
	 *
	 * @param string $category
	 * @return array
	 */
	public function &get($category = null)
	{
		$log = [];
		if(!is_null($category) && isset(self::$__log[$category]))
		{
			$log = self::$__log[$category];
		}
		else
		{
			foreach(self::$__log as $k => $v)
			{
				foreach($v as $k2 => $v2)
				{
					$log[$k2] = $v2;
				}
			}
			ksort($log);
		}

		return $log;
	}

	/**
	 * Log getter as string, get all categories or specific category
	 *
	 * @param string $category
	 * @return string
	 */
	public function getString($category = null, $eol = PHP_EOL)
	{
		return implode($eol, $this->get($category));
	}

	/**
	 * Log message
	 *
	 * @staticvar null $handler
	 * @param string $log_message
	 * @param int $level
	 * @param string $category
	 * @return void
	 */
	public function log($log_message, $level, $category = self::CATEGORY_DEFAULT)
	{
		if($level < self::$__level)
		{
			return; // do not log
		}

		$run_default = true;

		if(is_callable(self::$__handler)) // user defined handler
		{
			static $handler = null;

			if(is_null($handler))
			{
				$handler = &self::$__handler;
			}

			$run_default = !(bool)$handler($log_message, $level, $category, self::$__data);
		}

		if($run_default)
		{
			self::$__log[$category][self::$__id] = self::__formatMessage($log_message, $level, $category);

			if(!is_null(self::$__file_path)) // write to log file
			{
				self::__write(self::$__log[$category][self::$__id]);
			}

			self::$__id++;
		}

		self::$__data = []; // reset log message data
	}

	/**
	 * Log message date format setter
	 *
	 * @param string $date_format (ex: 'Y-m-d H:i:s')
	 * @return void
	 */
	public function setDateFormat($date_format)
	{
		self::$__date_format = $date_format;
	}

	/**
	 * Log file setter (for development environment)
	 *
	 * @param string $path
	 * @param boolean $use_root_path (add root path (PATH_ROOT) to start of path)
	 * @return void
	 */
	public function setLogFile($path, $use_root_path = true)
	{
		self::$__file_path = $path;
		self::$__file_use_root_path = (bool)$use_root_path;
	}

	/**
	 * Custom user defined log handler
	 *
	 * @param callable $handler
	 * @return void
	 */
	public function setLogHandler(callable $handler)
	{
		self::$__handler = $handler;
	}

	/**
	 * Log level setter
	 *
	 * @param int $log_level ()
	 * @return void
	 */
	public function setLogLevel($log_level = self::LEVEL_TRACE)
	{
		self::$__level = $log_level;
	}

	/**
	 * Log trace message
	 *
	 * @param string $log_message
	 * @param string $category
	 * @return void
	 */
	public function trace($log_message, $category = self::CATEGORY_DEFAULT)
	{
		$this->log($log_message, self::LEVEL_TRACE, $category);
	}

	/**
	 * Log warning message
	 *
	 * @param string $log_message
	 * @param string $category
	 * @return void
	 */
	public function warn($log_message, $category = self::CATEGORY_DEFAULT)
	{
		$this->log($log_message, self::LEVEL_WARN, $category);
	}
}