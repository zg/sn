<?php
require_once('init.php');
require_once('classes/class.Profile.php');

if($is_logged_in)
	header('Location: /social.php');

require_once('includes/header.php');
$message = '';
if(isset($_CLEAN['POST']) && 0 < count($_CLEAN['POST']))
{
	if(isset($_CLEAN['POST']['profile_name']) && isset($_CLEAN['POST']['password']))
	{
		if(preg_match('/^[a-z0-9_\-\.]+$/i',$_CLEAN['POST']['profile_name'],$matches))
		{
			$flag = false;

			$first_char = $_CLEAN['POST']['profile_name'][0];
			$last_char = $_CLEAN['POST']['profile_name'][(strlen($_CLEAN['POST']['profile_name']) - 1)];
			if(is_numeric($first_char))
			{
				$message = 'Your name cannot begin with a number.';
				$flag = true;
			}
			if($first_char == '.' || $first_char == '-' || $first_char == '_')
			{
				$message = 'You cannot begin your name with a dot, dash, or underscore.';
				$flag = true;
			}
			if($last_char == '.' || $last_char == '-')
			{
				$message = 'You cannot end your name with a dot or dash.';
				$flag = true;
			}
			if(strpos($_CLEAN['POST']['profile_name'],'--') || strpos($_CLEAN['POST']['profile_name'],'__') || strpos($_CLEAN['POST']['profile_name'],'..'))
			{
				$message = 'Your name cannot contain two consecutive dashes, underscores, or dots.';
				$flag = true;
			}
			if(25 <= strlen($_CLEAN['POST']['profile_name']))
			{
				$message = 'Your profile name is too long! Please limit it to 25 characters or less.';
				$flag = true;
			}
			if(strlen($_CLEAN['POST']['profile_name']) == 0 || strlen($_CLEAN['POST']['password']) == 0 || strlen($_CLEAN['POST']['confirm_password']) == 0)
			{
				$message = 'You forgot to input your profile name and/or password.';
				$flag = true;
			}
			if(file_exists('http://' . $_CLEAN['POST']['display_name']))
			{
				$message = 'Please refrain from advertising in your display name.';
				$flag = true;
			}
			if($_CLEAN['POST']['password'] !== $_CLEAN['POST']['confirm_password'])
			{
				$message = 'Your password does not match your confirm password.';
				$flag = true;
			}
			if(!$flag)
			{
				$profile_instance = new Profile;
				$profile_instance->set_profile_name($_CLEAN['POST']['profile_name']);
				$profile_instance->init();
				if($profile_instance->exists())
				{
					$message = 'That profile name is already taken.';
				}
				else
				{
					$profile_instance->set_display_name($_CLEAN['POST']['display_name']);
					$profile_instance->set_password($_CLEAN['POST']['password']);
					$profile_instance->set_password_type('plaintext');
					$response = $profile_instance->create();
					if($response !== false)
					{
						$response = $profile_instance->authenticate();
						if($response !== false)
							$_SESSION['login_data'] = base64_encode($profile_instance->get_profile_name() . '|' . $profile_instance->get_password());
						header('Location: /social.php');
					}
					else
					{
						$message = 'Error registering profile.';
					}
				}
			}
		}
		else
		{
			$message = 'The profile name you chose contains invalid characters.';
		}
	}
}
if(0 < strlen($message))
{
	echo '<div class="error_message">' . $message . '</div>';
}
?>
<form method="post">
<table>
<tr><td>Profile Name</td><td><input type="text" name="profile_name" maxlength="25"<?php echo (isset($_CLEAN['POST']['profile_name']) ? ' value="' . $_CLEAN['POST']['profile_name'] . '"' : ''); ?> /> (up to 25 alphanumeric characters)</td></tr>
<tr><td>Display Name</td><td><input type="text" name="display_name" maxlength="100"<?php echo (isset($_CLEAN['POST']['display_name']) ? ' value="' . $_CLEAN['POST']['display_name'] . '"' : ''); ?> /> (up to 100 characters)</td></tr>
<tr><td>Password</td><td><input type="password" name="password" /></td></tr>
<tr><td>Confirm Password</td><td><input type="password" name="confirm_password" /></td></tr>
<tr><td colspan="2"><input type="hidden" name="action" value="register" /><input type="submit" value="Register" /></td></tr>
</table>
</form>
<?php
require_once('includes/footer.php');
?>