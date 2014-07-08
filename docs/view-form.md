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

### Form Fields
The `\Drone\View\Form` class uses the following methods for adding fields:

- `checkbox()` - checkbox input, example: `$form->checkbox('field_name', [1 => 'Option 1', 2 => 'Option 2'])`
- `hidden()` - hidden input, example: `$form->hidden('field_name', 'default value')`
- `password()` - password input, example: `$form->password('field_name')`
- `radio()` - radio button, example: `$form->radio('field_name', [1 => 'Option 1', 2 => 'Option 2'])`
- `select()` - select list, example: `$form->select('field_name', [1 => 'Option 1', 2 => 'Option 2'])`
- `text()` - text input, example: `$form->text('field_name')`
- `textarea()` - multi-line text input, example: `$form->textarea('field_name')`




