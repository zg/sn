<?php
require_once('init.php');

$what  = array('image','video','link','status','code','reply');
$who   = array('anonymous','myself','custom');
$where = array('myself','friends','specific_friends','group','everyone','reply');

$who_where = array (
	'anonymous' => array('myself','group','everyone','reply'),
	'myself'    => array('myself','friends','specific_friends','group','everyone','reply'),
	'custom'    => array('myself','group','everyone','reply')
);

//no POST set
if(count($_CLEAN['POST']) == 0)
{
	header('Location: /new_post.php');
}

//missing fields
if(!isset($_CLEAN['POST']['what']) || !isset($_CLEAN['POST']['who']) || !isset($_CLEAN['POST']['where']))
{
	$_SESSION['eid'] = 9;
	if($_SERVER['REMOTE_ADDR'] == "192.168.1.1")
	{
		echo '<h2>$_CLEAN</h2>';
		echo '<pre>';
			var_dump($_CLEAN);
		echo '</pre>';
		echo '<h2>$_FILES</h2>';
		echo '<pre>';
			var_dump($_FILES);
		echo '</pre>';
		die('Condition 2');
	}
	header('Location: /new_post.php');
}

//submitted fields, but empty
if((strlen($_CLEAN['POST']['what']) == 0 && isset($_FILES['content'])) || strlen($_CLEAN['POST']['who']) == 0 || strlen($_CLEAN['POST']['where']) == 0)
{
	$_SESSION['eid'] = 9;
	if($_SERVER['REMOTE_ADDR'] == "192.168.1.1")
	{
		echo '<h2>$_CLEAN</h2>';
		echo '<pre>';
			var_dump($_CLEAN);
		echo '</pre>';
		echo '<h2>$_FILES</h2>';
		echo '<pre>';
			var_dump($_FILES);
		echo '</pre>';
		die('Condition 3');
	}
	header('Location: /new_post.php');
}

//missing content
if(!(isset($_CLEAN['POST']['content']) || isset($_FILES['content'])))
{
	$_SESSION['eid'] = 9;
	if($_SERVER['REMOTE_ADDR'] == "192.168.1.1")
	{
		echo '<h2>$_CLEAN</h2>';
		echo '<pre>';
			var_dump($_CLEAN);
		echo '</pre>';
		echo '<h2>$_FILES</h2>';
		echo '<pre>';
			var_dump($_FILES);
		echo '</pre>';
		die('Condition 4');
	}
	header('Location: /new_post.php');
}

//submitted 'content', but empty
if(isset($_CLEAN['POST']['content']) && strlen($_CLEAN['POST']['content']) == 0 && !isset($_FILES['content']))
{
	$_SESSION['eid'] = 9;
	if($_SERVER['REMOTE_ADDR'] == "192.168.1.1")
	{
		echo '<h2>$_CLEAN</h2>';
		echo '<pre>';
			var_dump($_CLEAN);
		echo '</pre>';
		echo '<h2>$_FILES</h2>';
		echo '<pre>';
			var_dump($_FILES);
		echo '</pre>';
		die('Condition 5');
	}
	header('Location: /new_post.php');
}

//invalid what/who/where
if((!in_array($_CLEAN['POST']['what'],$what) && isset($_FILES['content'])) || !in_array($_CLEAN['POST']['who'],$who) || !in_array($_CLEAN['POST']['where'],$where))
{
	$_SESSION['eid'] = 9;
	if($_SERVER['REMOTE_ADDR'] == "192.168.1.1")
	{
		echo '<h2>$_CLEAN</h2>';
		echo '<pre>';
			var_dump($_CLEAN);
		echo '</pre>';
		echo '<h2>$_FILES</h2>';
		echo '<pre>';
			var_dump($_FILES);
		echo '</pre>';
		die('Condition 6');
	}
	header('Location: /new_post.php');
}

//invalid who and where combination (i.e. anonymous posting to 'friends')
if(!in_array($_CLEAN['POST']['where'],$who_where[$_CLEAN['POST']['who']]))
{
	$_SESSION['eid'] = 9;
	if($_SERVER['REMOTE_ADDR'] == "192.168.1.1")
	{
		echo '<h2>$_CLEAN</h2>';
		echo '<pre>';
			var_dump($_CLEAN);
		echo '</pre>';
		echo '<h2>$_FILES</h2>';
		echo '<pre>';
			var_dump($_FILES);
		echo '</pre>';
		die('Condition 7');
	}
	header('Location: /new_post.php');
}

