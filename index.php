<?php
require_once('init.php');
require_once('classes/class.Profile.php');
require_once('classes/class.Group.php');
require_once('classes/class.GroupMembers.php');
require_once('classes/class.Post.php');
require_once('classes/class.HTML.php');

require_once('includes/header.php');

$q = $_CLEAN['GET']['q'];

$pages = array('news','faq','terms','contact','privacy','help');

$parts = array();

if(strpos($q,'/'))
{
	$parts = explode('/',$q);
}

if(in_array($q,$pages) || 0 < count($parts))
{
	if(0 < count($parts))
	{
		switch($parts[0])
		{
			case 'uploads':
				header('Location: /');
			break;
			case 'post':
				if(isset($parts[1]) && is_numeric($parts[1]))
				{
					$post_instance = new Post;
					$post_instance->set_post_id($parts[1]);
					$post_instance->init();
					if($post_instance->exists())
					{
						$group_instance = new Group;
						$group_instance->set_group_id($post_instance->get_group_id());
						$group_instance->init();
						$group_alias = $group_instance->get_group_alias();
						$privacy_level = $group_instance->get_privacy_level();

						$can_view = false;

						switch($privacy_level)
						{
							case 'public':
								$can_view = true;
							break;
							case 'password_protected':
								$can_view = false;

								if(isset($_SESSION[$group_alias]['password']))
								{
									if($_SESSION[$group_alias]['password'] == $group_instance->get_password())
									{
										$can_view = true;
									}
								}
							break;
							case 'profile_protected':
								$group_members = $group_instance->get_group_members();

								if(0 < strlen($group_instance->get_password()))
								{
									if($group_members)
									{
										foreach($group_members as $member)
										{
											if($_SESSION['profile_id'] == $member['profile_id'] && $member['permissions']['can_read'])
											{
												$is_member = true;
												break;
											}
										}
									}
								}
							break;
						}

						if($can_view)
						{
							$post_instance->fetch_post();
							$HTML = new HTML;
							echo $HTML->post_format($post_instance->get_posts());

							$reply_instance = new Post;
							$reply_instance->set_parent_id($post_instance->get_post_id());
							$reply_instance->init();
							$reply_instance->fetch_replies();

							echo $HTML->post_format($reply_instance->get_posts());
							
							if($is_logged_in)
							{
								$profile_instance = new Profile;
								$profile_instance->set_profile_id($_SESSION['profile_id']);
								$profile_instance->init();
								$display_name = $profile_instance->get_display_name();
							}
?>
<p>
<form method="post" action="/post.php">
	<span class="new_post"><textarea tabindex="10" name="content" class="reply input_gray" style="width:100%">Reply to this post...</textarea><input type="hidden" name="what" value="reply" />
	<br /><br />
	<span class="submit_button"><input type="hidden" name="where" value="reply" /><input type="hidden" name="parent_id" value="<?php echo $parts[1]; ?>" /><input tabindex="40" type="submit" value="Post" /></span>
	<p class="posting_as">
		Posting as: <select tabindex="20" name="who">
			<option value="">-- Select --</option>
			<option value="anonymous">Anonymous</option>
<?php if($is_logged_in){ ?>			<option value="myself"><?php echo $display_name; ?></option>
<?php } ?>
			<option value="custom">Custom</option>
		</select>
	</p>
	</span>
</form>
</p>
<?php
$flag = false;
if(isset($_SESSION['POST']))
{
?>
<script type="text/javascript">
$(document).ready(function(){
	$('select[name="what"]').val('<?php echo $_SESSION['POST']['what']; ?>').trigger('change');
<?php
	switch($_SESSION['POST']['what'])
	{
		case 'image':
		case 'video':
		default:
		break;
		case 'link':
		case 'status':
?>
	$('input[name="content"]').css('color','#000').val('<?php echo $_SESSION['POST']['content']; ?>');
<?php
		break;
		case 'code':
?>
	$('textarea[name="content"]').html('<?php echo str_replace("'","\'",$_SESSION['POST']['content']); ?>').trigger('change');
<?php
		break;
	}
?>
	$('select[name="who"]').val('<?php echo $_SESSION['POST']['who']; ?>').trigger('change');
	$('select[name="where"]').val('<?php echo $_SESSION['POST']['where']; ?>').trigger('change');
<?php if(isset($_SESSION['POST']['custom_name'])){ ?>	$('input[name="custom_name"]').trigger('focus').val('<?php echo $_SESSION['POST']['custom_name']; ?>').trigger('blur');
<?php } ?>
<?php if(isset($_SESSION['POST']['group_alias'])){ ?>	$('input[name="group_alias"]').trigger('focus').val('<?php echo $_SESSION['POST']['group_alias']; ?>').trigger('blur');
<?php } ?>
});
</script>
<?php
								unset($_SESSION['POST']);
							}
						}
						else
						{
							$error_message = 'You do not have permission to view this post.';

							$message_instance = new Message;
							$message_instance->set_type('error');
							$message_instance->set_message($error_message);
							echo $message_instance->print_formatted();
						}
					}
					else
					{
						$error_message = 'That post doesn\'t exist.';

						$message_instance = new Message;
						$message_instance->set_type('error');
						$message_instance->set_message($error_message);
						echo $message_instance->print_formatted();
					}
				}
			break;
		}
	}
	else
	{
		switch($q)
		{
			case 'news':
?><h2>News</h2><p>2012-02-18 - this website really needs a title..<?php
			break;
		}
	}
}
elseif(strlen($q)) // we assume they're viewing a group
{
	$group_instance = new Group;

	if(is_numeric($q))
		$group_instance->set_group_id($q);
	else
		$group_instance->set_group_alias($q);

	$group_instance->init();

	$group_alias = $group_instance->get_group_alias();
	$privacy_level = $group_instance->get_privacy_level();

	if(!$group_instance->exists())
	{
		$_SESSION['eid'] = 7;
		if(!isset($_GET['doesnt_exist']))
			header('Location: /' . $q . '?doesnt_exist');
	}
	else
	{
		$privacy_level = $group_instance->get_privacy_level();

		$can_view = false;

		switch($privacy_level)
		{
			case 'public':
				$can_view = true;
			break;
			case 'password_protected':
				$can_view = false;

				if(isset($_SESSION[$group_alias]['password']))
				{
					if($_SESSION[$group_alias]['password'] == $group_instance->get_password())
					{
						$can_view = true;
					}
				}
			break;
			case 'profile_protected':
				$group_members = $group_instance->get_group_members();

				if(0 < strlen($group_instance->get_password()))
				{
					if($group_members)
					{
						foreach($group_members as $member)
						{
							if($_SESSION['profile_id'] == $member['profile_id'] && $member['permissions']['can_read'])
							{
								$is_member = true;
								break;
							}
						}
					}
				}
			break;
		}

		if($can_view)
		{
			$profile_instance = new Profile;
			$profile_instance->set_profile_id($group_instance->get_owner_id());
			$profile_instance->init();

			$post_instance = new Post;
			$post_instance->set_group_id($group_instance->get_group_id());
			$post_instance->set_order_by('post.created DESC');
			$post_instance->init();

			$group_members = $group_instance->get_group_members();

			echo '<h2>' . $group_instance->get_group_name() . '</h2>';
			echo '<p>' . $group_instance->get_description() . '</p>';
			echo '<p>Created by <a href="profile.php?profile_id=' . $group_instance->get_owner_id() . '">' . $profile_instance->get_display_name() . '</a></p>';
			echo '<h3>Our members</h3>';
			echo '<ul>';
			if($group_members)
			{
				foreach($group_members as $member)
				{
					echo '<li>' . $member['display_name'] . ' ()</li>';
				}
			}
			echo '</ul>';

if($is_logged_in)
{
	$profile_instance = new Profile;
	$profile_instance->set_profile_id($_SESSION['profile_id']);
	$profile_instance->init();
	$display_name = $profile_instance->get_display_name();
}
require_once('includes/header.php');
?>
<p>
<form method="post" action="post.php">
	<span class="new_post_links"><b class="new_post_status new_post_selected">Update Status</b> <b class="new_post_link">Post Link</b> <b class="new_post_image">Add Image</b> <b class="new_post_video">Post Video (<font color="red">coming soon</font>)</b> <b class="new_post_code">Paste Code</b></span>
	<span class="new_post"><textarea tabindex="10" name="content" class="status input_gray" style="width:100%">What's new?</textarea><input type="hidden" name="what" value="status" />
	<br /><br />
	<span class="submit_button"><input type="hidden" name="where" value="group" /><input tabindex="40" type="submit" value="Post" /></span>
	<p class="posting_as">
		Posting as: <select tabindex="20" name="who">
			<option value="">-- Select --</option>
			<option value="anonymous">Anonymous</option>
<?php if($is_logged_in){ ?>			<option value="myself"><?php echo $display_name; ?></option>
<?php } ?>
			<option value="custom">Custom</option>
		</select>
	</p>
	</span>
</form>
</p>
<?php
$flag = false;
if(isset($_SESSION['POST']))
{
?>
<script type="text/javascript">
$(document).ready(function(){
	$('select[name="what"]').val('<?php echo $_SESSION['POST']['what']; ?>').trigger('change');
<?php
	switch($_SESSION['POST']['what'])
	{
		case 'image':
		case 'video':
		default:
		break;
		case 'link':
		case 'status':
?>
	$('input[name="content"]').css('color','#000').val('<?php echo $_SESSION['POST']['content']; ?>');
<?php
		break;
		case 'code':
?>
	$('textarea[name="content"]').html('<?php echo str_replace("'","\'",$_SESSION['POST']['content']); ?>').trigger('change');
<?php
		break;
	}
?>
	$('select[name="who"]').val('<?php echo $_SESSION['POST']['who']; ?>').trigger('change');
	$('select[name="where"]').val('<?php echo $_SESSION['POST']['where']; ?>').trigger('change');
<?php if(isset($_SESSION['POST']['custom_name'])){ ?>	$('input[name="custom_name"]').trigger('focus').val('<?php echo $_SESSION['POST']['custom_name']; ?>').trigger('blur');
<?php } ?>
<?php if(isset($_SESSION['POST']['group_alias'])){ ?>	$('input[name="group_alias"]').trigger('focus').val('<?php echo $_SESSION['POST']['group_alias']; ?>').trigger('blur');
<?php } ?>
});
</script>
<?php
	unset($_SESSION['POST']);
}
			echo '<h3>Posts</h3>';
			if($post_instance->exists())
			{
				$post_instance->fetch_group_posts();
				$HTML = new HTML;
				echo $HTML->post_format($post_instance->get_posts());
			}
			else
			{
				echo 'No post updates from this group.';
			}
		}
		elseif($privacy_level == 'password_protected')
		{
			if(isset($_CLEAN['POST']['password']))
			{
				if(sha1($_CLEAN['POST']['password']) == $group_instance->get_password())
				{
					$_SESSION[$group_alias]['password'] = sha1($_CLEAN['POST']['password']);
					$_SESSION['sid'] = 3;
					header('Location: /' . $q);
				}
			}
			echo '<p><form method="post">This group is password protected. You may attempt to enter the password three times, once per day.<br /><br />Password: <input type="password" name="password" /><input type="submit" /></form></p>';
		}
		elseif($privacy_level == 'profile_protected')
		{
			echo '<p>This group is profile protected. You must be added to the group in order to view the contents of this page.</p>';
		}
		elseif($privacy_level == 'private')
		{
			echo '<p>This group is private and cannot be accessed by anyone but the owner.</p>';
		}
	}
}
else
{
	if($is_logged_in)
	{
		$profile_instance = new Profile;
		$profile_instance->set_profile_id($_SESSION['profile_id']);
		$profile_instance->init();
		$display_name = $profile_instance->get_display_name();
	}
	echo '<h2>Welcome to the Public Forum!</h2>';
?>
<p>
<form method="post" action="post.php">
	<span class="new_post_links"><b class="new_post_status new_post_selected">Update Status</b> <b class="new_post_link">Post Link</b> <b class="new_post_image">Add Image</b> <b class="new_post_video">Post Video (<font color="red">coming soon</font>)</b> <b class="new_post_code">Paste Code</b></span>
	<span class="new_post"><textarea tabindex="10" name="content" class="status input_gray" style="width:100%">What's new?</textarea><input type="hidden" name="what" value="status" />
	<br /><br />
	<span class="submit_button"><input type="hidden" name="where" value="everyone" /><input tabindex="40" type="submit" value="Post" /></span>
	<p class="posting_as">
		Posting as: <select tabindex="20" name="who">
			<option value="">-- Select --</option>
			<option value="anonymous">Anonymous</option>
<?php if($is_logged_in){ ?>			<option value="myself"><?php echo $display_name; ?></option>
<?php } ?>
			<option value="custom">Custom</option>
		</select>
	</p>
	</span>
</form>
</p>
<?php
$flag = false;
if(isset($_SESSION['POST']))
{
?>
<script type="text/javascript">
$(document).ready(function(){
	$('select[name="what"]').val('<?php echo $_SESSION['POST']['what']; ?>').trigger('change');
<?php
	switch($_SESSION['POST']['what'])
	{
		case 'image':
		case 'video':
		default:
		break;
		case 'link':
		case 'status':
?>
	$('input[name="content"]').css('color','#000').val('<?php echo $_SESSION['POST']['content']; ?>');
<?php
		break;
		case 'code':
?>
	$('textarea[name="content"]').html('<?php echo str_replace("'","\'",$_SESSION['POST']['content']); ?>').trigger('change');
<?php
		break;
	}
?>
	$('select[name="who"]').val('<?php echo $_SESSION['POST']['who']; ?>').trigger('change');
	$('select[name="where"]').val('<?php echo $_SESSION['POST']['where']; ?>').trigger('change');
<?php if(isset($_SESSION['POST']['custom_name'])){ ?>	$('input[name="custom_name"]').trigger('focus').val('<?php echo $_SESSION['POST']['custom_name']; ?>').trigger('blur');
<?php } ?>
<?php if(isset($_SESSION['POST']['group_alias'])){ ?>	$('input[name="group_alias"]').trigger('focus').val('<?php echo $_SESSION['POST']['group_alias']; ?>').trigger('blur');
<?php } ?>
});
</script>
<?php
	unset($_SESSION['POST']);
}
	$post_instance = new Post;
	$post_instance->set_group_id(1);
	$post_instance->set_order_by('post.created DESC');
	$post_instance->fetch_group_posts();
	$group_posts = $post_instance->get_posts();

	if(0 < count($group_posts))
	{
		$HTML = new HTML;
		echo $HTML->post_format($group_posts);
	}
	else
	{
		echo 'No post updates from this group.';
	}
}

require_once('includes/footer.php');
?>