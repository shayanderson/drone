## \Drone\View\Decorate

The `\Drone\View\Decorate` helper class can be used to decorate strings, arrays, objects, array of objects and test values for empty values with decorator.

### Decorate String
Here is a string decorator example:
```php
use Drone\View\Decorate;

echo Decorate::string('mr. smith', 'Name: <i>{$name}</i>');
// or use the shorthand decorator:
echo Decorate::string('mr. smith', 'Name: <i>{$}</i>');
```
Both of these would output:
```html
Name: <i>mr. smith</i>
```
Callable filters can also be used, for example:
```php
echo Decorate::string('mr. smith', 'Name: <i>{$:format}</i>',
	['format' => function($name) { return ucwords($name); }]);
```
This would output:
```php
Name: <i>Mr. Smith</i>
```

### Decorate Array
Decorating an array is very useful, here is an example:
```php
use Drone\View\Decorate;

// example data (would probably come from database)
$data = [
	['id' => 5, 'title' => 'tester'],
	['id' => 14, 'title' => 'programmer']
];

// decorate data
echo Decorate::data($data, 'ID: {$id}, Title: {$title}<br />');
```
This would output:
```html
ID: 5, Title: tester<br />
ID: 14, Title: programmer<br />
```
Or single arrays can be used:
```php
// example data
$data = ['id' => 5, 'title' => 'tester'];

// decorate data
echo Decorate::data($data, 'ID: {$id}, Title: {$title}<br />');
```
This would output:
```html
ID: 5, Title: tester<br />
```

##### Filters
Callable filters can also be used:
```php
echo Decorate::data($data, 'ID: {$id}, Title: {$title:format_title}<br />',
	['format_title' => function($title) { return ucwords($title); }]);
```
This would output:
```html
ID: 5, Title: Tester<br />
ID: 14, Title: Programmer<br />
```
> A filter can also be setup to use the entire array as the parameter, for example:
```php
echo Decorate::data($data, 'ID: {$id}, Title: {$:format_title}<br />',
	['format_title' => function($array) { return ucwords($array['title']); }]);
```

Arrays keys can also be used in the decorator when desired, for example:
```php
echo Decorate::data($data, 'ID: {$id}, Title: {$title}, Key: {$:key}<br />');
```
This would output:
```html
ID: 5, Title: tester, Key: 0<br />
ID: 14, Title: programmer, Key: 1<br />
```
Also, values can be tested within the decorator, for example:
```php
// example data with 'is_active' added
$data = [
	['id' => 5, 'title' => 'tester', 'is_active' => 1],
	['id' => 14, 'title' => 'programmer', 'is_active' => 0]
];

echo Decorate::data($data,
	'ID: {$id}, Title: {$title}, Status:{$is_active:Active ?: Inactive}<br />');
```
This would output:
```html
ID: 5, Title: tester, Status:Active<br />
ID: 14, Title: programmer, Status:Inactive<br />
```

### Decorate Object
Objects can be decorated, for example:
```php
use Drone\View\Decorate;

// example object
$obj = new stdClass;
$obj->name = 'mr. smith';
$obj->id = 14;

// decorate data
echo Decorate::data($obj, 'ID: {$id}, Name: {$name}');
```
This would output:
```html
ID: 14, Name: mr. smith
```
Callable filters can also be used:
```php
echo Decorate::data($obj, 'ID: {$id}, Name: {$name:format_name}',
	['format_name' => function($data) { return ucwords($data['name']); }]);
```
This would output:
```html
ID: 14, Name: Mr. Smith
```

Also an array of objects can be decorated, for example:
```php
// example objects
$obj = new stdClass;
$obj->name = 'mr. smith';
$obj->id = 14;

$obj2 = new stdClass;
$obj2->name = 'mr. tester';
$obj2->id = 5;

// decorate data
echo Decorate::data([$obj, $obj2], 'ID: {$id}, Name: {$name}<br />');
```
This would output:
```html
ID: 14, Name: mr. smith<br />
ID: 5, Name: mr. tester<br />
```
Callable filters can also be used with array of objects:
```php
echo Decorate::data([$obj, $obj2], 'ID: {$id}, Name: {$name:format_name}<br />',
	['format_name' => function($data) { return ucwords($data['name']); }]);
```
Which would output:
```html
ID: 14, Name: Mr. Smith<br />
ID: 5, Name: Mr. Tester<br />
```

### Decorate Test Value
Values can be tested for non-empty values (equivalent to the PHP function `empty`) and based on the value can use one of two strings in a decorator. The logic used is: `if value not empty x, if value empty y in x?:y`, for example:
```php
use Drone\View\Decorate;

// example value
$var = false;

// test value
echo Decorate::test($var, 'Non-empty value ?: Empty value');
```
This would output:
```html
Empty value
```
> Empty PHP values can be defined as:
- "" (empty string)
- 0 (integer)
- 0.0 (float)
- "0" (string)
- null
- false
- array() (an empty array)