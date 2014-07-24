<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 0.1.6
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone\View;

use \Drone\Core\Data;

/**
 * View Form class - HTML form helper class
 *
 * @author Shay Anderson 06.14 <http://www.shayanderson.com/contact>
 */
class Form
{
	/**
	 * Form field types
	 */
	const
		FIELD_CHECKBOX = 'checkbox',
		FIELD_EMAIL = 'email',
		FIELD_HIDDEN = 'hidden',
		FIELD_PASSWORD = 'password',
		FIELD_RADIO = 'radio',
		FIELD_SELECT = 1,
		FIELD_TEXT = 'text',
		FIELD_TEXTAREA = 2;

	/**
	 * Get|post data
	 *
	 * @var array
	 */
	private $__data;

	/**
	 * Form fields
	 *
	 * @var array
	 */
	private $__fields = [];

	/**
	 * Form ID
	 *
	 * @var string
	 */
	private $__form_id;

	/**
	 * Form IDs
	 *
	 * @var array
	 */
	private static $__form_ids = [];

	/**
	 * Active field ID
	 *
	 * @var string
	 */
	private $__id;

	/**
	 * Global decorators
	 *
	 * @var string (ex: '<div>{$field}</div>')
	 */
	public static
		$decorator_checkbox_radio, // checkbox + radio fields
		$decorator_error,
		$decorator_errors,
		$decorator_field, // email, password, text fields
		$decorator_fields, // all fields
		$decorator_options, // all checkbox/radio options
		$decorator_select,
		$decorator_textarea;

	/**
	 * Init
	 *
	 * @param array $data ($_GET|$_POST array)
	 * @param string $form_id (optional, when using form listener for multiple forms in scope)
	 * @param boolean $sanitize_data (auto sanitize get|post data)
	 * @throws \Exception (when form ID has already has been used by another form object in scope)
	 */
	public function __construct(array &$data, $form_id = null, $sanitize_data = true)
	{
		if($sanitize_data) // auto sanitize data
		{
			$this->__data = drone()->data->filter($data, Data::FILTER_SANITIZE);
		}
		else
		{
			$this->__data = &$data;
		}

		if($form_id !== null && strlen($form_id) > 0)
		{
			if(in_array($form_id, self::$__form_ids))
			{
				throw new \Exception('Form ID \'' . $form_id . '\' already exists (must be unique)');
			}

			self::$__form_ids[] = $form_id;
			$this->__form_id = $form_id;
		}
	}

	/**
	 * Alias for getFormIdField() method
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getFormIdField();
	}

	/**
	 * Add form field
	 *
	 * @param mixed $type (int|string)
	 * @param string $id
	 * @param mixed $default_value
	 * @param mixed $options
	 * @return void
	 * @throws \Exception (when field ID already exists)
	 */
	private function __addField($type, $id, $default_value = null, $options = null)
	{
		if($this->isField($id))
		{
			throw new \Exception('Field ID \'' . $id . '\' already exists in form (field ID must be unique)');
		}

		$this->__fields[$id] = ['type' => $type];

		if($this->isSubmitted() && isset($this->__data[$id])) // add data as value
		{
			if($type !== self::FIELD_PASSWORD)
			{
				$this->__fields[$id]['value'] = $this->__data[$id];
			}
		}
		else if($default_value !== null && is_scalar($default_value)
			|| $type === self::FIELD_CHECKBOX && is_array($default_value))
		{
			$this->__fields[$id]['value'] = $default_value;
		}

		if($options !== null && is_array($options))
		{
			$this->__fields[$id]['options'] = $options;
		}

		$this->__id = $id; // set active ID
	}

