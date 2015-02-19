<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5+
 *
 * @package Drone
 * @version 0.2.2
 * @copyright 2015 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @link <https://github.com/shayanderson/drone>
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
	 * @param boolean $allow_whitespaces
	 * @return mixed
	 */
	public function filterAlnum($value, $allow_whitespaces = false)
	{
		return $allow_whitespaces ? preg_replace('/[^a-zA-Z0-9\s]+/', '', $value)
			: preg_replace('/[^a-zA-Z0-9]+/', '', $value);
	}

	/**
	 * Strip non-alpha characters
	 *
	 * @param mixed $value
	 * @param boolean $allow_whitespaces
	 * @return mixed
	 */
	public function filterAlpha($value, $allow_whitespaces = false)
	{
		return $allow_whitespaces ? preg_replace('/[^a-zA-Z\s]+/', '', $value)
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
	 * @param boolean $allow_whitespaces
	 * @return mixed
	 */
	public function filterWord($value, $allow_whitespaces = false)
	{
		return $allow_whitespaces ? preg_replace('/[^\w\s]/', '', $value)
			: preg_replace('/[^\w]/', '', $value);
	}

	/**
	* URL safe base64 decode value
	*
	* @param string $value
	* @return string
	*/
	public function formatBase64UrlDecode($value)
	{
		return base64_decode(str_pad(strtr($value, '-_', '+/'), strlen($value) % 4, '=', STR_PAD_RIGHT));
	}

	/**
	 * URL safe base64 encode value
	 *
	 * @param string $value
	 * @return string
	 */
	public function formatBase64UrlEncode($value)
	{
		return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
	}

	/**
	 * Format byte (ex: 2000 => '1.95 kb')
	 *
	 * @param int $value
	 * @param array $characters (ex: [' b', ' kb', ' mb', ' gb', ' tb', ' pb'])
	 * @return string (or false on invalid value)
	 */
	public function formatByte($value, array $characters = [' b', ' kb', ' mb', ' gb', ' tb', ' pb'])
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
	 * @param mixed $format
	 * @return mixed
	 */
	public function formatCurrency($value, $format = null)
	{
		return sprintf($format !== null ? $format : self::$default_format_currency, $value);
	}

	/**
	 * Format date
	 *
	 * @param mixed $value
	 * @param mixed $format
	 * @return mixed
	 */
	public function formatDate($value, $format = null)
	{
		return date($format !== null ? $format : self::$default_format_date, strtotime($value));
	}

	/**
	 * Format date/time
	 *
	 * @param mixed $value
	 * @param mixed $format
	 * @return mixed
	 */
	public function formatDateTime($value, $format = null)
	{
		return date($format !== null ? $format : self::$default_format_date_time, strtotime($value));
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
	 * @param mixed $format
	 * @return mixed
	 */
	public function formatTime($value, $format = null)
	{
		return date($format !== null ? $format : self::$default_format_time, strtotime($value));
	}

	/**
	 * Format time elapsed
	 *
	 * @param float $time_elapsed (ex: microtime(true) - $start)
	 * @param array $characters (ex: ['y', 'w', 'd', 'h', 'm', 's'])
	 * @return string (ex: '1h 35m 55s')
	 */
	public function formatTimeElapsed($time_elapsed, array $characters = ['y', 'w', 'd', 'h', 'm', 's'])
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
	 * @param boolean $allow_whitespaces
	 * @return boolean
	 */
	public function validateAlnum($value, $allow_whitespaces = false)
	{
		return $allow_whitespaces ? preg_match('/^[a-zA-Z0-9\s]+$/', $value) : ctype_alnum($value);
	}

	/**
	 * Validate value is alpha characters
	 *
	 * @param mixed $value
	 * @param boolean $allow_whitespaces
	 * @return boolean
	 */
	public function validateAlpha($value, $allow_whitespaces = false)
	{
		return $allow_whitespaces ? preg_match('/^[a-zA-Z\s]+$/', $value) : ctype_alpha($value);
	}

	/**
	 * Validate value between min and max values
	 *
	 * @param mixed $value
	 * @param int min
	 * @param int max
	 * @return boolean
	 */
	public function validateBetween($value, $min, $max)
	{
		return $value > $min && $value < $max;
	}

	/**
	 * Validate value contains value
	 *
	 * @param mixed $value
	 * @param mixed $contain_value
	 * @param boolean $is_case_insensitive
	 * @return boolean
	 */
	public function validateContains($value, $contain_value, $is_case_insensitive = false)
	{
		return $is_case_insensitive ? stripos($value, $contain_value) !== false
			: strpos($value, $contain_value) !== false;
	}

	/**
	 * Validate value does not contain value
	 *
	 * @param mixed $value
	 * @param mixed $contain_not_value
	 * @param boolean $is_case_insensitive
	 * @return boolean
	 */
	public function validateContainsNot($value, $contain_not_value, $is_case_insensitive = false)
	{
		return $is_case_insensitive ? stripos($value, $contain_not_value) === false
			: strpos($value, $contain_not_value) === false;
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
	 * Validate value is min length, or under max length, or between min and max lengths, or exact length
	 *
	 * @param mixed $value
	 * @param int $min
	 * @param int $max
	 * @param int $exact
	 * @return boolean
	 */
	public function validateLength($value, $min = 0, $max = 0, $exact = 0)
	{
		$min = (int)$min;
		$max = (int)$max;
		$exact = (int)$exact;

		if($min && $max)
		{
			return strlen($value) >= $min && strlen($value) <= $max;
		}
		else if($min)
		{
			return strlen($value) >= $min;
		}
		else if($max)
		{
			return strlen($value) <= $max;
		}
		else if($exact)
		{
			return strlen($value) === $exact;
		}

		return false;
	}

	/**
	 * Validate value is match to value
	 *
	 * @param mixed $value
	 * @param mixed $compare_value
	 * @param boolean $is_case_insensitive
	 * @return boolean
	 */
	public function validateMatch($value, $compare_value, $is_case_insensitive = false)
	{
		return $is_case_insensitive ? strcasecmp($value, $compare_value) === 0
			: strcmp($value, $compare_value) === 0;
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
	 * @param string $pattern
	 * @return boolean
	 */
	public function validateRegex($value, $pattern)
	{
		return preg_match($pattern, $value) === 1;
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
	 * @param boolean $allow_whitespaces
	 * @return boolean
	 */
	public function validateWord($value, $allow_whitespaces = false)
	{
		return $allow_whitespaces ? preg_match('/^[\w\s]+$/', $value) : preg_match('/^[\w]+$/', $value);
	}
}