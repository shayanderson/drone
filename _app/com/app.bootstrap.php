<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5+
 *
 * @package Drone
 * @version 0.2.0
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */

/**
 * Application + Drone Bootstrap
 */

//////////////////////////////////////////////////////////////////////////
// Logging
//////////////////////////////////////////////////////////////////////////
// set logging level
logger()->setLogLevel(\Drone\Logger::LEVEL_TRACE);
// logger()->setLogFile(PATH_ROOT . '_app/var/drone.log'); // set log file (optional)


//////////////////////////////////////////////////////////////////////////
// Settings
//////////////////////////////////////////////////////////////////////////
// framework settings
// set(\Drone\Core::KEY_DEBUG, false); // debug mode - off for production
// set(\Drone\Core::KEY_ERROR_BACKTRACE, false); // backtrace in log - off for production
// set(\Drone\Core::KEY_ERROR_LOG, true); // errors to server log - on for production


//////////////////////////////////////////////////////////////////////////
// Error handlers
//////////////////////////////////////////////////////////////////////////
error(function($error) { pa('<div style="color:#f00;">' . $error,
	debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), '</div>'); });
error(404, function() { drone()->run('error->\ErrorController->_404'); });
error(500, function() { drone()->run('error->\ErrorController->_500'); });


//////////////////////////////////////////////////////////////////////////
// Hooks
//////////////////////////////////////////////////////////////////////////
// after hook that displays log example:
drone()->hookAfter(function() { pa('', 'Log:', drone()->log->get()); });


//////////////////////////////////////////////////////////////////////////
// Mapped routes
//////////////////////////////////////////////////////////////////////////
// example: drone()->route(['/user/:id' => 'user->view']);