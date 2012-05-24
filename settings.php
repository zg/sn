<?php
require_once('init.php');
require_once('classes/class.Profile.php');
require_once('classes/class.Settings.php');
if(!$is_logged_in)
{
	$_SESSION['eid'] = 2;
	$_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
	header('Location: /login.php');
}
$profile_instance = new Profile;
$profile_instance->set_profile_id($_SESSION['profile_id']);
$profile_instance->init();

$settings_instance = new Settings;
$settings_instance->set_profile_id($_SESSION['profile_id']);
$settings_instance->init();
$current_settings = array (
	'display_name' => $profile_instance->get_display_name()
);
foreach($settings_instance->get_settings() as $key => $value)
	$current_settings[$key] = $value;

require_once('includes/header.php');

if(0 < count($_CLEAN['POST']))
{
	foreach($_CLEAN['POST'] as $setting_index => $new_setting)
	{
		switch($setting_index)
		{
			case 'display_name':
				$profile_instance->set_display_name($new_setting);
			break;
			case 'privacy_level':
				if($new_setting == 'public' || $new_setting == 'friends_only' || $new_setting == 'private')
				{
					$settings_instance->set_setting('privacy_level',$new_setting);
				}
			break;
			case 'confirm_friends':
				if($new_setting == 'on')
				{
					$settings_instance->set_setting('confirm_friends',1);
				}
			break;
			case 'appear_offline':
				if($new_setting == 'on')
				{
					$settings_instance->set_setting('appear_offline',1);
				}
			break;
		}
	}

	foreach(array('confirm_friends','appear_offline') as $setting_index)
	{
		if(!isset($_CLEAN['POST'][$setting_index]))
		{
			$settings_instance->set_setting($setting_index,0);
		}
	}

	if($settings_instance->update() && $profile_instance->update())
	{
		$_SESSION['sid'] = 4;
	}
	else
	{
		$_SESSION['eid'] = 11;
	}
	header('Location: /settings.php');
}
?>
<h2>Settings</h2>
<form method="post">
<?php
foreach($current_settings as $index => $setting)
{
	echo '<p>';
	switch($index)
	{
		case 'display_name':
			echo 'Display name: <input type="text" name="display_name" maxlength="25" value="' . $setting . '" />';
		break;
		case 'privacy_level':
			echo 'Profile visibility: <select name="privacy_level">';
				foreach(array('public' => 'Public','friends_only' => 'Friends only','private' => 'Private') as $key => $value)
				{
					echo '<option value="' . $key . '"' . ($setting == $key ? ' selected="selected"' : '') . '>' . $value . '</option>';
				}
			echo '</select>';
		break;
		case 'confirm_friends':
			echo '<input type="checkbox" id="confirm_friends" name="confirm_friends"' . ($setting == 1 ? ' checked="checked"' : '') . ' /> <label for="confirm_friends">Confirm friend requests</label>';
		break;
		case 'appear_offline':
			echo '<input type="checkbox" id="appear_offline" name="appear_offline"' . ($setting == 1 ? ' checked="checked"' : '') . ' /> <label for="appear_offline">Appear offline</label>';
		break;
		case 'profile_id':
		default:
		break;
	}
	echo '</p>';
}
?><input type="submit" /></form>
<?php
require_once('includes/footer.php');
?>