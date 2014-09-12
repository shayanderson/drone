## \Drone\View\Breadcrumb

The `\Drone\View\Breadcrumb` helper class can be used for HTML breadcrumbs, for example in a controller set the breadcrumbs object and add items:
```php
// set object
view()->breadcrumbs = new \Drone\View\Breadcrumb;

// add breadcrumbs
view()->breadcrumbs->add('Home', '/');
view()->breadcrumbs->add('Category 1', '/category-1.htm');
view()->breadcrumbs->add('Current Page');
```
Or the breadcrumbs can be set using an array:
```php
view()->breadcrumbs->add([
	'/' => 'Home',
	'/category-1.htm' => 'Category 1',
	'Current Page'
]);
```

> The breadcrumb items can also be added when setting the object:
```php
view()->breadcrumbs = new \Drone\View\Breadcrumb([
	'/' => 'Home',
	'/category-1.htm' => 'Category 1',
	'Current Page'
]);
```

Then in the view template display breadcrumbs HTML:
```html+php
<?=$breadcrumbs?>
```
Which will output HTML like:
```html
<a href="/">Home</a> &raquo; <a href="/category-1.htm">Category 1</a> &raquo; Current Page
```

### Base Items
A base item, or base items, can be used:
```php
// globally set 'Home' as base item (will be included in all breadcrumbs)
\Drone\View\Breadcrumb::base('Home', '/');
```
Or, multiple base items can be set using:
```php
\Drone\View\Breadcrumb::base([
	'/' => 'Home',
	'/account/' => 'Account'
]);
```

### Before and After Wrappers
*Before* and *after* wrappers can be used, for example:
```php
\Drone\View\Breadcrumb::$wrapper_before = '<div class="breadcrumb">';
\Drone\View\Breadcrumb::$wrapper_after = '</div>';

// set object
view()->breadcrumbs = new \Drone\View\Breadcrumb;

// add items
...
```
Now the breadcrumb items will be wrapped like `<div class="breadcrumb">[items]</div>`

### Templates
Item templates are used to set the HTML for each breadcrumb item, for example:
```php
// set template for items
\Drone\View\Breadcrumb::$template = '<a href="{$url}">{$title}</a>';
// set template for active item (item without URL)
\Drone\View\Breadcrumb::$template_active = '{$title}';
```

### Item Separator
The breadcrumb item separator can be changed using:
```php
\Drone\View\Breadcrumb::$separator = ' / ';
```

### Filters
Global filters can be used to modify all titles and/or URLs, for example:
```php
// upper case all titles
\Drone\View\Breadcrumb::$filter_title = function($title) { return strtoupper($title); };
```
The filter for URLs can be set using `\Drone\View\Breadcrumb::$filter_url`.