# Drone
### Rapid Development Framework for PHP 5.5.0+

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
- **[Core Methods](https://github.com/shayanderson/drone#core-methods)**
  - [Parameters](https://github.com/shayanderson/drone#parameters)
  - [Route Parameters](https://github.com/shayanderson/drone#route-parameters)
  - [Events](https://github.com/shayanderson/drone#events)
  - [Hooks](https://github.com/shayanderson/drone#hooks)
  - [Redirect](https://github.com/shayanderson/drone#redirect)
  - [Headers](https://github.com/shayanderson/drone#headers)
  - [Timers](https://github.com/shayanderson/drone#timers)
  - [Stopping the Application](https://github.com/shayanderson/drone#stopping-the-application)
- **[Request Variables](https://github.com/shayanderson/drone#request-variables)**
- **[Session Handling](https://github.com/shayanderson/drone#session-handling)**
  - [Flash Messages](https://github.com/shayanderson/drone#flash-messages)
- **[Data Handling](https://github.com/shayanderson/drone#data-handling)**
  - [Filter](https://github.com/shayanderson/drone#filter)
  - [Format](https://github.com/shayanderson/drone#format)
  - [Validate](https://github.com/shayanderson/drone#validate)
- **[Filesystem](https://github.com/shayanderson/drone#filesystem)**
  - [Directory](https://github.com/shayanderson/drone#directory)
  - [File](https://github.com/shayanderson/drone#file)
- **[Database Handling](https://github.com/shayanderson/drone#database-handling)**

## Quick Start
To install Drone simply download the package and install in your project directory. For Apache use the `./.htaccess` file, for Nginx refer to the `./nginx.conf` example configuration file.

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
- [`clear()`](https://github.com/shayanderson/drone#parameters) - clear param key/value pair (`drone()->clear()` alias)
- [`error()`](https://github.com/shayanderson/drone#error-handling) - trigger error (`drone()->error()` alias)
- [`error_last()`](https://github.com/shayanderson/drone#error-handling) - get last error (`drone()->errorLast()` alias)
- [`filter()`](https://github.com/shayanderson/drone#filter) - filter data (`drone()->data->filter()` alias)
- [`flash()`](https://github.com/shayanderson/drone#flash-messages) - set flash message (`drone()->flash` alias)
- [`format()`](https://github.com/shayanderson/drone#format) - format data (`drone()->data->format()` alias)
- [`get()`](https://github.com/shayanderson/drone#parameters) - get param value (`drone()->get()` alias)
- [`has()`](https://github.com/shayanderson/drone#parameters) - check if param exists (`drone()->has()` alias)
- `load_com()` - load common file
- [`logger()`](https://github.com/shayanderson/drone#logging) - `drone()->log` alias
- `pa()` - string/array printer
- [`param()`](https://github.com/shayanderson/drone#route-parameters) - get route param (similar to `view()->param()`)
- [`redirect()`](https://github.com/shayanderson/drone#redirect) - redirect to location (`drone()->redirect()` alias)
- [`request()`](https://github.com/shayanderson/drone#request-variables) - `drone()->request` alias
- [`session()`](https://github.com/shayanderson/drone#session-handling) - `drone()->session` alias
- [`set()`](https://github.com/shayanderson/drone#parameters) - set param value (`drone()->set()` alias)
- [`template()`](https://github.com/shayanderson/drone#view-templates) - get template formatted name (`drone()->view->template()` alias)
- [`template_global()`](https://github.com/shayanderson/drone#view-templates) - get global template formatted name (`drone()->view->templateGlobal()` alias)
- [`validate()`](https://github.com/shayanderson/drone#validate) - validate value (`drone()->data->validate()` alias)
- [`view()`](https://github.com/shayanderson/drone#views) - `drone->view` alias

> By default Drone helper functions are included in the application, if function name collisions occur with other source code do not include the helper functions file `_app/lib/Drone/com/helper.php`.

#### Settings
Drone can run without changing the default settings, however, the default settings should be changed in the `index.php` file when Drone is used in a production environment:
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

<blockquote>Static route lookups happen <i>after</i> mapped route lookups</blockquote>

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
class Controller extends \Drone\Controller
{
	public function view()
	{
		$this->id = param('id'); // get route param value (5)
	}
}
```
Likewise the request `/user/5/delete.htm` will be mapped to the controller file `_app/mod/user.php` with the route param `id` set to `5` and call the `Controller` public class method `delete`.

Custom controller class names can be used, for example:
```php
drone()->route([
	'/user/:id' => 'user->\UserController->view',
	'/user/:id/delete' => 'user->\UserController->delete'
]);
```
Now the request `/user/5.htm` will be mapped to the controller file `_app/mod/user.php` and will need to contain the `UserController` class with public method `view`.

> Mapped routes can also use callables, for example:
```php
drone()->route([
	'/account/:id' => ['user->\AccountController->view', 
		function() { drone()->trigger('acl.auth'); }],
]);
// or can be used like:
// drone()->route('/account/:id', 'user->\AccountController->view', 
//		function() { drone()->trigger('acl.auth'); });
```
This example uses a route callable that will be called *after* the *before* hook and called *before* the controller file is loaded.

<blockquote>A missing <code>Controller</code> class will trigger the 500 error handler</blockquote>

<blockquote>A missing <code>Controller</code> action (class public method) will trigger the 500 error handler</blockquote>

<blockquote>Mapped route lookups happen <i>before</i> static route lookups</blockquote>

##### Optional Route Parameters
*Optional* route params can be used, for example:
```php
drone()->route([
	'/product/:category_id/:id?' => 'product->view',
]);
```
Now the request `/product/14.htm` will route to the controller file with the param `category_id` with value `14`. Likewise, the request `/product/14/5.htm` will route to the controller file with the params `category_id` with value `14`, and the `id` param with value `5`, for example:
```php
$cat_id = param('category_id');
if(param('id') !== false) // test if exists
{
	$id = param('id');
}
```

##### Wildcard Route Parameters
*Wildcard* route params can be used, for example:
```php
drone()->route([
	'/user/*' => 'user->view',
]);
```
Now the request `/user/a/b/c.htm` will be mapped to the controller file with action and all params will become available, for example:
```php
$params = param(0, 1, 2); // ['a', 'b', 'c']
// or set a single value
$param = param(1); // 'b'
```
Wildcard route param labels can also be used, for example
```php
drone()->route([
	'/product/*(:category/:subcat/:id)' => 'product->view',
]);
```
Now the params can be accessed using the param labels, for example the request `/product/category1/category2/4.htm` would be used like:
```php
$category = param('category'); // 'category1', alias: param(0)
$subcategory = param('subcat'); // 'category2', alias: param(1)
$id = param('id'); // '4', alias: param(2)
```

> *Duplicate Content Protection* <br />
A request mapped to a route with *optional* or *wildcard* params *must* end in '/' when not using params in the request, for example route `/route.htm` would result in a 404 error, but `/route/` will work.

> Likewise, a request mapped to a route with *optional* or *wildcard* route params must *not* end in '/' when using params, for example route `/route/x/y/z/` would result in a 404 error, but `/route/x/y/z.htm` will work.

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
In the controller file several helper functions are called: `logger()` and `view()`. These helper functions access Drone core components (in this case `drone()->log` and `drone()->view`). So instead of calling `drone()->log->debug('x')` a helper function can be used (see more [Helper Functions](https://github.com/shayanderson/drone#helper-functions)).

View variables can be set using the `view()` helper function, which accesses the `\Drone\Core\View` object, for example:
```php
view()->my_var = 'my value';
```
Now the variable `$my_var` is accessible from the view template file.

> Controller files should never output anything (and outputs will be flushed when debug mode is off), instead output from view template files

#### Controller Class
When a route is mapped with an action (for example: `'/my/route' => 'route->action'`) the controller file *must* contain a `Controller` class (or a custom controller class if used, see [Mapped Routes](https://github.com/shayanderson/drone#mapped-routes)), otherwise a 500 error will be triggered.

Here is an example of a simple `Controller` class in a controller file:
```php
class Controller extends \Drone\Controller
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

<blockquote>Mapped route params are accessible from the <code>param()</code> helper function (example: <code>param('id')</code>)</blockquote>

> It is recommended that `Controller` classes extend the `\Drone\Controller` class, this is because the `\Drone\Controller` will automatically deny static requests to the controller file (or mapped requests with no action).

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
> - `view()->clearProperties()` - clears all view variables/properties
> - `view()->getProperties()` - get array of all view variables/properties

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
A category can also be used when logging a message, for example:
```php
logger()->debug('User login successful', 'account'); // log message with category 'account'
```
> The default category `app` is used when no category has be set

Data (as an array) can also be passed to the log handler using the `data()` method:
```php
logger()->data([1, 2, 3]);
logger()->debug('My message with data');
```
Now the message will include the data as a flattened string.

#### Log Levels
Drone uses the following logging methods for the logging levels: *debug*, *warn*, *error* and *fatal*:

- `logger()->debug()` - debugging messages
- `logger()->warn()` - warning messages
- `logger()->error()` - error messages (non-fatal)
- `logger()->fatal()` - fatal error messages

> The `logger()->trace` method is used by the framework for debugging purposes

#### Log Configuration
Logging configuration is done in the `index.php` file.

To set the global *log level* use:
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
drone()->log->setLogHandler(function($message, $level, $category, $data) {
	pdom('drone_log:add', ['message' => $message, 'level' => $level, 'category' => $category, 
		'data' => serialize($data)]);
	return true;
});
```
In the above example a custom log handler has been set and allows the log messages to be saved in the database table *drone_log* using the [`pdom()`](https://github.com/shayanderson/drone#database-handling) database function.

If a custom log handler is set and returns boolean value `false` Drone will continue on with the default logging logic (caching log messages and writing to a log file if configured), however, if `true` is returned by the log handler Drone will stop the default logging processes.

> Other useful logger methods:
> - `logger()->get()` - gets log as array
> - `logger()->getString()` - gets log as string
> - `logger()->setDateFormat()` - set log message date format

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
error(404); // trigger 404 error handler
```
Or, use custom error codes (cannot be `403`, `404` or `500` as these are used by Drone):
```php
error(100, 'My custom error'); // trigger 100 error handler
```

<blockquote>A custom error code will attempt to trigger a custom error handler, if the handler is not found the <code>500</code> error handler will be triggered</blockquote>

<blockquote>Errors are automatically sent to the <code>\Drone\Core\Logger</code> object to be logged</blockquote>

#### Setting Error Handlers
By default at least three errors handlers should be set in the `index.php` file: a *default* error handler, a *404* error handler and a *500* error handler, example:
```php
drone()->error(function($error) { echo '<div style="color:#f00;">' . $error . '</div>'; });
drone()->error(404, function() { drone()->run('error->\ErrorController->_404'); });
drone()->error(500, function() { drone()->run('error->\ErrorController->_500'); });
```

The *default* error handler will be called when errors are triggered inside the application (like E_USER_ERROR, E_USER_WARNING, etc.). This happens because of the default Drone error handler.
> The default Drone error handler does not need to be changed, but it can be changed using:
> ```php
> drone()->set(\Drone\Core::KEY_ERROR_HANDLER, ['\My\Class', 'errorHandlerMethod']);
> ```

Custom error handlers can also be set, for example:
```php
drone()->error(100, function() { drone()->run('error->\ErrorController->_100'); }
```
Now if a `100` error is triggered the handler would call the controller action method `_100()` in the `_app/mod/error.php` controller file.

> The `error_last()` helper function can be used to get the last error message.

## Core Methods
There are Drone core (`\Drone\Core`) methods that are available for application use.

#### Parameters
Application parameters, or *params*, can be useful for global variables and objects. Params can be managed using the following helper functions or methods:
- `clear()` - clear param (drone()->clear() alias)
- `get()` - get param value (drone()->get() alias)
- `drone()->getAll()` - get all params as array
- `has()` - check if param exists (drone()->has() alias)
- `set()` - set param value (drone()->set() alias)
Param example:
```php
set('user', new \User($user_id));
...
if(get('user')->isActive())
{
	// do something
}
```

> Drone uses some params for internal use, these param keys all share the prefix `__DRONE__.`, for example a Drone param is `__DRONE__.error.backtrace`

#### Route Parameters
Route parameters, or *route params*, are used to extract route param values. For example, for the mapped route `'/route/:id' => 'route->action'` the param `id` will be available as a route param, controller example:
```php
$id = param('id'); // get route param 'id'
```
To verify a route param exists check for the boolean value `false`:
```php
if(param('id') === false)
{
	// the param 'id' does not exist
}
```
Multiple params can also be fetched, for example:
```php
$params = param('id', 'name'); // ['id' => 'x', 'name' => 'y']
```
All params can be fetched using:
```php
$params = param(null); // ['id' => 'x', 'name' => 'y', ...]
// or count all params:
if(count(params(null)) > 2) // more than 2 params
```
> [*Optional*](https://github.com/shayanderson/drone#optional-route-parameters) and [*wildcard*](https://github.com/shayanderson/drone#wildcard-route-parameters) route params are also available

#### Events
Events are global callables that can be accessed from the application. Register an event example in `index.php`:
```php
drone()->event('cart.add', function(\Cart\Item $item) { return get('cart')->add($item); });
```
Now in any controller the event can be trigger:
```php
if(drone()->trigger('cart.add', $item)) // trigger event
{
	// alert user
	flash('alert.cart.add', 'Item ' . $item->sku . ' has been added to cart');
}
```

> Events support any number of function params, for example: `drone()->trigger('my_event', x, y, z)`

#### Hooks
Hooks can be used to initialize or finalize an application. The two types of hooks are: *before* (triggered before the controller file is imported) and *after* (triggered after the controller file is imported and action called when action exists).

Example of *before* and *after* hooks set in `index.php`:
```php
// call function to init application logic
drone()->hook(\Drone\Core::HOOK_BEFORE, function() { initAppLogic(); });
// print Drone log
drone()->hook(\Drone\Core::HOOK_AFTER, function() { pa('', 'Log:', drone()->log->get()); });
```

> For controller level hooks (special methods `__before()` and `__after`) see [Controller Class](https://github.com/shayanderson/drone#controller-class)

#### Redirect
Redirection to another location can be done in controller files use the `redirect()` (`\Drone\Core->redirect()` alias) function, for example:
```php
redirect('/new/route.htm'); // redirect
```
If the redirection is a permanent (301) redirect use:
```php
redirect('/forever/route.htm', true); // redirect with 301
```

#### Headers
HTTP headers can be sent in controller files using the `drone()->header()` header method, for example:
```php
drone()->header('Cache-Control', 'no-cache, must-revalidate');
```
> For redirection to another location use the helper function [redirect()](https://github.com/shayanderson/drone#redirect) instead of the header method

#### Timers
The `drone()->timer()` method can be used for timers, for example in a controller file:
```php
$elapsed_time = drone()->timer(); // 0.00060
// do some heavy lifting
$elapsed_time = drone()->timer(); // 0.00071
```
Also custom timers can be used, for example:
```php
drone()->timer('my_job'); // start timer at 0
// do some heavy lifting
$elapsed_time = drone()->timer('my_job'); // 0.00014
```

#### Stopping the Application
If the application needs to be stopped in a controller file it can be done manually:
```php
drone()->stop(); // the application will stop
```
<blockquote>The <code>drone()->stop()</code> method does not need to be called unless a forced stop is desired (Drone will automatically call <code>drone()->stop()</code> after executing the request, triggering an error or redirecting)</blockquote>

<blockquote><i>After</i> hooks are triggered during a forced application stop, but the <code>Controller</code> method <code>__after()</code> will not be called</blockquote>

## Request Variables
Request variables can be accessed using the `request()` helper function (which uses the `\Drone\Core\Request` object), for example:
```php
$name = request()->get('name'); // get value from GET variable 'name'
```
Methods used to get request variables:
- `request()->cookie()` - `$_COOKIE` alias
- `request()->get()` - `$_GET` alias
- `request()->evn()` - `$_ENV` alias
- `request()->file()` - `$_FILES` alias
- `request()->get()` - `$_GET` alias
- `request()->post()` - `$_POST` alias
- `request()->request()` - `$_REQUEST` alias
- `request()->server()` - `$_SERVER` alias

> *Get* methods can also fetch multiple variables using an array, example:
```php
$vars = request()->get(['var1', 'var2', 'var3']); // ['var1' => x, 'var2' => y, 'var3' => z]
```

Methods used to check if request variables exist:
- `request()->hasCookie()`
- `request()->hasFile()`
- `request()->hasGet()`
- `request()->hasPost()`
- `request()->hasRequest()`

> *Has* methods can be used to check if multiple variables exists using an array, example:
```php
if(request()->hasPost(['var1', 'var2', 'var3'])) // true or false
```

Methods used to remove request variables:
- `request()->removeCookie()`
- `request()->removeGet()`
- `request()->removePost()`
- `request()->removeRequest()`

Request variable values can be globally sanitized using the `request()->filter()` method, for example:
```php
// auto trim all GET + POST variable values
request()->filter(\Drone\Core\Request::TYPE_GET | \Drone\Core\Request::TYPE_POST,
	function($v) { return trim($v); });
```

Cookies are easy to set using:
```php
// set cookie 'my_cookie' that will expire in 10 days
request()->setCookie('my_cookie', 'cookie value', '+10 days');
```

> Other useful request methods:
> - `request()->getHost()`
> - `request()->getIpAddress()`
> - `request()->getMethod()` - get the request method
> - `request()->getPort()`
> - `request()->getProtocol()`
> - `request()->getQueryString()`
> - `request()->getReferrer()`
> - `request()->getSchema()`
> - `request()->getUri()`
> - `request()->isAjax()` - check if Ajax request
> - `request()->isPost()` - check if POST request method
> - `request()->isSecure()` - check if HTTPS request

## Session Handling
Sessions are handled with the `\Drone\Core\Session` object and accessed using the `session()` helper function, example:
```php
session()->set('my_key', 'my value');
...
if(session()->has('my_key'))
{
	$key = session()->get('my_key');
}
```

> The session handler will automatically start a session (if not already started) when the `session()` helper function is used in the application

Using array values in sessions are simple:
```php
session()->add('user', 'id', $user_id);
...
if(session()->has('user', 'id'))
{
	$user_id = session()->get('user', 'id');
}
```

> Other useful session methods:
> - `session()->clear()` - clear a session variable
> - `session()->count()` - used to get count of session array variable
> - `session()->destroy()` - destroy a session
> - `session()->flush()` - flush all session variables
> - `session()->getId()` - get session ID
> - `session()->isArray()` - check if session variable is array
> - `session()->isSession()` - check if session has been started
> - `session()->newId()` - regenerate session ID

#### Flash Messages
Flash messages are simple session messages that last only until they are used. The `\Drone\Core\Flash` object handles flash messages and can be accessed using the `flash()` helper function, example:
```php
// in a controller a validation error is set as a flash message
flash('error.email', 'Please enter your email address');
```
Next, in the view template file call the flash message:
```html+php
<?=flash('error.email')?>
```
The flash message will only appear once, and be destroyed immediately after. This is very helpful for displaying one-time client messages and errors.

> When the `flash()` helper function is called a session with be started automatically if required

The true power of flash messages is the use of templates, for example in the `index.php` file set a flash message template:
```php
// sets template for flash message group 'error'
\Drone\Core\Flash::template('error', '<div class="error">{$message}</div>');
```
Then set the flash message in the controller:
```php
flash('error.email', 'Please enter your email address');
```
Now in the view template when the `flash()` helper function is called with the group `error` (set with syntax `[group].[name]`) the template is applied:
```html+php
<?=flash('error.email')?>
```
This will output the HTML:
```html
<div class="error">Please enter your email address</div>
```

> Other useful flash methods:
> - `flash()->clear()` - clear a flash message
> - `flash()->flush()` - flush all flash messages
> - `flash()->has()` - check if flash message exists

## Data Handling
Drone supports data handling: filtering, formatting and validation using the `\Drone\Core\Data` object.

#### Filter
Data can be filtered/sanitized using the `filter()` helper function, for example:
```php
// trim value
$trimmed = filter(' my value ', \Drone\Core\Data::FILTER_TRIM); // 'my value'
```
> If no filter is passed to the `filter()` helper function the value will be trimmed

Array values can also be used, for example:
```php
$trimmed = filter([' value 1 ', ' value 2 '], \Drone\Core\Data::FILTER_TRIM);
// $trimmed is now: ['value 1', 'value 2']
```

Filters can also be used together:
```php
// trim value + strip non-word characters
$trimmed_words = filter(' my value! ', 
	\Drone\Core\Data::FILTER_TRIM | \Drone\Core\Data::FILTER_WORD); // 'myvalue'
```
Some filter methods use arguments (or *params*), for example:
```php
// strip non-word characters, but allow whitespaces
$words = filter('my value!', \Drone\Core\Data::FILTER_WORD, 
	[\Drone\Core\Data::PARAM_WHITESPACE => true]); // 'my value'
```
>Filter methods can also be called statically:
```php
$trimmed = \Drone\Core\Data::filterTrim(' my value '); // 'my value'
```

Available filters are:
- `FILTER_ALNUM` - strip non-alphanumeric characters
- `FILTER_ALPHA` - strip non-alpha characters
- `FILTER_DATE` - strip non-date characters
- `FILTER_DATE_TIME` - strip non-date/time characters
- `FILTER_DECIMAL` - strip non-decimal characters
- `FILTER_EMAIL` - strip non-email characters
- `FILTER_HTML_ENCODE` - encode HTML special characters
- `FILTER_NUMERIC` - strip non-numeric characters
- `FILTER_SANITIZE` - strip tags
- `FILTER_TIME` - strip non-time characters
- `FILTER_TRIM` - trim spaces
- `FILTER_URL_ENCODE` - encode URL
- `FILTER_WORD` - strip non-word characters (same as character class '\w')

Custom filters can be used, for example:
```php
// register custom filter
filter('strip_hypens', function($v) { return str_replace('-', '', $v); }
...
// use custom filter
$no_hypens = filter('my-value', 'strip_hypens'); // 'myvalue'

// or use custom filter with defined filter
$no_hypens_trimmed = filter(' my-value ', 'strip_hypens', 
	\Drone\Core\Data::FILTER_TRIM); // 'myvalue'
```

#### Format
Data can be formatted using the `format()` helper function, for example:
```php
// format number to currency
$currency = format(5, \Drone\Core\Data::FORMAT_CURRENCY); // '$5.00'
```

Array values can also be used, for example:
```php
$currencies = format([5, 10.5], \Drone\Core\Data::FORMAT_CURRENCY);
// $currencies is now: ['$5.00', '$10.50']
```

Formatters can also be used together:
```php
// format byte value + upper case
$bytes_upper = format(2000, 
	\Drone\Core\Data::FORMAT_BYTE | \Drone\Core\Data::FORMAT_UPPER); // '1.95 KB'
```
Some formatter methods use arguments (or *params*), for example:
```php
// format number to currency with custom currency format
$currency = format(5, \Drone\Core\Data::FORMAT_CURRENCY, 
	[\Drone\Core\Data::PARAM_FORMAT => '$%0.2f USD']); // '$5.00 USD'
```
> Format methods can also be called statically:
```php
$upper_words = \Drone\Core\Data::formatUpperWords('my value'); // 'My Value'
```

Available formats are:
- `FORMAT_BYTE`
- `FORMAT_CURRENCY`
- `FORMAT_DATE`
- `FORMAT_DATE_TIME`
- `FORMAT_LOWER`
- `FORMAT_TIME`
- `FORMAT_UPPER`
- `FORMAT_UPPER_WORDS`

Custom formats can be used, for example:
```php
// register custom format
format('quotes', function($v) { return '"' . $v . '"'; }
...
// use custom format
$quoted = format('my value', 'quotes'); // '"my value"'

// or use custom format with defined format
$quoted_upper = format('my value', 'quotes', 
	\Drone\Core\Data::FORMAT_UPPER); // '"MY VALUE"'
```

#### Validate
Data validation can be done using the `validate()` helper function, for example:
```php
// validate email value
if(!validate('bad-email@', \Drone\Core\Data::VALIDATE_EMAIL))
{
	// warn user
}
```
> If no validator is passed to the `validate()` helper function the `VALIDATE_REQUIRED` validator will be used, for example:
```php
if(validate('')) // not valid
if(validate('my value')) // valid
```

Array values can also be used, for example:
```php
$valid = validate([1 => 'bad-email@', 2 => 'good-email@example.com'], 
	\Drone\Core\Data::VALIDATE_EMAIL);
// $valid is now: [1 => false, 2 => true]
```

Validators can also be used together:
```php
// validate required + alpha characters
if(!validate('string14', \Drone\Core\Data::VALIDATE_REQUIRED | \Drone\Core\Data::VALIDATE_ALPHA))
{
	// warn
}
```
Some validator methods use arguments (or *params*), for example:
```php
// validate string length (minimum 4, maximum 50)
if(!validate('my string', \Drone\Core\Data::VALIDATE_LENGTH, 
	[\Drone\Core\Data::PARAM_MIN => 4, \Drone\Core\Data::PARAM_MAX => 50]))
{
	// warn
}
```
> Validation methods can also be called statically:
```php
if(!\Drone\Core\Data::validateEmail('bad-email@'))
```

Available validators are:
- `VALIDATE_ALNUM` - value is alphanumeric characters
- `VALIDATE_ALPHA` - value is alpha characters
- `VALIDATE_BETWEEN` - value between min and max values
- `VALIDATE_CONTAINS` - value contains value
- `VALIDATE_CONTAINS_NOT` - value does not contain value
- `VALIDATE_DECIMAL` - value is decimal
- `VALIDATE_EMAIL` - value is email
- `VALIDATE_IPV4` - value is IPv4 address
- `VALIDATE_IPV6` - value is IPv6 address
- `VALIDATE_LENGTH` - value is min length, or under max length, or between min and max lengths
- `VALIDATE_MATCH` - value is match to value
- `VALIDATE_NUMERIC` - value is numeric
- `VALIDATE_REGEX` - value is Perl-compatible regex pattern
- `VALIDATE_REQUIRED` - value exists (length > 0)
- `VALIDATE_URL` - value is URL
- `VALIDATE_WORD` - value is word (same as character class '\w')

Custom validators can be used, for example:
```php
// register custom validator
validate('upper', function($v) { return preg_match('/^[A-Z]*$/', $v); }
...
// use custom validator
if(!validate('my value', 'upper'))
{
	// warn
}

// or use custom validator with defined validator
if(!validate('my value', 'upper', \Drone\Core\Data::VALIDATE_REQUIRED))
```

## Filesystem
Drone offers filesystem support for directories and files.

#### Directory
The `\Drone\Filesystem\Directory` class can be used for directory handling, for example:
```php
$dir = new \Drone\Filesystem\Directory('_app/my_dir');

if($dir->exists()) // do something
```
Directory class methods:
- `copy()`
- `create()`
- `exists()`
- `getCount()` - count of directory items
- `getPath()`
- `move()`
- `read()` - read directory items into array
- `remove()`
- `writable()`

#### File
The `\Drone\Filesystem\File` class can be used for file handling, for example:
```php
$file = new \Drone\Filesystem\File('_app/my_file.txt');

if($file->exists()) // do something
```
File class methods:
- `chmod()`
- `copy()`
- `create()`
- `exists()`
- `getModifiedTime()`
- `getPath()`
- `getSize()` - in bytes
- `move()`
- `read()` - read file contents to string
- `remove()`
- `writable()`
- `write()` - write data to file

## Database Handling
Drone uses the [PDOm](https://github.com/shayanderson/pdom) PDO wrapper with MySQL support for database handling.

The PDOm bootstrap file is located by default at: `_app/com/pdom.bootstrap.php`
