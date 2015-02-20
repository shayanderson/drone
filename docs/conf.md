## \Drone\Conf

The `\Drone\Conf` helper class can be used to easily turn config arrays or files (that return arrays) into config objects.

### File Example
Here is an example of how to use with config file. First, setup the config file `_app/com/conf.php` to return array
```php
return [
	'core_debug' => false,
	'app' => [
		'ver' => 1.2,
		'key' => 'xyz',
		'debug' => true
	]
];
```
Now use the `\Drone\Conf` class to convert to usable config object:
```php
$conf = \Drone\Conf::file(PATH_ROOT . '_app/com/conf.php');
// now config settings can be used like:
echo 'App key is: ' . $conf->app->key; // 'xyz'
```

### Array Example
An array can be used instead of a file returning an array, for example:
```php
$conf = \Drone\Conf::get([
	'core_debug' => false,
	'app' => [
		'ver' => 1.2,
		'key' => 'xyz',
		'debug' => true
	]
]);

echo 'App key is: ' . $conf->app->key; // 'xyz'
```