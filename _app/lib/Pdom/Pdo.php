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
 * @version 1.4.b
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @link <https://github.com/shayanderson/pdom>
 */
namespace Pdom;

/**
 * PDOm core class
 *
 * @author Shay Anderson 03.14 <http://www.shayanderson.com/contact>
 */
class Pdo
{
	/**
	 * Default primary key column name
	 */
	const DEFAULT_PRIMARY_KEY_COLUMN = 'id';

	/**
	 * Configuration settings
	 *
	 * @var array
	 */
	private $__conf = [
		'debug' => false,
		'errors' => true,
		'objects' => true
	];

	/**
	 * Current connection ID
	 *
	 * @var int
	 */
	private static $__connection_id = 0;

	/**
	 * Connection ID
	 *
	 * @var int
	 */
	private $__id;

	/**
	 * Error occurred flag
	 *
	 * @var boolean
	 */
	private $__is_error = false;

	/**
	 * Primary key column name to table map
	 *
	 * @var array
	 */
	private $__key_map;

	/**
	 * Last error string (when error occurs)
	 *
	 * @var string
	 */
	private $__last_error;

	/**
	 * Debug log
	 *
	 * @var array
	 */
	private $__log = [];

	/**
	 * PDO object instance
	 *
	 * @var \PDO
	 */
	private $__pdo;

	/**
	 * Init
	 *
	 * @param int $id
	 * @param string $host
	 * @param string $database
	 * @param string $user
	 * @param string $password
	 * @param array $conf
	 */
	public function __construct($id, $host, $database, $user, $password, $conf)
	{
		$this->__id = $id;

		foreach($conf as $k => $v) // conf setter
		{
			if(isset($this->__conf[$k]) || array_key_exists($k, $this->__conf))
			{
				$this->__conf[$k] = $v;
			}
		}

		$this->__getPdoObject($id, ['host' => $host, 'database' => $database,
			'user' => $user, 'password' => $password]);
	}

	/**
	 * Init (static)
	 *
	 * @throws \Exception
	 */
	private static function __init()
	{
		if(!class_exists('\\PDO'))
		{
			throw new \Exception('Failed to find \\PDO class, install PDO (PHP Data Objects)');
		}
	}

	/**
	 * Trigger error
	 *
	 * @param string $message
	 * @return void
	 * @throws \Exception
	 */
	private function __error($message)
	{
		$message = 'Error: ' . $message;
		$this->__is_error = true;
		$this->__last_error = $message;
		$this->__log($message);

		if($this->__conf['errors'])
		{
			if($this->__conf['debug'])
			{
				print_r($this->log());
			}

			throw new \Exception('Pdom: ' . $message);
		}
	}

