<?php
require_once('classes/class.Database.php');
require_once('classes/abstract.class.Search.php');

class GroupSearch extends Search {
	private $results;
	private $query = '';

	public function query($order_by='')
	{
		$group_search_instance = new Database;
		$sql = 'SELECT group_id,group_alias,group_name FROM groups WHERE group_name LIKE "%' . $this->query . '%" OR group_alias LIKE "%' . $this->query . '%" OR description LIKE "%' . $this->query . '%"' . (0 < strlen($order_by) ? ' ORDER BY ' . $order_by : '');
		$group_search_instance->set_query($sql);
		$group_search_instance->query();

		$group_search_results = $group_search_instance->get_results();

		if($group_search_results)
		{
			foreach($group_search_results as $result)
			{
				$results = $this->get_results();
				$results[$result['group_id']] = $result;
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