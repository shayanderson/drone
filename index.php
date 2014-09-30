<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5+
 *
 * @package Drone
 * @version 0.1.9
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */

//////////////////////////////////////////////////////////////////////////
// Load Drone Framework + run application
//////////////////////////////////////////////////////////////////////////
// set root path
define('PATH_ROOT', __DIR__ . '/');

// include Drone common functions
require_once './_app/lib/Drone/com.php';

// set class autoloading paths
autoload([
	PATH_ROOT . '_app/lib',
	// PATH_ROOT . '_app/mdl'
]);

// include app/Drone bootstrap
require_once './_app/com/app.bootstrap.php';

// run application (execute last)
drone()->run();