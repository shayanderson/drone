<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 0.1.4
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone\Filesystem;

/**
 * Filesystem File handler class
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class File extends \Drone\Filesystem
{
	/**
	 * Directory object
	 *
	 * @var \Drone\Filesystem\Directory
	 */
	private $__dir;

	/**
	 * Directory object getter (lazy loader)
	 *
	 * @return \Drone\Filesystem\Directory
	 */
	private function &__getDir()
	{
		if(is_null($this->__dir))
		{
			$this->__dir = new Directory(dirname($this->_path) . DIRECTORY_SEPARATOR, false);
		}

		return $this->__dir;
	}

	/**
	 * Check if file exists, if not set error
	 *
	 * @return boolean
	 */
	private function __isFile()
	{
		if(!$this->exists())
		{
			$this->error = 'File \'' . $this->_path . '\' does not exist';
			return false;
		}

		return true;
	}

	/**
	 * Check if file directory is writable, if not set error
	 *
	 * @return boolean
	 */
	private function __isWritableDir()
	{
		if(!$this->__getDir()->exists())
		{
			$this->error = 'Directory \'' . $this->__getDir()->getPath() . '\' does not exist';
			return false;
		}

		if(!$this->__getDir()->writable())
		{
			$this->error = 'Directory \'' . $this->__getDir()->getPath() . '\' is not writable';
			return false;
		}

		return true;
	}

	/**
	 * Chnage file mode
	 *
	 * @param int $chmod
	 * @return void
	 */
	public function chmod($chmod = 0644)
	{
		chmod($this->_path, $chmod);
	}

	/**
	 * Copy file to another file location
	 *
	 * @param \Drone\Filesystem\File $file (file object with copy location)
	 * @return boolean
	 */
	public function copy(\Drone\Filesystem\File $file)
	{
		if(!$this->__isFile())
		{
			return false;
		}

		return copy($this->_path, $file->getPath());
	}

	/**
	 * Create empty file
	 *
	 * @param int $chmod
	 * @return boolean
	 */
	public function create($chmod = 0644)
	{
		if($this->exists()) // file already exists
		{
			$this->error = 'File \'' . $this->_path . '\' already exists';
			return false;
		}

		if(!$this->__isWritableDir())
		{
			return false;
		}

		if(touch($this->_path))
		{
			$this->chmod($chmod);
			return true;
		}

		return false;
	}

	/**
	 * File exists flag getter
	 *
	 * @return boolean
	 */
	public function exists()
	{
		return is_file($this->_path);
	}

	/**
	 * File modified time getter
	 *
	 * @return int (Unix timestamp, or false on error)
	 */
	public function getModifiedTime()
	{
		if(!$this->__isFile())
		{
			return false;
		}

		return filemtime($this->_path);
	}

	/**
	 * File size in bytes getter
	 *
	 * @return int (file size in bytes, or false on error)
	 */
	public function getSize()
	{
		if(!$this->__isFile())
		{
			return false;
		}

		return filesize($this->_path);
	}

	/**
	 * Move file to another file location
	 *
	 * @param \Drone\Filesystem\File $file (file object with new location)
	 * @return boolean
	 */
	public function move(\Drone\Filesystem\File $file)
	{
		if(!$this->__isFile())
		{
			return false;
		}

		return rename($this->_path, $file->getPath());
	}

	/**
	 * Read file contents to string
	 *
	 * @param int $offset (the offset where reading starts)
	 * @param int $limit (max length to read)
	 * @return string (or false on error)
	 */
	public function read($offset = 0, $limit = 0)
	{
		if(!$this->__isFile())
		{
			return false;
		}

		if(!is_readable($this->_path))
		{
			$this->error = 'File \'' . $this->_path . '\' is not readable';
			return false;
		}

		if((int)$limit > 0)
		{
			return file_get_contents($this->_path, false, null, (int)$offset > 0 ? (int)$offset : -1,
				(int)$limit);
		}
		else
		{
			return file_get_contents($this->_path, false, null, (int)$offset > 0 ? (int)$offset : -1);
		}
	}

	/**
	 * Remove file
	 *
	 * @return boolean
	 */
	public function remove()
	{
		if(!$this->exists())
		{
			$this->error = 'File \'' . $this->_path . '\' already exists';
			return false;
		}

		return unlink($this->_path);
	}

	/**
	 * File is writable flag getter
	 *
	 * @return boolean
	 */
	public function writable()
	{
		if($this->exists()) // file exists check if writable
		{
			return parent::writable();
		}

		// new file, check if directory writable
		return $this->__getDir()->writable();
	}

	/**
	 * Write data to file
	 *
	 * @param mixed $data
	 * @param boolean $append
	 * @return boolean
	 */
	public function write($data, $append = false)
	{
		if(!$this->exists() && !$this->__isWritableDir())
		{
			return false;
		}

		if($this->exists() && !$this->writable())
		{
			$this->error = 'File \'' . $this->_path . '\' is not writable';
			return false;
		}

		return file_put_contents($this->_path, $data, $append ? FILE_APPEND | LOCK_EX : LOCK_EX) !== false;
	}
}