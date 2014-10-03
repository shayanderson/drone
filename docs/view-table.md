## \Drone\View\Table

The `\Drone\View\Table` helper class can be used for HTML tables, for example in a controller set the table object and data:
```php
$this->table = new \Drone\View\Table([[1,2,3],[4,5,6]]);
```
Then in the view template display table:
```html+php
My Table:
<?php echo $table; ?>
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
> Data can also be added to a table object like:
```php
foreach($records as $record)
{
	$this->table->data($record);
}
```

### Columns
Table columns can be customized, like the number of columns and how the data is populated in the columns, for example:
```php
$this->table = new \Drone\View\Table([1,2,3,4,5,6,7,8,9]);
$this->table->columns = 3; // set number of table columns to 3
```
Which outputs the table:
<table><tr><td>1</td><td>2</td><td>3</td></tr><tr><td>4</td><td>5</td><td>6</td></tr><tr><td>7</td><td>8</td><td>9</td></tr></table>
> Specific columns can be used, for example:
```php
$this->table = new \Drone\View\Table(['col1' => x, 'col2' => y, 'col3' => z], ['col2', 'col3']);
```
In this example only the data `y` and `z` will be displayed (columns `col2` and `col3`)

The data can be displayed vertically using:
```php
$this->table = new \Drone\View\Table([1,2,3,4,5,6,7,8,9]);
$this->table->columns = 3;
$this->table->columns_vertical = true; // force vertical table data
```
Which outputs the table:
<table><tr><td>1</td><td>4</td><td>7</td></tr><tr><td>2</td><td>5</td><td>8</td></tr><tr><td>3</td><td>6</td><td>9</td></tr></table>

> When using column settings do *not* using multidimensional arrays like `[[1,2],[3,4]]`, instead use arrays like: `[1,2,3,4]`

### Headings
Headings can be used:
```php
$this->table = new \Drone\View\Table([1,2,3,4,5,6,7,8,9]);
$this->table->columns = 3;
$this->table->headings(); // use auto headings
```
Which outputs the table:

| 0 | 1 | 2 |
| --- | --- | --- |
| 1 | 2 | 3 |
| 4 | 5 | 6 |
| 7 | 8 | 9 |

When *auto* headings are used the array keys for the first row are used for the heading titles, for example:
```php
$this->table = new \Drone\View\Table(['one' => 1, 'two' => 2, 'three' => 3,4,5,6,7,8,9]);
$this->table->columns = 3;
$this->table->headings(); // use auto headings
```
Would output the table:

| one | two | three |
| --- | --- | --- |
| 1 | 2 | 3 |
| 4 | 5 | 6 |
| 7 | 8 | 9 |

Manual heading titles can be used instead of auto titles:
```php
$this->table = new \Drone\View\Table([1,2,3,4,5,6,7,8,9]);
$this->table->columns = 3;
$this->table->headings(['col1', 'col2', 'col3']); // use manual headings
```
Outputs the table:

| col1 | col2 | col3 |
| --- | --- | --- |
| 1 | 2 | 3 |
| 4 | 5 | 6 |
| 7 | 8 | 9 |

### Attributes
Table attributes can be set:
```php
...
$this->table->attribute('style', 'color:#555');
```
This would add the `style` attribute to the `table` tag:
```html
<table style="color:#555">
```

Multiple attributes can be set using an array:
```php
$this->table->attribute(['style' => 'color:#555', 'class' => 'myclass']);
```
Which would add the attributes:
```html
<table style="color:#555" class="myclass">
```

Row level attributes can be set:
```php
$this->table->attributeRow('style', 'color:#555');
```
Which would add the attributes to every `tr` tag:
```html
<tr style="color:#555">
```
Row indexes can be used to set attributes for a specific row:
```php
$this->table->attributeRow('style', 'color:#555', 2);
```
Now only the *2nd* row would have the attribute set:
```html
<tr style="color:#555">
```
> The index value can also be an array with multiple indexes:
```php
$this->table->attributeRow('style', 'color:#555', [2,4]);
```
Now the *2nd* and *4th* row would have the attributes set

Cell level attributes can be set:
```php
$this->table->attributeCell('style', 'text-decoration:underline');
```
Which adds the attribute to every `td` tag:
```html
<td style="text-decoration:underline">
```
> The `attributeCell()` method uses the same logic as the `attributeRow()` when using cell indexes and using multiple indexes

Heading attributes can be set using:
```php
$this->table->attributeHeading('style', 'color:#555');
```
Or attribute set for a specific heading cell:
```php
$this->table->attributeHeading('style', 'color:#555', 2); // 2nd heading cell
```
Or multiple cells:
```php
$this->table->attributeHeading('style', 'color:#555', [2,4]); // 2nd + 4th heading cells
```

### Column Templates
Column templates are used to modify column data, for example:
```php
$data = [
	['id' => 5, 'title' => 'Item 1'],
	['id' => 10, 'title' => 'My Item']
];
$this->table = new \Drone\View\Table($data, ['title']); // only display 'title' column
$this->table->headings(['Item']); // add column heading

// set column template
$this->table->columnTemplate('title', '<a href="?id={$id}">{$title}</a>');
```
This will output the HTML:
```html
<table>
	<thead>
		<tr>
			<th>Item</th>
		</tr>
	</thead>
	<tr>
		<td><a href="?id=5">Item 1</a></td>
	</tr>
	<tr>
		<td><a href="?id=10">My Item</a></td>
	</tr>
</table>
```