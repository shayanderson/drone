<?php
/**
 * PDOm - PDO Wrapper with MySQL Helper
 * 
 * Requirements:
 *	- PHP 5.4+
 *	- PHP PDO database extension <http://www.php.net/manual/en/book.pdo.php>
 *	- Database table names cannot include characters '.', '/', ':' or ' ' (whitespace)
 * 
 * @package PDOm
 * @version 0.0.4
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @link <https://github.com/shayanderson/pdom>
 */
namespace Pdom;

/**
 * PDOm Record class
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
abstract class Record
{
	/**
	 * Annotation tag for property column
	 */
	const ANNOTATION_COLUMN = '@column';

	/**
	 * Primary key column name (must be set in extending class for select() method use)
	 */
	const KEY = null;

	/**
	 * Table name (must be set in extending class)
	 */
	const TABLE = null;

	/**
	 * Column names
	 *
	 * @var array
	 */
	private $__columns;

	/**
	 * Init / autoload record
	 *
	 * @param mixed $id
	 */
	public function __construct($id = null)
	{
		if(!is_null($id))
		{
			$this->__init();

			if(!empty(static::KEY)) // autoload record
			{
				$this->{static::KEY} = $id;
				$this->select();
			}
		}
	}

	/**
	 * Init object
	 *
	 * @return void
	 * @throws \Exception
	 */
	private function __init()
	{
		if(is_null($this->__columns))
		{
			$this->__columns = [];

			if(empty(static::TABLE))
			{
				throw new \Exception(__METHOD__ . ': table name must be set using class constant TABLE');
			}

			if(!empty(static::KEY))
			{
				$this->__columns[] = static::KEY;
			}

			foreach((new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC
				| \ReflectionProperty::IS_PROTECTED) as $prop) /* @var $prop \ReflectionProperty */
			{
				if(strpos($prop->getDocComment(), self::ANNOTATION_COLUMN) !== false)
				{
					$this->__columns[] = $prop->name;
				}
			}

			if(count($this->__columns) < 1)
			{
				throw new \Exception(__METHOD__
					. ': zero class properties set as columns (at least one column must exist)');
			}

			$this->__columns = array_unique($this->__columns); // unique only
		}
	}

	/**
	 * Throw Exception if key column name not set
	 *
	 * @return void
	 * @throws \Exception
	 */
	private function __requireKey()
	{
		if(empty(static::KEY) || !isset($this->{static::KEY}))
		{
			throw new \Exception(__METHOD__
				. ': class constant KEY and primary key class property values must be set');
		}
	}

	/**
	 * Add/insert record data
	 *
	 * @param boolean $ignore (ignore insert errors)
	 * @return boolean (true on affected rows > 0)
	 */
	public function add($ignore = false)
	{
		$this->__init();
		$columns = &$this->getColumns();

		if(!empty(static::KEY))
		{
			unset($columns[static::KEY]); // rm primary key column
		}

		return pdom(static::TABLE . ':add' . ( $ignore ? '/ignore' : '' ), $columns) > 0;
	}

	/**
	 * Delete record
	 *
	 * @param boolean $ignore (ignore delete errors)
	 * @return boolean (true on affected rows > 0)
	 */
	public function delete($ignore = false)
	{
		$this->__init();
		$this->__requireKey();

		return pdom(static::TABLE . ':del' . ( $ignore ? '/ignore' : '' ),
			'WHERE ' . static::KEY . ' = :id LIMIT 1', ['id' => $this->{static::KEY}]) > 0;
	}

	/**
	 * Column names getter
	 *
	 * @return array (ex: ['col1', 'col2', ...])
	 */
	public function getColumnNames()
	{
		$this->__init();
		return $this->__columns;
	}

	/**
	 * Column names and values getter
	 *
	 * @return array (ex: ['col1' => 'value1', 'col2' => 'value2', ...])
	 */
	public function &getColumns()
	{
		$a = array_fill_keys($this->getColumnNames(), null);

		foreach($a as $k => &$v)
		{
			if(isset($this->{$k}))
			{
				$v = $this->{$k};
			}
		}

		return $a;
	}

	/**
	 * Column exists flag getter
	 *
	 * @param string $column_name
	 * @return boolean
	 */
	public function isColumn($column_name)
	{
		return in_array($column_name, $this->__columns);
	}

	/**
	 * Record exists in database table flag getter
	 *
	 * @return boolean
	 */
	public function isRecord()
	{
		$this->__requireKey();

		return pdom(static::TABLE . ':count', 'WHERE ' . static::KEY . ' = :id LIMIT 1',
			['id' => $this->{static::KEY}]) > 0;
	}

	/**
	 * Save/update record
	 *
	 * @param boolean $ignore (ignore update errors)
	 * @return boolean (true on affected rows > 0)
	 */
	public function save($ignore = false)
	{
		$this->__init();
		$this->__requireKey();
		$columns = &$this->getColumns();

		if(!empty(static::KEY))
		{
			unset($columns[static::KEY]); // rm primary key column
		}

		return pdom(static::TABLE . ':mod' . ( $ignore ? '/ignore' : '' ), $columns,
			'WHERE ' . static::KEY . ' = :id LIMIT 1', ['id' => $this->{static::KEY}]) > 0;
	}

	/**
	 * Select record data for class properties
	 *
	 * @param string $where_clause (optional for custom add-on WHERE clause, ex: 'is_active = :is_active')
	 * @param array $where_named_args (optional for custom add-on WHERE clause, ex: ['is_active' => 1])
	 * @return boolean (true on record selected and columns (class properties) populated)
	 * @throws \Exception
	 */
	public function select($where_clause = '', array $where_named_args = [])
	{
		$this->__init();
		$this->__requireKey();

		$r = pdom(static::TABLE . '(' . implode(', ', $this->getColumnNames()) . ')',
			'WHERE ' . ( !empty($where_clause) ? trim($where_clause) . ' AND ' : '' )
				. static::KEY . ' = :id LIMIT 1', ['id' => $this->{static::KEY}] + $where_named_args);

		if(!isset($r[0]))
		{
			return false;
		}

		$this->setColumns((array)$r[0]);
		return true;
	}

	/**
	 * Columns (class properties) value setter
	 *
	 * @param array $columns_and_values (ex: ['col1' => 'value1', 'col2' => 'value2', ...])
	 * @return void
	 */
	public function setColumns(array $columns_and_values)
	{
		$this->__init();

		foreach($columns_and_values as $k => $v)
		{
			if($this->isColumn($k))
			{
				$this->{$k} = $v;
			}
		}
	}
}