	/**
	 * PDO object getter (lazy loading) and host data setter
	 *
	 * @staticvar array $host
	 * @param int|null $id
	 * @param array $host_data
	 * @return \PDO (or null on host data setter)
	 */
	public function &__getPdoObject($id = null, array $host_data = [])
	{
		static $host = [];

		if(!empty($host_data))
		{
			$host[$id] = $host_data;

			$this->__log('Connection "' . $this->__id . '" registered (host: "'
				. $host[$this->__id]['host'] . '", database: "'
				. $host[$this->__id]['database'] . '")');
		}
		else if(empty($this->__pdo))
		{
			try
			{
				$this->__pdo = new \PDO('mysql:host=' . $host[$this->__id]['host'] . ';dbname='
					. $host[$this->__id]['database'], $host[$this->__id]['user'],
					$host[$this->__id]['password']);
				$this->__pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
			catch(\PDOException $ex)
			{
				$this->__error($ex->getMessage());
			}
		}

		return $this->__pdo;
	}

	/**
	 * Add debug log message
	 *
	 * @param string $message
	 * @return void
	 */
	private function __log($message)
	{
		if($this->__conf['debug'])
		{
			$this->__log[] = $message;
		}
	}

	/**
	 * Configuration settings getter
	 *
	 * @param null|string $key (null for get all)
	 * @return mixed
	 */
	public function conf($key)
	{
		if(is_null($key)) // get all
		{
			return $this->__conf;
		}

		if(isset($this->__conf[$key]) || array_key_exists($key, $this->__conf)) // getter
		{
			return $this->__conf[$key];
		}
	}

	/**
	 * PDO connection getter/setter
	 *
	 * @staticvar boolean $is_init
	 * @staticvar array $connections
	 * @param int $connection
	 * @return \self (or array|boolean)
	 * @throws \Exception
	 */
	public static function &connection($connection = 1)
	{
		static $is_init = false;
		static $connections = [];

		if(is_null($connection)) // connection keys getter
		{
			$keys = array_keys($connections);
			return $keys;
		}

		if(!$is_init) // init handler
		{
			self::__init();
			$is_init = true;
		}

		if(is_array($connection)) // register
		{
			if(isset($connection['host']) && isset($connection['database'])
				&& isset($connection['user']) && isset($connection['password'])) // verify connection
			{
				if(isset($connection['id']) && is_int($connection['id'])) // manual connection ID
				{
					$id = $connection['id'];

					if(isset($connections[$id]))
					{
						throw new \Exception('Connection ID "' . $id . '" already exists');
						return false;
					}
				}
				else // auto ID
				{
					$id = ++self::$__connection_id;

					while(isset($connections[$id])) // enforce unique ID
					{
						$id = ++self::$__connection_id;
					}
				}

				$connections[$id] = new self($id, $connection['host'],
					$connection['database'], $connection['user'], $connection['password'], $connection);

				return $id;
			}
		}
		else // getter
		{
			if(isset($connections[$connection]))
			{
				return $connections[$connection];
			}

			throw new \Exception('Connection "' . $connection . '" does not exist');
		}
	}

	/**
	 * Error has occurred flag getter
	 *
	 * @return boolean
	 */
	public function isError()
	{
		return $this->__is_error;
	}

	/**
	 * Table primary key column name getter/setter
	 *
	 * @param string $table
	 * @param string $key_column
	 * @return string
	 */
	public function key($table, $key_column = null)
	{
		if(is_null($table)) // get all
		{
			return $this->__key_map;
		}

		if(!is_null($key_column)) // setter
		{
			$this->__key_map[$table] = $key_column;
			return $key_column;
		}

		if(isset($this->__key_map[$table]))
		{
			return $this->__key_map[$table];
		}

		return self::DEFAULT_PRIMARY_KEY_COLUMN; // default
	}

	/**
	 * Last error message getter
	 *
	 * @return string
	 */
	public function lastError()
	{
		return $this->__last_error;
	}

	/**
	 * Debug log getter
	 *
	 * @return array
	 */
	public function log()
	{
		return $this->__log;
	}

	/**
	 * Execute query
	 *
	 * @param string $query
	 * @param array|null $params (prepared statement params)
	 * @return mixed (array|boolean|int|object)
	 */
	public function query($query, $params = null)
	{
		$this->__log('Query: ' . $query);
		if(is_array($params) && count($params) > 0)
		{
			$q_params = [];
			foreach($params as $k => $v)
			{
				if(is_array($v))
				{
					$this->__error('Invalid query parameter(s) type: array (only use scalar values)');
					return false;
				}

				$q_params[] = $k . ' => ' . $v;
			}

			$this->__log('(Query params: ' . implode(', ', $q_params) . ')');
		}

		try
		{
			$sh = $this->__getPdoObject()->prepare($query);
			if($sh->execute( is_array($params) ? $params : null ))
			{
				if(preg_match('/^\s*(select|show|describe|optimize|pragma|repair)/i', $query)) // fetch
				{
					return $sh->fetchAll( $this->conf('objects') ? \PDO::FETCH_CLASS : \PDO::FETCH_ASSOC );
				}
				else if(preg_match('/^\s*(delete|insert|update)/i', $query)) // modify
				{
					return $sh->rowCount();
				}
				else // other
				{
					return true;
				}
			}
			else
			{
				$this->__error($sh->errorInfo());
			}
		}
		catch(\PDOException $ex)
		{
			$this->__error($ex->getMessage());
		}

		return false;
	}
}