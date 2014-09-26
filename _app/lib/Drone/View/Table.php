<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5+
 *
 * @package Drone
 * @version 0.1.8
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone\View;

/**
 * View Table class - HTML table helper class
 *
 * @author Shay Anderson 06.14 <http://www.shayanderson.com/contact>
 */
class Table
{
	/**
	 * Part attributes
	 *
	 * @var array
	 */
	private $__attributes = [];

	/**
	 * Table data
	 *
	 * @var array
	 */
	private $__data;

	/**
	 * Table heading data
	 *
	 * @var array
	 */
	private $__headings;

	/**
	 * Cell index
	 *
	 * @var int
	 */
	private $__index_cell = 0;

	/**
	 * Row index
	 *
	 * @var int
	 */
	private $__index_row = 0;

	/**
	 * In row flag
	 *
	 * @var boolean
	 */
	private $__is_row = false;

	/**
	 * Cell templates
	 *
	 * @var array
	 */
	private $__templates = [];

	/**
	 * Use these columns in table (optional)
	 *
	 * @var array
	 */
	private $__use_columns;

	/**
	 * Number of columns in table
	 *
	 * @var int
	 */
	public $columns = 1;

	/**
	 * Vertical columns flag
	 *
	 * @var boolean
	 */
	public $columns_vertical = false;

	/**
	 * End of line character
	 *
	 * @var string (ex: PHP_EOL)
	 */
	public $end_of_line = '';

	/**
	 * Init
	 *
	 * @param array $data
	 * @param array $columns (optional, only use these columns in table)
	 */
	public function __construct(array $data = [], array $columns = [])
	{
		$this->__data = self::__cleanData($data);
		$this->__use_columns = $columns;
	}

	/**
	 * Get table string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->get();
	}

	/**
	 * Array of attributes to string
	 *
	 * @param array $attributes (ex: ['style' => 'color:#fff'])
	 * @return string (ex: ' style="color:#fff"')
	 */
	private static function &__attributes(array $attributes)
	{
		$html = '';

		foreach($attributes as $k => $v)
		{
			$html .= ' ' . $k . '="' . htmlentities($v) . '"';
		}

		return $html;
	}

	/**
	 * Add row cell
	 *
	 * @param string $html
	 * @param string $value
	 * @param array $row_data
	 *
	 * @return void
	 */
	private function __cell(&$html, $value, &$column, &$row_data)
	{
		$this->__index_cell++;

		if(is_array($value))
		{
			$value = '&nbsp;';
		}

		if(isset($this->__templates[$column]) || array_key_exists($column, $this->__templates)) // apply cell template
		{
			$value = $this->__templates[$column];

			preg_replace_callback('/\{\$([\w]+)\}/', function($m) use(&$value, &$row_data)
			{
				if(isset($m[0]) && isset($m[1]))
				{
					if(isset($row_data[$m[1]]) || array_key_exists($m[1], $row_data))
					{
						$value = str_replace($m[0], $row_data[$m[1]], $value);
					}
				}
			}, $value);
		}

		$html .= '<td' . $this->__getAttributes('td', $this->__index_cell) . '>' . $value . '</td>'
			. $this->end_of_line;
	}

	/**
	 * Prepare data for table
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	private static function __cleanData($data)
	{
		if(is_array($data))
		{
			array_walk_recursive($data, function(&$v) { // convert objects to arrays
				if(is_object($v))
				{
					$v = (array)$v;
				}
			});
		}
		else if(is_object($data)) // object to array
		{
			$data = (array)$data;
		}

		return $data;
	}

	/**
	 * Attributes string getter
	 *
	 * @param string $type
	 * @param int $index
	 * @param boolean $use_global
	 * @return string
	 */
	private function &__getAttributes($type, $index, $use_global = true)
	{
		$attr = '';

		if(isset($this->__attributes[$type][$index])) // part specific
		{
			$attr = &self::__attributes($this->__attributes[$type][$index]);
		}
		else if($use_global && isset($this->__attributes[$type][0])) // global
		{
			$attr = &self::__attributes($this->__attributes[$type][0]);
		}

		return $attr;
	}

	/**
	 * Add row
	 *
	 * @param string $html
	 * @return void
	 */
	private function __row(&$html)
	{
		if(!$this->__is_row) // start row
		{
			$this->__index_row++;

			$html .= '<tr' . $this->__getAttributes('tr', $this->__index_row) . '>' . $this->end_of_line;
			$this->__is_row = true;
		}
		else // end row
		{
			$html .= '</tr>' . $this->end_of_line;
			$this->__is_row = false;
		}
	}

	/**
	 * Attributes setter
	 *
	 * @param string $type
	 * @param string $name
	 * @param string $value
	 * @param int $index
	 * @return void
	 */
	private function __setAttributes($type, $name, $value, $index)
	{
		if(is_array($index))
		{
			foreach($index as $v)
			{
				$this->__setAttributes($type, $name, $value, $v);
			}
			return;
		}

		$this->__attributes[$type][$index][$name] = $value;
	}

	/**
	 * Table attribute setter
	 *
	 * @param string $name (ex: 'style')
	 * @param string $value (ex: 'color:#fff')
	 * @return void
	 */
	public function attribute($name, $value = null)
	{
		if(is_array($name))
		{
			foreach($name as $k => $v)
			{
				$this->attribute($k, $v);
			}
			return;
		}

		$this->__attributes['table'][$name] = $value;
	}

