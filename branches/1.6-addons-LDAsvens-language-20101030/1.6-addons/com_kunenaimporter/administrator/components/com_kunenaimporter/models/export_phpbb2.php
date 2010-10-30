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

// Import Joomla! libraries
jimport ( 'joomla.application.component.model' );
jimport ( 'joomla.application.application' );

require_once (JPATH_COMPONENT . DS . 'models' . DS . 'export.php');

class KunenaimporterModelExport_phpBB2 extends KunenaimporterModelExport {
	var $version;
	var $pnversion;

	function checkConfig() {
		parent::checkConfig ();
		if (JError::isError ( $this->ext_database ))
			return;

		$query = "SELECT config_value FROM #__config WHERE config_name='version'";
		$this->ext_database->setQuery ( $query );
		$this->version = $this->ext_database->loadResult ();
		if (! $this->version) {
			$this->error = $this->ext_database->getErrorMsg ();
			if (! $this->error)
				$this->error = 'Configuration information missing: phpBB version not found';
		}
		if ($this->error) {
			$this->addMessage ( '<div>phpBB version: <b style="color:red">FAILED</b></div>' );
			return false;
		}

		if ($this->version [0] == '.')
			$this->version = '2' . $this->version;
		$version = explode ( '.', $this->version, 3 );
		if ($version [0] != 2 || $version [1] != 0 || $version [2] < '15')
			$this->error = "Unsupported forum: phpBB $this->version";
		if ($this->error) {
			$this->addMessage ( '<div>phpBB version: <b style="color:red">' . $this->version . '</b></div>' );
			$this->addMessage ( '<div><b>Error:</b> ' . $this->error . '</div>' );
			return false;
		}
		$this->addMessage ( '<div>phpBB version: <b style="color:green">' . $this->version . '</b></div>' );

		$query = "SELECT config_value FROM #__config WHERE config_name='pnphpbb2_version'";
		$this->ext_database->setQuery ( $query );
		$this->version = $this->ext_database->loadResult ();
		if ($this->version) {
			$this->error = "Unsupported forum: PNphpBB $this->version";
			$this->addMessage ( '<div>PNphpBB version: <b style="color:red">' . $this->version . '</b></div>' );
			$this->addMessage ( '<div><b>Error:</b> ' . $this->error . '</div>' );
			return false;
		}
	}

	function buildImportOps() {
		// query: (select, from, where, groupby), functions: (count, export)
		$importOps = array ();
		$importOps ['categories'] = array ('count' => 'countCategories', 'export' => 'exportCategories' );
		$importOps ['config'] = array ('count' => 'countConfig', 'export' => 'exportConfig' );
		$importOps ['messages'] = array ('count' => 'countMessages', 'export' => 'exportMessages' );
		//$importOps['smilies'] = array('count'=>'countSmilies', 'export'=>'exportSmilies');
		$importOps ['sessions'] = array ('count' => 'countSessions', 'export' => 'exportSessions' );
		$importOps ['subscriptions'] = array ('count' => 'countSubscriptions', 'export' => 'exportSubscriptions' );
		$importOps ['userprofile'] = array ('count' => 'countUserProfile', 'export' => 'exportUserProfile' );
		$this->importOps = & $importOps;
	}

	function countCategories() {
		$query = "SELECT count(*) FROM #__categories";
		$count = $this->getCount ( $query );
		$query = "SELECT count(*) FROM #__forums";
		return $count + $this->getCount ( $query );
	}

