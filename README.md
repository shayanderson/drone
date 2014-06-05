# Drone
Drone Framework for PHP 5.5.0+

#### Features
- Class Autoloading
- Routing (mapped and static)
- Error Handling
- Logging / Debugging
- Data Handling (filtering, formatting and validation)
- Session Handling
- Filesystem Handling

#### Documentation Topics
 &nbsp; &nbsp; **[Quick Start](https://github.com/shayanderson/link)**<br />
 
## Quick Start
To install Drone simply download the package and install in your project directory.

All of the Drone bootstrapping is done in the *index.php* file.

#### Class Autoloading
Class autoloading is completed using the *autload()* function in the *index.php* file, example:
```php
// set class autoloading paths
autoload([
	PATH_ROOT . '_app/lib',
	PATH_ROOT . '_app/mdl'
]);
```
In this example class will be autoloaded from the *_app/lib* and the *_app/mdl* directories. The autoloader expects the use of namespaces, example:
```php
new \Mylib\Myclass;
```
Would load the class in *_app/lib/Mylib/Myclass.php* or *_app/mdl/Mylib/Myclass.php* (depending on where the class is located)

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
- clear() - clear param key/value pair
- error() - trigger error
- error_last() - get last error
- filter() - filter data
- flash() - set flash message
- format() - format data
- get() - get param value
- has() - check if param exists
- load_com() - load common file
- logger() - *drone()->log* alias
- pa() - string/array printer
- param() - get route param
- redirect() - redirect to location
- request() - *drone()->request* alias
- session() - *drone()->session* alias
- set() - set param value
- stop() - stop application
- template() - get template formatted name
- template_global() - get gloabl template formatted name
- validate() - validate value
- view() - *drone->view* alias






