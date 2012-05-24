<?php
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
class Permission implements Data {
	private $exists = false;
	private $permission_id = 0;
	private $can_create = 0;
	private $can_read = 0;
	private $can_update = 0;
	private $can_delete = 0;

	public function init()
	{
		$binary = decbin($this->permission_id);
		$this->can_create = (int)$binary[0];
		$this->can_read   = (int)$binary[1];
		$this->can_update = (int)$binary[2];
		$this->can_delete = (int)$binary[3];
	}

	public function create()
	{
		return true;
	}

	public function update()
	{
		return true;
	}

	public function delete()
	{
		return true;
	}

	public function fetch()
	{
		return array (
			'can_create' => $this->can_create, // admin
			'can_read'   => $this->can_read,   // member
			'can_update' => $this->can_update, // editor
			'can_delete' => $this->can_delete  // cleaner
		);
	}

	public function get_permission_id()
	{
		return $this->permission_id;
	}

	public function set_permission_id($permission_id)
	{
		if(is_numeric($permission_id) && 0 <= $permission_id && $permission_id <= 15)
		{
			$this->permission_id = $permission_id;
		}
	}

	public function get_can_create()
	{
		return $this->can_create;
	}

	public function set_can_create($can_create)
	{
		if($can_create == 0 || $can_create == 1)
		{
			$this->can_create = $can_create;
		}
		else
		{
			$this->can_create = ($can_create ? 1 : 0);
		}
	}

	public function get_can_read()
	{
		return $this->can_read;
	}

	public function set_can_read($can_read)
	{
		if($can_read == 0 || $can_read == 1)
		{
			$this->can_read = $can_read;
		}
		else
		{
			$this->can_read = ($can_read ? 1 : 0);
		}
	}

	public function get_can_update()
	{
		return $this->can_update;
	}

	public function set_can_update($can_update)
	{
		if($can_update == 0 || $can_update == 1)
		{
			$this->can_update = $can_update;
		}
		else
		{
			$this->can_update = ($can_update ? 1 : 0);
		}
	}

	public function get_can_delete()
	{
		return $this->can_delete;
	}

	public function set_can_delete($can_delete)
	{
		if($can_delete == 0 || $can_delete == 1)
		{
			$this->can_delete = $can_delete;
		}
		else
		{
			$this->can_delete = ($can_delete ? 1 : 0);
		}
	}
}
?>