<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 0.2.3
 * @copyright 2015 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @link <https://github.com/shayanderson/drone>
 */
namespace Drone;

// deny static requests (or mapped requests with no action)
drone()->deny();

/**
 * Drone abstract base Controller class
 *
 * @author Shay Anderson 06.14 <http://www.shayanderson.com/contact>
 */
abstract class Controller
{
	// nothing
}