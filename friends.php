<?php
require_once('init.php');
if(!$is_logged_in)
{
	$_SESSION['eid'] = 2;
	$_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
	header('Location: /login.php');
}
require_once('classes/class.Profile.php');
require_once('classes/class.Friends.php');
require_once('classes/class.HTML.php');
$HTML = new HTML;
require_once('includes/header.php');

$friend_instance = new Friends;
$friend_instance->set_profile_id($_SESSION['profile_id']);
$friend_instance->fetch_friends();
$friends = $friend_instance->get_friends();
?>
<h2>Manage Friends</h2>
<table cellpadding="5" border="1">
<tr><th>Profile Name</th><th>Friends since</th><th>Last Active</th><th>Actions</th></tr>
<?php
if(0 < count($friends['friends']))
{
	foreach($friends['friends'] as $friend_id => $friend_data)
	{
		echo '<tr>';
			echo '<td>' . $friend_data['display_name'] . ' (' . $friend_id . ')</td>';
			echo '<td>' . date('F j, Y \a\t h:i:s A',$friend_data['date_added']) . '</td>';
			echo '<td>' . ($friend_data['last_active'] == 0 ? 'Never' : date('F j, Y \a\t h:i:s A',$friend_data['last_active'])) . '</td>';
			echo '<td><input type="hidden" name="friend_id" value="' . $friend_id . '"><a href="#" class="remove_friend">Remove</a></td>';
		echo '</tr>';
	}
}
else
{
	echo '<tr><td colspan="3">You have no friends.</td></tr>';
}
?>
</table>
<hr noshade="noshade" color="#CCC" size="1" />
<h2>People you have Requested</h2>
<table cellpadding="5" border="1">
<tr><th>Profile Name</th><th>Date requested</th><th>Actions</th></tr>
<?php
if(0 < count($friends['requested']))
{
	foreach($friends['requested'] as $profile_id => $profile_data)
	{
		echo '<tr>';
			echo '<td>' . $profile_data['display_name'] . ' (' . $profile_id . ')</td>';
			echo '<td>' . date('F j, Y \a\t h:i:s A',$profile_data['date_requested']) . '</td>';
			echo '<td><input type="hidden" name="friend_id" value="' . $profile_id . '"><a href="#" class="remove_friend_request">Remove request</a></td>';
		echo '</tr>';
	}
}
else
{
	echo '<tr><td colspan="3">No friend requests found.</td></tr>';
}
?>
</table>
<hr noshade="noshade" color="#ccc" size="1" />
<h2>People who have Requested you</h2>
<table cellpadding="5" border="1">
<tr><th>Profile Name</th><th>Date requested</th><th>Actions</th></tr>
<?php
if(0 < count($friends['requests']))
{
	foreach($friends['requests'] as $profile_id => $profile_data)
	{
		echo '<tr>';
			echo '<td>' . $profile_data['display_name'] . ' (' . $profile_id . ')</td>';
			echo '<td>' . date('F j, Y \a\t h:i:s A',$profile_data['date_requested']) . '</td>';
			echo '<td><input type="hidden" name="friend_id" value="' . $profile_id . '"><a href="#" class="accept_friend_request">Accept</a> - <a href="#" class="deny_friend_request">Deny</a></td>';
		echo '</tr>';
	}
}
else
{
	echo '<tr><td colspan="3">Nobody has requested to be your friend.</td></tr>';
}
?>
</table>
<?php
require_once('includes/footer.php');
?>