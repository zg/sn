<?php
require_once('classes/class.Database.php');
require_once('classes/abstract.class.Search.php');

class StatusSearch extends Search {
	private $results;
	private $query = '';

	public function query($order_by='')
	{
		$status_search_instance = new Database;
		$sql = 'SELECT post.*,COUNT(`like`.profile_id) AS like_count,profiles.display_name FROM post LEFT JOIN profiles ON profiles.profile_id = post.profile_id LEFT OUTER JOIN `like` ON post.post_id = `like`.post_id WHERE post.content LIKE "%' . $this->query . '%" AND post.type = "status" GROUP BY post.post_id' . (0 < strlen($order_by) ? ' ORDER BY ' . $order_by : '');
		$status_search_instance->set_query($sql);
		$status_search_instance->query();
		$status_search_results = $status_search_instance->get_results();
		if($status_search_results)
		{
			foreach($status_search_results as $index => $post_data)
			{
				$status_search_results[$index]['likers'] = array();
				$likers_instance = new Database;
				$sql = 'SELECT `like`.profile_id,profiles.display_name FROM `like` LEFT JOIN profiles ON profiles.profile_id = `like`.profile_id WHERE `like`.post_id = "' . $post_data['post_id'] . '" ORDER BY profiles.display_name ASC';
				$likers_instance->set_query($sql);
				$likers_instance->query();
				$liker_results = $likers_instance->get_results();
				if($liker_results)
				{
					foreach($liker_results as $liker)
					{
						$status_search_results[$index]['likers'][$liker['profile_id']] = $liker['display_name'];
					}
				}
			}
		}
		$this->set_results($status_search_results);
	}

	public function get_query()
	{
		return $this->query;
	}

	public function set_query($query)
	{
		$this->query = $query;
	}

	public function get_results()
	{
		return $this->results;
	}

	public function set_results($results)
	{
		$this->results = $results;
	}
}
?>