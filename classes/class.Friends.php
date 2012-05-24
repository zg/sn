<?php
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
class Friends implements Data {
	private $exists = false;
	private $profile_id;
	private $friend_id;
	private $confirmed;
	private $date_requested;
	private $date_added;

	private $friends = array('friends'=>array(),'requested'=>array(),'requests'=>array());

	private $friend_order_by;
	private $requests_order_by;

	private $friend_type;

	private $fetched = false;

	public function init()
	{
		$friend_instance = new Database;
		$sql = 'SELECT * FROM friends WHERE (profile_id = "' . $this->profile_id . '" AND friend_id = "' . $this->friend_id . '") OR (profile_id = "' . $this->friend_id . '" AND friend_id = "' . $this->profile_id . '")';
		$friend_instance->set_query($sql);
		$friend_instance->query();
		$friend_row_count = $friend_instance->get_row_count();
		if(1 < $friend_row_count)
		{
			$delete_duplicate_instance = new Database;
			$sql = 'DELETE FROM friends WHERE profile_id = "' . $this->friend_id . '" AND friend_id = "' . $this->profile_id . '"';
			$delete_duplicate_instance->set_query($sql);
			$delete_duplicate_instance->query();
			
			$this->init();
			return;
		}
		if($friend_row_count)
		{
			$this->exists = true;

			$friend_data = $friend_instance->get_results();

			if($friend_data[0]['confirmed'] == 1)
				$this->set_friend_type('friend');
			elseif($this->profile_id == $_SESSION['profile_id'])
				$this->set_friend_type('requested');
			elseif($this->friend_id == $_SESSION['profile_id'])
				$this->set_friend_type('request');

			$this->set_confirmed($friend_data[0]['confirmed']);
			$this->set_date_requested($friend_data[0]['date_requested']);
			$this->set_date_added($friend_data[0]['date_added']);
		}
	}

	public function exists()
	{
		return $this->exists;
	}

	public function create()
	{
		$friend_instance = new Database;
		$sql = 'INSERT INTO friends (profile_id,friend_id,confirmed,date_requested,date_added) VALUES ("' . $this->profile_id . '","' . $this->friend_id . '","' . $this->confirmed . '","' . $this->date_requested . '","' . $this->date_added . '");';
		$friend_instance->set_query($sql);
		$friend_instance->query();
		return $friend_instance->get_results();
	}

	public function update()
	{
		$friend_instance = new Database;
		$sql = 'UPDATE friends SET confirmed = "' . $this->confirmed . '", "' . $this->date_requested . '", "' . $this->date_added . '" WHERE friend_id = "' . $this->friend_id . '" AND profile_id = "' . $this->profile_id . '"';
		$friend_instance->set_query($sql);
		$friend_instance->query();
		return $friend_instance->get_results();
	}

	public function delete()
	{
		$friend_instance = new Database;
		$sql = 'DELETE FROM friends WHERE friend_id = "' . $this->friend_id . '" AND profile_id = "' . $this->profile_id . '"';
		$friend_instance->set_query($sql);
		$friend_instance->query();
		return $friend_instance->get_results();
	}

	public function fetch_friends()
	{
		if($this->fetched)
			return;
		$friend_instance = new Database;
		$sql = 'SELECT friends.friend_id AS profile_id,friends.confirmed,friends.date_requested,friends.date_added,profiles.display_name,profiles.last_active FROM friends LEFT JOIN profiles ON friends.friend_id = profiles.profile_id WHERE friends.profile_id = "' . $this->profile_id . '"' . (0 < strlen($this->friend_order_by) ? ' ORDER BY ' . $this->friend_order_by : '');
		$friend_instance->set_query($sql);
		$friend_instance->query();
		$friend_results = $friend_instance->get_results();
		if($friend_results)
		{
			foreach($friend_results as $profile)
			{
				$friends = $this->get_friends();
				if($profile['confirmed'] == 1)
					$friends['friends'][$profile['profile_id']] = $profile;
				else
					$friends['requested'][$profile['profile_id']] = $profile;
				$this->set_friends($friends);
			}
		}

		$requests_instance = new Database;
		$sql = 'SELECT friends.profile_id,friends.confirmed,friends.date_requested,friends.date_added,profiles.display_name,profiles.last_active FROM friends LEFT JOIN profiles ON friends.profile_id = profiles.profile_id WHERE friends.friend_id = "' . $this->profile_id . '"' . (0 < strlen($this->requests_order_by) ? ' ORDER BY ' . $this->requests_order_by : '');
		$requests_instance->set_query($sql);
		$requests_instance->query();
		$requests_results = $requests_instance->get_results();
		if($requests_results)
		{
			foreach($requests_results as $profile)
			{
				$friends = $this->get_friends();
				if($profile['confirmed'] == 1)
					$friends['friend'][$profile['profile_id']] = $profile;
				else
					$friends['requests'][$profile['profile_id']] = $profile;
			}
		}
		$this->fetched = true;
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

	public function get_friend_id()
	{
		return $this->friend_id;
	}

	public function set_friend_id($friend_id)
	{
		if(is_numeric($friend_id))
		{
			$this->friend_id = $friend_id;
		}
	}

	public function get_confirmed()
	{
		return $this->confirmed;
	}

	public function set_confirmed($confirmed)
	{
		if(is_numeric($confirmed))
		{
			$this->confirmed = $confirmed;
		}
	}

	public function get_date_requested()
	{
		return $this->date_requested;
	}

	public function set_date_requested($date_requested)
	{
		if(is_numeric($date_requested))
		{
			$this->date_requested = $date_requested;
		}
	}

	public function get_date_added()
	{
		return $this->date_added;
	}

	public function set_date_added($date_added)
	{
		if(is_numeric($date_added))
		{
			$this->date_added = $date_added;
		}
	}

	public function get_friends()
	{
		return $this->friends;
	}

	public function set_friends($friends)
	{
		$this->friends = $friends;
	}

	public function get_friend_type()
	{
		return $this->friend_type;
	}

	public function set_friend_type($friend_type)
	{
		$this->friend_type = $friend_type;
	}

	public function get_order_by($type)
	{
		switch($type)
		{
			case 'friend':
				return $this->friend_order_by;
			break;
			case 'requests':
				return $this->requests_order_by;
			break;
		}
	}

	public function set_order_by($type,$order_by)
	{
		switch($type)
		{
			case 'friend':
				$this->friend_order_by = $order_by;
			break;
			case 'requests':
				$this->friend_order_by = $order_by;
			break;
		}
	}
}
?>