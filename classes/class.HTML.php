<?php
class HTML {
	public function post_format($posts)
	{
		$return_val = '';
		if($posts)
		{
			foreach($posts as $post)
			{
				switch($post['type'])
				{
					case 'image':
						if($post['share_id'])
						{
							require_once('classes/class.Post.php');
							$share_instance = new Post;
							$share_instance->set_post_id($post['share_id']);
							$share_instance->fetch_post();
							$share_results = $share_instance->get_posts();
							$post['content'] = $share_results[0]['content'];
						}
						if(isset($_SESSION['profile_id']))
						{
							$likes = false;
							foreach($post['likers'] as $profile_id => $display_name)
							{
								if($profile_id == $_SESSION['profile_id'])
								{
									$likes = true;
									break;
								}
							}
						}
						$return_val .= '<div class="profile_post">';
							if(isset($_SESSION['profile_id']))
							{
								$return_val .= '<div class="right">';
									$return_val .= '<input type="hidden" name="post_id" value="' . $post['post_id'] . '">';
									if($likes)
									{
										$return_val .= '<a href="#" class="unlike">Unlike</a>';
									}
									else
									{
										$return_val .= '<a href="#" class="like">Like</a>';
									}
									if($post['profile_id'] !== $_SESSION['profile_id'])
										$return_val .= ' - <a href="#" class="share">Share</a>';
									if($post['profile_id'] == $_SESSION['profile_id'])
										$return_val .= ' - <a href="#" class="delete">Delete</a>';
								$return_val .= '</div>';
							}
							$return_val .= '<div class="post_content image_post"><a href="' . $post['content']['path'] . '"><img src="' . $post['content']['thumb_path'] . '" alt="image" /></a></div><br />';
							$return_val .= 'by ' . ($post['profile_id'] == 0 ? (0 < strlen($post['alias']) ? $post['alias'] : 'Anonymous') : '<a href="profile.php?profile_id=' . $post['profile_id'] . '">' . $post['display_name'] . '</a>') . ' ';
							if(isset($share_results))
							{
								$return_val .= 'via ';
								if(0 < $share_results[0]['profile_id'])
									$return_val .= '<a href="profile.php?id=' . $share_results[0]['profile_id'] . '">';
								if($share_results[0]['profile_id'] == 0) // is Anonymous
									$return_val .= (0 < strlen($share_results[0]['alias']) ? $share_results[0]['alias'] : 'Anonymous');
								else
									$return_val .= $share_results[0]['display_name'];
								if(0 < $share_results[0]['profile_id'])
									$return_val .= '</a>';
								$return_val .= ' ';
							}
							$return_val .= 'on <a href="/post/' . $post['post_id'] . '">' . date('F j, Y \a\t h:i:s A',$post['created']) . '</a>';
							$return_val .= ' - <span class="likes_area"><a href="#">' . $post['like_count'] . ' like' . ($post['like_count'] == 1 ? '' : 's') . '</a></span>';
							$return_val .= '<div class="likers" style="display:none">';
							$likers = '';
							foreach($post['likers'] as $profile_id => $display_name)
							{
								if(isset($_SESSION['profile_id']) && $profile_id == $_SESSION['profile_id'])
									$likers = 'You, ' . $likers;
								else
									$likers .= '<a href="profile.php?profile_id=' . $profile_id . '">' . $display_name . '</a>, ';
							}
							$return_val .= rtrim($likers,', ');
							$return_val .= '</div>';
						$return_val .= '</div>';
					break;
					case 'reply':
					case 'status':
						if($post['share_id'])
						{
							require_once('classes/class.Post.php');
							$share_instance = new Post;
							$share_instance->set_post_id($post['share_id']);
							$share_instance->fetch_post();
							$share_results = $share_instance->get_posts();
							$post['content'] = $share_results[0]['content'];
						}
						if(isset($_SESSION['profile_id']))
						{
							$likes = false;
							foreach($post['likers'] as $profile_id => $display_name)
							{
								if($profile_id == $_SESSION['profile_id'])
								{
									$likes = true;
									break;
								}
							}
						}
						$return_val .= '<div class="profile_post">';
							if(isset($_SESSION['profile_id']))
							{
								$return_val .= '<div class="right">';
									$return_val .= '<input type="hidden" name="post_id" value="' . $post['post_id'] . '">';
									if($likes)
									{
										$return_val .= '<a href="#" class="unlike">Unlike</a>';
									}
									else
									{
										$return_val .= '<a href="#" class="like">Like</a>';
									}
									if($post['profile_id'] !== $_SESSION['profile_id'])
										$return_val .= ' - <a href="#" class="share">Share</a>';
									if($post['profile_id'] == $_SESSION['profile_id'])
										$return_val .= ' - <a href="#" class="delete">Delete</a>';
								$return_val .= '</div>';
							}
							$return_val .= '<div class="post_content">' . nl2br($post['content']) . '</div><br />';
							$return_val .= 'by ' . ($post['profile_id'] == 0 ? (0 < strlen($post['alias']) ? $post['alias'] : 'Anonymous') : '<a href="profile.php?profile_id=' . $post['profile_id'] . '">' . $post['display_name'] . '</a>') . ' ';
							if(isset($share_results))
							{
								$return_val .= 'via ';
								if(0 < $share_results[0]['profile_id'])
									$return_val .= '<a href="profile.php?id=' . $share_results[0]['profile_id'] . '">';
								if($share_results[0]['profile_id'] == 0) // is Anonymous
									$return_val .= (0 < strlen($share_results[0]['alias']) ? $share_results[0]['alias'] : 'Anonymous');
								else
									$return_val .= $share_results[0]['display_name'];
								if(0 < $share_results[0]['profile_id'])
									$return_val .= '</a>';
								$return_val .= ' ';
							}
							$return_val .= 'on <a href="/post/' . $post['post_id'] . '">' . date('F j, Y \a\t h:i:s A',$post['created']) . '</a>';
							$return_val .= ' - <span class="likes_area"><a href="#">' . $post['like_count'] . ' like' . ($post['like_count'] == 1 ? '' : 's') . '</a></span>';
							$return_val .= '<div class="likers" style="display:none">';
							$likers = '';
							foreach($post['likers'] as $profile_id => $display_name)
							{
								if(isset($_SESSION['profile_id']) && $profile_id == $_SESSION['profile_id'])
									$likers = 'You, ' . $likers;
								else
									$likers .= '<a href="profile.php?profile_id=' . $profile_id . '">' . $display_name . '</a>, ';
							}
							$return_val .= rtrim($likers,', ');
							$return_val .= '</div>';
						$return_val .= '</div>';
					break;
					case 'link':
						if($post['share_id'])
						{
							require_once('classes/class.Post.php');
							$share_instance = new Post;
							$share_instance->set_post_id($post['share_id']);
							$share_instance->fetch_post();
							$share_results = $share_instance->get_posts();
							$post['content'] = $share_results[0]['content'];
						}
						if(isset($_SESSION['profile_id']))
						{
							$likes = false;
							foreach($post['likers'] as $profile_id => $display_name)
							{
								if($profile_id == $_SESSION['profile_id'])
								{
									$likes = true;
									break;
								}
							}
						}
						$return_val .= '<div class="profile_post">';
							if(isset($_SESSION['profile_id']))
							{
								$return_val .= '<div class="right">';
									$return_val .= '<input type="hidden" name="post_id" value="' . $post['post_id'] . '">';
									if($likes)
									{
										$return_val .= '<a href="#" class="unlike">Unlike</a>';
									}
									else
									{
										$return_val .= '<a href="#" class="like">Like</a>';
									}
									if($post['profile_id'] !== $_SESSION['profile_id'])
										$return_val .= ' - <a href="#" class="share">Share</a>';
									if($post['profile_id'] == $_SESSION['profile_id'])
										$return_val .= ' - <a href="#" class="delete">Delete</a>';
								$return_val .= '</div>';
							}
							$return_val .= '<div class="post_content">' . $post['content'] . '</div><br />';
							$return_val .= 'by ' . ($post['profile_id'] == 0 ? (0 < strlen($post['alias']) ? $post['alias'] : 'Anonymous') : '<a href="profile.php?profile_id=' . $post['profile_id'] . '">' . $post['display_name'] . '</a>') . ' ';
							if(isset($share_results))
							{
								$return_val .= 'via ';
								if(0 < $share_results[0]['profile_id'])
									$return_val .= '<a href="profile.php?id=' . $share_results[0]['profile_id'] . '">';
								if($share_results[0]['profile_id'] == 0) // is Anonymous
									$return_val .= (0 < strlen($share_results[0]['alias']) ? $share_results[0]['alias'] : 'Anonymous');
								else
									$return_val .= $share_results[0]['display_name'];
								if(0 < $share_results[0]['profile_id'])
									$return_val .= '</a>';
								$return_val .= ' ';
							}
							$return_val .= 'on <a href="/post/' . $post['post_id'] . '">' . date('F j, Y \a\t h:i:s A',$post['created']) . '</a>';
							$return_val .= ' - <span class="likes_area"><a href="#">' . $post['like_count'] . ' like' . ($post['like_count'] == 1 ? '' : 's') . '</a></span>';
							$return_val .= '<div class="likers" style="display:none">';
							$likers = '';
							foreach($post['likers'] as $profile_id => $display_name)
							{
								if(isset($_SESSION['profile_id']) && $profile_id == $_SESSION['profile_id'])
									$likers = 'You, ' . $likers;
								else
									$likers .= '<a href="profile.php?profile_id=' . $profile_id . '">' . $display_name . '</a>, ';
							}
							$return_val .= rtrim($likers,', ');
							$return_val .= '</div>';
						$return_val .= '</div>';
					break;
					case 'code':
						if($post['share_id'])
						{
							require_once('classes/class.Post.php');
							$share_instance = new Post;
							$share_instance->set_post_id($post['share_id']);
							$share_instance->fetch_post();
							$share_results = $share_instance->get_posts();
							$post['content'] = $share_results[0]['content'];
						}
						if(isset($_SESSION['profile_id']))
						{
							$likes = false;
							foreach($post['likers'] as $profile_id => $display_name)
							{
								if($profile_id == $_SESSION['profile_id'])
								{
									$likes = true;
									break;
								}
							}
						}
						$return_val .= '<div class="profile_post">';
							if(isset($_SESSION['profile_id']))
							{
								$return_val .= '<div class="right">';
									$return_val .= '<input type="hidden" name="post_id" value="' . $post['post_id'] . '">';
									if($likes)
									{
										$return_val .= '<a href="#" class="unlike">Unlike</a>';
									}
									else
									{
										$return_val .= '<a href="#" class="like">Like</a>';
									}
									if($post['profile_id'] !== $_SESSION['profile_id'])
										$return_val .= ' - <a href="#" class="share">Share</a>';
									if($post['profile_id'] == $_SESSION['profile_id'])
										$return_val .= ' - <a href="#" class="delete">Delete</a>';
								$return_val .= '</div>';
							}
							$return_val .= '<div class="post_content code_post"><pre>' . $post['content'] . '</pre></div><br />';
							$return_val .= 'by ' . ($post['profile_id'] == 0 ? (0 < strlen($post['alias']) ? $post['alias'] : 'Anonymous') : '<a href="profile.php?profile_id=' . $post['profile_id'] . '">' . $post['display_name'] . '</a>') . ' ';
							if(isset($share_results))
							{
								$return_val .= 'via ';
								if(0 < $share_results[0]['profile_id'])
									$return_val .= '<a href="profile.php?id=' . $share_results[0]['profile_id'] . '">';
								if($share_results[0]['profile_id'] == 0) // is Anonymous
									$return_val .= (0 < strlen($share_results[0]['alias']) ? $share_results[0]['alias'] : 'Anonymous');
								else
									$return_val .= $share_results[0]['display_name'];
								if(0 < $share_results[0]['profile_id'])
									$return_val .= '</a>';
								$return_val .= ' ';
							}
							$return_val .= 'on <a href="/post/' . $post['post_id'] . '">' . date('F j, Y \a\t h:i:s A',$post['created']) . '</a>';
							$return_val .= ' - <span class="likes_area"><a href="#">' . $post['like_count'] . ' like' . ($post['like_count'] == 1 ? '' : 's') . '</a></span>';
							$return_val .= '<div class="likers" style="display:none">';
							$likers = '';
							foreach($post['likers'] as $profile_id => $display_name)
							{
								if(isset($_SESSION['profile_id']) && $profile_id == $_SESSION['profile_id'])
									$likers = 'You, ' . $likers;
								else
									$likers .= '<a href="profile.php?profile_id=' . $profile_id . '">' . $display_name . '</a>, ';
							}
							$return_val .= rtrim($likers,', ');
							$return_val .= '</div>';
						$return_val .= '</div>';
					break;
				}
			}
		}
		return $return_val;
	}
}
?>