<?php
require_once('init.php');
require_once('classes/class.common.php');
require_once('classes/class.ProfileSearch.php');
require_once('classes/class.GroupSearch.php');
require_once('classes/class.StatusSearch.php');
require_once('classes/class.HTML.php');

if(!$is_logged_in)
{
	$_SESSION['eid'] = 2;
	$_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
	header('Location: /login.php');
}
require_once('includes/header.php');
$profile_search_instance = new ProfileSearch;
$group_search_instance = new GroupSearch;
$status_search_instance = new StatusSearch;
$HTML = new HTML();
if(isset($_CLEAN['POST']['query']))
{
	if(0 < strlen($_CLEAN['POST']['query']))
	{
		$profile_search_instance->set_query($_CLEAN['POST']['query']);
		$profile_search_instance->query();
		$profile_results = $profile_search_instance->get_results();
		
		$group_search_instance->set_query($_CLEAN['POST']['query']);
		$group_search_instance->query();
		$group_results = $group_search_instance->get_results();

		$status_search_instance->set_query($_CLEAN['POST']['query']);
		$status_search_instance->query();
		$status_results = $status_search_instance->get_results();
	}
}
else
{
	header('Location: /');
}
?>
<h2>Searching for "<?php echo $_CLEAN['POST']['query']; ?>"</h2>
<hr />
<h4>People</h4>
<form method="post" action="find_friends.php">
<?php
if(0 < count($profile_results))
{
	foreach($profile_results as $profile_id => $profile_data)
	{
		if($profile_id == 1)
			continue;
?>
<div><input type="radio" name="profile_id" id="profile_<?php echo $profile_id; ?>" value="<?php echo $profile_id; ?>" /> <label for="profile_<?php echo $profile_id; ?>"><?php echo $profile_data['display_name']; ?></label></div>
<?php
	}
?>
<br /><input type="submit" value="Add" />
<?php
}
else
{
	echo '<div class="error_message">No people found for that query.</div>';
}
?>
</form>
<h4>Groups</h4>
<?php
if(0 < count($group_results))
{
	foreach($group_results as $group_result)
	{
		echo '<div><a href="/' . $group_result['group_alias'] . '">' . $group_result['group_name'] . '</a></div>';
	}
}
else
{
	echo '<div class="error_message">No groups found for that query.</div>';
}
?>
<h4>Statuses</h4>
<?php
if(0 < count($status_results))
{
	echo $HTML->post_format($status_results);
?>
<?php
}
else
{
	echo 'No public statuses found for that query.';
}
require_once('includes/footer.php');
?>