	/**
	 * Add field validation rule
	 *
	 * @staticvar int $callable_id
	 * @param mixed $rule (callable|int)
	 * @param string $error_message
	 * @param mixed $param
	 * @return void
	 */
	private function __addRule($rule, $error_message, $param = null)
	{
		static $callable_id = 0;

		if($this->isField($this->__id))
		{
			if(is_callable($rule))
			{
				$id = ++$callable_id;
				$this->__fields[$this->__id]['rule'][$id]['callable'] = $rule;
			}
			else
			{
				$this->__fields[$this->__id]['rule'][$rule] = []; // create
			}

			if(!empty($error_message))
			{
				$this->__fields[$this->__id]['rule'][isset($id) ? $id : $rule]['message'] = $error_message;
			}

			if(!isset($id) && $param !== null)
			{
				$this->__fields[$this->__id]['rule'][$rule]['param'] = $param;
			}
		}
	}

	/**
	 * Array of attributes to string
	 *
	 * @param array $attributes (or null for no attributes, ex: ['style' => 'color:#fff'])
	 * @return string (ex: ' style="color:#fff"')
	 */
	private static function &__attributes($attributes)
	{
		$html = '';

		if($attributes !== null)
		{
			foreach($attributes as $k => $v)
			{
				if(is_int($k)) // attribute with no value
				{
					$html .= ' ' . $v;
				}
				else // attribute + value
				{
					$html .= ' ' . $k . '="' . htmlentities($v) . '"';
				}
			}
		}

		return $html;
	}

	/**
	 * Decorator string method
	 *
	 * @param string $str
	 * @param mixed $decorator (string|null when no decorator)
	 * @return string
	 */
	private static function __decorate($str, $decorator = null)
	{
		if($decorator === null) // no decorator
		{
			return $str;
		}

		return preg_match('/\{\$[a-z]*\}/i', $decorator) ? preg_replace('/\{\$[a-z]*\}/i', $str, $decorator)
			: $str . $decorator; // if no decorator pattern like {$anything} append decorator to str
	}

	/**
	 * Form has form ID flag getter
	 *
	 * @return boolean
	 */
	private function __isFormId()
	{
		return $this->__form_id !== null;
	}

	/**
	 * Validate field value against validation rule
	 *
	 * @param mixed $rule (callable|int)
	 * @param mixed $value
	 * @param array $field
	 * @param array $rule_arr
	 * @param boolean $is_valid
	 * @return void
	 */
	private static function __validate($rule, $value, array &$field, array &$rule_arr, &$is_valid)
	{
		$valid = true;

		if(isset($rule_arr['callable']))
		{
			$f = $rule_arr['callable'];
			$valid = (bool)$f($value);
		}
		else
		{
			if($rule === 0) // force rule
			{
				$valid = false;
			}
			else
			{
				$valid = isset($rule_arr['param']) ? drone()->data->validate($value, $rule, $rule_arr['param'])
					: drone()->data->validate($value, $rule);
			}
		}

		if(!$valid)
		{
			$is_valid = false;

			if(isset($rule_arr['message']))
			{
				$field['error'][$rule] = $rule_arr['message'];
			}
		}
	}

	/**
	 * Add checkbox field to form
	 *
	 * @param string $id
	 * @param array $options
	 * @param mixed $default_checked (int|string when setting, or null)
	 * @return \Drone\View\Form
	 */
	public function &checkbox($id, array $options, $default_checked = null)
	{
		$this->__addField(self::FIELD_CHECKBOX, $id, $default_checked, $options);
		return $this;
	}

	/**
	 * Add email field (HTML5) to form
	 *
	 * @param string $id
	 * @param mixed $default_value (string when setting, or null)
	 * @return \Drone\View\Form
	 */
	public function &email($id, $default_value = null)
	{
		$this->__addField(self::FIELD_EMAIL, $id, $default_value);
		return $this;
	}

	/**
	 * Manually set field as active
	 *
	 * @param string $id
	 * @return \Drone\View\Form
	 * @throws \Exception (when field does not exist)
	 */
	public function field($id)
	{
		if(!$this->isField($id))
		{
			throw new \Exception('Field with ID \'' . $id . '\' does not exist');
		}

		$this->__id = $id; // set active ID
		return $this;
	}

