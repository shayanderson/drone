# Drone
Rapid Development Framework for PHP 5.5.0+

#### Features
- Class Autoloading
- Routing (mapped and static)
- Error Handling
- Logging / Debugging
- Data Handling (filtering, formatting and validation)
- Session Handling
- Database Handling
- Filesystem Handling

#### Documentation Topics
- **[Quick Start](https://github.com/shayanderson/drone#quick-start)**
  - [Directory Structure](https://github.com/shayanderson/drone#directory-structure)
  - [Class Autoloading](https://github.com/shayanderson/drone#class-autoloading)
  - [Drone Function](https://github.com/shayanderson/drone#drone-function)
  - [Helper Functions](https://github.com/shayanderson/drone#helper-functions)
  - [Settings](https://github.com/shayanderson/drone#settings)
  - [Run Application](https://github.com/shayanderson/drone#run-application)
- **[Routes](https://github.com/shayanderson/drone#routes)**
  - [Static Routes](https://github.com/shayanderson/drone#static-routes)
  - [Mapped Routes](https://github.com/shayanderson/drone#mapped-routes)
- **[Controllers](https://github.com/shayanderson/drone#controllers)**
  - [Controller Class](https://github.com/shayanderson/drone#controller-class)
- **[Views](https://github.com/shayanderson/drone#views)**
  - [View Templates](https://github.com/shayanderson/drone#view-templates)
- **[Logging](https://github.com/shayanderson/drone#logging)**
  - [Log Levels](https://github.com/shayanderson/drone#log-levels)
  - [Log Configuration](https://github.com/shayanderson/drone#log-configuration)
  - [Custom Log Handler](https://github.com/shayanderson/drone#custom-log-handler)
- **[Error Handling](https://github.com/shayanderson/drone#error-handling)**
  - [Setting Error Handlers](https://github.com/shayanderson/drone#setting-error-handlers)

## Quick Start
To install Drone simply download the package and install in your project directory.

All of the Drone bootstrapping is done in the `index.php` file.

#### Directory Structure
By default Drone uses the following directory structure:
- **_app** (framework + application source files)
  - **com** (common application files)
  - **lib** (framework + application class files)
  - **mod** (controller files)
  - **tpl** (view template files)
    - **_global** (global view template files)
- **skin** (front asset files)

The directories for controllers (`_app/mod`), templates (`_app/tpl`) and global templates (`_app/tpl/_global`) can be changed using Drone settings.

#### Class Autoloading
Class autoloading is completed using the `autoload()` function in the `index.php` file, example:
```php
// set class autoloading paths
autoload([
	PATH_ROOT . '_app/lib',
	PATH_ROOT . '_app/mdl'
]);
```
In this example classes will be autoloaded from the `_app/lib` and the `_app/mdl` directories. The autoloader expects the use of namespaces, example:
```php
$myobj = new \Mylib\Myclass;
```
Would load the class `_app/lib/Mylib/Myclass.php` or `_app/mdl/Mylib/Myclass.php` (depending on where the class is located).

#### Drone Function
The `drone()` function can be used to easily access the Drone core class, example:
```php
drone()->trigger('myevent');
```

#### Helper Functions
Drone helper functions can be used to access Drone components easily, example of the `request()` helper function:
```php
$name = request()->post('name'); // get POST request value for 'name'
```
Drone helper functions available:
- **clear()** - clear param key/value pair (`drone()->clear()` alias)
- **error()** - trigger error (`drone()->error()` alias)
- **error_last()** - get last error (`drone()->errorLast()` alias)
- **filter()** - filter data (`drone()->data->filter()` alias)
- **flash()** - set flash message (`drone()->flash` alias)
- **format()** - format data (`drone()->data->format()` alias)
- **get()** - get param value (`drone()->get()` alias)
- **has()** - check if param exists (`drone()->has()` alias)
- **load_com()** - load common file
- **logger()** - `drone()->log` alias
- **pa()** - string/array printer
- **param()** - get route param (`drone()->param()` alias)
- **redirect()** - redirect to location (`drone()->redirect()` alias)
- **request()** - `drone()->request` alias
- **session()** - `drone()->session` alias
- **set()** - set param value (`drone()->set()` alias)
- **stop()** - stop application (`drone()->stop()` alias)
- **template()** - get template formatted name (`drone()->view->template()` alias)
- **template_global()** - get gloabl template formatted name (`drone()->view->templateGlobal()` alias)
- **validate()** - validate value (`drone()->data->validate()` alias)
- **view()** - `drone->view` alias

#### Settings
Drone can run without changing the default settings, however, the default settings should be changed when Drone is used in a production environment in the `index.php` file:
```php
// turn debug mode off - this will prevent unwanted output in a production environment
drone()->set(\Drone\Core::KEY_DEBUG, false);

// turn off backtrace in log - this should only be used in a development environment
drone()->set(\Drone\Core::KEY_ERROR_BACKTRACE, false);

// turn on logging of errors in the default Web server log file
drone()->set(\Drone\Core::KEY_ERROR_LOG, true);
```

#### Run Application
The last call in the `index.php` file should run the application:
```php
drone()->run();
```
Nothing should happen after this call as the output buffer has already ended.

To setup an application response simply create a new controller file in the `_app/mod` directory, for example `_app/mod/hello-world.php`:
```php
// display view template with auto template name
view()->display();
```
Next, create a view template `_app/tpl/hello-world.tpl`:
```html
Hello world
```
Finally, visit your Web application with request `/hello-world.htm` in a browser and you should see the `Hello world` text.

## Routes
There are two types of routes in Drone: *static* and *mapped*.

The Drone routing workflow is:

1. Check match for mapped route
2. Check for static route
3. Trigger 404 handler

#### Static Routes
Static routes require no mapping and instead rely on static file paths. For example, the application request `/hello-world.htm` will search for the controller file `_app/mod/hello-world.php`.

<blockquote>A missing static route file will trigger the 404 error handler</blockquote>

<blockquote>Static route lookups happen *after* mapped route lookups</blockquote>

#### Mapped Routes
Mapped routes require mapping in the `index.php` file, example:
```php
drone()->route([
	'/item-view' => 'item/view',
	'/item-delete/:id' => 'item/delete'
]);
```
In the example above Drone will map the request `/item-view.htm` to the controller file `_app/mod/item/view.php`. The next array element will map the request `/item-delete/14.htm` to the controller file `_app/mod/item/delete.php`, and Drone will map the route param `id` to value `14`.

Here is another example that uses Drone's `Controller` class logic:
```php
drone()->route([
	'/user/:id' => 'user->view',
	'/user/:id/delete' => 'user->delete'
]);
```
In this example the request `/user/5.htm` will be mapped to the controller file `_app/mod/user.php` with the route param `id` set to `5`. In this case the controller file `_app/mod/user.php` will need to contain the `Controller` class with the public method `view` (the action), for example:
```php
class Controller
{
	public function view()
	{
		$this->id = param('id'); // get route param value (5)
	}
}
```
Likewise the request `/user/5/delete.htm` will be mapped to the controller file `_app/mod/user.php` with the route param `id` set to `5` and call the `Controller` public class method `delete`.

<blockquote>A missing `Controller` class will trigger the 500 error handler</blockquote>

<blockquote>A missing `Controller` action (class public method) will trigger the 500 error handler</blockquote>

<blockquote>Mapped route lookups happen *before* static route lookups</blockquote>

## Controllers
Controllers are files that may or may not contain a `Controller` class depending on if the requested route is mapped, and mapped with an action (see [Mapped Routes](https://github.com/shayanderson/drone#mapped-routes)).

An example of a simple controller file is the default `_app/mod/index.php` controller:
```php
// log example
logger()->debug('Index controller start');

// set params
view()->drone_ver = \Drone\Core::VERSION;
view()->drone_params = drone()->getAll();

// display view (displays _app/tpl/index.tpl when no template name)
view()->display();

// log example
logger()->debug('Index controller end');
```
In the controller file serveral helper functions are called: `logger()` and `view()`. These helper functions access Drone core components (in this case `drone()->log` and `drone()->view`). So instead of calling `drone()->log->debug('x')` a helper function can be used (see more [Helper Functions](https://github.com/shayanderson/drone#helper-functions).

View variables can be set using the `view()` helper function, which accesses the `\Drone\Core\View` object, for example:
```php
view()->my_var = 'my value';
```
Now the variable `my_var` is accessible from the view template file.

> Controller files should never output anything (and outputs will be flushed when debug mode is off), instead output from view template files

#### Controller Class
When a route is mapped with an action (for example: `'/my/route' => 'controller->action'`) the controller file *must* contain a `Controller` class, otherwise a 500 error will be triggered.

Here is an example of a simple `Controller` class in a controller file:
```php
class Controller
{
	public function action()
	{
		logger()->debug('Controller action called');
		
		// action logic here
	}
}
```
In the mapped route example above the class method `action()` will be called for the request `/my/route.htm`.

> The `Controller` class can use two special methods:
> - `__before()` - called *before* the controller action method is called
> - `__after()` - called *after* the controller action method is called

<blockquote>Mapped route params are accessible from the `param()` helper function (example: `param('id')`)</blockquote>

<blockquote>The `Controller` class constant `DENY` will deny all static requests (or mapped requests with no action)</blockquote>

## Views
The Drone `\Drone\Core\View` object handles all view logic like view variables and template path formatting.

The view object is accessible via the `view()` helper function.

View variables (or properties) are set in controller files, for example:
```php
view()->my_var = 'my value';
view()->another_var = 'another value';
view()->display(); // display template file
```

The `view()->display()` method is used to display a template file. If `view()->display()` is not called then no view will be displayed (no output buffer).

When the view display method is called from the controller it will automatically display a similarly named template file, for example, the controller file `_app/mod/test-controller.php` will display the `_app/tpl/test-controller.tpl` when `view()->display()` is called.

To assign a custom view template file use a template name, for example:
```php
// display template file '_app/tpl/my-dir/my-template.tpl'
view()->display('my-dir/my-template');
```

> Other useful view methods:
> - `clearProperties()` - clears all view variables/properties
> - `getProperties()` - get array of all view variables/properties

#### View Templates
Now the variables set in the view example above are accessed in the view template file like:
```html+php
Value for 'my_var' is: <?=$my_var?> <br />
Value for 'another_var' is: <?=$another_var?>
```
Which would output:
```html
Value for 'my_var' is: my value
Value for 'another_var' is: another value
```

*Template global* files can be included using the `template_global()` helper function, for example:
```html+php
<?php include template_global('header'); ?>
Some body text
<?php include template_global('footer'); ?>
```
This example includes the global template files `_app/tpl/_global/header.tpl` and `_app/tpl/_global/footer.tpl`

> The helper function `template()` can be used to include non-global template files

## Logging
The `\Drone\Core\Logger` object is used for logging and accessed using the `logger()` helper function.

Log a simple application message example:
```php
logger()->debug('My log message'); // log message with debug level
```

#### Log Levels
Drone uses the following logging methods for the logging levels: *debug*, *warn*, *error* and *fatal*:

- `logger()->debug()` - debugging messages
- `logger()->warn()` - warning messages
- `logger()->error()` - error messages (non-fatal)
- `logger()->fatal()` - fatal error messages

> The `logger()->trace` method is used by the framework for debugging purposes

#### Log Configuration
Logging configuration is done in the `index.php` file.

To set the global *logging level* use:
```php
drone()->log->setLogLevel(\Drone\Core\Logger::LEVEL_DEBUG);
```
This means only messages with the *debug* level or higher will be logged.

To set a log file where log messages will be outputted to use something like:
```php
drone()->log->setLogFile('_app/var/drone.log');
```
This will output log messages to the log file `_app/var/drone.log`.

> Using a log file is *not* recommended for production environments

#### Custom Log Handler
Setting a custom log handler is simple, for example:
```php
drone()->log->setLogHandler(function($message, $level, $category) {
	pdom('drone_log:add', ['message' => $message, 'level' => $level, 'category' => $category]);
	return false;
});
```
In the above example a custom log handler has been set and allows the log messages to be saved in the database table *drone_log*.

> If a custom log handler is set and returns boolean value `false` Drone will continue on with the default logging logic (caching log messages and writing to a log file if configured), however, if `true` is returned by the log handler Drone will stop the default logging processes.

## Error Handling
Errors can be triggered using the `error()` helper function, here is an example:
```php
if($something_bad)
{
	// trigger 500 error handler
	error('Something bad happened');
}
```
Errors can also be triggered using error codes, for example a *404 Not Found*:
```php
error(404, 'This page is not found'); // trigger 404 error handler
```
Or, use custom error codes (cannot be `403`, `404` or `500` as these are used by Drone):
```php
error(100, 'My custom error'); // trigger 100 error handler
```

> A custom error code will attempt to trigger a custom error handler, if the handler is not found the `500` error handler will be triggered

#### Setting Error Handlers
By default at least three errors handlers should be set in the `index.php` file: a *default* error handler, a *404* error handler and a *500* error handler, example:
```php
drone()->error(function($error) { pa('<div style="color:#f00;">' . $error,
	debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), '</div>'); });
drone()->error(404, function() { drone()->run('error->_404'); });
drone()->error(500, function() { drone()->run('error->_500'); });
```

The *default* error handler will be called when errors are triggered inside the application (like E_USER_ERROR, E_USER_WARNING, etc.). This happens because of the default Drone error handler.
> The default Drone error handler does not need to be changed, but it can be changed using:
> ```php
> drone()->set(\Drone\Core::KEY_ERROR_HANDLER, ['\My\Class', 'errorHandlerMethod']);
> ```

Custom error handlers can also be set, for example:
```php
drone()->error(100, function() { drone()->run('error->_100'); }
```
Now if a `100` error is triggered the handler would call the controller action method `_100()` in the `_app/mod/error.php` controller file.

> The `error_last()` helper function can be used to get the last error message.