	function &exportCategories($start = 0, $limit = 0) {
		// Import the categories
		$query = "(SELECT " . "cat_id+500 AS id, " . "0 AS parent, " . "cat_title AS name, " . //			"cat_emoticon, ".
		"0 AS locked, " . //			"alert_admin, ".
		//			"moderated, ".
		//			"moderators, ".
		"0 AS pub_access," . "0 AS pub_recurse, " . "0 AS admin_access," . "0 AS admin_recurse, " . "cat_order AS ordering, " . "1 AS published, " . //			"checked_out, ".
		//			"checked_out_time, ".
		//			"review, ".
		//			"hits, ".
		"cat_desc AS description, " . //			"headerdesc, ".
		//			"class_sfx, ".
		"0 AS id_last_msg, " . "0 AS numPosts, " . "0 AS numTopics, " . //			"0 AS time_last_msg ".
		"0 AS dummy " . "FROM #__categories) " .

		"UNION ALL " .

		"(SELECT " . "forum_id+1 AS id, " . "cat_id+500 AS parent, " . "forum_name AS name, " . //			"cat_emoticon, ".
		"(forum_status=1) AS locked, " . //			"alert_admin, ".
		//			"moderated, ".
		//			"moderators, ".
		"0 AS pub_access," . "0 AS pub_recurse, " . "0 AS admin_access," . "0 AS admin_recurse, " . "forum_order AS ordering, " . "1 AS published, " . //			"checked_out, ".
		//			"checked_out_time, ".
		//			"review, ".
		//			"hits, ".
		"forum_desc AS description, " . //			"headerdesc, ".
		//			"class_sfx, ".
		"forum_last_post_id AS id_last_msg, " . "forum_posts AS numPosts, " . "forum_topics AS numTopics, " . //			"time_last_msg ".
		"0 AS dummy " . "FROM #__forums) ORDER BY id";
		$result = $this->getExportData ( $query, $start, $limit );
		foreach ( $result as $key => $item ) {
			$row = & $result [$key];
			$row->name = prep ( $row->name );
			$row->description = prep ( $row->description );
		}
		return $result;
	}

	function countConfig() {
		return 1;
	}

