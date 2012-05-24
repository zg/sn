<?php
class common {
	public function clean($variable)
	{
		if(!is_array($variable))
			$variable = htmlentities($variable,ENT_QUOTES | ENT_IGNORE,'UTF-8',false);
		else
			foreach ($variable as $key => $value)
				$variable[$key] = $this->clean($value);
		return $variable;
	}

	public function validate_email($email,$return=false)
	{
		return (preg_match("/([0-9a-z][-_.]?[0-9a-z]*)@([0-9a-z][-.]?[0-9a-z]*\\.[a-z]{2,3})/",$email,$match)?($return?$match:true):false);
	}

	public function validate_url($url)
	{
		return (preg_match("^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$", $url) ? true : false);
	}

	public function convert_smart_quotes($str)
	{
		return str_replace(array(chr(145),chr(146),chr(147),chr(148),chr(151)),array("'","'",'"','"','-'),$str);
	}

	public function generate_random_string($length=8)
	{
		$image_name = '';

		for($character = 0; $character < $length; $character++)
		{
			$possible_chars = range('a','z');

			foreach(range(0,9) as $number)
				$possible_chars[] = $number;

			for($shuffle = 0; $shuffle < mt_rand(1,100); $shuffle++)
				shuffle($possible_chars);

			$image_name .= $possible_chars[mt_rand(0,(count($possible_chars) - 1))];
		}

		return $image_name;
	}

	public function password_strength($password)
	{
		$strength = 0;
		$patterns = array('#[a-z]#','#[A-Z]#','#[0-9]#','/[¬!"£$%^&*()`{}\[\]:@~;\'#<>?,.\/\\-=_+\|]/');
		foreach($patterns as $pattern)
			if(preg_match($pattern,$password))
				$strength++;
		return $strength; // 0-1 = weak, 2 = not weak, 3 = acceptable, 4 = strong
	}

	public function format_size($size,$decimals=1)
	{
		$suffix = array('B','KB','MB','GB','TB','PB','EB','ZB','YB','NB','DB');
		$index = 0;
		while($size >= 1024 && ($index < count($suffix) - 1))
		{
			$size /= 1024;
			$index++;
		}
		return round($size, $decimals) . ' ' . $suffix[$index];
	}

	public function redirect($location,$maintain_GET=false,$html=false,$wait=0)
	{
		$redirect_to = array();
		$location = str_replace('?','',$location);
		if($maintain_GET === true)
		{
			global $_CLEAN;
			foreach($_CLEAN['GET'] as $index => $value)
				$redirect_to[$index] = $value;
		}
		$implode_array = array();
		foreach($redirect_to as $index => $value)
			$implode_array[] = $index . '=' . $value;
		$redirect_to = implode('&',$implode_array);
		if($html)
			return '<meta http-equiv="refresh" content="' . $wait . ';/?' . $location . '" />';
		else
			header('Location: /?' . (strpos($redirect_to,$location) ? $redirect_to : $redirect_to . (0 < strlen($redirect_to) ? '&' : '') . $location));
	}
	
	public function relative_time($date)
	{
		$diff = time() - strtotime($date);
		$plural = function($diff,$negative=false){
			return ($negative ? (-$diff == 1 ? '' : 's') : ($diff == 1 ? '' : 's'));
		};
		if($diff > 0)
		{
			if($diff < 60)
				return $diff . " second" . $plural($diff) . " ago";
			$diff = round($diff / 60);
			if($diff < 60)
				return $diff . " minute" . $plural($diff) . " ago";
			$diff = round($diff / 60);
			if($diff < 24)
				return $diff . " hour" . $plural($diff) . " ago";
			$diff = round($diff / 24);
			if($diff < 7)
				return $diff . " day" . $plural($diff) . " ago";
			$diff = round($diff / 7);
			if($diff < 4)
				return $diff . " week" . $plural($diff) . " ago";
			return "on " . date("F j, Y", strtotime($date));
		} else {
			if($diff > -60)
				return "in about " . -$diff . " second" . $plural($diff,true);
			$diff = round($diff / 60);
			if ($diff > -60)
				return "in about " . -$diff . " minute" . $plural($diff,true);
			$diff = round($diff / 60);
			if ($diff > -24)
				return "in about " . -$diff . " hour" . $plural($diff,true);
			$diff = round($diff / 24);
			if ($diff > -7)
				return "in about " . -$diff . " day" . $plural($diff,true);
			$diff = round($diff / 7);
			if ($diff > -4)
				return "in about " . -$diff . " week" . $plural($diff,true);
			return "on " . date("F j, Y", strtotime($date));
		}
	}

	public function curl_get_contents($url,array $post_data=array(),$verbose=false,$ref_url=false,$cookie_location=false,$return_transfer=true)
	{
		$return_val = false;
	 
		$pointer = curl_init();
	 
		curl_setopt($pointer, CURLOPT_URL, $url);
		curl_setopt($pointer, CURLOPT_TIMEOUT, 40);
		curl_setopt($pointer, CURLOPT_RETURNTRANSFER, $return_transfer);
		curl_setopt($pointer, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.28 Safari/534.10");
		curl_setopt($pointer, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($pointer, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($pointer, CURLOPT_HEADER, false);
		curl_setopt($pointer, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($pointer, CURLOPT_AUTOREFERER, true);
	 
		if($cookie_location !== false)
		{
			curl_setopt($pointer, CURLOPT_COOKIEJAR, $cookie_location);
			curl_setopt($pointer, CURLOPT_COOKIEFILE, $cookie_location);
			curl_setopt($pointer, CURLOPT_COOKIE, session_name() . '=' . session_id());
		}
	 
		if($verbose !== false)
		{
			$verbose_pointer = fopen($verbose,'w');
			curl_setopt($pointer, CURLOPT_VERBOSE, true);
			curl_setopt($pointer, CURLOPT_STDERR, $verbose_pointer);
		}
	 
		if($ref_url !== false)
		{
			curl_setopt($pointer, CURLOPT_REFERER, $ref_url);
		}
	 
		if(count($post_data) > 0)
		{
			curl_setopt($pointer, CURLOPT_POST, true);
			curl_setopt($pointer, CURLOPT_POSTFIELDS, $post_data);
		}
	 
		$return_val = curl_exec($pointer);
	 
		$http_code = curl_getinfo($pointer, CURLINFO_HTTP_CODE);
	 
		if($http_code == 404)
		{
			return false;
		}
	 
		curl_close($pointer);
	 
		unset($pointer);
	 
		return $return_val;
	}
}
?>