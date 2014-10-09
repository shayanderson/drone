<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5+
 *
 * @package Drone
 * @version 0.2.0
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone\View;

/**
 * View Breadcrumb class - HTML breadcrumb helper class
 *
 * @author Shay Anderson 07.14 <http://www.shayanderson.com/contact>
 */
class Breadcrumb
{
	/**
	 * Item keys
	 */
	const
		KEY_TITLE = 0,
		KEY_URL = 1;

	/**
	 * Base items
	 *
	 * @var array
	 */
	private static $__base = [];

	/**
	 * Items
	 *
	 * @var array
	 */
	private $__items = [];

	/**
	 * Callable filter for all titles
	 *
	 * @var callable
	 */
	public static $filter_title;

	/**
	 * Callable filter for all URLs
	 *
	 * @var callable
	 */
	public static $filter_url;

	/**
	 * Breadcrumb item template
	 *
	 * @var string (ex: '<a href="{$url}">{$title}</a>')
	 */
	public static $template = '<a href="{$url}">{$title}</a>';

	/**
	 * Active breadcrumb item template
	 *
	 * @var string (ex: '{$title}')
	 */
	public static $template_active = '{$title}';

	/**
	 * Breadcrumb item separator
	 *
	 * @var string
	 */
	public static $separator = ' &raquo; ';

	/**
	 * Breadcrumb wrapper after items
	 *
	 * @var string (ex: '</div>')
	 */
	public static $wrapper_after;

	/**
	 * Breadcrumb wrapper before items
	 *
	 * @var string (ex: '<div class="breadcrumb'>')
	 */
	public static $wrapper_before;

	/**
	 * Init
	 *
	 * @param mixed $breadcrumbs (optional, array setter ex: ['/url.htm' => 'Title', 'Current Page'])
	 */
	public function __construct($items = null)
	{
		if(is_array($items))
		{
			$this->add($items);
		}
	}

	/**
	 * Breadcrumb string getter
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->get();
	}

	/**
	 * Add breadcrumb item
	 *
	 * @param mixed $title (string when setter or array for multiple add ex: ['/url.htm' => 'Title', 'Current Page'])
	 * @param mixed $url (string when setter, null when no URL (active item))
	 * @return void
	 */
	public function add($title, $url = null)
	{
		if(is_array($title)) // add array of items
		{
			foreach($title as $k => $v)
			{
				$this->add($v, $k);
			}
			return;
		}

		if($url === null || is_int($url)) // auto active
		{
			$this->__items[] = [self::KEY_TITLE => $title];
		}
		else
		{
			$this->__items[] = [self::KEY_TITLE => $title, self::KEY_URL => $url];
		}
	}

	/**
	 * Base item(s) setter
	 *
	 * @param mixed $title (string when setter or array for multiple add ex: ['/url.htm' => 'Title', 'Current Page'])
	 * @param string $url
	 */
	public static function base($title, $url)
	{
		if(is_array($title))
		{
			self::$__base = $title;
		}
		else
		{
			self::$__base[] = [self::KEY_TITLE => $title, self::KEY_URL => $url];
		}
	}

	/**
	 * Breadcrumb string getter
	 *
	 * @param boolean $use_wrapper (use before/after wrapper in string)
	 * @return string
	 */
	public function get($use_wrapper = true)
	{
		$str = '';

		if(count($this->__items) > 0)
		{
			$i = 0;
			foreach(array_merge(self::$__base, $this->__items) as $v)
			{
				if($i > 0)
				{
					$str .= self::$separator;
				}

				if(is_callable(self::$filter_title))
				{
					$v[self::KEY_TITLE] = call_user_func(self::$filter_title, $v[self::KEY_TITLE]);
				}

				if(isset($v[self::KEY_URL])) // non-active item
				{
					if(is_callable(self::$filter_url))
					{
						$v[self::KEY_URL] = call_user_func(self::$filter_url, $v[self::KEY_URL]);
					}
					
					$str .= str_replace('{$url}', $v[self::KEY_URL], str_replace('{$title}', $v[self::KEY_TITLE],
						self::$template));
				}
				else // active item
				{
					$str .= str_replace('{$title}', $v[self::KEY_TITLE], self::$template_active);
				}

				$i++;
			}

			return $use_wrapper ? self::$wrapper_before . $str . self::$wrapper_after : $str;
		}

		return '';
	}

	/**
	 * Items getter
	 *
	 * @return array
	 */
	public function getItems()
	{
		return $this->__items;
	}
}