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
		// direct to account
	}
	else
	{
		// warn user invalid credentials
	}
}
```
Then in the view template display form:
```html+php
<form method="post">
	<?php echo $form->getFormIdField(); // form listener ?>
	
	<?php echo $form->getErrors('username'); // display validation errors ?>
	<label>Username:</label>
	<?php echo $form->get('username'); // display username field ?>
	
	<?=$form->getErrors('pwd')?>
	<label>Password:</label>
	<?=$form->get('username')?>
	
	<input type="submit" value="Login" />
</form>
```