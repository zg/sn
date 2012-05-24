<?php
function clean($elem)
{
	if(!is_array($elem))
		$elem = htmlentities($elem,ENT_QUOTES | ENT_IGNORE,'UTF-8',false);
	else
		foreach ($elem as $key => $value)
			$elem[$key] = $this->clean($value);
	return $elem;
}
//require this to get a database connection
$connect = mysql_connect('','','');
mysql_select_db('');
$_CLEAN = array('POST'=>array(),'GET'=>array());
foreach($_POST as $key => $value)
{
	if($key == 'password' || $key == 'confirm_password')
		$_CLEAN['POST'][$key] = $value;
	else
		$_CLEAN['POST'][$key] = clean($value);
}
foreach($_GET as $key => $value)
	$_CLEAN['GET'][$key] = clean($value);
?>