	/**
	 * Force field error
	 *
	 * @param string $error_message
	 * @return \Drone\View\Form
	 */
	public function forceError($error_message)
	{
		$this->__addRule(0, $error_message);
		$this->isValid(); // push forced error to error queue
		return $this;
	}

	/**
	 * Form field HTML string getter
	 *
	 * @param string $id
	 * @param mixed $attributes (array when setting, or null)
	 * @param mixed $options_decorator (string when setting, or null)
	 * @return string
	 * @throws \OutOfBoundsException (when field does not exist)
	 */
	public function get($id, $attributes = null, $options_decorator = null)
	{
		$html = '';

		if($this->isField($id))
		{
			if(!is_array($attributes))
			{
				$attributes = [];
			}

			$attributes = ['name' => $id] + $attributes;

			switch($this->__fields[$id]['type'])
			{
				case self::FIELD_CHECKBOX:
				case self::FIELD_RADIO:
					foreach($this->__fields[$id]['options'] as $k => $v)
					{
						$opt_attributes = $attributes;
						if(isset($opt_attributes['checked']))
						{
							if(is_array($opt_attributes['checked']))
							{
								if($this->__fields[$id]['type'] === self::FIELD_CHECKBOX
									&& in_array($k, $opt_attributes['checked']))
								{
									$checked = true;
								}
							}
							else if($opt_attributes['checked'] === $k)
							{
								$checked = true;
							}
							unset($opt_attributes['checked']);
						}
						else if(isset($this->__fields[$id]['value']))
						{
							if(is_array($this->__fields[$id]['value']))
							{
								if($this->__fields[$id]['type'] === self::FIELD_CHECKBOX
									&& in_array($k, $this->__fields[$id]['value']))
								{
									$checked = true;
								}
							}
							else if($this->__fields[$id]['value'] === $k)
							{
								$checked = true;
							}
						}

						$html .= self::__decorate('<input type="' . $this->__fields[$id]['type'] . '"'
							. self::__attributes($opt_attributes) . ' value="' . $k . '"'
							. ( isset($checked) ? ' checked' : '' ) . '>' . $v,
								$options_decorator ?: self::$decorator_options);

						unset($checked);
					}
					$html = self::__decorate($html, self::$decorator_checkbox_radio ?: self::$decorator_fields);
					break;

				case self::FIELD_EMAIL:
				case self::FIELD_HIDDEN:
				case self::FIELD_PASSWORD:
				case self::FIELD_TEXT:
					if(isset($this->__fields[$id]['value'])) // set default value
					{
						$attributes += ['value' => $this->__fields[$id]['value']];
					}

					$html = self::__decorate('<input type="' . $this->__fields[$id]['type'] . '"'
						. self::__attributes($attributes) . '>',
						$this->__fields[$id]['type'] !== self::FIELD_HIDDEN ? ( self::$decorator_field
							?: self::$decorator_fields ) : null);
					break;

				case self::FIELD_SELECT:
					if(isset($attributes['selected']))
					{
						$selected = $attributes['selected'];
						unset($attributes['selected']);
					}
					else if(isset($this->__fields[$id]['value']))
					{
						$selected = $this->__fields[$id]['value'];
					}

					$html = '<select' . self::__attributes($attributes) . '>';

					foreach($this->__fields[$id]['options'] as $k => $v)
					{
						$html .= '<option value="' . $k . '"' . ( isset($selected) && $selected === $k
							? ' selected' : '' ) . '>' . $v	. '</option>';
					}

					$html = self::__decorate($html . '</select>', self::$decorator_select ?: self::$decorator_fields);
					break;

				case self::FIELD_TEXTAREA:
					$html = self::__decorate('<textarea' . self::__attributes($attributes) . '>'
						. ( isset($this->__fields[$id]['value']) ? $this->__fields[$id]['value'] : '' )
						. '</textarea>', self::$decorator_textarea ?: self::$decorator_fields);
					break;
			}
		}
		else
		{
			throw new \OutOfBoundsException('Field \'' . $id . '\' does not exist');
		}

		return $html;
	}

