<?php
require_once('init.php');
if(!$is_logged_in)
{
	$_SESSION['eid'] = 1;
	$_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
	header('Location: /login.php');
}
require_once('classes/class.Stream.php');
require_once('classes/class.HTML.php');
require_once('includes/header.php');
if($is_logged_in)
{
	$profile_instance = new Profile;
	$profile_instance->set_profile_id($_SESSION['profile_id']);
	$profile_instance->init();
	$display_name = $profile_instance->get_display_name();
}
?>
<p>
<form method="post" action="post.php">
	<span class="new_post_links"><b class="new_post_status new_post_selected">Update Status</b> <b class="new_post_link">Post Link</b> <b class="new_post_image">Add Image</b> <b class="new_post_video">Post Video (<font color="red">coming soon</font>)</b> <b class="new_post_code">Paste Code</b></span>
	<span class="new_post"><textarea tabindex="10" name="content" class="status input_gray" style="width:100%">What's new?</textarea><input type="hidden" name="what" value="status" />
	<br /><br />
	<span class="submit_button"><input type="hidden" name="who" value="myself" /><input tabindex="40" type="submit" value="Post" /></span>
	<p class="post_to">
		Post to: <select tabindex="30" name="where">
			<option value="">-- Select --</option>
<?php if($is_logged_in){ ?>
			<option value="friends" selected="selected">my friends</option>
			<option value="specific_friends">specific friends</option>
<?php } ?>		</select>
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
$stream_instance = new Stream;
$stream_instance->set_profile_id($_SESSION['profile_id']);
$stream_instance->set_order_by('post.created DESC');
$stream_instance->fetch_stream();
$HTML = new HTML;
if($is_logged_in)
{
	$stream_data = $stream_instance->get_stream();
	if($stream_data)
	{
		echo $HTML->post_format($stream_data);
	}
	else
	{
		echo 'No posts to display.';
	}
}
else
{
?>
<h2>Welcome to needs a title!</h2>
Register above to start connecting with friends!
<?php
}
require_once('includes/footer.php');
?>