## \Drone\View\Table

The `\Drone\View\Table` helper class can be used for HTML tables, for example in a controller set the table object and data:
```php
view()->table = new \Drone\View\Table([[1,2,3],[4,5,6]]);
```
Then in the view template display table:
```html+php
My Table:
<?php $table->display(); ?>
```
Which outputs the HTML:
```html
<table>
<tr>
<td>1</td>
<td>2</td>
<td>3</td>
</tr>
<tr>
<td>4</td>
<td>5</td>
<td>6</td>
</tr>
</table>
```