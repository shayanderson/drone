## \Drone\View\Form

The `\Drone\View\Form` helper class can be used for HTML forms, for example in a controller set the form object and set form fields:
```php
// set form object with POST request + 'login_form' as form ID
view()->form = new \Drone\View\Form($_POST, 'login_form');

// set form fields
view()->form
	// add text field
	->text('username')
		->validateRequired('Username is required')
	// add password field
	->password('pwd')
		->validateRequired('Password is required');
		
// listen for form submit + validate data
if(view()->form->isValid())
{
	// check if user credentials valid
	if(User::validateLogin(view()->form->getData('username'), view()->form->getData('pwd')))
	{
		// set session, redirect to account, etc...
	}
	else
	{
		// warn user invalid credentials
		view()->form->field('username')->forceError('Invalid username and/or password');
	}
}
```
Then in the view template display form:
```html+php
<form method="post">
	<?php echo $form->getFormIdField(); // form listener ?>
	
	<?php echo $form->getErrors('username'); // display validation errors ?>
	<label>Username:</label>
	<?php echo $form->get('username'); // display username field ?><br />
	
	<?=$form->getErrors('pwd')?>
	<label>Password:</label>
	<?=$form->get('username')?><br />
	
	<input type="submit" value="Login" />
</form>
```
> When setting the form object the `form_id` param is optional - it is used to detect when an exact form is submitted. When using the `form_id` make sure to include the `<?=$form->getFormIdField()?>` (or `<?=$form?>`) code in the view template to ensure the form ID will be used.

<blockquote>The form object will auto sanitize form data - to disable auto sanitizing set the object using a <code>false</code> flag as the third param: <code>view()->form = new \Drone\View\Form($_POST, 'login_form', false);</code></blockquote>

### Form Fields
The `\Drone\View\Form` class uses the following methods for adding fields:

- `checkbox()` - checkbox input, example: `$form->checkbox('field_name', [1 => 'Option 1', 2 => 'Option 2'])`
- `hidden()` - hidden input, example: `$form->hidden('field_name', 'default value')`
- `password()` - password input, example: `$form->password('field_name')`
- `radio()` - radio button, example: `$form->radio('field_name', [1 => 'Option 1', 2 => 'Option 2'])`
- `select()` - select list, example: `$form->select('field_name', [1 => 'Option 1', 2 => 'Option 2'])`
- `text()` - text input, example: `$form->text('field_name')`
- `textarea()` - multi-line text input, example: `$form->textarea('field_name')`

### Form Field Attributes
Form field attributes are added in the view template file, for example:
```html+php
<?=$form->get('username', ['class' => 'textclass', 'maxlength' => 30])?>
```
Will output the HTML:
```html
<input type="text" name="username" class="textclass" maxlength="30">
```

### Form Field Decorators
Global decorators can be used for form fields. The global decorators are:

- `\Drone\View\Form::$decorator_checkbox_radio` - for checkbox and radio button fields
- `\Drone\View\Form::$decorator_error` - for single error message
- `\Drone\View\Form::$decorator_errors` - for multiple error messages
- `\Drone\View\Form::$decorator_field` - for password and text input fields
- `\Drone\View\Form::$decorator_fields` - for all form fields
- `\Drone\View\Form::$decorator_options` - for checkbox and radio button options
- `\Drone\View\Form::$decorator_select` - for select lists
- `\Drone\View\Form::$decorator_textarea` - for textarea fields

A global decorator can be set before setting the form object like:
```php
// set global decorator for text (and password) fields
\Drone\View\Form::$decorator_field = '<div class="field">{$field}</div>';

// set
view()->form
	// add text field
	->text('username');
```
Now in the view template:
```html+php
<?=$form->get('username')?>
```
Will output the HTML:
```html
<div class="field"><input type="text" name="username"></div>
```
> A decorator can use the pattern like `{$string}<br />`, or simply `{$}<br />`, or if no `{$...}<br />` pattern is found the decorator is added to the end of the string like `[string]<br />`.

### Form Validator Methods
Form field validation methods can be used to validate form data, for example:
```php
view()->form
	// add text field
	->text('username')
		->validateRequired('Username is required')
		->validateLength(4, 30, 'Username must be between 4-30 characters');
```
Will apply the required and length validation rules.

Custom validation rules can also be used, for example:
```php
view()->form
	// add text field
	->text('username')
		->validate(function($v) { return $v !== 'some value'; }, 'Username field value does not equal \'some value\'');
```
This custom validation rule will flag the field value invalid if the value does not equal `some value`.

Forced errors can be used, for example:
```php
view()->form
	->text('username');
	
// do some logic to check valid login
if(!$valid_login)
{
	view()->form->field('username')->forceError('Invalid username and/or password');
}
```
Now if the login is invalid the error will be displayed to the user in the view template file.
> The method `field()` is used to make the form object refocus on a specific field

### Form Field Errors
Form field validation errors (and *forced* errors) can be displayed using two methods:

- `getError()`
- `getErrors()`

The `getError()` method is used to fetch the first field error message, for example in the controller file:
```php
view()->form
	// add text field
	->text('username')
		->validateRequired('Username is required')
		->validateLength(4, 30, 'Username must be between 4-30 characters');
```
Then in the view template file:
```html+php
<?php echo $form->getError('username'); // display first field error ?>
<label>Username:</label>
<?php echo $form->get('username'); // display username field ?><br />
```
Now if the form is submitted with no value for field `username` the error displayed will be `Username is required`.

The `getErrors` method will display all field errors, for example in the view template file:
```html+php
<?php echo $form->getErrors('username', '<br />'); // display all field errors ?>
<label>Username:</label>
<?php echo $form->get('username'); // display username field ?><br />
```
Now if the form is submitted with no value for field `username` the HTML displayed will be `Username is required<br />Username must be between 4-30 characters<br />`. 
> The `<br />` string used as the second param is the decorator.

All form field errors can be fetched using `null` in place of the field name:
```html+php
<?php echo $form->getErrors(null, '<br />'); // display all form field errors ?>
```

### Accessing Form Data
Form field data is accessed using the `getData()` method:
```php
view()->form
	->text('username')
	->text('first_name')
	->password('pwd');

if(view()->form->isSubmitted())
{
	$username = view()->form->getData('username');
	$fname = view()->form->getData('first_name');
	$password = view()->form->getData('password');
}
```
Or the data can be returned as an object:
```php
	$data = view()->form->getData(); // stdClass Object(['username' => x, 'first_name' => y, 'pwd' => z])
```
Or the data can be returned as array:
```php
	$data = view()->form->getData(null, false); // ['username' => x, 'first_name' => y, 'pwd' => z]
```
Or the data can be returned for specific fields:
```php
	$data = view()->form->getData(['username', 'pwd']); // stdClass Object(['username' => x, 'pwd' => y])
```
Or the data for fields can be mapped to different keys:
```php
	$data = view()->form->getData([
		'username' => 'uid',
		'pwd' => 'u_pwd'
	]); // stdClass Object(['uid' => x, 'u_pwd' => y])
```