	/**
	 * Form data getter
	 *
	 * @param mixed $fields (optional, get single field value: 'field1',
	 *		or specific fields ex: ['field1', 'field3'],
	 *		or mapped fields ex: ['field_name' => 'custom_name', ...])
	 * @param boolean $return_object
	 * @return array (or object)
	 */
	public function getData($fields = null, $return_object = true)
	{
		if(is_array($fields) && count($fields) > 0)
		{
			$out = [];

			foreach($fields as $k => $v)
			{
				if(is_int($k)) // field
				{
					if(isset($this->__data[$v]))
					{
						$out[$v] = $this->__data[$v];
					}
				}
				else // map field
				{
					if(isset($this->__data[$k]))
					{
						$out[$v] = $this->__data[$k];
					}
				}

				if($return_object && isset($out[$v]) && is_array($out[$v]))
				{
					$out[$v] = (object)$out[$v];
				}
			}

			if($return_object)
			{
				$out = (object)$out;
			}

			return $out;
		}
		else if($fields === null) // get all
		{
			if(!$return_object)
			{
				return $this->__data;
			}

			$out = $this->__data;

			foreach($out as &$v)
			{
				if(is_array($v))
				{
					$v = (object)$v;
				}
			}

			return (object)$out;
		}
		else if(isset($this->__data[$fields])) // get single value
		{
			return $this->__data[$fields];
		}
	}

	/**
	 * Field error string getter
	 *
	 * @param string $id
	 * @param mixed $decorator (string when setting, or null)
	 * @return string
	 */
	public function getError($id, $decorator = null)
	{
		if(isset($this->__fields[$id]['error']))
		{
			return self::__decorate(array_values($this->__fields[$id]['error'])[0],
				$decorator ?: self::$decorator_error);
		}

		return '';
	}

	/**
	 * Field errors as string (or array) getter
	 *
	 * @param string $id
	 * @param mixed $decorator (string when setting, or null)
	 * @param boolean $return_array
	 * @return mixed (array|string)
	 */
	public function getErrors($id, $decorator = null, $return_array = false)
	{
		if($id === null) // get all
		{
			if($return_array)
			{
				$ret_arr = [];

				foreach($this->__fields as $v)
				{
					if(isset($v['error']))
					{
						$ret_arr = array_merge($ret_arr, $v['error']);
					}
				}

				return $ret_arr;
			}

			$html = '';

			foreach($this->__fields as $k => $v)
			{
				$html .= $this->getErrors($k, $decorator);
			}

			return $html;
		}

		if(isset($this->__fields[$id]['error']))
		{
			if($return_array)
			{
				return $this->__fields[$id]['error'];
			}

			$html = '';

			foreach($this->__fields[$id]['error'] as $v)
			{
				$html .= self::__decorate($v, $decorator ?: self::$decorator_errors);
			}

			return $html;
		}
	}

	/**
	 * Form ID field (listener) HTML getter
	 *
	 * @return string
	 */
	public function getFormIdField()
	{
		return $this->__isFormId() ? '<input type="hidden" name="' . $this->__form_id . '">' : '';
	}

	/**
	 * Add hidden field to form
	 *
	 * @param string $id
	 * @param mixed $default_value (string when setting, or null)
	 * @return \Drone\View\Form
	 */
	public function &hidden($id, $default_value = null)
	{
		$this->__addField(self::FIELD_HIDDEN, $id, $default_value);
		return $this;
	}

	/**
	 * Form field exists flag getter
	 *
	 * @param string $id
	 * @return boolean
	 */
	public function isField($id)
	{
		return $id !== null && isset($this->__fields[$id]);
	}

	/**
	 * Form has been submitted flag getter
	 *
	 * @return boolean
	 */
	public function isSubmitted()
	{
		return $this->__isFormId() ? isset($this->__data[$this->__form_id]) : !empty($this->__data);
	}

