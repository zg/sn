<?php
require_once('init.php');

$return_val = array('response'=>'error','data'=>array());
if(isset($_CLEAN['POST']['action']))
{
	switch($_CLEAN['POST']['action'])
	{
		case 'share':
			if(!$is_logged_in)
			{
				$return_val['data'] = 'not_logged_in';
				die($return_val);
			}
			require_once('classes/class.Post.php');
			$post_instance = new Post;
			if(isset($_CLEAN['POST']['post_id']) && is_numeric($_CLEAN['POST']['post_id']))
			{
				$post_instance->set_post_id($_CLEAN['POST']['post_id']);
			}
			elseif(isset($_CLEAN['POST']['alias']))
			{
				$post_instance->set_alias($_CLEAN['POST']['alias']);
			}
			$post_instance->fetch_post();
			$post_results = $post_instance->get_posts();
			if($post_results)
			{
				$share_instance = new Post;
				$share_instance->set_profile_id($_SESSION['profile_id']);
				$share_instance->set_share_id($post_results[0]['post_id']);
				$share_instance->set_type($post_results[0]['type']);
				$return_val['response'] = ($share_instance->create() ? 'success' : 'error');
				$return_val['data'] = '';
			}
		break;
		case 'delete':
			if(!$is_logged_in)
			{
				$return_val['data'] = 'not_logged_in';
				die($return_val);
			}
			require_once('classes/class.Post.php');
			$post_instance = new Post;
			$post_instance->set_post_id($_CLEAN['POST']['post_id']);
			$post_instance->set_profile_id($_SESSION['profile_id']);
			$post_instance->fetch_post();
			$post_results = $post_instance->get_posts();
			if($post_results)
			{
				if($post_results[0]['profile_id'] == $_SESSION['profile_id'])
				{
					$return_val['response'] = ($post_instance->delete() ? 'success' : 'error');
					$return_val['data'] = '';
				}
			}
		break;
		case 'like':
		case 'unlike':
			if(!$is_logged_in)
			{
				$return_val['data'] = 'not_logged_in';
				die($return_val);
			}
			require_once('classes/class.Like.php');
			$like_instance = new Like;
			$like_instance->set_profile_id($_SESSION['profile_id']);
			$like_instance->set_post_id($_CLEAN['POST']['post_id']);
			$like_instance->init();
			$like = 0;
			if($_CLEAN['POST']['action'] == 'like')
				$like = 1;
			elseif($_CLEAN['POST']['action'] == 'unlike')
				$like = 0;
			if(isset($_CLEAN['POST']['post_id']) && is_numeric($_CLEAN['POST']['post_id']))
			{
				if($like_instance->exists()) // like exists
				{
					if($like == 0) // they 'unliked' the post
						$like_instance->delete();
				}
				else // they 'liked' the post
				{
					$like_instance->create();
				}

				$return_val['response'] = 'success';
			}
		break;
		case 'accept_friend_request':
			if(!$is_logged_in)
			{
				$return_val['data'] = 'not_logged_in';
				die($return_val);
			}
			require_once('classes/class.Friends.php');
			if(isset($_CLEAN['POST']['profile_id']) && is_numeric($_CLEAN['POST']['profile_id']))
			{
				$time = time();
				$friend_instance = new Friends;
				$friend_instance->set_profile_id($_CLEAN['POST']['profile_id']);
				$friend_instance->set_friend_id($_SESSION['profile_id']);
				$friend_instance->set_confirmed(1);
				$friend_instance->set_date_added($time);
				$friend_instance->update();
				$return_val['response'] = 'success';
			}
		break;
		case 'deny_friend_request':
			if(!$is_logged_in)
			{
				$return_val['data'] = 'not_logged_in';
				die($return_val);
			}
			require_once('classes/class.Friends.php');
			if(isset($_CLEAN['POST']['profile_id']) && is_numeric($_CLEAN['POST']['profile_id']))
			{
				$friend_instance = new Friends;
				$friend_instance->set_profile_id($_CLEAN['POST']['profile_id']);
				$friend_instance->set_friend_id($_SESSION['profile_id']);
				$friend_instance->delete();
				$return_val['response'] = 'success';
			}
		break;
		case 'remove_friend_request':
			if(!$is_logged_in)
			{
				$return_val['data'] = 'not_logged_in';
				die($return_val);
			}
			require_once('classes/class.Friends.php');
			if(isset($_CLEAN['POST']['profile_id']) && is_numeric($_CLEAN['POST']['profile_id']))
			{
				$friend_instance = new Friends;
				$friend_instance->set_profile_id($_SESSION['profile_id']);
				$friend_instance->set_friend_id($_CLEAN['POST']['profile_id']);
				$friend_instance->delete();
				$return_val['response'] = 'success';
			}
		break;
		case 'remove_friend':
			if(!$is_logged_in)
			{
				$return_val['data'] = 'not_logged_in';
				die($return_val);
			}
			require_once('classes/class.Friends.php');
			if(isset($_CLEAN['POST']['profile_id']) && is_numeric($_CLEAN['POST']['profile_id']))
			{
				$friend_instance = new Friends;
				$friend_instance->set_profile_id($_SESSION['profile_id']);
				$friend_instance->set_friend_id($_CLEAN['POST']['profile_id']);
				$friend_instance->delete();

				$friend_instance = new Friends;
				$friend_instance->set_profile_id($_CLEAN['POST']['profile_id']);
				$friend_instance->set_friend_id($_SESSION['profile_id']);
				$friend_instance->delete();
				$return_val['response'] = 'success';
			}
		break;
		case 'check_group_alias':
			if(isset($_CLEAN['POST']['group_alias']) && 0 < strlen($_CLEAN['POST']['group_alias']))
			{
				require_once('classes/class.Group.php');
				$group_instance = new Group;

				$group_instance->set_group_alias($_CLEAN['POST']['group_alias']);
				$group_instance->init();

				$return_val['response'] = 'success';
				$return_val['data'] = array('does_not_exists','public');

				if($group_instance->exists())
				{
					$return_val['data'] = array('exists',$group_instance->get_privacy_level());
				}
			}
		break;
	}
}
die(json_encode($return_val));
?>