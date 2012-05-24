<?php
require_once('init.php');
if(!$is_logged_in)
{
	$_SESSION['eid'] = 2;
	$_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
	header('Location: /login.php');
}
require_once('classes/class.Profile.php');
require_once('classes/class.Settings.php');
require_once('classes/class.Friends.php');
require_once('classes/class.Post.php');
require_once('classes/class.HTML.php');

if(isset($_CLEAN['GET']['profile_id']) && is_numeric($_CLEAN['GET']['profile_id']) && 0 < $_CLEAN['GET']['profile_id'])
	$profile_id = $_CLEAN['GET']['profile_id'];
elseif(isset($_SESSION['eid']))
{
	require_once('includes/header.php');
	require_once('includes/footer.php');
	exit;
}
else
{
	$profile_id = $_SESSION['profile_id'];
}

$message = '';

require_once('includes/header.php');

$profile_instance = new Profile;
$profile_instance->set_profile_id($profile_id);
$profile_instance->init();

$settings_instance = new Settings;
$settings_instance->set_profile_id($profile_id);
$privacy_level = $settings_instance->get_setting('privacy_level');

if($_SESSION['profile_id'] !== $profile_id)
{
	if($privacy_level == 'private')
	{
		$_SESSION['eid'] = 14;
		header('Location: /profile.php');
	}
	elseif($privacy_level == 'friends_only')
	{
		$friends_instance = new Friends;
		$friends_instance->set_profile_id($profile_id);
		$friends_instance->init();

		if(!$friends_instance->exists())
		{
			$_SESSION['eid'] = 13;
			header('Location: /profile.php');
		}
	}
}

$post_instance = new Post;
$post_instance->set_profile_id($profile_id);
$post_instance->set_order_by('post.created DESC');

$profile_data = $profile_instance->exists();

if(!$profile_data)
{
	$message = 'Profile not found.';
	$_SESSION['eid'] = 3;
	header('Location: /profile.php');
}
else
{
	$profile_id = $profile_instance->get_profile_id();
	$display_name = $profile_instance->get_display_name();
	$post_instance->fetch_profile_posts();
}

$HTML = new HTML;

$profile_posts = $post_instance->get_posts();
echo '<h2>' . $display_name . '</h2>';
echo '<p><b>Status Updates</b></p>';
if($profile_posts)
{
	echo $HTML->post_format($profile_posts);
}
else
{
	if($_SESSION['profile_id'] == $profile_id)
	{
		echo 'You\'ve never posted anything!';
	}
	else
	{
		echo 'No post updates from this profile.';
	}
}
require_once('includes/footer.php');
?>