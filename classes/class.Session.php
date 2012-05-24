<?php
require_once('classes/class.Database.php');

class Session {
	private $database;

	private $session_id;
	private $data;
	private $expiration;

	private $user_agent;
	private $lifetime;
	private $session_name;
	private $session_save_path;

	public function __construct()
	{
		$this->user_agent = sha1((isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''));
		$this->database = new Database;
		
		$this->set_lifetime(get_cfg_var("session.gc_maxlifetime"));
		setcookie('PHPSESSID',session_id(),(60*60*24*14));

		session_set_cookie_params(60*60*24*14);
		session_set_save_handler (
			array($this, "open"    ),
			array($this, "close"   ),
			array($this, "read"    ),
			array($this, "write"   ),
			array($this, "destroy" ),
			array($this, "gc"      )
		);
	}

	public function open($save_path, $session_name)
	{
		$this->set_session_save_path($save_path);
		$this->set_session_name($session_name);

		return true;
	}

	public function close()
	{
		return true;
	}

	public function read($session_id)
	{
		$time = time();

		$this->set_session_id($session_id);

		$sql = 'SELECT data FROM session WHERE session_id = "' . $this->session_id . '" AND user_agent = "' . $this->user_agent . '" AND ' . $time . ' < expiration';
		$this->database->set_query($sql);
		$this->database->query();
		$results = $this->database->get_results();

		return $results[0]['data'];
	}

	public function write($session_id,$data)
	{
		$time = time() + $this->lifetime;

		$this->set_session_id($session_id);
		$this->set_data($data);

		$sql = "REPLACE INTO session (session_id,user_agent,data,expiration) VALUES ('" . $this->session_id . "', '" . $this->user_agent . "', '" . $this->data . "', '" . $time . "');";
		$this->database->set_query($sql);
		$this->database->query();

		return $this->database->get_results();
	}

	function destroy($session_id)
	{
		$this->set_session_id($session_id);

		$sql = 'DELETE FROM session WHERE session_id = "' . $this->session_id . '" AND user_agent = "' . $this->user_agent . '"';
		$this->database->set_query($sql);
		$this->database->query();

		return $this->database->get_results();
	}

	function gc($max_lifetime)
	{
		$time = time();
		$this->database = new Database;
		$sql = 'DELETE FROM session WHERE expiration < ' . $time;
		$this->database->set_query($sql);
		$this->database->query();
		return $this->database->get_results();
	}

	public function get_session_id()
	{
		return $this->session_id;
	}

	public function set_session_id($session_id)
	{
		$this->session_id = $session_id;
	}

	public function get_user_agent()
	{
		return $this->user_agent;
	}

	public function set_user_agent($user_agent)
	{
		$this->user_agent = $user_agent;
	}

	public function get_data()
	{
		return $this->data;
	}

	public function set_data($data)
	{
		$this->data = $data;
	}

	public function get_expiration()
	{
		return $this->expiration;
	}

	public function set_expiration($expiration)
	{
		$this->expiration = $expiration;
	}

	public function get_session_save_path()
	{
		return $this->session_save_path;
	}

	public function set_session_save_path($session_save_path)
	{
		$this->session_save_path = $session_save_path;
	}

	public function get_session_name()
	{
		return $this->session_name;
	}

	public function set_session_name($session_name)
	{
		$this->session_name = $session_name;
	}

	public function get_lifetime()
	{
		return $this->lifetime;
	}

	public function set_lifetime($lifetime)
	{
		$this->lifetime = $lifetime;
	}
}
?>