<?php
/**
 * @version $Id$
 * Kunena Forum Importer Component
 * @package com_kunenaimporter
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2009 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */
defined ( '_JEXEC' ) or die ();

require_once (JPATH_ROOT . DS . 'components' . DS . 'com_kunena' . DS . 'lib' . DS . 'kunena.defines.php');
require_once (KUNENA_PATH_LIB . DS . 'kunena.config.class.php');

class CKunenaTable extends JTable {
	protected $_exists;

	function store($updateNulls = false) {
		if (! $this->_exists)
			$ret = $this->_db->insertObject ( $this->_tbl, $this, $this->_tbl_key );
		else
			$ret = $this->_db->updateObject ( $this->_tbl, $this, $this->_tbl_key );
		if (! $ret) {
			$this->setError ( get_class ( $this ) . '::store failed - ' . $this->_db->getErrorMsg () );
		}

		return $ret;
	}

	function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	function load($oid = null) {
		$ret = parent::load ( $oid );
		if ($ret === true)
			$this->_exists = true;
		return $ret;
	}
}

class CKunenaTableExtUser extends CKunenaTable {
	var $extid = null;
	var $extusername = null;
	var $id = null;
	var $name = null;
	var $username = null;
	var $email = null;
	var $password = null;
	var $usertype = null;
	var $block = null;
	var $gid = null;
	var $registerDate = null;
	var $lastvisitDate = null;
	var $params = null;
	var $conflict = null;
	var $error = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunenaimporter_users', 'extid', $database );
	}

	function loadIdMap($list) {
		if (empty($list)) return array();
		$list = implode(',', $list);
		$this->_db->setQuery ( "SELECT id, extid, lastvisitDate FROM #__kunenaimporter_users WHERE extid IN ({$list})" );
		return $this->_db->loadObjectList('extid');
	}
}

class CKunenaTableAnnouncements extends CKunenaTable {
	var $id = null;
	var $title = null;
	var $sdescription = null;
	var $description = null;
	var $created = null;
	var $published = null;
	var $ordering = null;
	var $showdate = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_announcement', 'id', $database );
	}
}

class CKunenaTableAttachments extends CKunenaTable {
	var $id = null;
	var $mesid = null;
	var $userid = null;
	var $hash = null;
	var $size = null;
	var $folder = null;
	var $filetype = null;
	var $filename = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_attachments', 'mesid', $database );
	}
}

class CKunenaTableCategories extends CKunenaTable {
	var $id = null;
	var $parent = null;
	var $name = null;
	var $cat_emoticon = null;
	var $locked = null;
	var $alert_admin = null;
	var $moderated = null;
	var $moderators = null;
	var $pub_access = null;
	var $pub_recurse = null;
	var $admin_access = null;
	var $admin_recurse = null;
	var $ordering = null;
	var $future2 = null;
	var $published = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $review = null;
	var $allow_anonymous = null;
	var $post_anonymous = null;
	var $hits = null;
	var $description = null;
	var $headerdesc = null;
	var $class_sfx = null;
	var $allow_polls = null;
	var $id_last_msg = null;
	var $numTopics = null;
	var $numPosts = null;
	var $time_last_msg = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_categories', 'id', $database );
	}

	function store($updateNulls = false) {
		$ret = parent::store ( $updateNulls );
		if ($ret) {
			// we must reset fbSession (allowed), when forum record was changed
			$this->_db->setQuery ( "UPDATE #__kunena_sessions SET allowed='na'" );
			// FIXME: Need to check and validate query error codes
			$this->_db->query ();
			
		}
		return $ret;
	}
}

class CKunenaTableConfig extends CKunenaConfig {
	protected function bind($array, $ignore = '') {
		if (! is_array ( $array )) {
			$this->_error = strtolower ( get_class ( $this ) ) . '::bind failed.';
			return false;
		} else {
			foreach ( $array as $k => $v ) {
				if (isset ( $this->$k ))
					$this->$k = $v;
			}
		}

		return true;
	}

	public function save($data) {
		$this->remove ();
		$this->bind ( $data );
		$this->create ();
	}
}

class CKunenaTableFavorites extends CKunenaTable {
	var $thread = null;
	var $userid = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_favorites', 'thread', $database );
	}
}

class CKunenaTableMessages extends CKunenaTable {
	var $id = null;
	var $parent = null;
	var $thread = null;
	var $catid = null;
	var $name = null;
	var $userid = null;
	var $email = null;
	var $subject = null;
	var $time = null;
	var $ip = null;
	var $topic_emoticon = null;
	var $locked = null;
	var $hold = null;
	var $ordering = null;
	var $hits = null;
	var $moved = null;
	var $modified_by = null;
	var $modified_time = null;
	var $modified_reason = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_messages', 'id', $database );
	}
}