	function &exportConfig($start = 0, $limit = 0) {
		$config = array ();
		if ($start)
			return $config;

		$query = "SELECT config_name, config_value AS value FROM #__config";
		$result = $this->getExportData ( $query, 0, 1000, 'config_name' );

		if (! $result)
			return $config;

		$config ['id'] = 1; // $result['config_id']->value;
		$config ['board_title'] = $result ['sitename']->value;
		$config ['email'] = $result ['board_email']->value;
		$config ['board_offline'] = $result ['board_disable']->value;
		$config ['board_ofset'] = $result ['board_timezone']->value;
		// 		$config['offline_message'] = null;
		// 		$config['default_view'] = null;
		// 		$config['enablerss'] = null;
		// 		$config['enablepdf'] = null;
		$config ['threads_per_page'] = $result ['topics_per_page']->value;
		$config ['messages_per_page'] = $result ['posts_per_page']->value;
		// 		$config['messages_per_page_search'] = null;
		// 		$config['showhistory'] = null;
		// 		$config['historylimit'] = null;
		// 		$config['shownew'] = null;
		// 		$config['newchar'] = null;
		// 		$config['jmambot'] = null;
		$config ['disemoticons'] = $result ['allow_smilies']->value ^ 1;
		// 		$config['template'] = null;
		// 		$config['templateimagepath'] = null;
		// 		$config['joomlastyle'] = null;
		// 		$config['showannouncement'] = null;
		// 		$config['avataroncat'] = null;
		// 		$config['catimagepath'] = null;
		// 		$config['numchildcolumn'] = null;
		// 		$config['showchildcaticon'] = null;
		// 		$config['annmodid'] = null;
		// 		$config['rtewidth'] = null;
		// 		$config['rteheight'] = null;
		// 		$config['enablerulespage'] = null;
		// 		$config['enableforumjump'] = null;
		// 		$config['reportmsg'] = null;
		// 		$config['username'] = null;
		// 		$config['askemail'] = null;
		// 		$config['showemail'] = null;
		// 		$config['showuserstats'] = null;
		// 		$config['poststats'] = null;
		// 		$config['statscolor'] = null;
		// 		$config['showkarma'] = null;
		// 		$config['useredit'] = null;
		// 		$config['useredittime'] = null;
		// 		$config['useredittimegrace'] = null;
		// 		$config['editmarkup'] = null;
		$config ['allowsubscriptions'] = 1;
		// 		$config['subscriptionschecked'] = null;
		// 		$config['allowfavorites'] = null;
		// 		$config['wrap'] = null;
		// 		$config['maxsubject'] = null;
		$config ['maxsig'] = $result ['allow_sig']->value ? $result ['max_sig_chars']->value : 0;
		// 		$config['regonly'] = null;
		$config ['changename'] = $result ['allow_namechange']->value;
		// 		$config['pubwrite'] = null;
		$config ['floodprotection'] = $result ['flood_interval']->value;
		// 		$config['mailmod'] = null;
		// 		$config['mailadmin'] = null;
		// 		$config['captcha'] = null;
		// 		$config['mailfull'] = null;
		$config ['allowavatar'] = $result ['allow_avatar_upload']->value || $result ['allow_avatar_local']->value;
		$config ['allowavatarupload'] = $result ['allow_avatar_upload']->value;
		$config ['allowavatargallery'] = $result ['allow_avatar_local']->value;
		// 		$config['imageprocessor'] = null;
		$config ['avatarsmallheight'] = $result ['avatar_max_height']->value > 50 ? 50 : $result ['avatar_max_height']->value;
		$config ['avatarsmallwidth'] = $result ['avatar_max_width']->value > 50 ? 50 : $result ['avatar_max_width']->value;
		$config ['avatarheight'] = $result ['avatar_max_height']->value > 100 ? 100 : $result ['avatar_max_height']->value;
		$config ['avatarwidth'] = $result ['avatar_max_width']->value > 100 ? 100 : $result ['avatar_max_width']->value;
		$config ['avatarlargeheight'] = $result ['avatar_max_height']->value;
		$config ['avatarlargewidth'] = $result ['avatar_max_width']->value;
		// 		$config['avatarquality'] = null;
		$config ['avatarsize'] = ( int ) ($result ['avatar_filesize']->value / 1000);
		// 		$config['allowimageupload'] = null;
		// 		$config['allowimageregupload'] = null;
		// 		$config['imageheight'] = null;
		// 		$config['imagewidth'] = null;
		// 		$config['imagesize'] = null;
		// 		$config['allowfileupload'] = null;
		// 		$config['allowfileregupload'] = null;
		// 		$config['filetypes'] = null;
		// 		$config['filesize'] = null;
		// 		$config['showranking'] = null;
		// 		$config['rankimages'] = null;
		// 		$config['avatar_src'] = null;
		// 		$config['fb_profile'] = null;
		// 		$config['pm_component'] = null;
		// 		$config['discussbot'] = null;
		// 		$config['userlist_rows'] = null;
		// 		$config['userlist_online'] = null;
		// 		$config['userlist_avatar'] = null;
		// 		$config['userlist_name'] = null;
		// 		$config['userlist_username'] = null;
		// 		$config['userlist_group'] = null;
		// 		$config['userlist_posts'] = null;
		// 		$config['userlist_karma'] = null;
		// 		$config['userlist_email'] = null;
		// 		$config['userlist_usertype'] = null;
		// 		$config['userlist_joindate'] = null;
		// 		$config['userlist_lastvisitdate'] = null;
		// 		$config['userlist_userhits'] = null;
		// 		$config['showlatest'] = null;
		// 		$config['latestcount'] = null;
		// 		$config['latestcountperpage'] = null;
		// 		$config['latestcategory'] = null;
		// 		$config['latestsinglesubject'] = null;
		// 		$config['latestreplysubject'] = null;
		// 		$config['latestsubjectlength'] = null;
		// 		$config['latestshowdate'] = null;
		// 		$config['latestshowhits'] = null;
		// 		$config['latestshowauthor'] = null;
		// 		$config['showstats'] = null;
		// 		$config['showwhoisonline'] = null;
		// 		$config['showgenstats'] = null;
		// 		$config['showpopuserstats'] = null;
		// 		$config['popusercount'] = null;
		// 		$config['showpopsubjectstats'] = null;
		// 		$config['popsubjectcount'] = null;
		// 		$config['usernamechange'] = null;
		// 		$config['rules_infb'] = null;
		// 		$config['rules_cid'] = null;
		// 		$config['rules_link'] = null;
		// 		$config['enablehelppage'] = null;
		// 		$config['help_infb'] = null;
		// 		$config['help_cid'] = null;
		// 		$config['help_link'] = null;
		// 		$config['showspoilertag'] = null;
		// 		$config['showvideotag'] = null;
		// 		$config['showebaytag'] = null;
		// 		$config['trimlongurls'] = null;
		// 		$config['trimlongurlsfront'] = null;
		// 		$config['trimlongurlsback'] = null;
		// 		$config['autoembedyoutube'] = null;
		// 		$config['autoembedebay'] = null;
		// 		$config['ebaylanguagecode'] = null;
		$config ['fbsessiontimeout'] = $result ['session_length']->value;
		// 		$config['highlightcode'] = null;
		// 		$config['rsstype'] = null;
		// 		$config['rsshistory'] = null;
		$config ['fbdefaultpage'] = 'categories';
		// 		$config['default_sort'] = null;
		$result = array ('1' => $config );
		return $result;
	}

