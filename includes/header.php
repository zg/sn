<?php
require_once('init.php');

$menu_items = array (
	'logged_in' => array (
		'left' => array (
			'Public Forum' => '/',
			'News Feed' => '/social.php',
//			'New Post' => '/new_post.php',
			'Your profile' => '/profile.php',
			'Manage friends' => '/friends.php',
			'Find friends' => '/find_friends.php'
		),
		'right' => array (
			'Settings' => '/settings.php',
			'Logout' => '/logout.php'
		)
	),


	'logged_out' => array (
		'left' => array (
			'Public Forum' => '/',
			'New Post' => '/new_post.php'
		),
		'right' => array (
			'Register' => '/register.php',
			'Login' => '/login.php'
		)
	)
);
?>
<html>
<head>
<title>needs a title</title>
<meta http-equiv="Media-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="/main.css" />
<script type="text/javascript" src="/jquery.js"></script>
<script type="text/javascript" src="/main.js"></script>
</head>
<body>
<div class="container">
<div class="title"><?php if($is_logged_in){ ?><div class="right"><form method="post" action="search.php"><input tabindex="1" type="text" name="query" value="Search" /></form></div><?php } ?>needs a title</div>
<div class="menu">
<?php
if($is_logged_in)
{
?>
<div class="right"><?php foreach($menu_items['logged_in']['right'] as $title => $link){ echo '<a href="' . $link . '">' . $title . '</a>'; } ?></div>
<?php
	foreach($menu_items['logged_in']['left'] as $title => $link)
	{
		echo '<a href="' . $link . '">' . $title . '</a>';
	}
}
else
{
?>
<div class="right"><?php foreach($menu_items['logged_out']['right'] as $title => $link){ echo '<a href="' . $link . '">' . $title . '</a>'; } ?></div>
<?php
	foreach($menu_items['logged_out']['left'] as $title => $link)
	{
		echo '<a href="' . $link . '">' . $title . '</a>';
	}
}
?></div>
<div class="content">
<?php
if(isset($_SESSION['eid']) && is_numeric($_SESSION['eid']))
{
	$error_message = (isset($error_messages[$_SESSION['eid']]) ? $error_messages[$_SESSION['eid']] : '');
	unset($_SESSION['eid']);
}
if(isset($_SESSION['sid']) && is_numeric($_SESSION['sid']))
{
	$success_message = (isset($success_messages[$_SESSION['sid']]) ? $success_messages[$_SESSION['sid']] : '');
	unset($_SESSION['sid']);
}
if(isset($message) && strlen($message))
{
	$message_instance = new Message;
	$message_instance->set_message($message);
	echo $message_instance->print_unformatted();
}
elseif(isset($success_message) && strlen($success_message))
{
	$message_instance = new Message;
	$message_instance->set_type('success');
	$message_instance->set_message($success_message);
	echo $message_instance->print_formatted();
}
elseif(isset($error_message) && strlen($error_message))
{
	$message_instance = new Message;
	$message_instance->set_type('error');
	$message_instance->set_message($error_message);
	echo $message_instance->print_formatted();
}
?>