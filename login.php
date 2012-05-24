<?php
require_once('init.php');
if($is_logged_in)
{
	header('Location: /social.php');
}
require_once('includes/header.php');
require_once('classes/class.Profile.php');

if(isset($_CLEAN['POST']['profile_name']) && isset($_CLEAN['POST']['password']))
{
	if(preg_match('/^\w+$/i',$_CLEAN['POST']['profile_name']))
	{
		$profile_instance = new Profile;
		$profile_instance->set_profile_name($_CLEAN['POST']['profile_name']);
		$profile_instance->init();
		if($profile_instance->exists())
		{
			if($profile_instance->get_profile_id() == 1)
			{
				$_SESSION['eid'] = 0;
				header('Location: /login.php');
			}
			$profile_instance->set_password($_CLEAN['POST']['password']);
			$profile_instance->set_password_type('plaintext');
			$response = $profile_instance->authenticate();
			if(is_array($response))
			{
				$_SESSION['login_data'] = base64_encode($profile_instance->get_profile_name() . '|' . $profile_instance->get_password());
				if(isset($_SESSION['redirect']))
				{
					$redirect = $_SESSION['redirect'];
					unset($_SESSION['redirect']);
					header('Location: ' . urldecode($redirect));
				}
				else
				{
					header('Location: /social.php');
				}
			}
			else
			{
				$_SESSION['eid'] = 0;
				header('Location: /login.php');
			}
		}
		else
		{
			$_SESSION['eid'] = 0;
			header('Location: /login.php');
		}
	}
	else
	{
		$_SESSION['eid'] = 0;
		header('Location: /login.php');
	}
}
?>
<form method="post">
<table>
<tr><td>Profile Name</td><td><input tabindex="1" type="text" name="profile_name"<?php echo (isset($_CLEAN['POST']['profile_name']) ? ' value="' . $_CLEAN['POST']['profile_name'] . '"' : ''); ?> /></td></tr>
<tr><td>Password</td><td><input tabindex="2" type="password" name="password" /></td></tr>
<tr><td colspan="2"><input tabindex="3" type="submit" value="Login" /></td></tr>
</table>
</form>
<?php
require_once('includes/footer.php');
?>