	function countMessages() {
		$query = "SELECT count(*) FROM #__posts";
		return $this->getCount ( $query );
	}

	function &exportMessages($start = 0, $limit = 0) {
		$query = "SELECT " . "p.post_id AS id, " . "IF(p.post_id=t.topic_first_post_id,0,t.topic_first_post_id) AS parent, " . "t.topic_first_post_id AS thread, " . "t.forum_id+1 AS catid, " . "IF(p.post_username, p.post_username, u.username) AS name, " . "p.poster_id AS userid, " . "u.user_email AS email, " . "IF(x.post_subject, x.post_subject, t.topic_title) AS subject, " . "p.post_time AS time, " . "p.poster_ip AS ip, " . //			"topic_emoticon, ".
		"(t.topic_status=1 AND p.post_id=t.topic_first_post_id) AS locked, " . "(a.approval_id>0) AS hold, " . "(t.topic_type>0 AND p.post_id=t.topic_first_post_id) AS ordering, " . "t.topic_views AS hits, " . //			"t.topic_moved_id AS moved, ".
		"IF(p.post_edit_time,u.username,'') AS modified_by, " . "p.post_edit_time AS modified_time, " . //			"p.post_edit_reason AS modified_reason, ".
		"x.post_text AS message " . "FROM `#__posts` AS p " . "LEFT JOIN `#__posts_text` AS x ON p.post_id = x.post_id " . "LEFT JOIN `#__topics` AS t ON p.topic_id = t.topic_id " . "LEFT JOIN `#__users` AS u ON p.poster_id = u.user_id " . "LEFT JOIN `#__approve_posts` AS a ON p.post_id = a.post_id " . "ORDER BY p.post_id";
		$result = $this->getExportData ( $query, $start, $limit );
		// Iterate over all the posts and convert them to Kunena
		foreach ( $result as $key => $item ) {
			$row = & $result [$key];
			$row->name = prep ( $row->name );
			$row->email = prep ( $row->email );
			$row->subject = prep ( $row->subject );
			$row->modified_reason = prep ( $row->modified_reason );
			$row->message = prep ( $row->message );
		}
		return $result;
	}

	function countSessions() {
		$query = "SELECT COUNT(*) FROM `#__users` AS u WHERE user_lastvisit>0";
		return $this->getCount ( $query );
	}
	function &exportSessions($start = 0, $limit = 0) {
		$query = "SELECT " . "user_id AS userid, " . //			"allowed, ".
		"user_lastvisit AS lasttime, " . //			"readtopics, ".
		"user_session_time AS currvisit " . "FROM `#__users` WHERE user_lastvisit>0";
		$result = $this->getExportData ( $query, $start, $limit );

		foreach ( $result as $key => $item ) {
			$row = & $result [$key];
			$row->lasttime = date ( "Y-m-d H:i:s", $row->lasttime );
			$row->currvisit = date ( "Y-m-d H:i:s", $row->currvisit );
		}
		return $result;
	}

	function countSubscriptions() {
		$query = "SELECT COUNT(*) FROM `#__topics_watch`";
		return $this->getCount ( $query );
	}
	function &exportSubscriptions($start = 0, $limit = 0) {
		$query = "SELECT " . "t.topic_first_post_id AS thread, " . "w.user_id AS userid " . //			"future1, ".
		"FROM `#__topics_watch` AS w LEFT JOIN `#__topics` AS t ON w.topic_id=t.topic_id";
		$result = $this->getExportData ( $query, $start, $limit );
		return $result;
	}

	function countUserProfile() {
		$query = "SELECT COUNT(*) FROM `#__users` WHERE user_id > 0";
		return $this->getCount ( $query );
	}

