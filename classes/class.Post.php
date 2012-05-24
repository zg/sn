<?php
require_once('classes/class.Data.php');
require_once('classes/class.Database.php');
require_once('classes/class.Media.php');
class Post implements Data {
	private $exists = false;
	private $post_id = 0;
	private $parent_id = 0;
	private $alias = '';
	private $profile_id = 0;
	private $group_id = 0;
	private $share_id;
	private $type;
	private $content;
	private $created;

	private $order_by;

	private $posts = array();

	private $fetched = false;

	public function init()
	{
		$post_instance = new Database;
		if($this->post_id !== 0)
		{
			$sql = 'SELECT * FROM post WHERE post_id = "' . $this->post_id . '"';
		}
		elseif($this->parent_id !== 0)
		{
			$sql = 'SELECT * FROM post WHERE parent_id = "' . $this->parent_id . '"';
		}
		elseif($this->alias !== '')
		{
			$sql = 'SELECT * FROM post WHERE alias = "' . $this->alias . '"';
		}
		elseif($this->profile_id !== 0)
		{
			$sql = 'SELECT * FROM post WHERE profile_id = "' . $this->profile_id . '"';
		}
		elseif($this->group_id !== 0)
		{
			$sql = 'SELECT * FROM post WHERE group_id = "' . $this->group_id . '"';
		}
		else
		{
			return;
		}
		$post_instance->set_query($sql);
		$post_instance->query();
		$post_results = $post_instance->get_results();

		if($post_results)
		{
			$this->set_post_id($post_results[0]['post_id']);
			$this->set_parent_id($post_results[0]['parent_id']);
			$this->set_alias($post_results[0]['alias']);
			$this->set_profile_id($post_results[0]['profile_id']);
			$this->set_group_id($post_results[0]['group_id']);
			$this->set_share_id($post_results[0]['share_id']);
			$this->set_type($post_results[0]['type']);
			$this->set_content($post_results[0]['content']);
			$this->set_created($post_results[0]['created']);
			$this->exists = true;
		}
	}

	public function create()
	{
		$time = time();
		$post_instance = new Database;
		$sql = 'INSERT INTO post (parent_id,alias,profile_id,group_id,share_id,type,content,created) VALUES ("' . $this->parent_id . '","' . $this->alias . '","' . $this->profile_id . '","' . $this->group_id . '","' . $this->share_id . '","' . $this->type . '","' . $this->content . '","' . $time . '");';
		$post_instance->set_query($sql);
		$post_instance->query();
		$this->set_post_id($post_instance->get_insert_id());
		return $post_instance->get_results();
	}

	public function update()
	{
		$post_instance = new Database;
		$sql = 'UPDATE post SET group_id = "' . $this->group_id . '", content = "' . $this->content . '" WHERE ' . ($this->post_id == 0 ? 'alias = "' . $this->alias . '"' : 'post_id = "' . $this->post_id . '"') . ' AND profile_id "' . $this->profile_id . '"';
		$post_instance->set_query($sql);
		$post_instance->query();
		return $post_instance->get_results();
	}

	public function delete()
	{
		$post_instance = new Database;
		$sql = 'DELETE FROM post WHERE ' . ($this->post_id == 0 ? 'alias = "' . $this->alias . '"' : 'post_id = "' . $this->post_id . '"') . ' AND profile_id = "' . $this->profile_id . '"';
		$post_instance->set_query($sql);
		$post_instance->query();
		return $post_instance->get_results();
	}

