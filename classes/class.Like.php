<?php
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
class Like implements Data {
	private $exists = false;
	private $post_id;
	private $profile_id;
	private $likers = array();

	public function init()
	{
		$like_instance = new Database;
		$sql = 'SELECT * FROM `like` WHERE profile_id = "' . $this->profile_id . '" AND post_id = "' . $this->post_id . '"';
		$like_instance->set_query($sql);
		$like_instance->query();
		if(0 < $like_instance->get_row_count())
		{
			$this->exists = true;
		}
	}

	public function exists()
	{
		return $this->exists;
	}

	public function create()
	{
		$like_instance = new Database;
		$sql = 'INSERT INTO `like` (profile_id,post_id) VALUES ("' . $this->profile_id . '","' . $this->post_id . '");';
		$like_instance->set_query($sql);
		$like_instance->query();
		return $like_instance->get_results();
	}

	public function update()
	{
		// waste of space
	}

	public function delete()
	{
		$like_instance = new Database;
		$sql = 'DELETE FROM `like` WHERE post_id = "' . $this->post_id . '" AND profile_id = "' . $this->profile_id . '"';
		$like_instance->set_query($sql);
		$like_instance->query();
		return $like_instance->get_results();
	}

	public function fetch_likers()
	{
		$like_instance = new Database;
		$sql = 'SELECT profile_id FROM `like` WHERE post_id = "' . $this->post_id . '"';
		$like_instance->set_query($sql);
		$like_instance->query();
		if($like_instance->get_row_count())
		{
			$like_results = $like_instance->get_results();
			foreach($like_results as $liker)
			{
				$likers = $this->get_likers();
				$likers[] = $liker;
				$this->set_likers($likers);
			}
		}
	}

	public function get_likes()
	{
		return $this->likes;
	}

	public function set_likes($likes)
	{
		$this->likes = $likes;
	}

	public function get_likers()
	{
		return $this->likers;
	}

	public function set_likers($likers)
	{
		$this->likers = $likers;
	}

	public function get_post_id()
	{
		return $this->post_id;
	}

	public function set_post_id($post_id)
	{
		if(is_numeric($post_id))
		{
			$this->post_id = $post_id;
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
}
?>