class CKunenaTableMessages_Text extends CKunenaTable {
	var $mesid = null;
	var $message = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_messages_text', 'mesid', $database );
	}
}

class CKunenaTableModeration extends CKunenaTable {
	var $catid = null;
	var $userid = null;
	var $future1 = null;
	var $future2 = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_moderation', 'catid', $database );
	}
}

class CKunenaTablePolls extends CKunenaTable {
	var $id = null;
	var $title = null;
	var $threadid = null;
	var $polltimetolive = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_polls', 'id', $database );
	}
}

class CKunenaTablePolls_Options extends CKunenaTable {
	var $id = null;
	var $pollid = null;
	var $text = null;
	var $votes = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_polls_options', 'id', $database );
	}
}

class CKunenaTablePolls_Users extends CKunenaTable {
	var $pollid = null;
	var $userid = null;
	var $votes = null;
	var $lasttime = null;
	var $lastvote = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_polls_users', 'id', $database );
	}
}

class CKunenaTableRanks extends CKunenaTable {
	var $rank_id = null;
	var $rank_title = null;
	var $rank_min = null;
	var $rank_special = null;
	var $rank_image = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_ranks', 'rank_id', $database );
	}
}

class CKunenaTableSessions extends CKunenaTable {
	var $userid = null;
	var $allowed = null;
	var $lasttime = null;
	var $readtopics = null;
	var $currvisit = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_sessions', 'userid', $database );
	}
}

class CKunenaTableSmilies extends CKunenaTable {
	var $id = null;
	var $code = null;
	var $location = null;
	var $greylocation = null;
	var $emoticonbar = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_smileys', 'id', $database );
	}
}

class CKunenaTableSubscriptions extends CKunenaTable {
	var $thread = null;
	var $userid = null;
	var $future1 = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_subscriptions', 'thread', $database );
	}

	function store($updateNulls = false) {
		$this->_db->setQuery ( "INSERT INTO #__kunena_subscriptions (thread, userid) VALUES ({$this->thread}, {$this->userid})" );
		$this->_db->query ();
		return !$this->_db->getErrorNum ();
	}

}

class CKunenaTableSubscriptions_Categories extends CKunenaTable {
	var $thread = null;
	var $userid = null;
	var $future1 = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_subscriptions_categories', 'thread', $database );
	}
}

class CKunenaTableThankYou extends CKunenaTable {
	var $postid = null;
	var $userid = null;
	var $targetuserid = null;
	var $time = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_thankyou', 'thread', $database );
	}
}

class CKunenaTableUserProfile extends CKunenaTable {
	var $userid = null;
	var $view = null;
	var $signature = null;
	var $moderator = null;
	var $banned = null;
	var $ordering = null;
	var $posts = null;
	var $avatar = null;
	var $karma = null;
	var $karma_time = null;
	var $group_id = null;
	var $uhits = null;
	var $personalText = null;
	var $gender = null;
	var $birthdate = null;
	var $location = null;
	var $ICQ = null;
	var $AIM = null;
	var $YIM = null;
	var $MSN = null;
	var $SKYPE = null;
	var $TWITTER = null;
	var $FACEBOOK = null;
	var $GTALK = null;
	var $MYSPACE = null;
	var $LINKEDIN = null;
	var $DELICIOUS = null;
	var $FRIENDFEED = null;
	var $DIGG = null;
	var $BLOGSPOT = null;
	var $FLICKR = null;
	var $BEBO = null;
	var $websitename = null;
	var $websiteurl = null;
	var $rank = null;
	var $hideEmail = null;
	var $showOnline = null;

	function __construct($database) {
		parent::__construct ( '#__kunena_users', 'userid', $database );
	}
}

class CKunenaTableUsersBanned extends CKunenaTable {
	var $id = null;
	var $userid = null;
	var $ip = null;
	var $blocked = null;
	var $expiration = null;
	var $created_by = null;
	var $created_time = null;
	var $reason_private = null;
	var $reason_public = null;
	var $modified_by = null;
	var $modified_time = null;
	var $comments = null;
	var $params = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_users_banned', 'id', $database );
	}
}

class CKunenaTableVersion extends CKunenaTable {
	var $id = null;
	var $version = null;
	var $versiondate = null;
	var $installdate = null;
	var $build = null;
	var $versionname = null;
	var $state = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_version', 'id', $database );
	}
}

class CKunenaTableWhoIsOnline extends CKunenaTable {
	var $id = null;
	var $userid = null;
	var $time = null;
	var $item = null;
	var $what = null;
	var $func = null;
	var $do = null;
	var $task = null;
	var $link = null;
	var $userip = null;
	var $user = null;

	function __construct(&$database) {
		parent::__construct ( '#__kunena_whoisonline', 'id', $database );
	}
}