<?php
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
require_once('classes/class.GroupMembers.php');
require_once('classes/class.Permission.php');
class Group implements Data {
	private $exists = false;
	private $group_id = 0;
	private $owner_id = 0;
	private $group_alias;
	private $group_name;
	private $description;
	private $privacy_level;
	private $password;

	private $group_members = array();

	public function init()
	{
		$group_instance = new Database;
		if($this->group_id == 0)
			$sql = 'SELECT groups.* FROM groups LEFT JOIN group_members AS members ON groups.group_id = members.group_id LEFT JOIN profiles ON profiles.profile_id = members.profile_id WHERE groups.group_alias = "' . $this->group_alias . '"';
		else
			$sql = 'SELECT groups.* FROM groups LEFT JOIN group_members AS members ON groups.group_id = members.group_id LEFT JOIN profiles ON profiles.profile_id = members.profile_id WHERE groups.group_id = "' . $this->group_id . '"';
		$group_instance->set_query($sql);
		$group_instance->query();
		$group_results = $group_instance->get_results();

		if($group_results)
		{
			$this->exists = true;
			$this->set_group_id($group_results[0]['group_id']);
			$this->set_owner_id($group_results[0]['owner_id']);
			$this->set_group_alias($group_results[0]['group_alias']);
			$this->set_group_name($group_results[0]['group_name']);
			$this->set_description($group_results[0]['description']);
			$this->set_privacy_level($group_results[0]['privacy_level']);
			$this->set_password($group_results[0]['password']);

			$group_members_instance = new GroupMembers;
			$group_members_instance->set_group_id($group_results[0]['group_id']);
			$this->set_group_members($group_members_instance->fetch_group_members());
		}
	}

	public function exists()
	{
		return $this->exists;
	}

	public function create()
	{
		$group_instance = new Database;
		$sql = 'INSERT INTO groups (owner_id,group_alias,group_name) VALUES ("' . $this->owner_id . '","' . $this->group_alias . '","' . $this->group_name . '");';
		$group_instance->set_query($sql);
		$group_instance->query();
		$this->set_group_id($group_instance->get_insert_id());
		return $group_instance->get_results();
	}

	public function update()
	{
		$group_instance = new Database;
		$sql = 'UPDATE groups SET group_name = "' . $this->group_name . '" WHERE group_id = "' . $this->group_id . '" AND profile_id = "' . $this->profile_id . '"';
		$group_instance->set_query($sql);
		$group_instance->query();

		$permission_instance = new Permission;
		$permission_instance->set_permission_id($this->permission_id);
		$permission_instance->set_can_create($this->can_create);
		$permission_instance->set_can_read($this->can_read);
		$permission_instance->set_can_update($this->can_update);
		$permission_instance->set_can_delete($this->can_delete);
		$permission_instance->update();

		return $group_instance->get_results();
	}

	public function delete()
	{
		$group_instance = new Database;
		$sql = 'DELETE FROM groups WHERE group_id = "' . $this->group_id . '" AND profile_id = "' . $this->profile_id . '"';
		$group_instance->set_query($sql);
		$group_instance->query();

		$permission_instance = new Database;
		$permission_instance->set_permission_id($this->permission_id);
		$permission_instance->delete();

		return $group_instance->get_results();
	}

	public function get_group_id()
	{
		return $this->group_id;
	}

	public function set_group_id($group_id)
	{
		if(is_numeric($group_id))
		{
			$this->group_id = $group_id;
		}
	}

	public function get_owner_id()
	{
		return $this->owner_id;
	}

	public function set_owner_id($owner_id)
	{
		if(is_numeric($owner_id))
		{
			$this->owner_id = $owner_id;
		}
	}

	public function get_group_alias()
	{
		return $this->group_alias;
	}

	public function set_group_alias($group_alias)
	{
		$this->group_alias = $group_alias;
	}

	public function get_group_name()
	{
		return $this->group_name;
	}

	public function set_group_name($group_name)
	{
		$this->group_name = $group_name;
	}

	public function get_group_members()
	{
		return $this->group_members;
	}

	public function set_group_members($group_members)
	{
		$this->group_members = $group_members;
	}

	public function get_description()
	{
		return $this->description;
	}

	public function set_description($description)
	{
		$this->description = $description;
	}

	public function get_privacy_level()
	{
		return $this->privacy_level;
	}

	public function set_privacy_level($privacy_level)
	{
		$this->privacy_level = $privacy_level;
	}

	public function get_password()
	{
		return $this->password;
	}

	public function set_password($password)
	{
		$this->password = $password;
	}
}
?>