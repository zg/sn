<?php
require_once('init.php');
require_once('classes/class.Message.php');
require_once('classes/class.Settings.php');
require_once('classes/class.Friends.php');
require_once('classes/class.Profile.php');
require_once('classes/class.ProfileSearch.php');
if(!$is_logged_in)
{
	$_SESSION['eid'] = 2;
	$_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
	header('Location: /login.php');
}
require_once('includes/header.php');
$show_form = false;
if(isset($_CLEAN['POST']['profile_id']) && is_numeric($_CLEAN['POST']['profile_id']))
{
	if(isset($_CLEAN['POST']['confirm']) && $_CLEAN['POST']['confirm'] == "Yes")
	{
		$time = time();

		$friend_instance = new Friends;
		$friend_instance->set_profile_id($_SESSION['profile_id']);
		$friend_instance->set_friend_id($_CLEAN['POST']['profile_id']);
		$friend_instance->init();

		$settings_instance = new Settings;
		$settings_instance->set_profile_id($_CLEAN['POST']['profile_id']);
		$settings_instance->init();

		$confirm_friends = $settings_instance->get_setting('confirm_friends');

		if($confirm_friends == 0)
		{
			$friend_instance->set_date_added($time);
			$friend_instance->set_confirmed(1);
		}

		if($_CLEAN['POST']['profile_id'] == $_SESSION['profile_id'])
		{
			$error_message = 'You cannot add yourself.';

			$message_instance = new Message;
			$message_instance->set_type('error');
			$message_instance->set_message($error_message);
			echo $message_instance->print_formatted();

			$show_form = true;
		}
		elseif($_CLEAN['POST']['profile_id'] == 1)
		{
			$error_message = 'You cannot add Anonymous.';

			$message_instance = new Message;
			$message_instance->set_type('error');
			$message_instance->set_message($error_message);
			echo $message_instance->print_formatted();

			$show_form = true;
		}
		else
		{
			$friend_type = $friend_instance->get_friend_type();
			switch($friend_type)
			{
				case 'friend':
					$error_message = 'You already have this person added.';

					$message_instance = new Message;
					$message_instance->set_type('error');
					$message_instance->set_message($error_message);
					echo $message_instance->print_formatted();

					$show_form = true;
				break;
				case 'requested':
					$error_message = 'You have already requested to be friends with this profile. Please wait until they respond to your request. In the meantime, you can check on this friend request and others by visiting your <a href="friends.php">friends page</a>.';

					$message_instance = new Message;
					$message_instance->set_type('error');
					$message_instance->set_message($error_message);
					echo $message_instance->print_formatted();

					$show_form = true;
				break;
				case 'request':
					$error_message = 'This person already requested to add you! Head over to your <a href="friends.php">friends page</a> to accept their request.';

					$message_instance = new Message;
					$message_instance->set_type('error');
					$message_instance->set_message($error_message);
					echo $message_instance->print_formatted();

					$show_form = true;
				break;
				default: // neither the requester or the requested have any connection
					$friend_instance->set_date_requested($time);

					if($friend_instance->create())
					{
						if($confirm_friends == 0)
						{
							$_SESSION['sid'] = 6;
						}
						else
						{
							$_SESSION['sid'] = 5;
						}
					}
					else
					{
						$_SESSION['eid'] = 12;
					}

					header('Location: /find_friends.php');
				break;
			}
		}
	}
	else
	{
		header('Location: /social.php');
	}
}
elseif(isset($_CLEAN['POST']['query']))
{
	if(0 < strlen($_CLEAN['POST']['query']))
	{
		$profile_search_instance = new ProfileSearch;
		$profile_search_instance->set_query($_CLEAN['POST']['query']);
		$profile_search_instance->query();
		$profile_search_results = $profile_search_instance->get_results();

		if(1 < count($profile_search_results))
		{
			$message = 'No exact matches for "' . $_CLEAN['POST']['query'] . '", but here are some close matches:';
			$message .= '<form method="post">';
			foreach($profile_search_results as $profile_id => $profile_search_result)
			{
				if($profile_id == 1)
				{
					continue;
				}
				$message .= '<p><input type="radio" name="profile_id" id="profile_' . $profile_id . '" value="' . $profile_id . '" /> <label for="profile_' . $profile_id . '">' . $profile_search_result['display_name'] . '</label></p>';
			}
			$message .= '<input type="hidden" name="confirm" value="Yes" />';
			$message .= '<input type="submit" value="Add" />';
			$message .= '</form>';

			$message_instance = new Message;
			$message_instance->set_message($message);
			echo $message_instance->print_unformatted();
		}
		elseif(count($profile_search_results) == 1)
		{
			$message = '<form method="post">';
			foreach($profile_search_results as $profile_id => $profile_search_result)
			{
				$message .= 'Possible exact match found!';
				$message .= '<p>Would you like to add "' . $profile_search_result['display_name'] . '" to your friends?</p>';
				$message .= '<input type="hidden" name="profile_id" value="' . $profile_id . '" />';
				$message .= '<input type="hidden" name="add" value="true" />';
				$message .= '<input type="submit" name="confirm" value="Yes" /> <input type="submit" name="confirm" value="No" />';
			}
			$message .= '</form>';

			$message_instance = new Message;
			$message_instance->set_message($message);
			echo $message_instance->print_unformatted();
		}
		else
		{
			$error_message = 'No found for that query. Please try something else.';

			$message_instance = new Message;
			$message_instance->set_type('error');
			$message_instance->set_message($error_message);
			echo $message_instance->print_formatted();
			$show_form = true;
		}
	}
	else
	{
		$error_message = 'No query was entered! Please type someone to search for.';

		$message_instance = new Message;
		$message_instance->set_type('error');
		$message_instance->set_message($error_message);
		echo $message_instance->print_formatted();
		$show_form = true;
	}
}
else
{
	$show_form = true;
}
if($show_form === true)
{
?>
<h2>Find Friends</h2>
<?php
?>
<p>Type in a query below to search for your friend.</p>
<form method="post">
<input type="text" name="query" maxlength="50"<?php echo (isset($_CLEAN['POST']['query']) ? ' value="' . $_CLEAN['POST']['query'] . '"' : ''); ?> /><input type="submit" value="Find" />
</form>
<?php
}
require_once('includes/footer.php');
?>