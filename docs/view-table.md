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

#### Columns
Table columns can be customized, like the number of columns and how the data is populated in the columns, for example:
```php
view()->table = new \Drone\View\Table([1,2,3,4,5,6,7,8,9]);
view()->table->columns = 3; // set number of table columns to 3
```
Which outputs the table:
<table><tr><td>1</td><td>2</td><td>3</td></tr><tr><td>4</td><td>5</td><td>6</td></tr><tr><td>7</td><td>8</td><td>9</td></tr></table>

The data can be displayed vertically using:
```php
view()->table = new \Drone\View\Table([1,2,3,4,5,6,7,8,9]);
view()->table->columns = 3;
view()->table->columns_vertical = true; // force vertical table data
```
Which outputs the table:
<table><tr><td>1</td><td>4</td><td>7</td></tr><tr><td>2</td><td>5</td><td>8</td></tr><tr><td>3</td><td>6</td><td>9</td></tr></table>