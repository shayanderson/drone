# Drone
Drone Framework for PHP 5.5.0+

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
 &nbsp; &nbsp; **[Quick Start](https://github.com/shayanderson/drone#quick-start):** [Directory Structure](https://github.com/shayanderson/drone#directory-structure), [Class Autoloading](https://github.com/shayanderson/drone#class-autoloading), [Drone Function](https://github.com/shayanderson/drone#drone-function), [Helper Functions](https://github.com/shayanderson/drone#helper-functions), [Settings](https://github.com/shayanderson/drone#settings), [Run Application](https://github.com/shayanderson/drone#run-application)<br />
 &nbsp; &nbsp; **[Routes](https://github.com/shayanderson/drone#routes):**

## Quick Start
To install Drone simply download the package and install in your project directory.

All of the Drone bootstrapping is done in the *index.php* file.

#### Directory Structure
By default Drone uses the following directory structure:
- _app (framework + application source files)
..- com (common application files)
..- lib (framework + application class files)
..- mod (controller files)
..- tpl (view template files)
...- _global (global view template files)

#### Class Autoloading
Class autoloading is completed using the *autoload()* function in the *index.php* file, example:
```php
// set class autoloading paths
autoload([
	PATH_ROOT . '_app/lib',
	PATH_ROOT . '_app/mdl'
]);
```
In this example classes will be autoloaded from the *_app/lib* and the *_app/mdl* directories. The autoloader expects the use of namespaces, example:
```php
$myobj = new \Mylib\Myclass;
```
Would load the class *_app/lib/Mylib/Myclass.php* or *_app/mdl/Mylib/Myclass.php* (depending on where the class is located).

#### Drone Function
The *drone()* function can be used to easily access the Drone core class, example:
```php
drone()->trigger('myevent');
```

#### Helper Functions
Drone helper functions can be used to access Drone components easily, example of the *request()* helper function:
```php
$name = request()->post('name'); // get POST request value for 'name'
```
Drone helper functions available:
- **clear()** - clear param key/value pair
- **error()** - trigger error
- **error_last()** - get last error
- **filter()** - filter data
- **flash()** - set flash message
- **format()** - format data
- **get()** - get param value
- **has()** - check if param exists
- **load_com()** - load common file
- **logger()** - *drone()->log* alias
- **pa()** - string/array printer
- **param()** - get route param
- **redirect()** - redirect to location
- **request()** - *drone()->request* alias
- **session()** - *drone()->session* alias
- **set()** - set param value
- **stop()** - stop application
- **template()** - get template formatted name
- **template_global()** - get gloabl template formatted name
- **validate()** - validate value
- **view()** - *drone->view* alias

#### Settings
Drone can run without changing the default settings, however, the default settings should be changed when Drone is used in a production environment in the *index.php* file:
```php
// turn debug mode off - this will prevent unwanted output in a production environment
drone()->set(\Drone\Core::KEY_DEBUG, false);

// turn off backtrace in log - this should only be used in a development environment
drone()->set(\Drone\Core::KEY_ERROR_BACKTRACE, false);

// turn on logging of errors in the default Web server log file
drone()->set(\Drone\Core::KEY_ERROR_LOG, true);
```

#### Run Application
The last call in the *index.php* file should run the application:
```php
drone()->run();
```
Nothing should happen after this call as the output buffer has already ended.

To setup an application response simply create a new controller file in the *_app/mod* directory, for example *_app/mod/hello-world.php*:
```php
// display view template with auto template name
view()->display();
```
Next, create a view template *_app/tpl/hello-world.tpl*:
```html
Hello world
```
Finally, visit your Web application with request '/hello-world.htm' in a browser and you should see the *Hello world* text.

## Routes
There are two types of routes in Drone: static and mapped.

#### Static Routes
Static routes require no mapping and instead rely on static file paths. For example, the application request '/hello-world.htm' will search for the controller file *_app/mod/hello-world.php*

#### Mapped Routes