//specific_friends but no friends submitted
if($_CLEAN['POST']['where'] == 'specific_friends' && !isset($_CLEAN['POST']['friends']))
{
	$_SESSION['eid'] = 9;
	if($_SERVER['REMOTE_ADDR'] == "192.168.1.1")
	{
		echo '<h2>$_CLEAN</h2>';
		echo '<pre>';
			var_dump($_CLEAN);
		echo '</pre>';
		echo '<h2>$_FILES</h2>';
		echo '<pre>';
			var_dump($_FILES);
		echo '</pre>';
		die('Condition 8');
	}
	header('Location: /new_post.php');
}

if($_CLEAN['POST']['what'] == 'reply' && !isset($_CLEAN['POST']['parent_id']))
{
	$_SESSION['eid'] = 9;
	if($_SERVER['REMOTE_ADDR'] == "192.168.1.1")
	{
		echo '<h2>$_CLEAN</h2>';
		echo '<pre>';
			var_dump($_CLEAN);
		echo '</pre>';
		echo '<h2>$_FILES</h2>';
		echo '<pre>';
			var_dump($_FILES);
		echo '</pre>';
		die('Condition 8');
	}
	header('Location: /new_post.php');
}

$group_id = 1; //public forum

$_SESSION['POST'] = $_CLEAN['POST'];

if($_CLEAN['POST']['where'] == 'group')
{
	require_once('classes/class.Group.php');
	$group_instance = new Group;
	$group_instance->set_group_alias($_CLEAN['POST']['group_alias']);
	$group_instance->init();

	if(!$group_instance->exists())
	{
		$_SESSION['eid'] = 10;
		header('Location: /new_post.php');
	}

	$group_id = $group_instance->get_group_id();
	$group_alias = $group_instance->get_group_alias();
	$group_password = $group_instance->get_password();

	if(0 < strlen($group_password))
	{
		$can_post = false;

		if((isset($_CLEAN['POST']['group_password']) && sha1($_CLEAN['POST']['group_password']) == $group_password) || (isset($_SESSION[$group_alias]['password']) && $_SESSION[$group_alias]['password'] == $group_password))
			$can_post = true;

		if(!$can_post)
		{
			$_SESSION['eid'] = 15;
			header('Location: /new_post.php');
		}
	}
}

$privacy_level = 'public';

if($_CLEAN['POST']['where'] == 'myself' || $_CLEAN['POST']['where'] == 'friends' || $_CLEAN['POST']['where'] == 'specific_friends')
	$privacy_level = 'private';

$profile_id = (isset($_SESSION['profile_id']) ? $_SESSION['profile_id'] : 0);
$what = $_CLEAN['POST']['what'];
$who = $_CLEAN['POST']['who'];
$where = $_CLEAN['POST']['where'];
$content = (isset($_FILES['content']) ? $_FILES['content'] : $_CLEAN['POST']['content']);

