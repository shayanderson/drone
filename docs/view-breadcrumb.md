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
Then in the view template display breadcrumbs HTML:
```html+php
<?=$breadcrumbs?>
```
Which will output HTML like:
```html
<a href="/">Home</a> &raquo; <a href="/category-1.htm">Category 1</a> &raquo; Current Page
```