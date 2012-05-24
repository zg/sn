<?php
require_once('init.php');
require_once('classes/class.Profile.php');
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
	<span class="submit_button"><input tabindex="40" type="submit" value="Post" /></span>
	<p class="posting_as">
		Posting as: <select tabindex="20" name="who">
			<option value="">-- Select --</option>
			<option value="anonymous">Anonymous</option>
<?php if($is_logged_in){ ?>			<option value="myself"><?php echo $display_name; ?></option>
<?php } ?>
			<option value="custom">Custom</option>
		</select>
	</p>
	<p class="post_to">
		Post to: <select tabindex="30" name="where">
			<option value="">-- Select --</option>
			<optgroup label="private">
				<option value="myself">just me</option>
<?php if($is_logged_in){ ?>
				<option value="friends">my friends</option>
				<option value="specific_friends">specific friends</option>
<?php } ?>			</optgroup>
			<optgroup label="public">
				<option value="group">a group</option>
				<option value="everyone">everyone</option>
			</optgroup>
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
require_once('includes/footer.php');
?>