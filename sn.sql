SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `friends` (
  `profile_id` int(40) NOT NULL,
  `friend_id` int(40) NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  `date_requested` int(10) NOT NULL,
  `date_added` int(10) NOT NULL,
  KEY `profile_id` (`profile_id`),
  KEY `friend_id` (`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` int(40) NOT NULL AUTO_INCREMENT,
  `owner_id` int(40) NOT NULL,
  `group_alias` varchar(255) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `privacy_level` enum('public','password_protected','profile_protected','private') NOT NULL DEFAULT 'public',
  `password` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `group_members` (
  `group_id` int(40) NOT NULL,
  `profile_id` int(40) NOT NULL,
  `permission_id` int(40) NOT NULL,
  `member_since` int(10) NOT NULL,
  PRIMARY KEY (`group_id`,`profile_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `like` (
  `profile_id` int(40) NOT NULL,
  `post_id` int(40) NOT NULL,
  KEY `profile_id` (`profile_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `media` (
  `media_id` int(40) NOT NULL AUTO_INCREMENT,
  `profile_id` int(40) NOT NULL,
  `path` text NOT NULL,
  `thumb_path` text NOT NULL,
  `mime_type` varchar(20) NOT NULL,
  `file_size` int(50) NOT NULL,
  `width` int(20) NOT NULL,
  `height` int(20) NOT NULL,
  PRIMARY KEY (`media_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(40) NOT NULL AUTO_INCREMENT,
  `profile_id` int(40) NOT NULL,
  `type` enum('like','friend','requested','comment') NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `profile_id` (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `post` (
  `post_id` int(40) NOT NULL AUTO_INCREMENT,
  `parent_id` int(40) NOT NULL DEFAULT '0',
  `alias` varchar(250) NOT NULL,
  `profile_id` int(40) NOT NULL,
  `group_id` int(40) NOT NULL DEFAULT '1',
  `share_id` int(40) NOT NULL DEFAULT '0' COMMENT 'post_id of sharer',
  `type` enum('image','video','link','status','code','reply') NOT NULL,
  `content` text NOT NULL,
  `created` int(10) NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `profile_id` (`profile_id`),
  KEY `permission_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `profiles` (
  `profile_id` int(40) NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(25) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `password` varchar(40) NOT NULL,
  `created` int(10) NOT NULL,
  `updated` int(10) NOT NULL,
  `last_active` int(10) NOT NULL,
  PRIMARY KEY (`profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `session` (
  `session_id` varchar(50) NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `data` text NOT NULL,
  `expiration` int(10) NOT NULL,
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `settings` (
  `profile_id` int(40) NOT NULL AUTO_INCREMENT,
  `privacy_level` enum('public','friends_only','private') NOT NULL DEFAULT 'friends_only',
  `confirm_friends` tinyint(1) NOT NULL DEFAULT '1',
  `appear_offline` tinyint(1) NOT NULL,
  PRIMARY KEY (`profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
