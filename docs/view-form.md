## \Drone\View\Form

The `\Drone\View\Form` helper class can be used for HTML forms, for example in a controller set the form object and set form fields:
```php
// set form object with POST request + 'login_form' as form ID
$this->form = new \Drone\View\Form($_POST, 'login_form');

// set form fields
$this->form
	// add text field
	->text('username')
		->validateRequired('Username is required')
	// add password field
	->password('pwd')
		->validateRequired('Password is required');

// set decorator for field errors
\Drone\View\Form::$decorator_errors = '<div class="errors">{$errors}</div>';
\Drone\View\Form::$decorator_errors_message = '{$error}<br />';

// listen for form submit + validate data
if($this->form->isValid())
{
	// check if user credentials valid
	if(User::validateLogin($this->form->username, $this->form->pwd))
	{
		// set session, redirect to account, etc...
	}
	else
	{
		// warn user invalid credentials
		$this->form->field('username')->forceError('Invalid username and/or password');
	}
}
```
Then in the view template display form:
```html+php
<form method="post">
	<!-- form listener -->
	<?=$form?>

	<label>Username:</label>
	<?php echo $form->getErrors('username'); // display validation errors ?>
	<?php echo $form->get('username'); // display username field ?><br />

	<label>Password:</label>
	<?=$form->getErrorsAndField('pwd') // shorthand method ?><br />

	<input type="submit" value="Login" />
</form>
```
> When setting the form object the `form_id` param is optional - it is used to detect when an exact form is submitted. When using the `form_id` make sure to include the `<?=$form->getFormIdField()?>` (or `<?=$form?>`) code in the view template to ensure the form ID will be used.

<blockquote>The form object will auto sanitize form data - to disable auto sanitizing set the object using a <code>false</code> flag as the third param: <code>$this->form = new \Drone\View\Form($_POST, 'login_form', false);</code></blockquote>

### Form Fields
The `\Drone\View\Form` class uses the following methods for adding fields:

- `checkbox()` - checkbox input, example: `$form->checkbox('field_name', [1 => 'Option 1', 2 => 'Option 2'], 'default checked (optional)')`
- `hidden()` - hidden input, example: `$form->hidden('field_name', 'default value (optional)')`
- `password()` - password input, example: `$form->password('field_name', 'default value (optional)')`
- `radio()` - radio button, example: `$form->radio('field_name', [1 => 'Option 1', 2 => 'Option 2'], 'default checked (optional)')`
- `select()` - select list, example: `$form->select('field_name', [1 => 'Option 1', 2 => 'Option 2'], 'default selected (optional)')`
- `text()` - text input, example: `$form->text('field_name', 'default value (optional)')`
- `textarea()` - multi-line text input, example: `$form->textarea('field_name', 'default value (optional)')`

### Form Field Attributes
Form field attributes are added in the view template file, for example:
```html+php
<?=$form->get('username', ['class' => 'textclass', 'maxlength' => 30])?>
```
Will output the HTML:
```html
<input type="text" name="username" class="textclass" maxlength="30">
```

Global default field attributes can be used for form fields. These attributes are used when no other attributes have been set. The global attributes are:

- `\Drone\View\Form::$attributes_checkbox_radio` - for checkbox and radio button fields
- `\Drone\View\Form::$attributes_field` - for email, password and text input fields
- `\Drone\View\Form::$attributes_fields` - for all form fields
- `\Drone\View\Form::$attributes_select` - for select lists
- `\Drone\View\Form::$attributes_textarea` - for textarea fields

Global attribute example:
```php
// default field class
\Drone\View\Form::$attributes_field = ['class' => 'form-control'];
```

### Form Field Decorators
Global decorators can be used for form fields. The global decorators are:

