<?php
require_once('classes/class.Media.php');
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
class Stream implements Data {
	private $exists = false;
	private $profile_id;
	private $stream_data;

	private $order_by = '';

	private $fetched = false;

	public function init()
	{
	}

	public function create()
	{
		return true;
	}

	public function update()
	{
		return true;
	}

	public function delete()
	{
		return true;
	}

	public function fetch_stream()
	{
		if($this->fetched)
			return;
		$stream_instance = new Database;
		$sql = 'SELECT DISTINCT post.*,profiles.profile_id,profiles.profile_name,profiles.display_name FROM post LEFT JOIN friends ON friends.friend_id = post.profile_id LEFT JOIN profiles ON post.profile_id = profiles.profile_id WHERE friends.profile_id = "' . $this->profile_id . '" OR profiles.profile_id = "' . $this->profile_id . '"' . (0 < strlen($this->order_by) ? ' ORDER BY ' . $this->order_by : '');
		$stream_instance->set_query($sql);
		$stream_instance->query();
		$stream_data_results = $stream_instance->get_results();
		if($stream_data_results)
		{
			foreach($stream_data_results as $index => $stream_data)
			{
				if($stream_data['type'] == "image")
				{
					$content_instance = new Media;
					$content_instance->set_media_id($stream_data['content']);
					$content_instance->fetch_content();
					$stream_data_results[$index]['content'] = $content_instance->get_content();
					$stream_data_results[$index]['content']['path'] = str_replace('/srv/http/net/w-3/sn','',$stream_data_results[$index]['content']['path']);
					$stream_data_results[$index]['content']['thumb_path'] = str_replace('/srv/http/net/w-3/sn','',$stream_data_results[$index]['content']['thumb_path']);
				}
				$stream_data_results[$index]['likers'] = array();
				$likers_instance = new Database;
				$sql = 'SELECT `like`.profile_id,profiles.display_name FROM `like` LEFT JOIN profiles ON profiles.profile_id = `like`.profile_id WHERE `like`.post_id = "' . $stream_data['post_id'] . '" ORDER BY profiles.display_name ASC';
				$likers_instance->set_query($sql);
				$likers_instance->query();
				$stream_data_results[$index]['like_count'] = $likers_instance->get_row_count();
				$liker_results = $likers_instance->get_results();
				if($liker_results)
				{
					foreach($liker_results as $liker)
					{
						$stream_data_results[$index]['likers'][$liker['profile_id']] = $liker['display_name'];
					}
				}
			}
		}
		$this->set_stream($stream_data_results);
		$this->fetched = true;
	}

	public function get_stream()
	{
		return $this->stream_data;
	}

	public function set_stream($stream_data)
	{
		$this->stream_data = $stream_data;
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

	public function get_order_by()
	{
		return $this->order_by;
	}

	public function set_order_by($order_by)
	{
		$this->order_by = $order_by;
	}
}
?>