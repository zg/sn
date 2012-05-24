<?php
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
require_once('classes/class.Profile.php');
require_once('classes/class.Permission.php');
class GroupMembers implements Data {
	private $group_id;
	private $profile_id;
	private $permission_id;

	public function init()
	{
	}

	public function create()
	{
		$group_members_instance = new Database;
		$sql = 'INSERT INTO group_members (group_id,profile_id,permission_id) VALUES ("' . $this->group_id . '","' . $this->profile_id . '","' . $this->permission_id . '");';
		$group_members_instance->set_query($sql);
		$group_members_instance->query();
		return $group_members_instance->get_results();
	}

	public function update()
	{
		return true;
	}

	public function delete()
	{
		$group_members_instance = new Database;
		$sql = 'DELETE FROM group_members WHERE group_id = "' . $this->group_id . '" AND profile_id = "' . $this->profile_id . '" AND permission_id = "' . $this->permission_id . '"';
		$group_members_instance->set_query($sql);
		$group_members_instance->query();
		return $group_members_instance->get_results();
	}

	public function fetch_group_members()
	{
		$group_members_instance = new Database;
		$sql = 'SELECT profile_id,permission_id FROM group_members WHERE group_id = "' . $this->group_id . '" GROUP BY group_id';
		$group_members_instance->set_query($sql);
		$group_members_instance->query();
		$group_members_results = $group_members_instance->get_results();

		if($group_members_results)
		{
			foreach($group_members_results as $group_member_id => $group_member)
			{
				$profile_instance = new Profile;
				$profile_instance->set_profile_id($group_member['profile_id']);
				$profile_instance->init();
				$group_members_results[$group_member_id]['display_name'] = $profile_instance->get_display_name();

				$permission_instance = new Permission;
				$permission_instance->set_permission_id($group_member['permission_id']);
				$permission_instance->init();
				$group_members_results[$group_member_id]['permissions'] = $permission_instance->fetch();
			}
		}

		return $group_members_results;
	}

	public function fetch_profile_groups()
	{
		$profile_groups_instance = new Database;
		$sql = 'SELECT profile_id,permission_id FROM group_members WHERE profile_id = "' . $this->profile_id . '"';
		$profile_groups_instance->set_query($sql);
		$profile_groups_instance->query();
		$profile_groups_results = $profile_groups_instance->get_results();

		foreach($profile_groups_results as $group_member_id => $group_member)
		{
			$permission_instance = new Permission;
			$permission_instance->set_permission_id($group_member['permission_id']);
			$permission_instance->init();
			$profile_groups_results[$group_member_id]['permissions'] = $permission_instance->fetch();
		}

		return $profile_groups_results;
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

	public function get_permission_id()
	{
		return $this->permission_id;
	}

	public function set_permission_id($permission_id)
	{
		if(is_numeric($permission_id))
		{
			$this->permission_id = $permission_id;
		}
	}
}
?>