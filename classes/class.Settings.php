<?php
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
class Settings implements Data {
	private $profile_id;
	private $settings = array();

	public function init()
	{
		$settings_instance = new Database;
		$sql = 'SELECT * FROM settings WHERE profile_id = "' . $this->profile_id . '"';
		$settings_instance->set_query($sql);
		$settings_instance->query();

		$settings_results = $settings_instance->get_results();

		if($settings_results)
		{
			$this->set_settings($settings_results[0]);
		}
	}

	public function create()
	{
		$settings_instance = new Database;
		$sql = 'INSERT INTO settings (' . implode(',',array_keys($this->settings)) . ') VALUES ("' . implode('","',array_values($this->settings)) . '");';
		$settings_instance->set_query($sql);
		$settings_instance->query();
		return $settings_instance->get_results();
	}

	public function update()
	{
		$keys = array_keys($this->settings);
		$values = array_values($this->settings);
		$settings_instance = new Database;
		$sql = 'UPDATE settings SET ';
		foreach($keys as $index => $key)
		{
			if($keys[$index] == "profile_id")
				continue;
			$sql .= $keys[$index] . ' = "' . $values[$index] . '", ';
		}
		$sql = rtrim($sql,', ');
		$sql .= ' WHERE profile_id = "' . $this->profile_id . '"';
		$settings_instance->set_query($sql);
		$settings_instance->query();
		return $settings_instance->get_results();
	}

	public function delete()
	{
		$settings_instance = new Database;
		$sql = 'DELETE FROM settings WHERE profile_id = "' . $this->profile_id . '"';
		$settings_instance->set_query($sql);
		$settings_instance->query();
		return $settings_instance->get_results();
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

	public function get_setting($index)
	{
		if(isset($this->settings[$index]))
		{
			return $this->settings[$index];
		}
	}

	public function set_setting($index,$new_value)
	{
		if(isset($this->settings[$index]))
		{
			$this->settings[$index] = $new_value;
		}
	}

	public function get_settings()
	{
		return $this->settings;
	}

	public function set_settings($settings)
	{
		if(is_array($settings))
		{
			$this->settings = $settings;
		}
	}

	public function get_setting_types()
	{
		return $this->setting_types;
	}

	public function set_setting_types($setting_types)
	{
		if(is_array($setting_types))
		{
			$this->setting_types = $setting_types;
		}
	}
}
?>