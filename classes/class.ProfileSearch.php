<?php
require_once('classes/class.Database.php');
require_once('classes/abstract.class.Search.php');

class ProfileSearch extends Search {
	private $results;
	private $query = '';

	public function query($order_by='')
	{
		$profile_search_instance = new Database;
		$sql = 'SELECT profile_id,display_name FROM profiles WHERE profile_name LIKE "%' . $this->query . '%" OR display_name LIKE "%' . $this->query . '%"' . (0 < strlen($order_by) ? ' ORDER BY ' . $order_by : '');
		$profile_search_instance->set_query($sql);
		$profile_search_instance->query();

		$profile_search_results = $profile_search_instance->get_results();

		if($profile_search_results)
		{
			foreach($profile_search_results as $result)
			{
				$results = $this->get_results();
				$results[$result['profile_id']] = $result;
				$this->set_results($results);
			}
		}
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