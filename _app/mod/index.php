<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 0.1.7
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */

/**
 * Index controller
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */

// log example
logger()->debug('Index controller start');

// set params
view()->drone_ver = \Drone\Core::VERSION;
view()->drone_params = drone()->getAll();

// display view (displays '_app/tpl/index.tpl' when no template name)
view()->display();

// log example
logger()->debug('Index controller end');