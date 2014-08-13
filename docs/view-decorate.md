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
Callable filters can also be used:
```php
echo Decorate::data($data, 'ID: {$id}, Title: {$title:format_title}<br />',
	['format_title' => function($row) { return ucwords($row['title']); }]);
```
This would output:
```html
ID: 5, Title: Tester<br />
ID: 14, Title: Programmer<br />
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