	/**
	 * Cell attribute setter
	 *
	 * @param string $name (ex: 'style')
	 * @param string $value (ex: 'color:#fff')
	 * @param mixed $index (int for single index, array for multiple indexes)
	 * @return void
	 */
	public function attributeCell($name, $value, $index = 0)
	{
		$this->__setAttributes('td', $name, $value, $index);
	}

	/**
	 * Heading attribute setter
	 *
	 * @param string $name (ex: 'style')
	 * @param string $value (ex: 'color:#fff')
	 * @param mixed $index (int for single index, array for multiple indexes)
	 * @return void
	 */
	public function attributeHeading($name, $value, $index = 0)
	{
		$this->__setAttributes('head', $name, $value, $index);
	}

	/**
	 * Row attribute setter
	 *
	 * @param string $name (ex: 'style')
	 * @param string $value (ex: 'color:#fff')
	 * @param mixed $index (int for single index, array for multiple indexes)
	 * @return void
	 */
	public function attributeRow($name, $value, $index = 0)
	{
		$this->__setAttributes('tr', $name, $value, $index);
	}

	/**
	 * Column template setter
	 *
	 * @param string $column
	 * @param string $template
	 * @return void
	 */
	public function columnTemplate($column, $template)
	{
		$this->__templates[$column] = $template;
	}

	/**
	 * Add data to table data
	 *
	 * @param mixed $value
	 * @return void
	 */
	public function data($value)
	{
		$this->__data[] = self::__cleanData($value);
	}

	/**
	 * Display/print table
	 *
	 * @return void
	 */
	public function display()
	{
		echo $this->get();
	}

	/**
	 * Get table string
	 *
	 * @return string
	 */
	public function get()
	{
		$html = [];

		if(count($this->__data) > 0)
		{
			$this->columns = (int)$this->columns;

			$html['head'] = '<table' . (isset($this->__attributes['table']) && is_array($this->__attributes['table'])
				? self::__attributes($this->__attributes['table']) : '' ) . '>' . $this->end_of_line;
			$html['body'] = '';

			if($this->columns_vertical && $this->columns > 0) // set data for vertical columns
			{
				$data = [];
				$count = (int)ceil(count($this->__data) / $this->columns); // item count per column

				$i = 0;
				foreach($this->__data as $k => $v)
				{
					$data[$i][$k] = $v; // push item
					$i++;

					if($i >= $count)
					{
						$i = 0; // reset
					}
				}
			}

			$i = $cells = 0;
			foreach(isset($data) ? $data : $this->__data as $k => $row)
			{
				$i++;

				if($i === 1) $this->__row($html['body']); // start

				if($i === $this->columns)
				{
					$i = 0;
				}

				if($this->__index_row === 1 && $this->__headings === true) // auto headings
				{
					$headings = true;
					$this->__headings = [];
				}

				if(!is_array($row)) // single row
				{
					if(isset($headings) && $this->__index_row === 1)
					{
						$this->__headings[] = $k; // auto heading
					}

					if(empty($this->__use_columns) || in_array($k, $this->__use_columns))
					{
						$this->__cell($html['body'], $row, $k, $this->__data);
					}
				}
				else // array row
				{
					if(count($row) !== $this->columns)
					{
						while(count($row) < $this->columns) // fix empty cell values
						{
							$row[] = '&nbsp;';
						}
					}

					foreach($row as $k => $v)
					{
						if(isset($headings))
						{
							$this->__headings[] = $k; // auto heading
						}

						if(empty($this->__use_columns) || in_array($k, $this->__use_columns))
						{
							$this->__cell($html['body'], $v, $k, $row);
						}
					}

					if(isset($headings))
					{
						unset($headings);
					}

					$i = 0;
					$this->__row($html['body']); // force end
					continue;
				}

				if($i === 0) $this->__row($html['body']); // end
			}

			if($this->__is_row)
			{
				if($i < $this->columns) // fix empty cell values
				{
					while($i < $this->columns)
					{
						$this->__cell($html['body'], '&nbsp;', null, null);
						$i++;
					}
				}

				$this->__row($html['body']); // cleanup
			}

			if(isset($data))
			{
				unset($data); // cleanup
			}

			if(is_array($this->__headings) && count($this->__headings) > 0) // headings
			{
				$html['head'] .= '<thead>' . $this->end_of_line . '<tr' . $this->__getAttributes('head', 0, false)
					. '>' . $this->end_of_line;
				$i = 0;
				foreach($this->__headings as $v)
				{
					$i++;
					$html['head'] .= '<th' . $this->__getAttributes('head', $i, false) . '>'
						. ( strlen($v) > 0 ? $v : '&nbsp;' ) . '</th>' . $this->end_of_line;
				}
				$html['head'] .= '</tr>' . $this->end_of_line . '</thead>' . $this->end_of_line;
			}

			$html['foot'] = '</table>' . $this->end_of_line;
		}

		return implode('', $html);
	}

	/**
	 * Table data getter
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->__data;
	}

	/**
	 * Table headings setter
	 *
	 * @param mixed $headings (null for auto headings, array for string headings)
	 * @return void
	 */
	public function headings($headings = null)
	{
		$this->__headings = is_array($headings) ? $headings : true;
	}
}