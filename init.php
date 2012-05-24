<?php
require_once('classes/class.Session.php');
$session = new Session;

require_once('classes/class.Message.php');

require_once('classes/class.common.php');
require_once('classes/class.Profile.php');
require_once('classes/class.Settings.php');

$error_messages = array (
	0 => 'Profile name or password is invalid.',
	1 => 'Your session has expired or could not be found. Please log back in.',
	2 => 'You must be logged in to view that page.',
	3 => 'Profile not found.',
	4 => 'Failed to update post.',
	5 => 'You forgot to input a post.',
	6 => 'Invalid URL specified.',
	7 => 'The group you requested could not be found.',
	8 => 'You do not have permission to view this group.',
	9 => 'Invalid submission.',
	10 => 'That group doesn\'t exist. You will need to <a href="create_group.php">create it</a> before you can post to it.',
	11 => 'Error saving your settings.',
	12 => 'Error requesting friend.',
	13 => 'You must be friends with this person to view their profile.',
	14 => 'This profile is private.',
	15 => 'Invalid group password.'
);

$success_messages = array (
	0 => 'Successfully registered account! Welcome to the needs a title!',
	1 => 'You have been successfully logged out.',
	2 => 'Successfully posted status!',
	3 => 'Success! Welcome to the group!',
	4 => 'Settings saved successfully.',
	5 => 'You have successfully requested this person to be your friend.',
	6 => 'This person doesn\'t require confirmation for their friend requests, so therefore you two are now friends!'
);

$common = new common;

$is_logged_in = false;

$user_data = array();

$_CLEAN = array (
	 'GET' => array(),
	'POST' => array()
);

foreach($_GET as $key => $value)
{
	$_CLEAN['GET'][$key] = $common->clean($value);
}

foreach($_POST as $key => $value)
{
	if($key == 'password' || $key == 'confirm_password')
	{
		$_CLEAN['POST'][$key] = $value;
	}
	else
	{
		$_CLEAN['POST'][$key] = $common->clean($value);
	}
}

if(session_id() == '')
{
	session_start();
	session_regenerate_id(true);
}

if(isset($_SESSION['login_data']))
{
	// typical login data consists of: base64_encode('profile_name|5f4dcc3b5aa765d61d8327deb882cf99')
	$login_data = base64_decode($_SESSION['login_data']);
	if(strpos($login_data,'|') && substr_count($login_data,'|') == 1) // we want at least one | but just one
	{
		list($profile_name,$password) = explode('|',$login_data);
		if(strlen($password) == 40) // sha1
		{
			$profile_instance = new Profile;
			$profile_instance->set_profile_name($profile_name);
			$profile_instance->init();
			if($profile_instance->exists())
			{
				$profile_instance->set_password($password);
				$profile_instance->set_password_type('sha1');
				$response = $profile_instance->authenticate();
				if($response)
				{
					$_SESSION['profile_id'] = $profile_instance->get_profile_id();
					$is_logged_in = true; // success

					$settings_instance = new Settings;
					$settings_instance->set_profile_id($profile_instance->get_profile_id());
					$settings_instance->init();
					if($settings_instance->get_setting('appear_offline') == 0)
					{
						$profile_instance->update_last_active();
					}
				}
			}
		}
	}
}
if($is_logged_in === false && isset($_SESSION['login_data']))
{
	unset($_SESSION['login_data']);
	$_SESSION['profile_id'] = 1;
}
?>