	function &exportUserProfile($start = 0, $limit = 0) {
		$query = "SELECT " . "user_id AS userid, " . //			"view, ".
		"user_sig AS signature, " . //			"moderator, ".
		//			"ordering, ".
		"user_posts AS posts, " . //			"avatar, ".
		//			"karma, ".
		//			"karma_time, ".
		//			"group_id, ".
		//			"uhits, ".
		//			"personalText, ".
		//			"gender, ".
		//			"user_birthday AS birthdate, ".
		"user_from AS location, " . "user_icq AS ICQ, " . "user_aim AS AIM, " . "user_yim AS YIM, " . "user_msnm AS MSN, " . //			"SKYPE, ".
		//			"GTALK, ".
		//			"websitename, ".
		"user_website AS websiteurl, " . //			"rank, ".
		//			"hideEmail, ".
		//			"showOnline ".
		"0 AS dummy " . "FROM `#__users` WHERE user_id > 0 ORDER BY user_id";
		$result = $this->getExportData ( $query, $start, $limit );

		foreach ( $result as $key => $item ) {
			$row = & $result [$key];
			// Convert bbcode in signature
			$row->signature = prep ( $row->signature );
			$row->location = prep ( $row->location );
		}
		return $result;
	}

	function countUsers() {
		$query = "SELECT count(*) FROM #__users AS f WHERE user_id > 0 && user_lastvisit>0 ";
		return $this->getCount ( $query );
	}

	function &exportUsers($start = 0, $limit = 0) {
		$prefix = $this->ext_database->_table_prefix;
		$prefix = substr ( $prefix, 0, strpos ( $prefix, '_phpbb_' ) );

		// PostNuke
		$query = "SELECT u.user_id AS extuserid, username, user_email AS email, user_password AS password, user_regdate, (b.ban_userid>0) AS blocked FROM #__users AS u LEFT OUTER JOIN #__banlist AS b ON u.pn_uid = b.ban_userid WHERE user_id > 0 && user_lastvisit>0 ORDER BY u.user_id";

		$result = $this->getExportData ( $query, $start, $limit );
		foreach ( $result as $key => $item ) {
			$row = & $result [$key];
			$row->name = $row->username = $row->username;

			if ($row->user_regdate > $row->pn_user_regdate)
				$row->user_regdate = $row->pn_user_regdate;
				// Convert date for last visit and register date.
			$row->registerDate = date ( "Y-m-d H:i:s", $row->user_regdate );
			$row->lastvisitDate = date ( "Y-m-d H:i:s", $row->user_lastvisit );

			// Set user type and group id - 1=admin, 2=moderator
			if ($row->user_level == "1") {
				$row->usertype = "Administrator";
			} else {
				$row->usertype = "Registered";
			}

			// Convert bbcode in signature
			$row->user_sig = prep ( $row->user_sig );

			// No imported users will get mails from the admin
			$row->emailadmin = "0";

			unset ( $row->user_regdate, $row->user_lastvisit, $row->user_level );
		}
		return $result;
	}

	function countSmilies() {
		return false;

		$query = "SELECT count(*) FROM #__smilies";
		return $this->getCount ( $query );
	}

	function &exportSmilies($start = 0, $limit = 0) {
		$query = "SELECT smiley_id AS id, code AS code, smiley_url AS location, smiley_url AS greylocation, 1 AS emoticonbar FROM `#__smilies` ";
		$result = $this->getExportData ( $query, $start, $limit );
		return $result;
	}

}

