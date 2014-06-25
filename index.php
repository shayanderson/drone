<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 1.0.1.b
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */

//////////////////////////////////////////////////////////////////////////
// Loading
//////////////////////////////////////////////////////////////////////////
// set root path
define('PATH_ROOT', __DIR__ . '/');

// include common functions
require_once './_app/lib/Drone/com/core.php';

// set class autoloading paths
autoload([
	PATH_ROOT . '_app/lib',
	// PATH_ROOT . '_app/mdl'
]);

// include helper functions
require_once './_app/lib/Drone/com/helper.php';

// include PDOm (MySQL database) support (optional)
require_once './_app/com/pdom.bootstrap.php';


//////////////////////////////////////////////////////////////////////////
// Logging
//////////////////////////////////////////////////////////////////////////
// set logging level
drone()->log->setLogLevel(\Drone\Core\Logger::LEVEL_TRACE);

// log to file example: (not recommended for production)
// drone()->log->setLogFile('_app/var/drone.log');

// custom user defined log handler that logs to database example:
// drone()->log->setLogHandler(function($message, $level, $category) {
//	pdom('drone_log:add', ['message' => $message, 'level' => $level, 'category' => $category]);
//	return true; });


//////////////////////////////////////////////////////////////////////////
// Settings
//////////////////////////////////////////////////////////////////////////
// framework settings
// drone()->set(\Drone\Core::KEY_DEBUG, false); // debug mode - off for production
// drone()->set(\Drone\Core::KEY_ERROR_BACKTRACE, false); // backtrace in log - off for production
// drone()->set(\Drone\Core::KEY_ERROR_LOG, true); // errors to server log - on for production


//////////////////////////////////////////////////////////////////////////
// Error handlers
//////////////////////////////////////////////////////////////////////////
drone()->error(function($error) { pa('<div style="color:#f00;">' . $error,
	debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), '</div>'); });
drone()->error(404, function() { drone()->run('error->\ErrorController->_404'); });
drone()->error(500, function() { drone()->run('error->\ErrorController->_500'); });


//////////////////////////////////////////////////////////////////////////
// Hooks
//////////////////////////////////////////////////////////////////////////
// after hook that displays log example:
drone()->hook(\Drone\Core::HOOK_AFTER, function() { pa('', 'Log:', drone()->log->get()); });


//////////////////////////////////////////////////////////////////////////
// Mapped routes
//////////////////////////////////////////////////////////////////////////
// example: drone()->route(['/user/:id' => 'user->view']);


//////////////////////////////////////////////////////////////////////////
// Events
//////////////////////////////////////////////////////////////////////////
// example: drone()->event('user.delete', function(User $user) { return $user->delete(); });


//////////////////////////////////////////////////////////////////////////
// Filter request variables
//////////////////////////////////////////////////////////////////////////
// auto trim GET + POST values example:
// drone()->request->filter(\Drone\Core\Request::TYPE_GET | \Drone\Core\Request::TYPE_POST,
//		function($v) { return \Drone\Core\Data::filterTrim($v); });


//////////////////////////////////////////////////////////////////////////
// Flash message templates
//////////////////////////////////////////////////////////////////////////
// example: \Drone\Core\Flash::template('error', '<div class="error">{$message}</div>');

// run application (execute last)
drone()->run();