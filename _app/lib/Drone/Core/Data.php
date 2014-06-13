<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 1.0.b
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone\Core;

/**
 * Data handler - filter/santize, format and validate data
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class Data
{
	/**
	 * Filter flags
	 */
	const
		FILTER_ALNUM = 0x1, // strip non-alphanumeric characters
		FILTER_ALPHA = 0x2, // strip non-alpha characters
		FILTER_DATE = 0x4, // strip non-date characters
		FILTER_DATE_TIME = 0x8, // strip non-date/time characters
		FILTER_DECIMAL = 0x10, // strip non-decimal characters
		FILTER_EMAIL = 0x20, // strip non-email characters
		FILTER_HTML_ENCODE = 0x40, // encode HTML special characters
		FILTER_NUMERIC = 0x80, // strip non-numeric characters
		FILTER_SANITIZE = 0x100, // strip tags
		FILTER_TIME = 0x200, // strip non-time characters
		FILTER_TRIM = 0x400, // trim spaces
		FILTER_URL_ENCODE = 0x800, // encode URL
		FILTER_WORD = 0x1000; // strip non-word characters (same as character class '\w')

	/**
	 * Format flags
	 */
	const
		FORMAT_BYTE = 0x1,
		FORMAT_CURRENCY = 0x2,
		FORMAT_DATE = 0x4,
		FORMAT_DATE_TIME = 0x8,
		FORMAT_LOWER = 0x10, // lower case
		FORMAT_TIME = 0x20,
		FORMAT_UPPER = 0x40, // upper case
		FORMAT_UPPER_WORDS = 0x80; // capitalize words

	/**
	 * Param keys
	 */
	const
		PARAM_CASE_INSENSITIVE = 'case', // allow case-insensitive value compare
		PARAM_EXACT = 'exact', // exact value
		PARAM_FORMAT = 'format', // format value for format methods
		PARAM_MIN = 'min', // minimum value
		PARAM_MAX = 'max', // maximum value
		PARAM_PATTERN = 'pattern', // regex pattern for matching
		PARAM_VALUE = 'value', // when second value is required
		PARAM_WHITESPACE = 'whitespace'; // allow whitespaces in value flag

	/**
	 * Validate flags
	 */
	const
		VALIDATE_ALNUM = 0x1, // value is alphanumeric characters
		VALIDATE_ALPHA = 0x2, // value is alpha characters
		VALIDATE_BETWEEN = 0x4, // value between min and max values
		VALIDATE_CONTAINS = 0x8, // value contains value
		VALIDATE_CONTAINS_NOT = 0x10, // value does not contain value
		VALIDATE_DECIMAL = 0x20, // value is decimal
		VALIDATE_EMAIL = 0x40, // value is email
		VALIDATE_IPV4 = 0x80, // value is IPv4 address
		VALIDATE_IPV6 = 0x100, // value is IPv6 address
		VALIDATE_LENGTH = 0x200, // value is min length, or under max length, or between min and max lengths
		VALIDATE_MATCH = 0x400, // value is match to value
		VALIDATE_NUMERIC = 0x800, // value is numeric
		VALIDATE_REGEX = 0x1000, // value is Perl-compatible regex pattern
		VALIDATE_REQUIRED = 0x2000, // value exists (length > 0)
		VALIDATE_URL = 0x4000, // value is URL
		VALIDATE_WORD = 0x8000; // value is word (same as character class '\w')

	/**
	 * Flags to methods map
	 *
	 * @var array
	 */
	static private $__methods = [
		'filter' => [
			self::FILTER_ALNUM => 'filterAlnum',
			self::FILTER_ALPHA => 'filterAlpha',
			self::FILTER_DATE => 'filterDate',
			self::FILTER_DATE_TIME => 'filterDateTime',
			self::FILTER_DECIMAL => 'filterDecimal',
			self::FILTER_EMAIL => 'filterEmail',
			self::FILTER_HTML_ENCODE => 'filterHtmlEncode',
			self::FILTER_NUMERIC => 'filterNumeric',
			self::FILTER_SANITIZE => 'filterSanitize',
			self::FILTER_TIME => 'filterTime',
			self::FILTER_TRIM => 'filterTrim',
			self::FILTER_URL_ENCODE => 'filterUrlEncode',
			self::FILTER_WORD => 'filterWord'
		],
		'format' => [
			self::FORMAT_BYTE => 'formatByte',
			self::FORMAT_CURRENCY => 'formatCurrency',
			self::FORMAT_DATE => 'formatDate',
			self::FORMAT_DATE_TIME => 'formatDateTime',
			self::FORMAT_LOWER => 'formatLower',
			self::FORMAT_TIME => 'formatTime',
			self::FORMAT_UPPER => 'formatUpper',
			self::FORMAT_UPPER_WORDS => 'formatUpperWords'
		],
		'validate' => [
			self::VALIDATE_ALNUM => 'validateAlnum',
			self::VALIDATE_ALPHA => 'validateAlpha',
			self::VALIDATE_BETWEEN => 'validateBetween',
			self::VALIDATE_CONTAINS => 'validateContains',
			self::VALIDATE_CONTAINS_NOT => 'validateContainsNot',
			self::VALIDATE_DECIMAL => 'validateDecimal',
			self::VALIDATE_EMAIL => 'validateEmail',
			self::VALIDATE_IPV4 => 'validateIpv4',
			self::VALIDATE_IPV6 => 'validateIpv6',
			self::VALIDATE_LENGTH => 'validateLength',
			self::VALIDATE_MATCH => 'validateMatch',
			self::VALIDATE_NUMERIC => 'validateNumeric',
			self::VALIDATE_REGEX => 'validateRegex',
			self::VALIDATE_REQUIRED => 'validateRequired',
			self::VALIDATE_URL => 'validateUrl',
			self::VALIDATE_WORD => 'validateWord'
		]
	];

	/**
	 * Default currency format (ex: '$%0.2f')
	 *
	 * @var string (sprintf format: <http://www.php.net/manual/en/function.sprintf.php>)
	 */
	public static $default_format_currency = '$%0.2f';

	/**
	 * Default date format (ex: 'm/d/Y')
	 *
	 * @var string (date/time format: <http://www.php.net/manual/en/function.date.php>)
	 */
	public static $default_format_date = 'm/d/Y';

	/**
	 * Default date/time format (ex: 'm/d/Y H:i:s')
	 *
	 * @var string (date/time format: <http://www.php.net/manual/en/function.date.php>)
	 */
	public static $default_format_date_time = 'm/d/Y H:i:s';

	/**
	 * Default time format (ex: 'H:i:s')
	 *
	 * @var string (date/time format: <http://www.php.net/manual/en/function.date.php>)
	 */
	public static $default_format_time = 'H:i:s';

	/**
	 * Apply callable(s) to value(s)
	 *
	 * @param string $type
	 * @param mixed $value
	 * @param array $args
	 * @return mixed (boolean on validate, mixed on filter + format)
	 */
	private function __apply($type, $value, $args)
	{
		if(is_object($value)) // allow objects, convert to array
		{
			$value = (array)$value;
		}

		if(is_array($value))
		{
			foreach($value as &$v)
			{
				$v = call_user_func_array([$this, '__apply'], [$type, $v, $args]);
			}

			return $value;
		}
		else if(is_array($args))
		{
			if(count($args) < 1) // apply auto calls when no args
			{
				if($type === 'filter') // auto trim
				{
					return self::filterTrim($value);
				}
				else if($type === 'validate') // auto require
				{
					return self::validateRequired($value);
				}
			}

			$calls = [];
			$params = null;

			foreach($args as $arg) // prepare stack
			{
				if(is_int($arg)) // defined
				{
					foreach(self::$__methods[$type] as $k => $m)
					{
						if($arg & $k)
						{
							$calls[] = $m;
						}
					}
				}
				else if(is_array($arg)) // function params
				{
					$params = $arg;
				}
				else if(($arg = self::__register($type, $arg)) !== false) // custom
				{
					$calls[] = $arg;
				}
			}

			foreach($calls as $call) // process stack
			{
				if($type === 'validate') // validation only
				{
					if(!(!is_callable($call) ? self::$call($value, $params) : $call($value, $params)))
					{
						return false; // validation fails
					}
				}
				else
				{
					$value = !is_callable($call) ? self::$call($value, $params) : $call($value, $params);
				}
			}
		}

		return $type === 'validate' ? true : $value; // validation passes if has not failed
	}

	/**
	 * Register callable for custom filter, format or validate
	 *
	 * @staticvar array $callables
	 * @param string $type
	 * @param string $name (callable key)
	 * @param callable $callable
	 * @return mixed (null on register, callable on getter, false on error)
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	private static function __register($type, $name, callable $callable = null)
	{
		static $callables = [];

		if(!is_null($callable))
		{
			if(!is_string($name))
			{
				throw new \InvalidArgumentException(__METHOD__ . ': Registered ' . $type
					. ' name must be string instead of \'' . gettype($name) . '(' . $name . ')\'');
				return false;
			}

			if(!isset($callables[$type]))
			{
				$callables[$type] = [];
			}

			$callables[$type][$name] = $callable;
			return;
		}

		if(isset($callables[$type][$name]))
		{
			return $callables[$type][$name];
		}
		else
		{
			throw new \Exception(__METHOD__ . ': Registered ' . $type . ' not found: \'' . $name . '\'');
			return false;
		}
	}

	/**
	 * Filter value
	 *
	 * @param mixed $value
	 * @param mixed $_ (flags or strings)
	 * @return mixed
	 */
	public function filter($value, $_ = null)
	{
		if(is_callable($_)) // register custom filter
		{
			self::__register('filter', $value, $_);
			return;
		}

		return $this->__apply('filter', $value, array_slice(func_get_args(), 1));
	}

	/**
	 * Strip non-alphanumeric characters
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_WHITESPACE)
	 * @return mixed
	 */
	public static function filterAlnum($value, $params = null)
	{
		return isset($params[self::PARAM_WHITESPACE]) ? preg_replace('/[^a-zA-Z0-9\s]+/', '', $value)
			: preg_replace('/[^a-zA-Z0-9]+/', '', $value);
	}

	/**
	 * Strip non-alpha characters
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_WHITESPACE)
	 * @return mixed
	 */
	public static function filterAlpha($value, $params = null)
	{
		return isset($params[self::PARAM_WHITESPACE]) ? preg_replace('/[^a-zA-Z\s]+/', '', $value)
			: preg_replace('/[^a-zA-Z]+/', '', $value);
	}

	/**
	 * Strip non-date characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function filterDate($value)
	{
		return preg_replace('/[^0-9\-\/]/', '', $value);
	}

	/**
	 * Strip non-date/time characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function filterDateTime($value)
	{
		return preg_replace('/[^0-9\-\/\:\s]/', '', $value);
	}

	/**
	 * Strip non-decimal characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function filterDecimal($value)
	{
		$value = filter_var($value, \FILTER_SANITIZE_NUMBER_FLOAT, \FILTER_FLAG_ALLOW_FRACTION);

		if(substr_count($value, '.') > 1) // multiple '.', only allow one
		{
			$value = substr($value, 0, strpos($value, '.', 2));
		}

		return $value;
	}

	/**
	 * Strip non-email characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function filterEmail($value)
	{
		return filter_var($value, \FILTER_SANITIZE_EMAIL);
	}

	/**
	 * Encode HTML special characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function filterHtmlEncode($value)
	{
		return filter_var($value, \FILTER_SANITIZE_SPECIAL_CHARS);
	}

	/**
	 * Strip non-numeric characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function filterNumeric($value)
	{
		return filter_var($value, \FILTER_SANITIZE_NUMBER_INT);
	}

	/**
	 * Strip tags
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function filterSanitize($value)
	{
		return filter_var($value, \FILTER_SANITIZE_STRING);
	}

	/**
	 * Strip non-time characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function filterTime($value)
	{
		return preg_replace('/[^0-9\:]/', '', $value);
	}

	/**
	 * Trim spaces
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function filterTrim($value)
	{
		return trim($value);
	}

	/**
	 * Encode URL
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function filterUrlEncode($value)
	{
		return filter_var($value, \FILTER_SANITIZE_ENCODED);
	}

	/**
	 * Strip non-word characters (same as character class '\w')
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_WHITESPACE)
	 * @return mixed
	 */
	public static function filterWord($value, $params = null)
	{
		return isset($params[self::PARAM_WHITESPACE]) ? preg_replace('/[^\w\s]/', '', $value)
			: preg_replace('/[^\w]/', '', $value);
	}

	/**
	 * Format value
	 *
	 * @param mixed $value
	 * @param mixed $_ (flags or strings)
	 * @return mixed
	 */
	public function format($value, $_ = null)
	{
		if(is_callable($_)) // register custom filter
		{
			self::__register('format', $value, $_);
			return;
		}

		return $this->__apply('format', $value, array_slice(func_get_args(), 1));
	}

	/**
	 * Format byte (ex: 2000 => '1.95 kb')
	 *
	 * @param int $value
	 * @return string (or false on invalid value)
	 */
	public static function formatByte($value)
	{
		$value = (float)$value;

		if($value <= 0)
		{
			return '0 b';
		}

		return round($value / pow(1024, ( $k = floor(log($value, 1024)) )), 2) . ' '
			. ['b', 'kb', 'mb', 'gb', 'tb', 'pb'][$k];
	}

	/**
	 * Format currency
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_FORMAT)
	 * @return mixed
	 */
	public static function formatCurrency($value, $params = null)
	{
		return sprintf(isset($params[self::PARAM_FORMAT]) ? $params[self::PARAM_FORMAT]
			: self::$default_format_currency, $value);
	}

	/**
	 * Format date
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_FORMAT)
	 * @return mixed
	 */
	public static function formatDate($value, $params = null)
	{
		return date(isset($params[self::PARAM_FORMAT]) ? $params[self::PARAM_FORMAT]
			: self::$default_format_date, strtotime($value));
	}

	/**
	 * Format date/time
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_FORMAT)
	 * @return mixed
	 */
	public static function formatDateTime($value, $params = null)
	{
		return date(isset($params[self::PARAM_FORMAT]) ? $params[self::PARAM_FORMAT]
			: self::$default_format_date_time, strtotime($value));
	}

	/**
	 * Format to lower case
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function formatLower($value)
	{
		return strtolower($value);
	}

	/**
	 * Format time
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_FORMAT)
	 * @return mixed
	 */
	public static function formatTime($value, $params = null)
	{
		return date(isset($params[self::PARAM_FORMAT]) ? $params[self::PARAM_FORMAT]
			: self::$default_format_time, strtotime($value));
	}

	/**
	 * Format to upper case
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function formatUpper($value)
	{
		return strtoupper($value);
	}

	/**
	 * Format capitalize words
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public static function formatUpperWords($value)
	{
		return ucwords($value);
	}

	/**
	 * Validate value
	 *
	 * @param mixed $value
	 * @param mixed $_ (flags or strings)
	 * @return boolean
	 */
	public function validate($value, $_ = null)
	{
		if(is_callable($_)) // register custom filter
		{
			self::__register('validate', $value, $_);
			return;
		}

		return $this->__apply('validate', $value, array_slice(func_get_args(), 1));
	}

	/**
	 * Validate value is alphanumeric characters
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_WHITESPACE)
	 * @return boolean
	 */
	public static function validateAlnum($value, $params = null)
	{
		return isset($params[self::PARAM_WHITESPACE]) ? preg_match('/^[a-zA-Z0-9\s]+$/', $value)
			: ctype_alnum($value);
	}

	/**
	 * Validate value is alpha characters
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_WHITESPACE)
	 * @return boolean
	 */
	public static function validateAlpha($value, $params = null)
	{
		return isset($params[self::PARAM_WHITESPACE]) ? preg_match('/^[a-zA-Z\s]+$/', $value)
			: ctype_alpha($value);
	}

	/**
	 * Validate value between min and max values
	 *
	 * @param mixed $value
	 * @param mixed $params (PARAM_MIN, PARAM_MAX)
	 * @return boolean
	 * @throws \Exception
	 */
	public static function validateBetween($value, $params)
	{
		if(!isset($params[self::PARAM_MIN]) || !isset($params[self::PARAM_MAX]))
		{
			throw new \Exception(__METHOD__ . ': Method \'validateBetween()\' requires array params: \''
				. self::PARAM_MIN . '\' and \'' . self::PARAM_MAX . '\'');
			return false;
		}

		return $value > $params[self::PARAM_MIN] && $value < $params[self::PARAM_MAX];
	}

	/**
	 * Validate value contains value
	 *
	 * @param mixed $value
	 * @param mixed $params (PARAM_VALUE, optional: PARAM_CASE_INSENSITIVE)
	 * @return boolean
	 * @throws \Exception
	 */
	public static function validateContains($value, $params)
	{
		if(!isset($params[self::PARAM_VALUE]))
		{
			throw new \Exception(__METHOD__ . ': Method \'validateContains()\' requires array param: \''
				. self::PARAM_VALUE . '\'');
			return false;
		}

		return isset($params[self::PARAM_CASE_INSENSITIVE]) ? stripos($value, $params[self::PARAM_VALUE]) !== false
			: strpos($value, $params[self::PARAM_VALUE]) !== false;
	}

	/**
	 * Validate value does not contain value
	 *
	 * @param mixed $value
	 * @param mixed $params (PARAM_VALUE, optional: PARAM_CASE_INSENSITIVE)
	 * @return boolean
	 * @throws \Exception
	 */
	public static function validateContainsNot($value, $params)
	{
		if(!isset($params[self::PARAM_VALUE]))
		{
			throw new \Exception(__METHOD__ . ': Method \'validateContainsNot()\' requires array param: \''
				. self::PARAM_VALUE . '\'');
			return false;
		}

		return isset($params[self::PARAM_CASE_INSENSITIVE]) ? stripos($value, $params[self::PARAM_VALUE]) === false
			: strpos($value, $params[self::PARAM_VALUE]) === false;
	}

	/**
	 * Validate value is decimal
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public static function validateDecimal($value)
	{
		if(preg_match('/^[0-9\.]+$/', $value))
		{
			return substr_count($value, '.') <= 1;
		}

		return false;
	}

	/**
	 * Validate value is email
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public static function validateEmail($value)
	{
		return filter_var($value, \FILTER_VALIDATE_EMAIL) !== false;
	}

	/**
	 * Validate value is IPv4 address
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public static function validateIpv4($value)
	{
		return filter_var($value, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4) !== false;
	}

	/**
	 * Validate value is IPv6 address
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public static function validateIpv6($value)
	{
		return filter_var($value, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6) !== false;
	}

	/**
	 * Validate value is min length, or under max length, or between min and max lengths
	 *
	 * @param mixed $value
	 * @param mixed $params (PARAM_MIN, PARAM_MAX, PARAM_EXACT)
	 * @return boolean
	 * @throws \Exception
	 */
	public static function validateLength($value, $params)
	{
		if(isset($params[self::PARAM_MIN]) && isset($params[self::PARAM_MAX]))
		{
			return strlen($value) >= (int)$params[self::PARAM_MIN]
				&& strlen($value) <= (int)$params[self::PARAM_MAX];
		}
		else if(isset($params[self::PARAM_MIN]))
		{
			return strlen($value) >= (int)$params[self::PARAM_MIN];
		}
		else if(isset($params[self::PARAM_MAX]))
		{
			return strlen($value) <= (int)$params[self::PARAM_MAX];
		}
		else if(isset($params[self::PARAM_EXACT]))
		{
			return strlen($value) === (int)$params[self::PARAM_EXACT];
		}
		else
		{
			throw new \Exception(__METHOD__ . ': Method \'validateLength()\' requires array params: \''
				. self::PARAM_MIN . '\' and/or \'' . self::PARAM_MAX . '\', or \'' . self::PARAM_EXACT . '\'');
			return false;
		}
	}

	/**
	 * Validate value is match to value
	 *
	 * @param mixed $value
	 * @param mixed $params (PARAM_VALUE, optional: PARAM_CASE_INSENSITIVE, PARAM_PATTERN)
	 * @return boolean
	 * @throws \Exception
	 */
	public static function validateMatch($value, $params)
	{
		if(isset($params[self::PARAM_PATTERN])) // test regex pattern
		{
			if(!self::validateRegex($params[self::PARAM_PATTERN]))
			{
				throw new \Exception(__METHOD__ . ': Method \'validateMatch()\' param: \'' . self::PARAM_PATTERN
					. '\' is not a valid pattern (tested with method \'validateRegex()\')');
				return false;
			}

			return preg_match($params[self::PARAM_PATTERN], $value);
		}

		if(!isset($params[self::PARAM_VALUE]))
		{
			throw new \Exception(__METHOD__ . ': Method \'validateMatch()\' requires array param: \''
				. self::PARAM_VALUE . '\'');
			return false;
		}

		return isset($params[self::PARAM_CASE_INSENSITIVE]) ? strcasecmp($value, $params[self::PARAM_VALUE]) === 0
			: strcmp($value, $params[self::PARAM_VALUE]) === 0;
	}

	/**
	 * Validate value is numeric
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public static function validateNumeric($value)
	{
		return preg_match('/^[0-9]+$/', $value);
	}

	/**
	 * Validate value is Perl-compatible regex pattern
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public static function validateRegex($value)
	{
		return @preg_match($value, '') !== false;
	}

	/**
	 * Validate value exists (length(trim(value)) > 0)
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public static function validateRequired($value)
	{
		return strlen(trim($value)) > 0;
	}

	/**
	 * Validate value is URL
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public static function validateUrl($value)
	{
		return filter_var($value, \FILTER_VALIDATE_URL) !== false;
	}

	/**
	 * Validate value is word (same as character class '\w')
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_WHITESPACE)
	 * @return boolean
	 */
	public static function validateWord($value, $params = null)
	{
		return isset($params[self::PARAM_WHITESPACE]) ? preg_match('/^[\w\s]+$/', $value)
			: preg_match('/^[\w]+$/', $value);
	}
}