//--- Function to prepare strings for MySQL storage ---/
function prep($s) {
	// Parse out the $uid things that fuck up bbcode


	$s = preg_replace ( '/\&lt;/', '<', $s );
	$s = preg_replace ( '/\&gt;/', '>', $s );
	$s = preg_replace ( '/\&quot;/', '"', $s );
	$s = preg_replace ( '/\&amp;/', '&', $s );
	$s = preg_replace ( '/\&nbsp;/', ' ', $s );

	$s = preg_replace ( '/\&#39;/', "'", $s );
	$s = preg_replace ( '/\&#40;/', '(', $s );
	$s = preg_replace ( '/\&#41;/', ')', $s );
	$s = preg_replace ( '/\&#46;/', '.', $s );
	$s = preg_replace ( '/\&#58;/', ':', $s );
	$s = preg_replace ( '/\&#123;/', '{', $s );
	$s = preg_replace ( '/\&#125;/', '}', $s );

	// <strong> </strong>
	$s = preg_replace ( '/\[b:(.*?)\]/', '[b]', $s );
	$s = preg_replace ( '/\[\/b:(.*?)\]/', '[/b]', $s );

	// <em> </em>
	$s = preg_replace ( '/\[i:(.*?)\]/', '[i]', $s );
	$s = preg_replace ( '/\[\/i:(.*?)\]/', '[/i]', $s );

	// <u> </u>
	$s = preg_replace ( '/\[u:(.*?)\]/', '[u]', $s );
	$s = preg_replace ( '/\[\/u:(.*?)\]/', '[/u]', $s );

	// quote
	$s = preg_replace ( '/\[quote:(.*?)\]/', '[quote]', $s );
	$s = preg_replace ( '/\[quote(:(.*?))?="(.*?)"\]/', '[b]\\3[/b]\n[quote]', $s );
	$s = preg_replace ( '/\[\/quote:(.*?)\]/', '[/quote]', $s );

	// image
	#$s = preg_replace('/\[img:(.*?)="(.*?)"\]/', '[img="\\2"]', $s);
	$s = preg_replace ( '/\[img:(.*?)\](.*?)\[\/img:(.*?)\]/si', '[img]\\2[/img]', $s );

	// color
	$s = preg_replace ( '/\[color=(.*?):(.*?)\]/', '[color=\\1]', $s );
	$s = preg_replace ( '/\[\/color:(.*?)\]/', '[/color]', $s );

	// size
	$s = preg_replace ( '/\[size=\d:(.*?)\]/', '[size=1]', $s );
	$s = preg_replace ( '/\[size=1[0123]:(.*?)\]/', '[size=2]', $s );
	$s = preg_replace ( '/\[size=1[4567]:(.*?)\]/', '[size=3]', $s );
	$s = preg_replace ( '/\[size=((1[89])|(2[01])):(.*?)\]/', '[size=4]', $s );
	$s = preg_replace ( '/\[size=2[234567]:(.*?)\]/', '[size=5]', $s );
	$s = preg_replace ( '/\[size=((2[89])|(3[01])):(.*?)\]/', '[size=6]', $s );
	$s = preg_replace ( '/\[size=3[2-9]:(.*?)\]/', '[size=7]', $s );
	$s = preg_replace ( '/\[\/size:(.*?)\]/', '[/size]', $s );

	// code
	// $s = preg_replace('/\[code:(.*?):(.*?)\]/',    '[code:\\1]', $s);
	// $s = preg_replace('/\[\/code:(.*?):(.*?)\]/', '[/code:\\1]', $s);


	// $s = preg_replace('/\[code:(.*?):(.*?)\]/',    '[code]', $s);
	// #$s = preg_replace('/\[\/code:(.*?):(.*?)\]/', '[/code]', $s);


	$s = preg_replace ( '/\[code:(.*?)]/', '[code]', $s );
	$s = preg_replace ( '/\[\/code:(.*?)]/', '[/code]', $s );

	// lists
	$s = preg_replace ( '/\[list(:(.*?))?\]/', '[ul]', $s );
	$s = preg_replace ( '/\[list=([a1]):(.*?)\]/', '[ol]', $s );
	$s = preg_replace ( '/\[\/list:u:(.*?)\]/', '[/ul]', $s );
	$s = preg_replace ( '/\[\/list:o:(.*?)\]/', '[/ol]', $s );

	$s = preg_replace ( '/\[\*:(.*?)\]/', '[li]', $s );
	$s = preg_replace ( '/\[\/\*:(.*?)\]/', '[/li]', $s );

	$s = preg_replace ( '/<!-- s(.*?) --><img src=\"{SMILIES_PATH}.*?\/><!-- s.*? -->/', ' \\1 ', $s );

	$s = preg_replace ( '/\<!-- e(.*?) -->/', '', $s );
	$s = preg_replace ( '/\<!-- w(.*?) -->/', '', $s );
	$s = preg_replace ( '/\<!-- m(.*?) -->/', '', $s );

	$s = preg_replace ( '/\<a class=\"postlink\" href=\"(.*?)\">(.*?)<\/a>/', '[url=\\1]\\2[/url]', $s );
	$s = preg_replace ( '/\<a href=\"(.*?)\">(.*?)<\/a>/', '[url=\\1]\\2[/url]', $s );

	$s = preg_replace ( '/\<a href=.*?mailto:.*?>/', '', $s );

	$s = preg_replace ( '/\[\/url:(.*?)]/', '[/url]', $s );

	$s = preg_replace ( '/\<\/a>/', '', $s );

	# $s = preg_replace('/\\\\/', '', $s);


	$s = addslashes ( $s );

	return $s;
}