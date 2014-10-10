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
 * Data handler - filter/santize, format and validate data
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class Data
{
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
	 * Strip non-alphanumeric characters
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_WHITESPACE)
	 * @return mixed
	 */
	public function filterAlnum($value, $params = null)
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
	public function filterAlpha($value, $params = null)
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
	public function filterDate($value)
	{
		return preg_replace('/[^0-9\-\/]/', '', $value);
	}

	/**
	 * Strip non-date/time characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function filterDateTime($value)
	{
		return preg_replace('/[^0-9\-\/\:\s]/', '', $value);
	}

	/**
	 * Strip non-decimal characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function filterDecimal($value)
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
	public function filterEmail($value)
	{
		return filter_var($value, \FILTER_SANITIZE_EMAIL);
	}

	/**
	 * Encode HTML special characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function filterHtmlEncode($value)
	{
		return filter_var($value, \FILTER_SANITIZE_SPECIAL_CHARS);
	}

	/**
	 * Strip non-numeric characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function filterNumeric($value)
	{
		return filter_var($value, \FILTER_SANITIZE_NUMBER_INT);
	}

	/**
	 * Strip tags
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function filterSanitize($value)
	{
		return filter_var($value, \FILTER_SANITIZE_STRING);
	}

	/**
	 * Strip non-time characters
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function filterTime($value)
	{
		return preg_replace('/[^0-9\:]/', '', $value);
	}

	/**
	 * Trim spaces
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function filterTrim($value)
	{
		return trim($value);
	}

	/**
	 * Encode URL
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function filterUrlEncode($value)
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
	public function filterWord($value, $params = null)
	{
		return isset($params[self::PARAM_WHITESPACE]) ? preg_replace('/[^\w\s]/', '', $value)
			: preg_replace('/[^\w]/', '', $value);
	}

	/**
	 * Format byte (ex: 2000 => '1.95 kb')
	 *
	 * @param int $value
	 * @param mixed $params
	 * @param array $characters (ex: [' b', ' kb', ' mb', ' gb', ' tb', ' pb'])
	 * @return string (or false on invalid value)
	 */
	public function formatByte($value, $params = null,
		array $characters = [' b', ' kb', ' mb', ' gb', ' tb', ' pb'])
	{
		if(count($characters) !== 6)
		{
			return 'Invalid format characters (must be 6 characters)';
		}

		$value = (float)$value;

		if($value <= 0)
		{
			return '0' . $characters[0];
		}

		return round($value / pow(1024, ( $k = floor(log($value, 1024)) )), 2) . $characters[$k];
	}

	/**
	 * Format currency
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_FORMAT)
	 * @return mixed
	 */
	public function formatCurrency($value, $params = null)
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
	public function formatDate($value, $params = null)
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
	public function formatDateTime($value, $params = null)
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
	public function formatLower($value)
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
	public function formatTime($value, $params = null)
	{
		return date(isset($params[self::PARAM_FORMAT]) ? $params[self::PARAM_FORMAT]
			: self::$default_format_time, strtotime($value));
	}

	/**
	 * Format time elapsed
	 *
	 * @param float $time_elapsed (ex: microtime(true) - $start)
	 * @param mixed $params
	 * @param array $characters (ex: ['y', 'w', 'd', 'h', 'm', 's'])
	 * @return string (ex: '1h 35m 55s')
	 */
	public function formatTimeElapsed($time_elapsed, $params = null,
		array $characters = ['y', 'w', 'd', 'h', 'm', 's'])
	{
		if(count($characters) !== 6)
		{
			return 'Invalid format characters (must be 6 characters)';
		}

		$b = [
			$characters[0] => $time_elapsed / 31556926 % 12,
			$characters[1] => $time_elapsed / 604800 % 52,
			$characters[2] => $time_elapsed / 86400 % 7,
			$characters[3] => $time_elapsed / 3600 % 24,
			$characters[4] => $time_elapsed / 60 % 60,
			$characters[5] => $time_elapsed % 60,
		];

		$out = [];
		foreach($b as $k => $v)
		{
			if($v > 0)
			{
				$out[] = $v . $k;
			}
		}

		return implode(' ', $out);
	}

	/**
	 * Format to upper case
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function formatUpper($value)
	{
		return strtoupper($value);
	}

	/**
	 * Format capitalize words
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function formatUpperWords($value)
	{
		return ucwords($value);
	}

	/**
	 * Validate value is alphanumeric characters
	 *
	 * @param mixed $value
	 * @param mixed $params (optional: PARAM_WHITESPACE)
	 * @return boolean
	 */
	public function validateAlnum($value, $params = null)
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
	public function validateAlpha($value, $params = null)
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
	public function validateBetween($value, $params)
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
	public function validateContains($value, $params)
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
	public function validateContainsNot($value, $params)
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
	public function validateDecimal($value)
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
	public function validateEmail($value)
	{
		return filter_var($value, \FILTER_VALIDATE_EMAIL) !== false;
	}

	/**
	 * Validate value is IPv4 address
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function validateIpv4($value)
	{
		return filter_var($value, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4) !== false;
	}

	/**
	 * Validate value is IPv6 address
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function validateIpv6($value)
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
	public function validateLength($value, $params)
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
	public function validateMatch($value, $params)
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
	public function validateNumeric($value)
	{
		return preg_match('/^[0-9]+$/', $value);
	}

	/**
	 * Validate value is Perl-compatible regex pattern
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function validateRegex($value)
	{
		return @preg_match($value, '') !== false;
	}

	/**
	 * Validate value exists (length(trim(value)) > 0)
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function validateRequired($value)
	{
		return strlen(trim($value)) > 0;
	}

	/**
	 * Validate value is URL
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	public function validateUrl($value)
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
	public function validateWord($value, $params = null)
	{
		return isset($params[self::PARAM_WHITESPACE]) ? preg_match('/^[\w\s]+$/', $value)
			: preg_match('/^[\w]+$/', $value);
	}
}