- `\Drone\View\Form::$decorator_checkbox_radio` - for checkbox and radio button fields
- `\Drone\View\Form::$decorator_default_validation_message` - default validation error message when error message not used
- `\Drone\View\Form::$decorator_error` - for single error message
- `\Drone\View\Form::$decorator_errors` - for multiple error messages
- `\Drone\View\Form::$decorator_field` - for email, password and text input fields
- `\Drone\View\Form::$decorator_fields` - for all form fields
- `\Drone\View\Form::$decorator_options` - for checkbox and radio button options
- `\Drone\View\Form::$decorator_select` - for select lists
- `\Drone\View\Form::$decorator_textarea` - for textarea fields

A global decorator can be set before setting the form object like:
```php
// set global decorator for text (and password) fields
\Drone\View\Form::$decorator_field = '<div class="field">{$field}</div>';

// set
$this->form
	// add text field
	->text('username');
```
> A decorator can use the pattern like `{$string}<br />`, or simply `{$}<br />`, or if no `{$...}<br />` pattern is found the decorator is added to the end of the string like `[string]<br />`.

Now in the view template:
```html+php
<?=$form->get('username')?>
```
Will output the HTML:
```html
<div class="field"><input type="text" name="username"></div>
```
> Global decorators can be disabled for any field using:
```php
// false disables global decorator for this field only
<?=$form->get('username', null, false)?>
```

### Form Validator Methods
Form field validation methods can be used to validate form data, for example:
```php
$this->form
	// add text field
	->text('username')
		->validateRequired('Username is required')
		->validateLength(4, 30, 'Username must be between 4-30 characters');
```
Will apply the required and length validation rules.
> If no error message is used, for example:
```php
$this->form
	// add text field
	->text('username')
		->validateRequired();
```
The default validation error message decorator will be used, and by default the value is:
```php
\Drone\View\Form::$decorator_default_validation_message = 'Enter valid value for field \'{$field}\'';
```

Field validation error message can also be set *after* setting the validation rule, for example:
```php
$this->form
	// add text field
	->text('username')
		->validateRequired();
// more code
//  and logic here

// now set validation message for required rule
$this->form->field('username')
	->validateRequiredMessage('Username is required');
```

The form validation methods are:

- `validateEmail()` - validate email address
- `validateLength()` - validate value length
- `validateMatch()` - validate field x with field y value
- `validateRegex()` - validate value using regex pattern
- `validateRequired()` - validate value is required

Custom validation rules can also be used, for example:
```php
$this->form
	// add text field
	->text('username')
		->validate(function($v) { return $v !== 'some value'; },
			'Username field value does not equal \'some value\'');
```
This custom validation rule will flag the field value invalid if the value does not equal `some value`.

Forced errors can be used, for example:
```php
$this->form
	->text('username');

// do some logic to check valid login
if(!$valid_login)
{
	$this->form->field('username')->forceError('Invalid username and/or password');
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
$this->form
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
$this->form
	->text('username')
	->text('first_name')
	->password('pwd');

if($this->form->isSubmitted())
{
	$username = $this->form->getData('username');
	$fname = $this->form->getData('first_name');
	$password = $this->form->getData('password');
}
```
Or the shorthand version can be used:
```php
if($this->form->isSubmitted())
{
	$username = $this->form->username;
	$fname = $this->form->first_name;
	$password = $this->form->password;
}
```
Or the data can be returned as an object:
```php
	// stdClass Object(['username' => x, 'first_name' => y, 'pwd' => z])
	$data = $this->form->getData();
```
Or the data can be returned as array:
```php
	$data = $this->form->getData(null, false); // ['username' => x, 'first_name' => y, 'pwd' => z]
```
Or the data can be returned for specific fields:
```php
	// stdClass Object(['username' => x, 'pwd' => y])
	$data = $this->form->getData(['username', 'pwd']);
```
Or the data for fields can be mapped to different keys:
```php
	$data = $this->form->getData([
		'username' => 'uid',
		'pwd' => 'u_pwd'
	]); // stdClass Object(['uid' => x, 'u_pwd' => y])
```
The `hasData()` method can be used to test if field value exists, for example:
```php
	if($this->form->hasData('username')) ...
```
Or the shorthand version:
```php
	if($this->form->username !== false) ...
```