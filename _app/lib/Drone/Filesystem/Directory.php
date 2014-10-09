<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5+
 *
 * @package Drone
 * @version 0.2.0
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone\Filesystem;

/**
 * Filesystem Directory handler class
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class Directory extends \Drone\Filesystem
{
	/**
	 * Init
	 *
	 * @param string $path
	 * @param boolean $use_root_path (add root path (PATH_ROOT) to start of path)
	 */
	public function __construct($path, $use_root_path = true)
	{
		parent::__construct($path, $use_root_path);
		$this->_path = parent::formatDirectory($this->_path);
	}

	/**
	 * Check if directory exists, if not set error
	 *
	 * @return boolean
	 */
	private function __isDir()
	{
		if(!$this->exists())
		{
			$this->error = 'Directory \'' . $this->_path . '\' does not exist';
			return false;
		}

		return true;
	}

	/**
	 * Copy directory to another directory location
	 *
	 * @param \Drone\Filesystem\Directory $directory (directory object with copy location)
	 * @return boolean
	 */
	public function copy(\Drone\Filesystem\Directory $directory, $chmod = 0644)
	{
		if(!$this->__isDir())
		{
			return false;
		}

		if(!$directory->create($chmod))
		{
			$this->error = 'Failed to create new directory \'' . $directory->getPath() . '\'';
			return false;
		}

		foreach($this->read(false, true) as $asset)
		{
			if($asset['type'] === 'dir')
			{
				$dir = new Directory($this->_path . $asset['name'], false);

				if(!$dir->copy(new Directory($directory->getPath() . $asset['name'], false), $chmod))
				{
					$this->error = 'Failed to create new subdirectory \'' . $dir->getPath()
						. '\' (' . $dir->error . '), try elevated chmod';
					return false;
				}
			}
			else // file
			{
				$file = new File($this->_path . $asset['name'], false);

				if(!$file->copy(new File($directory->getPath() . $asset['name'], false), $chmod))
				{
					$this->error = 'Failed to create new file \'' . $file->getPath()
						. '\' (' . $file->error . '), try elevated chmod';
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Create directory
	 *
	 * @param int $chmod
	 * @param boolean $recursive
	 * @return boolean
	 */
	public function create($chmod = 0644, $recursive = false)
	{
		if($this->exists())
		{
			$this->error = 'Directory \'' . $this->_path . '\' already exists';
			return false;
		}

		if(!$recursive) // not recursive, check if parent dir writable
		{
			$dir_write = dirname($this->_path);

			if(!is_writable($dir_write))
			{
				$this->error = 'Directory \'' . $dir_write . '\' is not writable';
				return false;
			}
		}

		if(!@mkdir($this->_path, $chmod, $recursive))
		{
			$this->error = parent::_getLastError();
			return false;
		}

		return true;
	}

	/**
	 * Directory exists flag getter
	 *
	 * @return boolean
	 */
	public function exists()
	{
		return is_dir($this->_path);
	}

	/**
	 * Count of directory assets getter
	 *
	 * @return int
	 */
	public function getCount()
	{
		return count($this->read());
	}

	/**
	 * Move directory to another directory location
	 *
	 * @param \Drone\Filesystem\Directory $directory (directory object with new location)
	 * @return boolean
	 */
	public function move(\Drone\Filesystem\Directory $directory)
	{
		if(!$this->__isDir())
		{
			return false;
		}

		return rename($this->_path, $directory->getPath());
	}

	/**
	 * Read directory contents to array
	 *
	 * @param boolean $absolute_pathnames (will use absolute pathnames instead of relative pathnames)
	 * @param boolean $include_types (will return array like [['name' => 'file.txt', 'type' => 'file'], ...])
	 * @return array (or false on error, ex: ['file1.txt', 'file2.txt', ...])
	 */
	public function read($absolute_pathnames = false, $include_types = false)
	{
		if(!$this->__isDir())
		{
			return false;
		}

		if(!is_readable($this->_path))
		{
			$this->error = 'Directory \'' . $dir_write . '\' is not readable';
			return false;
		}

		if(!$include_types)
		{
			return array_map(function($v) use (&$absolute_pathnames) {
				return $absolute_pathnames ? $this->_path . $v : $v; },
					array_values(array_diff(scandir($this->_path), ['.', '..'])));
		}

		// include asset types
		$assets = $this->read();
		foreach($assets as &$v)
		{
			$v = ['name' => ( $absolute_pathnames ? $this->_path : '' ) . $v,
				'type' => filetype($this->_path . $v)];
		}
		return $assets;
	}

	/**
	 * Remove directory
	 *
	 * @param boolean $recursive (empty directory contents and remove)
	 * @return boolean
	 */
	public function remove($recursive = false)
	{
		if(!$this->__isDir())
		{
			return false;
		}

		if(!$recursive)
		{
			if(!@rmdir($this->_path))
			{
				$this->error = parent::_getLastError();
				return false;
			}

			return true;
		}

		// recursive remove
		foreach($this->read(false, true) as $asset)
		{
			if($asset['type'] === 'dir')
			{
				$dir = new Directory($this->_path . $asset['name'], false);

				if(!$dir->remove(true))
				{
					$this->error = 'Failed to remove subdirectory \'' . $dir->getPath()
						. '\' (' . $dir->error . '), try elevated chmod';
					return false;
				}
			}
			else // file
			{
				$file = new File($this->_path . $asset['name'], false);

				if(!$file->remove())
				{
					$this->error = 'Failed to remove file \'' . $file->getPath()
						. '\' (' . $file->error . '), try elevated chmod';
					return false;
				}
			}
		}

		return $this->remove(); // finally remove base directory
	}
}