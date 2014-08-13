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