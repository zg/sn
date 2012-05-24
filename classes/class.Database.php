<?php
class Database {
	private $connection = false;
	private $query = '';
	private $insert_id = 0;
	private $results = array();
	private $field_info = array();
	private $row_count = 0;

	public $error;

	private $location = 'localhost';
	private $username = 'sn';
	private $password = '@@';
	private $database = 'sn';

	public function __construct()
	{
		$connection = mysql_connect($this->location,$this->username,$this->password);

		$this->set_connection($connection);

		$db_select = mysql_select_db($this->database,$this->get_connection());
	}

	public function query()
	{
		$query = mysql_query($this->query,$this->connection);

		if(0 < strlen(mysql_error()) && $_SERVER['REMOTE_ADDR'] == '192.168.1.1')
		{
			echo '<hr />';
			echo '<h2>MySQL error</h2>';
			echo '<font style="color:#f00;font-size:16px"><b>' . mysql_error() . '</b></font>';
			echo '<hr />';
		}

		$this->set_insert_id(mysql_insert_id($this->connection));

		switch(substr(strtolower($this->query),0,6))
		{
			case 'select':
				$this->set_row_count(mysql_num_rows($query));

				$row_count = $this->get_row_count();

				if($row_count == 0)
				{
					$this->set_results(0);
				}
				else
				{
					while($result = mysql_fetch_assoc($query))
					{
						$results = $this->get_results();
						$results[] = $result;
						$this->set_results($results);
					}
				}
			break;
			case 'insert':
			case 'update':
			case 'delete':
			case 'replace':
			default:
				$this->set_results(($query === true));
			break;
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

	public function get_insert_id()
	{
		return $this->insert_id;
	}

	public function set_insert_id($insert_id)
	{
		if(is_numeric($insert_id))
		{
			$this->insert_id = $insert_id;
		}
	}

	public function get_results()
	{
		return $this->results;
	}

	public function set_results($results)
	{
		$this->results = $results;
	}

	public function get_row_count()
	{
		return $this->row_count;
	}

	public function set_row_count($row_count)
	{
		if(is_numeric($row_count))
		{
			$this->row_count = $row_count;
		}
	}

	public function get_connection()
	{
		return $this->connection;
	}

	public function set_connection($connection)
	{
		if(is_resource($connection))
		{
			$this->connection = $connection;
		}
		else
		{
			$this->connection = false;
		}
	}
}
?>