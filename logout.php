<?php
require_once('init.php');
if($is_logged_in)
	session_unset();
$_SESSION['sid'] = 1;
header('Location: /login.php');
?>