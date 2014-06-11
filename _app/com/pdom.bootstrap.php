<?php
/**
 * PDOm bootstrap
 */

// include PDOm function
require_once PATH_ROOT . '_app/lib/Pdom/pdom.php';

// register database connection
pdom([
	// database connection params
	'host' => 'localhost',
	'database' => 'test',
	'user' => 'myuser',
	'password' => 'mypass',

	// display errors (default true)
	'errors' => true,

	// debug messages and errors to log (default false)
	'debug' => true,

	// return objects instead of arrays (default true)
	// 'objects' => false
]);