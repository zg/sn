<?php
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
class Media implements Data {
	private $media_id;
	private $profile_id;
	private $path;
	private $thumb_path;
	private $mime_type;
	private $file_size;
	private $width;
	private $height;

	private $fetched = false;

	public function init()
	{
	}

	public function create()
	{
		$media_instance = new Database;
		$sql = 'INSERT INTO media (profile_id,path,thumb_path,mime_type,file_size,width,height) VALUES ("' . $this->profile_id . '","' . $this->path . '","' . $this->thumb_path . '","' . $this->mime_type . '","' . $this->file_size . '","' . $this->width . '","' . $this->height . '");';
		$media_instance->set_query($sql);
		$media_instance->query();
		$this->set_media_id($media_instance->get_insert_id());
		return $media_instance->get_results();
	}

	public function update()
	{
		$media_instance = new Database;
		$sql = 'UPDATE media SET profile_id = "' . $this->profile_id . '", path = "' . $this->path . '", thumb_path = "' . $this->thumb_path . '", mime_type = "' . $this->mime_type . '", file_size = "' . $this->file_size . '", width = "' . $this->width . '", height = "' . $this->height . '" WHERE media_id = "' . $this->media_id . '"';
		$media_instance->set_query($sql);
		$media_instance->query();
		return $media_instance->get_results();
	}

	public function delete()
	{
		$media_instance = new Database;
		$sql = 'DELETE FROM media WHERE media_id = "' . $this->media_id . '"';
		$media_instance->set_query($sql);
		$media_instance->query();
		return $media_instance->get_results();
	}

	public function fetch_content()
	{
		if($this->fetched)
			return;
		$media_instance = new Database;
		$sql = 'SELECT * FROM media WHERE media_id = "' . $this->media_id . '"';
		$media_instance->set_query($sql);
		$media_instance->query();
		$content_results = $media_instance->get_results();
		if($content_results)
		{
			$this->set_profile_id($content_results[0]['profile_id']);
			$this->set_path($content_results[0]['path']);
			$this->set_thumb_path($content_results[0]['thumb_path']);
			$this->set_mime_type($content_results[0]['mime_type']);
			$this->set_file_size($content_results[0]['file_size']);
			$this->set_width($content_results[0]['width']);
			$this->set_height($content_results[0]['height']);
		}
		$this->fetched = true;
	}

	public function get_content()
	{
		return array (
			'profile_id' => $this->profile_id,
			'path'       => $this->path,
			'thumb_path' => $this->thumb_path,
			'mime_type'  => $this->mime_type,
			'file_size'  => $this->file_size,
			'width'      => $this->width,
			'height'     => $this->height
		);
	}

	public function get_media_id()
	{
		return $this->media_id;
	}

	public function set_media_id($media_id)
	{
		if(is_numeric($media_id))
		{
			$this->media_id = $media_id;
		}
	}

	public function get_profile_id()
	{
		return $this->profile_id;
	}

	public function set_profile_id($profile_id)
	{
		if(is_numeric($profile_id))
		{
			$this->profile_id = $profile_id;
		}
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

	public function get_mime_type()
	{
		return $this->mime_type;
	}

	public function set_mime_type($mime_type)
	{
		$this->mime_type = $mime_type;
	}

	public function get_file_size()
	{
		return $this->file_size;
	}

	public function set_file_size($file_size)
	{
		$this->file_size = $file_size;
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