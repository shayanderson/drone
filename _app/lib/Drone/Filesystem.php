<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 1.0.0.b
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone;

/**
 * Abstract Filesystem class
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
abstract class Filesystem
{
	/**
	 * Asset path
	 *
	 * @var string
	 */
	protected $_path;

	/**
	 * Last error message when error occurs
	 *
	 * @var string (or false when no error)
	 */
	public $error = false;

	/**
	 * Init
	 *
	 * @param string $path
	 * @param boolean $use_root_path (add root path (PATH_ROOT) to start of path)
	 */
	public function __construct($path, $use_root_path = true)
	{
		$this->_path = ( $use_root_path ? PATH_ROOT : '' ) . $path;
	}

	/**
	 * Last occurred error getter
	 *
	 * @return string
	 */
	protected static function _getLastError()
	{
		$err = error_get_last();

		return isset($err['message']) ? $err['message'] : 'Unknown error has occurred';
	}

	/**
	 * Create asset
	 *
	 * @parm int $chmod
	 * @return boolean
	 */
	abstract public function create($chmod = 0644);

	/**
	 * Asset exists flag getter
	 *
	 * @return boolean
	 */
	abstract public function exists();

	/**
	 * Format directory path, ex: '/my/path' => '/my/path/'
	 *
	 * @param string $path
	 * @return string
	 */
	public static function formatDirectory($path)
	{
		return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Asset path getter
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->_path;
	}

	/**
	 * Read asset contents
	 *
	 * @return mixed
	 */
	abstract public function read();

	/**
	 * Remove asset
	 *
	 * @return boolean
	 */
	abstract public function remove();

	/**
	 * Asset is writable flag getter
	 *
	 * @return boolean
	 */
	public function writable()
	{
		return is_writable($this->_path);
	}
}