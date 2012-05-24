<?php
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
class Profile extends Database implements Data {
	private $exists = false;

	private $profile_id = 0;
	private $profile_name;
	private $display_name;
	private $password;
	private $created;
	private $updated;
	private $last_active;

	private $fetched = false;

	private $password_type = 'plaintext';

	public function init()
	{
		$profile_instance = new Database;
		if($this->profile_id !== 0)
			$sql = 'SELECT * FROM profiles WHERE profile_id = "' . $this->profile_id . '"';
		else
			$sql = 'SELECT * FROM profiles WHERE profile_name = "' . $this->profile_name . '"';
		$profile_instance->set_query($sql);
		$profile_instance->query();

		$profile_results = $profile_instance->get_results();

		if($profile_results)
		{
			$this->exists = true;
			$this->set_profile_id($profile_results[0]['profile_id']);
			$this->set_profile_name($profile_results[0]['profile_name']);
			$this->set_display_name($profile_results[0]['display_name']);
			$this->password_type = 'sha1';
			$this->set_password($profile_results[0]['password']);
		}
	}

	public function exists()
	{
		return $this->exists;
	}

	public function create()
	{
		$time = time();

		$this->prepare_password();

		$reg_instance = new Database;
		$sql = 'INSERT INTO profiles (profile_name,display_name,password,created) VALUES ("' . $this->profile_name . '","' . $this->display_name . '","' . $this->password . '","' . $time . '");';
		$reg_instance->set_query($sql);
		$reg_instance->query();

		$profile_id = $reg_instance->get_insert_id();

		$settings_instance = new Database;
		$sql = 'INSERT INTO settings (profile_id) VALUES ("' . $profile_id . '");';
		$settings_instance->set_query($sql);
		$settings_instance->query();

		return ($reg_instance->get_results() && $settings_instance->get_results());
	}

	public function update()
	{
		$time = time();

		$this->prepare_password();

		$profile_instance = new Database;
		$sql = 'UPDATE profiles SET profile_name = "' . $this->profile_name . '", display_name = "' . $this->display_name . '", password = "' . $this->password . '", updated = "' . $time . '" WHERE profile_id = "' . $this->profile_id . '"';
		$profile_instance->set_query($sql);
		$profile_instance->query();
		return $profile_instance->get_results();
	}

	public function delete()
	{
		$profile_instance = new Database;
		$sql = 'DELETE FROM profiles WHERE profile_id = "' . $this->profile_id . '"';
		$profile_instance->set_query($sql);
		$profile_instance->query();
		return $profile_instance->get_results();
	}

	public function update_last_active()
	{
		$time = time();

		$profile_instance = new Database;
		$sql = 'UPDATE profiles SET last_active = "' . $time . '" WHERE profile_id = "' . $this->profile_id . '"';
		$profile_instance->set_query($sql);
		$profile_instance->query();
		return $profile_instance->get_results();
	}

	public function authenticate()
	{
		$this->prepare_password();

		$profile_instance = new Database;
		$sql = 'SELECT profile_id,profile_name FROM profiles WHERE profile_name = "' . $this->profile_name . '" AND password = "' . $this->password . '"';
		$profile_instance->set_query($sql);
		$profile_instance->query();
		return $profile_instance->get_results();
	}

	private function prepare_password()
	{
		if($this->password_type == 'plaintext')
		{
			$this->set_password(sha1($this->password));
			$this->set_password_type('sha1');
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

	public function get_profile_name()
	{
		return $this->profile_name;
	}

	public function set_profile_name($profile_name)
	{
		$this->profile_name = $profile_name;
	}

	public function get_display_name()
	{
		return $this->display_name;
	}

	public function set_display_name($display_name)
	{
		$this->display_name = $display_name;
	}

	public function get_password_type()
	{
		return $this->password_type;
	}

	public function set_password_type($password_type)
	{
		if($password_type == 'plaintext' || $password_type == 'sha1')
		{
			$this->password_type = $password_type;
		}
	}

	public function get_password()
	{
		return $this->password;
	}

	public function set_password($password)
	{
		$this->password = $password;
	}

	public function get_created()
	{
		return $this->created;
	}

	public function set_created($created)
	{
		$this->created = $created;
	}

	public function get_updated()
	{
		return $this->updated;
	}

	public function set_updated($updated)
	{
		$this->updated = $updated;
	}

	public function get_last_active()
	{
		return $this->last_active;
	}

	public function set_last_active($last_active)
	{
		$this->last_active = $last_active;
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