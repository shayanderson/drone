<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 1.0.0.b
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */

/**
 * Error controller class
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class ErrorController extends \Drone\Controller
{
	/**
	 * Finalize action
	 *
	 * @return void
	 */
	public function __after()
	{
		view()->display();
	}

	/**
	 * 404 not found action
	 *
	 * @return void
	 */
	public function _404()
	{
		view()->error_code = 404;
		view()->error_message = '404 error';
	}

	/**
	 * 500 error action
	 */
	public function _500()
	{
		view()->error_code = 500;
		view()->error_message = '500 error<br /><br />' . error_last();
	}
}