	public function fetch_post()
	{
		if($this->fetched)
			return;
		$post_instance = new Database;
		$sql = 'SELECT post.*,COUNT(`like`.profile_id) AS like_count,profiles.display_name FROM post LEFT JOIN profiles ON profiles.profile_id = post.profile_id LEFT OUTER JOIN `like` ON post.post_id = `like`.post_id WHERE post.' . ($this->post_id == 0 ? 'alias = "' . $this->alias . '"' : 'post_id = "' . $this->post_id . '"') . (0 < strlen($this->order_by) ? ' ORDER BY ' . $this->order_by : '');
		$post_instance->set_query($sql);
		$post_instance->query();
		$post_data_results = $post_instance->get_results();
		foreach($post_data_results as $index => $post_data) //grab all likers of each post
		{
			if($post_data['type'] == "image")
			{
				$content_instance = new Media;
				$content_instance->set_media_id($post_data['content']);
				$content_instance->fetch_content();
				$post_data_results[$index]['content'] = $content_instance->get_content();
				$post_data_results[$index]['content']['path'] = str_replace('/srv/http/net/w-3/sn','',$post_data_results[$index]['content']['path']);
				$post_data_results[$index]['content']['thumb_path'] = str_replace('/srv/http/net/w-3/sn','',$post_data_results[$index]['content']['thumb_path']);
			}
			$post_data_results[$index]['likers'] = array();
			$likers_instance = new Database;
			$sql = 'SELECT `like`.profile_id,profiles.display_name FROM `like` LEFT JOIN profiles ON profiles.profile_id = `like`.profile_id WHERE `like`.post_id = "' . $post_data['post_id'] . '" ORDER BY profiles.display_name ASC';
			$likers_instance->set_query($sql);
			$likers_instance->query();
			$liker_results = $likers_instance->get_results();
			if($liker_results)
			{
				foreach($liker_results as $liker)
				{
					$post_data_results[$index]['likers'][$liker['profile_id']] = $liker['display_name'];
				}
			}
		}
		$this->set_posts($post_data_results);
		$this->fetched = true;
	}

	public function fetch_replies()
	{
		if($this->fetched)
			return;
		$post_instance = new Database;
		$sql = 'SELECT post.*,COUNT(`like`.profile_id) AS like_count,profiles.display_name FROM post LEFT JOIN profiles ON profiles.profile_id = post.profile_id LEFT OUTER JOIN `like` ON post.post_id = `like`.post_id WHERE post.parent_id = ' . $this->parent_id . (0 < strlen($this->order_by) ? ' ORDER BY ' . $this->order_by : '');
		$post_instance->set_query($sql);
		$post_instance->query();
		$post_data_results = $post_instance->get_results();
		foreach($post_data_results as $index => $post_data) //grab all likers of each post
		{
			if($post_data['type'] == "image")
			{
				$content_instance = new Media;
				$content_instance->set_media_id($post_data['content']);
				$content_instance->fetch_content();
				$post_data_results[$index]['content'] = $content_instance->get_content();
				$post_data_results[$index]['content']['path'] = str_replace('/srv/http/net/w-3/sn','',$post_data_results[$index]['content']['path']);
				$post_data_results[$index]['content']['thumb_path'] = str_replace('/srv/http/net/w-3/sn','',$post_data_results[$index]['content']['thumb_path']);
			}
			$post_data_results[$index]['likers'] = array();
			$likers_instance = new Database;
			$sql = 'SELECT `like`.profile_id,profiles.display_name FROM `like` LEFT JOIN profiles ON profiles.profile_id = `like`.profile_id WHERE `like`.post_id = "' . $post_data['post_id'] . '" ORDER BY profiles.display_name ASC';
			$likers_instance->set_query($sql);
			$likers_instance->query();
			$liker_results = $likers_instance->get_results();
			if($liker_results)
			{
				foreach($liker_results as $liker)
				{
					$post_data_results[$index]['likers'][$liker['profile_id']] = $liker['display_name'];
				}
			}
		}
		$this->set_posts($post_data_results);
		$this->fetched = true;
	}

	public function fetch_group_posts()
	{
		if($this->fetched)
			return;
		$post_instance = new Database;
		$sql = 'SELECT post.*,COUNT(`like`.profile_id) AS like_count,profiles.display_name FROM post LEFT JOIN profiles ON profiles.profile_id = post.profile_id LEFT OUTER JOIN `like` ON post.post_id = `like`.post_id WHERE post.group_id = "' . $this->group_id . '" GROUP BY post.post_id' . (0 < strlen($this->order_by) ? ' ORDER BY ' . $this->order_by : '');
		$post_instance->set_query($sql);
		$post_instance->query();
		$post_data_results = $post_instance->get_results();
		if($post_data_results)
		{
			foreach($post_data_results as $index => $post_data) //grab all likers of each post
			{
				if($post_data['type'] == "image")
				{
					$content_instance = new Media;
					$content_instance->set_media_id($post_data['content']);
					$content_instance->fetch_content();
					$post_data_results[$index]['content'] = $content_instance->get_content();
					$post_data_results[$index]['content']['path'] = str_replace('/srv/http/net/w-3/sn','',$post_data_results[$index]['content']['path']);
					$post_data_results[$index]['content']['thumb_path'] = str_replace('/srv/http/net/w-3/sn','',$post_data_results[$index]['content']['thumb_path']);
				}
				$post_data_results[$index]['likers'] = array();
				$likers_instance = new Database;
				$sql = 'SELECT `like`.profile_id,profiles.display_name FROM `like` LEFT JOIN profiles ON profiles.profile_id = `like`.profile_id WHERE `like`.post_id = "' . $post_data['post_id'] . '" ORDER BY profiles.display_name ASC';
				$likers_instance->set_query($sql);
				$likers_instance->query();
				$liker_results = $likers_instance->get_results();
				if($liker_results)
				{
					foreach($liker_results as $liker)
					{
						$post_data_results[$index]['likers'][$liker['profile_id']] = $liker['display_name'];
					}
				}
			}
		}
		$this->set_posts($post_data_results);
		$this->fetched = true;
	}