	/**
	 * Form fields values are valid
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		if(!$this->isSubmitted()) // no data
		{
			return false;
		}

		$is_valid = true;

		foreach($this->__fields as $k => &$f)
		{
			if(isset($f['rule']))
			{
				foreach($f['rule'] as $r => $v)
				{
					self::__validate($r, isset($this->__data[$k]) ? $this->__data[$k] : null, $f, $v, $is_valid);
				}
			}
		}

		return $is_valid;
	}

	/**
	 * Add password field to form
	 *
	 * @param string $id
	 * @return \Drone\View\Form
	 */
	public function &password($id)
	{
		$this->__addField(self::FIELD_PASSWORD, $id);
		return $this;
	}

	/**
	 * Add radio button field to form
	 *
	 * @param string $id
	 * @param array $options
	 * @param mixed $default_checked (int|string when setting, or null)
	 * @return \Drone\View\Form
	 */
	public function &radio($id, array $options, $default_checked = null)
	{
		$this->__addField(self::FIELD_RADIO, $id, $default_checked, $options);
		return $this;
	}

	/**
	 * Add select field to form
	 *
	 * @param string $id
	 * @param array $options
	 * @param mixed $default_selected (int|string when setting, or null)
	 * @return \Drone\View\Form
	 */
	public function &select($id, array $options, $default_selected = null)
	{
		$this->__addField(self::FIELD_SELECT, $id, $default_selected, $options);
		return $this;
	}

	/**
	 * Add text field to form
	 *
	 * @param string $id
	 * @param mixed $default_value (string when setting, or null)
	 * @return \Drone\View\Form
	 */
	public function &text($id, $default_value = null)
	{
		$this->__addField(self::FIELD_TEXT, $id, $default_value);
		return $this;
	}

	/**
	 * Add textarea field to form
	 *
	 * @param string $id
	 * @param mixed $default_value (string when setting, or null)
	 * @return \Drone\View\Form
	 */
	public function &textarea($id, $default_value = null)
	{
		$this->__addField(self::FIELD_TEXTAREA, $id, $default_value);
		return $this;
	}

	/**
	 * Add validation rule as callable to field
	 *
	 * @param \Drone\View\callable $validation_func
	 * @param string $error_message (optional)
	 * @return \Drone\View\Form
	 */
	public function &validate(callable $validation_func, $error_message = '')
	{
		$this->__addRule($validation_func, $error_message);
		return $this;
	}

	/**
	 * Add validate email rule to field
	 *
	 * @param string $error_message (optional)
	 * @return \Drone\View\Form
	 */
	public function &validateEmail($error_message = '')
	{
		$this->__addRule(Data::VALIDATE_EMAIL, $error_message);
		return $this;
	}

	/**
	 * Add validate length rule to field
	 *
	 * @param int $min
	 * @param int $max
	 * @param string $error_message (optional)
	 * @return \Drone\View\Form
	 * @throws \Exception (when min + max params are zero)
	 */
	public function &validateLength($min = 0, $max = 0, $error_message = '')
	{
		$min = (int)$min;
		$max = (int)$max;

		if($min < 1 && $max < 1)
		{
			throw new \Exception('Minimum length or maximum length must be greater than zero');
		}

		$this->__addRule(Data::VALIDATE_LENGTH, $error_message, $min > 0 && $max > 0 ?
			[Data::PARAM_MIN => $min, Data::PARAM_MAX => $max] : ( $min > 0 ? [Data::PARAM_MIN => $min]
				: [Data::PARAM_MAX => $max] ));
		return $this;
	}

	/**
	 * Add validate required rule to field
	 *
	 * @param string $error_message (optional)
	 * @return \Drone\View\Form
	 */
	public function &validateRequired($error_message = '')
	{
		$this->__addRule(Data::VALIDATE_REQUIRED, $error_message);
		return $this;
	}
}