switch($what)
{
	case 'image':
		require_once('classes/class.Image.php');
		require_once('classes/class.Media.php');

		$image_instance = new Image;
		$image_instance->set_privacy_level($privacy_level);
		$image_instance->set_name($content['name']);
		$image_instance->set_mime_type($content['type']);
		$image_instance->set_tmp_name($content['tmp_name']);
		$image_instance->set_error($content['error']);
		$image_instance->set_size($content['size']);
		$image_instance->init();
		$image_instance->create();
		$image_instance->create_thumb();

		$content_instance = new Media;
		$content_instance->set_profile_id($profile_id);
		$content_instance->set_path($image_instance->get_path());
		$content_instance->set_thumb_path($image_instance->get_thumb_path());
		$content_instance->set_mime_type($image_instance->get_mime_type());
		$content_instance->set_width($image_instance->get_width());
		$content_instance->set_height($image_instance->get_height());
		$content_instance->create();

		$media_id = $content_instance->get_media_id();

		switch($image_instance->get_status())
		{
			case 'invalid':
			break;
			case 'success':
				require_once('classes/class.Post.php');
				$post_instance = new Post;
				$post_instance->set_profile_id($profile_id);
				if($who == 'custom')
					$post_instance->set_alias($_CLEAN['POST']['custom_name']);
				$post_instance->set_group_id($group_id);
				$post_instance->set_type('image');
				$post_instance->set_content($media_id);
				$post_results = $post_instance->create();
				if($post_results)
				{
					unset($_SESSION['POST']);
					$_SESSION['sid'] = 2;

					if($who == 'anonymous' || $who == 'custom')
					{
						header('Location: /post/' . $post_instance->get_post_id());
					}
					elseif($who == 'myself')
					{
						if($where == 'myself' || $where == 'friends' || $where == 'specific_friends')
						{
							header('Location: /social.php');
						}
						elseif($where == 'group')
						{
							header('Location: /' . $group_id);
						}
						else
						{
							header('Location: /post/' . $post_instance->get_post_id());
						}
					}
				}
				else
				{
					$_SESSION['eid'] = 4;
					header('Location: /new_post.php');
				}
			break;
		}
	break;
	case 'video':
	break;
	case 'link':
		if(filter_var($content,FILTER_VALIDATE_URL) === false)
		{
			$_SESSION['eid'] = 6;
			header('Location: /new_post.php');
		}

		require_once('classes/class.common.php');
		$common = new common;

		$title = false;

		$page = $common->curl_get_contents($content);

		if(preg_match("#<title>(.+)<\/title>#iU", $page, $matches))
			$title = $matches[1];

		$content = '<a href=\'' . $content . '\' target=\'_blank\'>' . ($title ? $title : $content) . '</a>';
		
		require_once('classes/class.Post.php');
		$post_instance = new Post;
		$post_instance->set_profile_id($profile_id);
		if($who == 'custom')
			$post_instance->set_alias($_CLEAN['POST']['custom_name']);
		$post_instance->set_group_id($group_id);
		$post_instance->set_type($what);
		$post_instance->set_content($content);
		$post_results = $post_instance->create();
		if($post_results)
		{
			unset($_SESSION['POST']);
			$_SESSION['sid'] = 2;

			if($who == 'anonymous' || $who == 'custom')
			{
				header('Location: /post/' . $post_instance->get_post_id());
			}
			elseif($who == 'myself')
			{
				if($where == 'myself' || $where == 'friends' || $where == 'specific_friends')
				{
					header('Location: /social.php');
				}
				elseif($where == 'group')
				{
					header('Location: /' . $group_id);
				}
				else
				{
					header('Location: /post/' . $post_instance->get_post_id());
				}
			}
		}
		else
		{
			$_SESSION['eid'] = 4;
			header('Location: /new_post.php');
		}
	break;
	case 'status':
	case 'code':
		require_once('classes/class.Post.php');
		$post_instance = new Post;
		$post_instance->set_profile_id($profile_id);
		$post_instance->set_group_id($group_id);
		if($who == 'custom')
			$post_instance->set_alias($_CLEAN['POST']['custom_name']);
		$post_instance->set_type($what);
		$post_instance->set_content($content);
		$post_results = $post_instance->create();
		if($post_results)
		{
			unset($_SESSION['POST']);
			$_SESSION['sid'] = 2;

			if($who == 'anonymous' || $who == 'custom')
			{
				header('Location: /post/' . $post_instance->get_post_id());
			}
			elseif($who == 'myself')
			{
				if($where == 'myself' || $where == 'friends' || $where == 'specific_friends')
				{
					header('Location: /social.php');
				}
				elseif($where == 'group')
				{
					header('Location: /' . $group_id);
				}
				else
				{
					header('Location: /post/' . $post_instance->get_post_id());
				}
			}
		}
		else
		{
			$_SESSION['eid'] = 4;
			header('Location: /new_post.php');
		}
	break;
	case 'reply':
		require_once('classes/class.Post.php');
		$post_instance = new Post;
		$post_instance->set_profile_id($profile_id);
		$post_instance->set_group_id($group_id);
		if($who == 'custom')
			$post_instance->set_alias($_CLEAN['POST']['custom_name']);
		$post_instance->set_parent_id($_CLEAN['POST']['parent_id']);
		$post_instance->set_type($what);
		$post_instance->set_content($content);
		$post_results = $post_instance->create();
		if($post_results)
		{
			unset($_SESSION['POST']);
			$_SESSION['sid'] = 2;

			if($who == 'anonymous' || $who == 'custom')
			{
				header('Location: /post/' . $post_instance->get_parent_id());
			}
			elseif($who == 'myself')
			{
				if($where == 'myself' || $where == 'friends' || $where == 'specific_friends')
				{
					header('Location: /social.php');
				}
				elseif($where == 'group')
				{
					header('Location: /' . $group_id);
				}
				else
				{
					header('Location: /post/' . $post_instance->get_parent_id());
				}
			}
		}
		else
		{
			$_SESSION['eid'] = 4;
			header('Location: /new_post.php');
		}
	break;
}
?>