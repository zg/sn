<?php
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
require_once('classes/class.common.php');
require_once('classes/class.Media.php');
class Image implements Data {
	private $media_id;
	private $status = 'success';

	private $privacy_level = 'public'; // public, friends, or private
	private $path = '/srv/http/net/w-3/sn/uploads/img/';
	private $thumb_path = '/srv/http/net/w-3/sn/uploads/img/';
	private $max_width = 250;
	private $max_height = 250;

	private $name;
	private $mime_type;
	private $tmp_name;
	private $error;
	private $size;

	private $new_name = '';
	private $extension = '';

	private $width;
	private $height;

	public function init()
	{
		$images = array (
			'jpg' => @imagecreatefromjpeg($this->tmp_name),
			'gif' => @imagecreatefromgif($this->tmp_name),
			'png' => @imagecreatefrompng($this->tmp_name)
		);

		$split = @explode('.',$this->name);
		if(count($split) == 0) //file doesn't have an extension
		{
			if($images['jpg']) //assume it could be a jpg/jpeg
				$this->set_extension('jpg');
			elseif($images['gif']) //assume it could be a gif
				$this->set_extension('gif');
			elseif($images['png']) //assume it could be a png
				$this->set_extension('png');
		}
		else //file has an extension
		{
			$possible_extension = strtolower($split[count($split) - 1]);
			switch($possible_extension)
			{
				case 'jpg':
				case 'jpeg':
					if($images['jpg'])
						$this->set_extension('jpg');
				break;
				case 'gif':
					if($images['gif'])
						$this->set_extension('gif');
				break;
				case 'png':
					if($images['png'])
						$this->set_extension('png');
				break;
				default: //unknown extension, attempt to manipulate it
					if($images['jpg']) //assume it could be a jpg/jpeg
						$this->set_extension('jpg');
					elseif($images['gif']) //assume it could be a gif
						$this->set_extension('gif');
					elseif($images['png']) //assume it could be a png
						$this->set_extension('png');
				break;
			}
		}

		if($this->get_extension() == '') //if the extension has still not been created...
		{
			switch($this->mime_type) //lets check the file type they specified
			{
				case 'image/jpg':
				case 'image/jpeg':
					if($images['jpg'])
						$this->set_extension('jpg');
				break;
				case 'image/gif':
					if($images['gif'])
						$this->set_extension('gif');
				break;
				case 'image/png':
					if($images['png'])
						$this->set_extension('png');
				break;
			}
		}

		foreach($images as $index => $image)
			if(!$image)
				unset($images[$index]);

		if($this->get_extension() == '')
			return $this->set_status('invalid');

		$this->set_width(imagesx($images[$this->get_extension()]));
		$this->set_height(imagesy($images[$this->get_extension()]));

		$common = new common;
		$random_string = str_shuffle(str_shuffle($common->generate_random_string(8)));
		$file_name = sha1($random_string) . '.' . $this->get_extension();
		$this->set_new_name(sha1($random_string));
		$this->set_path('/srv/http/net/w-3/sn/uploads/img/' . $this->privacy_level . '/' . $this->new_name . '.' . $this->get_extension());
		$this->set_thumb_path('/srv/http/net/w-3/sn/uploads/img/' . $this->privacy_level . '_thumb/' . $this->new_name . '.' . $this->get_extension());
	}

	public function create_thumb()
	{
		if(!file_exists($this->tmp_name))
			$this->set_tmp_name($this->path);
		switch($this->extension)
		{
			case 'jpg':
			case 'jpeg':
				$source = @imagecreatefromjpeg($this->tmp_name);
			break;
			case 'gif':
				$source = @imagecreatefromgif($this->tmp_name);
			break;
			case 'png':
				$source = @imagecreatefrompng($this->tmp_name);
			break;
		}

		$old_width = $this->width;
		$old_height = $this->height;

		if($this->max_width < $old_width || $this->max_height < $old_height)
		{
			$key_width = $this->max_width / $old_width;
			$key_height = $this->max_height / $old_height;
			($key_width < $key_height) ? $keys = $key_width : $keys = $key_height;
			$thumb_width = ceil($old_width * $keys) + 1;
			$thumb_height = ceil($old_height * $keys) + 1;
		}
		else
		{
			$thumb_width = $old_width;
			$thumb_height = $old_height;
		}

		$destination = imagecreatetruecolor($thumb_width,$thumb_height);
		imagecopyresampled($destination,$source,0,0,0,0,$thumb_width,$thumb_height,$old_width,$old_height);

		switch($this->extension)
		{
			case 'png':
				imagepng($destination,$this->thumb_path);
			break;
			case 'jpg':
			case 'jpeg':
				imagejpeg($destination,$this->thumb_path);
			break;
			case 'gif':
				system('convert ' . $this->tmp_name . ' -resize 250x250 ' . $this->thumb_path);
			break;
		}

		imagedestroy($destination);
		imagedestroy($source);
	}

	public function create()
	{
		@move_uploaded_file($this->tmp_name,$this->path);
	}

	public function update(){}

	public function delete()
	{
		@unlink($this->path);
	}

	public function delete_thumb()
	{
		@unlink($this->thumb_path);
	}

	public function get_status()
	{
		return $this->status;
	}

	public function set_status($status)
	{
		$this->status = $status;
	}

	public function get_username()
	{
		return $this->username;
	}

	public function set_username($username)
	{
		$this->username = $username;
	}

	public function get_privacy_level()
	{
		return $this->privacy_level;
	}

	public function set_privacy_level($privacy_level)
	{
		$this->privacy_level = $privacy_level;
	}

	public function get_path()
	{
		return $this->path;
	}

	public function set_path($path)
	{
		$this->path = $path;
	}

	public function get_thumb_path()
	{
		return $this->thumb_path;
	}

	public function set_thumb_path($thumb_path)
	{
		$this->thumb_path = $thumb_path;
	}

	public function get_name()
	{
		return $this->name;
	}

	public function set_name($name)
	{
		$this->name = $name;
	}

	public function get_extension()
	{
		return $this->extension;
	}

	public function set_extension($extension)
	{
		$this->extension = $extension;
	}

	public function get_mime_type()
	{
		return $this->mime_type;
	}

	public function set_mime_type($mime_type)
	{
		$this->mime_type = $mime_type;
	}

	public function get_tmp_name()
	{
		return $this->tmp_name;
	}

	public function set_tmp_name($tmp_name)
	{
		$this->tmp_name = $tmp_name;
	}

	public function get_error()
	{
		return $this->error;
	}

	public function set_error($error)
	{
		$this->error = $error;
	}

	public function get_size()
	{
		return $this->size;
	}

	public function set_size($size)
	{
		$this->size = $size;
	}

	public function get_new_name()
	{
		return $this->new_name;
	}

	public function set_new_name($new_name)
	{
		$this->new_name = $new_name;
	}

	public function get_max_width()
	{
		return $this->max_width;
	}

	public function set_max_width($max_width)
	{
		$this->max_width = $max_width;
	}

	public function get_max_height()
	{
		return $this->max_height;
	}

	public function set_max_height($max_height)
	{
		$this->max_height = $max_height;
	}

	public function get_width()
	{
		return $this->width;
	}

	public function set_width($width)
	{
		$this->width = $width;
	}

	public function get_height()
	{
		return $this->height;
	}

	public function set_height($height)
	{
		$this->height = $height;
	}
}
?>