	public function fetch_profile_posts()
	{
		if($this->fetched)
			return;
		$post_instance = new Database;
		$sql = 'SELECT post.*,COUNT(`like`.profile_id) AS like_count,profiles.display_name FROM post LEFT JOIN profiles ON profiles.profile_id = post.profile_id LEFT OUTER JOIN `like` ON post.post_id = `like`.post_id WHERE post.profile_id = "' . $this->profile_id . '" GROUP BY post.post_id' . (0 < strlen($this->order_by) ? ' ORDER BY ' . $this->order_by : '');
		$post_instance->set_query($sql);
		$post_instance->query();
		$post_data_results = $post_instance->get_results();
		if($post_data_results)
		{
			foreach($post_data_results as $index => $post_data) //grab all likers of each post
			{
				if($post_data['type'] == "image")
				{
					$content_instance = new Media;
					$content_instance->set_media_id($post_data['content']);
					$content_instance->fetch_content();
					$post_data_results[$index]['content'] = $content_instance->get_content();
					$post_data_results[$index]['content']['path'] = str_replace('/srv/http/net/w-3/sn','',$post_data_results[$index]['content']['path']);
					$post_data_results[$index]['content']['thumb_path'] = str_replace('/srv/http/net/w-3/sn','',$post_data_results[$index]['content']['thumb_path']);
				}
				$post_data_results[$index]['likers'] = array();
				$likers_instance = new Database;
				$sql = 'SELECT `like`.profile_id,profiles.display_name FROM `like` LEFT JOIN profiles ON profiles.profile_id = `like`.profile_id WHERE `like`.post_id = "' . $post_data['post_id'] . '" ORDER BY profiles.display_name ASC';
				$likers_instance->set_query($sql);
				$likers_instance->query();
				$liker_results = $likers_instance->get_results();
				if($liker_results)
				{
					foreach($liker_results as $liker)
					{
						$post_data_results[$index]['likers'][$liker['profile_id']] = $liker['display_name'];
					}
				}
			}
		}
		$this->set_posts($post_data_results);
		$this->fetched = true;
	}

	public function exists()
	{
		return $this->exists;
	}

	public function get_posts()
	{
		return $this->posts;
	}
	
	public function set_posts($posts)
	{
		$this->posts = $posts;
	}

	public function get_post_id()
	{
		return $this->post_id;
	}

	public function set_post_id($post_id)
	{
		if(is_numeric($post_id))
		{
			$this->post_id = $post_id;
		}
	}

	public function get_parent_id()
	{
		return $this->parent_id;
	}

	public function set_parent_id($parent_id)
	{
		if(is_numeric($parent_id))
		{
			$this->parent_id = $parent_id;
		}
	}

	public function get_alias()
	{
		return $this->alias;
	}

	public function set_alias($alias)
	{
		$this->alias = $alias;
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

	public function get_share_id()
	{
		return $this->share_id;
	}

	public function set_share_id($share_id)
	{
		if(is_numeric($share_id))
		{
			$this->share_id = $share_id;
		}
	}

	public function get_type()
	{
		return $this->type;
	}

	public function set_type($type)
	{
		$this->type = $type;
	}

	public function get_content()
	{
		return $this->content;
	}

	public function set_content($content)
	{
		$this->content = $content;
	}

	public function get_created()
	{
		return $this->created;
	}

	public function set_created($created)
	{
		$this->created = $created;
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