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

class KunenaimporterModelExport_Smf2 extends KunenaimporterModelExport {
	var $version;
	var $auth_method;
	var $params;
	protected $config = null;

	function __construct() {
		$component = JComponentHelper::getComponent ( 'com_kunenaimporter' );
		$this->params = new JParameter ( $component->params );
		$this->getConfiguration();
		if ($this->config) {
			$app = JFactory::getApplication ();
			$option ['driver'] = $app->getCfg ( 'dbtype' );
			$option ['host'] = $this->config['db_server'];
			$option ['user'] = $this->config['db_user'];
			$option ['password'] = $this->config['db_passwd'];
			$option ['database'] = $this->config['db_name'];
			$option ['prefix'] = $this->config['db_prefix'];
			$this->ext_database = JDatabase::getInstance ( $option );
		} else {
			$this->ext_database = false;
		}
		parent::__construct ();
	}

	function getConfiguration() {
		if (!$this->config) {
			$configFile = JPATH_ROOT . '/' . $this->params->get('path') . '/Settings.php';
			if (!is_file($configFile)) {
				return null;
			}
			include ($configFile);
			unset($configFile);
			$this->config = get_defined_vars();
		}
		return $this->config;
	}

	function checkConfig() {
		parent::checkConfig ();
		if ($this->error)
			return false;

		if (empty($this->config)) {
			$this->error = "Settings.php not found from " . JPATH_ROOT . '/' . $this->params->get('path') . '';
			$this->addMessage ( '<div>SMF found: <b style="color:red">NO</b></div>' );
			return false;
		}

		$query = "SELECT value FROM `#__settings` WHERE `variable` = 'smfVersion'";
		$this->ext_database->setQuery ( $query );
		$this->version = $this->ext_database->loadResult ();
		if (! $this->version) {
			$this->error = $this->ext_database->getErrorMsg ();
			if (! $this->error)
				$this->error = 'Configuration information missing: SMF version not found';
		}
		if ($this->error) {
			$this->addMessage ( '<div>SMF version: <b style="color:red">FAILED</b></div>' );
			return false;
		}

		if (version_compare($this->version, '2.0 RC3', '<'))
			$this->error = "Unsupported forum: SMF $this->version";
		if ($this->error) {
			$this->addMessage ( '<div>SMF version: <b style="color:red">' . $this->version . '</b></div>' );
			$this->addMessage ( '<div><b>Error:</b> ' . $this->error . '</div>' );
			return false;
		}
		$this->addMessage ( '<div>SMF version: <b style="color:green">' . $this->version . '</b></div>' );
			}

	function getAuthMethod() {
		return $this->auth_method;
	}

	function buildImportOps() {
		// query: (select, from, where, groupby), functions: (count, export)
		$importOps = array ();
		$importOps ['users'] = array ('count' => 'countUsers', 'export' => 'exportUsers' );
		$importOps ['categories'] = array ('count' => 'countCategories', 'export' => 'exportCategories' );
		$importOps ['config'] = array ('count' => 'countConfig', 'export' => 'exportConfig' );
		$importOps ['messages'] = array ('count' => 'countMessages', 'export' => 'exportMessages' );
		$importOps ['sessions'] = array ('count' => 'countSessions', 'export' => 'exportSessions' );
		$importOps ['subscriptions'] = array ('count' => 'countSubscriptions', 'export' => 'exportSubscriptions' );
		$importOps ['userprofile'] = array ('count' => 'countUserProfile', 'export' => 'exportUserProfile' );
		$this->importOps = & $importOps;
	}

	function countCategories() {
		static $count = false;
		if ($count === false) {
			$query = "SELECT COUNT(*) FROM #__categories";
			$count = $this->getCount ( $query );
			$query = "SELECT COUNT(*) FROM #__boards";
			$count += $this->getCount ( $query );
		}
		return $count;
	}

	function &exportCategories($start = 0, $limit = 0) {
		$query = "SELECT MAX(id_board) FROM #__boards";
		$this->ext_database->setQuery ( $query );
		$maxboard = $this->ext_database->loadResult ();
		$query = "(SELECT
			id_board AS id,
			IF(id_parent,id_parent,id_cat+{$maxboard}) AS parent,
			name AS name,
			0 AS cat_emoticon,
			0 AS locked,
			0 AS alert_admin,
			1 AS moderated,
			NULL AS moderators,
			0 AS pub_access,
			1 AS pub_recurse,
			0 AS admin_access,
			1 AS admin_recurse,
			board_order AS ordering,
			0 AS future2,
			1 AS published,
			0 AS checked_out,
			'0000-00-00 00:00:00' AS checked_out_time,
			0 AS review,
			0 AS allow_anonymous,
			0 as post_anonymous,
			0 AS hits,
			description AS description,
			'' AS headerdesc,
			'' AS class_sfx,
			0 AS allow_polls,
			id_last_msg AS id_last_msg,
			num_posts AS numPosts,
			num_topics AS numTopics,
			0 AS time_last_msg
		FROM #__boards ORDER BY id)
		UNION ALL
		(SELECT
			id_cat+{$maxboard} AS id,
			0 AS parent,
			name AS name,
			0 AS cat_emoticon,
			0 AS locked,
			0 AS alert_admin,
			1 AS moderated,
			NULL AS moderators,
			0 AS pub_access,
			1 AS pub_recurse,
			0 AS admin_access,
			1 AS admin_recurse,
			cat_order AS ordering,
			0 AS future2,
			1 AS published,
			0 AS checked_out,
			'0000-00-00 00:00:00' AS checked_out_time,
			0 AS review,
			0 AS allow_anonymous,
			0 as post_anonymous,
			0 AS hits,
			'' AS description,
			'' AS headerdesc,
			'' AS class_sfx,
			0 AS allow_polls,
			0 AS id_last_msg,
			0 AS numPosts,
			0 AS numTopics,
			0 AS time_last_msg
		FROM #__categories ORDER BY id)";
		$result = $this->getExportData ( $query, $start, $limit, 'id' );
		foreach ( $result as $item ) {
			$row = & $result [$item->id];
			$row->name = $this->prep ( $row->name );
			$row->description = $this->prep ( $row->description );
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
		$query = "SELECT * FROM `#__settings`";
		$this->ext_database->setQuery ( $query );
		$result = $this->ext_database->loadObjectList ();

		$config ['id'] = 1;
		$config ['board_title'] = $this->config['mbname'];
		$config ['email'] = $this->config['webmaster_email'];
		$config ['board_offline'] = (bool)$this->config['maintenance'];
		$config ['board_ofset'] = $result->time_offset; // + default_timezone
		$config ['offline_message'] = "<h1>{$this->config['mmessage']}</h1><p>{$this->config['mmessage']}</p>";
		// $config ['default_view'] = null;
		// $config ['enablerss'] = null;
		// $config ['enablepdf'] = null;
		// $config ['threads_per_page'] = null;
		// $config ['messages_per_page'] = null;
		// $config ['messages_per_page_search'] = null;
		// $config ['showhistory'] = null;
		// $config ['historylimit'] = null;
		// $config ['shownew'] = null;
		// $config ['newchar'] = null;
		// $config ['jmambot'] = null;
		// $config ['disemoticons'] = null;
		// $config ['template'] = null;
		// $config ['templateimagepath'] = null;
		// $config ['joomlastyle'] = null;
		// $config ['showannouncement'] = null;
		// $config ['avataroncat'] = null;
		// $config ['catimagepath'] = null;
		// $config ['numchildcolumn'] = null;
		// $config ['showchildcaticon'] = null;
		// $config ['annmodid'] = null;
		// $config ['rtewidth'] = null;
		// $config ['rteheight'] = null;
		// $config ['enablerulespage'] = null;
		// $config ['enableforumjump'] = null;
		// $config ['reportmsg'] = null;
		// $config ['username'] = null;
		// $config ['askemail'] = null;
		// $config ['showemail'] = null;
		// $config ['showuserstats'] = null;
		// $config ['poststats'] = null;
		// $config ['statscolor'] = null;
		// $config ['showkarma'] = null;
		// $config ['useredit'] = null;
		// $config ['useredittime'] = null;
		// $config ['useredittimegrace'] = null;
		// $config ['editmarkup'] = null;
		// $config ['allowsubscriptions'] = null;
		// $config ['subscriptionschecked'] = null;
		// $config ['allowfavorites'] = null;
		// $config ['wrap'] = null;
		// $config ['maxsubject'] = null;
		// $config ['maxsig'] = null;
		// $config ['regonly'] = ! allow_guestAccess;
		// $config ['changename'] = null;
		// $config ['pubwrite'] = ;
		// $config ['floodprotection'] = ;
		// $config ['mailmod'] = null;
		// $config ['mailadmin'] = null;
		// $config ['captcha'] = null;
		// $config ['mailfull'] = null;
		// $config ['allowavatar'] = null;
		// $config ['allowavatarupload'] = null;
		// $config ['allowavatargallery'] = null;
		// $config ['imageprocessor'] = null;
		// $config ['avatarsmallheight'] = null;
		// $config ['avatarsmallwidth'] = null;
		// $config ['avatarheight'] = null;
		// $config ['avatarwidth'] = null;
		$config ['avatarlargeheight'] = $result->avatar_max_height_upload;
		$config ['avatarlargewidth'] = $result->avatar_max_width_upload;
		// $config ['avatarquality'] = null;
		// $config ['avatarsize'] = null;
		$config ['allowimageupload'] = $result->attachmentEnable;
		// $config ['allowimageregupload'] = null;
		$config ['imageheight'] = $result->max_image_height;
		$config ['imagewidth'] = $result->max_image_width;
		$config ['imagesize'] = $result->attachmentSizeLimit;
		$config ['allowfileupload'] = $result->attachmentEnable;
		// $config ['allowfileregupload'] = null;
		// $config ['filetypes'] = null;
		$config ['filesize'] = $result->attachmentSizeLimit;
		// $config ['showranking'] = null;
		// $config ['rankimages'] = null;
		// $config ['avatar_src'] = null;
		// $config ['fb_profile'] = null;
		// $config ['pm_component'] = null;
		// $config ['discussbot'] = null;
		// $config ['userlist_rows'] = null;
		// $config ['userlist_online'] = null;
		// $config ['userlist_avatar'] = null;
		// $config ['userlist_name'] = null;
		// $config ['userlist_username'] = null;
		// $config ['userlist_group'] = null;
		// $config ['userlist_posts'] = null;
		// $config ['userlist_karma'] = null;
		// $config ['userlist_email'] = null;
		// $config ['userlist_usertype'] = null;
		// $config ['userlist_joindate'] = null;
		// $config ['userlist_lastvisitdate'] = null;
		// $config ['userlist_userhits'] = null;
		// $config ['showlatest'] = null;
		// $config ['latestcount'] = null;
		// $config ['latestcountperpage'] = null;
		// $config ['latestcategory'] = null;
		// $config ['latestsinglesubject'] = null;
		// $config ['latestreplysubject'] = null;
		// $config ['latestsubjectlength'] = null;
		// $config ['latestshowdate'] = null;
		// $config ['latestshowhits'] = null;
		// $config ['latestshowauthor'] = null;
		// $config ['showstats'] = null;
		$config ['showwhoisonline'] = $result->who_enabled;
		// $config ['showgenstats'] = null;
		// $config ['showpopuserstats'] = null;
		// $config ['popusercount'] = null;
		// $config ['showpopsubjectstats'] = null;
		// $config ['popsubjectcount'] = null;
		// $config ['usernamechange'] = null;
		// $config ['rules_infb'] = null;
		// $config ['rules_cid'] = null;
		// $config ['rules_link'] = null;
		// $config ['enablehelppage'] = null;
		// $config ['help_infb'] = null;
		// $config ['help_cid'] = null;
		// $config ['help_link'] = null;
		// $config ['showspoilertag'] = null;
		// $config ['showvideotag'] = null;
		// $config ['showebaytag'] = null;
		// $config ['trimlongurls'] = null;
		// $config ['trimlongurlsfront'] = null;
		// $config ['trimlongurlsback'] = null;
		// $config ['autoembedyoutube'] = null;
		// $config ['autoembedebay'] = null;
		// $config ['ebaylanguagecode'] = null;
		// $config ['fbsessiontimeout'] = null;
		// $config ['highlightcode'] = null;
		// $config ['rsstype'] = null;
		// $config ['rsshistory'] = null;
		// $config ['fbdefaultpage'] = null;
		// $config ['default_sort'] = null;

		// $config ['alphauserpointsnumchars'] = null;
		// $config ['sef'] = null;
		// $config ['sefcats'] = null;
		// $config ['sefutf8'] = null;
		// $config ['showimgforguest'] = null;
		// $config ['showfileforguest'] = null;
		// $config ['pollnboptions'] = null;
		// $config ['pollallowvoteone'] = null;
		// $config ['pollenabled'] = null;
		// $config ['poppollscount'] = null;
		// $config ['showpoppollstats'] = null;
		// $config ['polltimebtvotes'] = null;
		// $config ['pollnbvotesbyuser'] = null;
		// $config ['pollresultsuserslist'] = null;
		// $config ['maxpersotext'] = null;
		// $config ['ordering_system'] = null;
		// $config ['post_dateformat'] = null;
		// $config ['post_dateformat_hover'] = null;
		// $config ['hide_ip'] = null;
		// $config ['imagetypes'] = null;
		// $config ['checkmimetypes'] = null;
		// $config ['imagemimetypes'] = null;
		// $config ['imagequality'] = null;
		$config ['thumbheight'] = $result->attachmentThumbHeight;
		$config ['thumbwidth'] = $result->attachmentThumbWidth;
		// $config ['hideuserprofileinfo'] = null;
		// $config ['integration_access'] = null;
		// $config ['integration_login'] = null;
		// $config ['integration_avatar'] = null;
		// $config ['integration_profile'] = null;
		// $config ['integration_private'] = null;
		// $config ['integration_activity'] = null;
		// $config ['boxghostmessage'] = null;
		// $config ['userdeletetmessage'] = null;
		// $config ['latestcategory_in'] = null;
		// $config ['topicicons'] = null;
		// $config ['onlineusers'] = null;
		// $config ['debug'] = null;
		// $config ['catsautosubscribed'] = null;
		// $config ['showbannedreason'] = null;
		// $config ['version_check'] = null;
		// $config ['showthankyou'] = null;
		// $config ['showpopthankysoustats'] = null;
		// $config ['popthankscount'] = null;
		// $config ['mod_see_deleted'] = null;
		// $config ['bbcode_img_secure'] = null;
		$result = array ('1' => $config );
		return $result;
	}

	function countMessages() {
		$query = "SELECT COUNT(*) FROM #__messages";
		return $this->getCount ( $query );
	}

	function &exportMessages($start = 0, $limit = 0) {
		$query = "SELECT
			m.id_msg AS id,
			IF(m.id_msg=t.id_first_msg,0,t.id_first_msg) AS parent,
			t.id_first_msg AS thread,
			t.id_board AS catid,
			IF(m.poster_name, m.poster_name, u.member_name) AS name,
			m.id_member AS userid,
			m.poster_email AS email,
			m.subject AS subject,
			m.poster_time AS time,
			m.poster_ip AS ip,
			0 AS topic_emoticon,
			IF(m.id_msg=t.id_first_msg,locked,0) AS locked,
			IF(m.approved=1,0,1) AS hold,
			IF(m.id_msg=t.id_first_msg,t.is_sticky,0) AS ordering,
			t.num_views AS hits,
			0 AS moved,
			IF(m.modified_time>0,m.id_msg_modified,0) AS modified_by,
			m.modified_time AS modified_time,
			'' AS modified_reason,
			m.body AS message
		FROM `#__messages` AS m
		LEFT JOIN `#__topics` AS t ON m.id_topic = t.id_topic
		LEFT JOIN `#__members` AS u ON m.id_member = u.id_member
		ORDER BY m.id_msg";
		$result = $this->getExportData ( $query, $start, $limit, 'id' );
		foreach ( $result as $item ) {
			$row = & $result [$item->id];
			$row->subject = $this->prep ( $row->subject );
			$row->message = $this->prep ( $row->message );
		}
		return $result;
	}

	function countSessions() {
		$query = "SELECT COUNT(*) FROM `#__members` WHERE last_login>0";
		return $this->getCount ( $query );
	}
	function &exportSessions($start = 0, $limit = 0) {
		$query = "SELECT
			id_member AS userid,
			NULL AS allowed,
			last_login AS lasttime,
			'' AS readtopics,
			last_login AS currvisit
		FROM `#__members`
		WHERE last_login>0";
		$result = $this->getExportData ( $query, $start, $limit );
		return $result;
	}

	function countSubscriptions() {
		$query = "SELECT COUNT(*) FROM `#__log_notify`";
		return $this->getCount ( $query );
	}
	function &exportSubscriptions($start = 0, $limit = 0) {
		$query = "SELECT
			t.id_first_msg AS thread,
			s.id_member AS userid,
			0 AS future1
		FROM `#__log_notify` AS s
		INNER JOIN `#__topics` AS t ON s.id_topic=t.id_topic";
		$result = $this->getExportData ( $query, $start, $limit );
		return $result;
	}

	function countUserProfile() {
		$query = "SELECT COUNT(*) FROM `#__members`";
		return $this->getCount ( $query );
	}

	function &exportUserProfile($start = 0, $limit = 0) {
		$query = "SELECT
			u.id_member AS userid,
			'flat' AS view,
			u.signature AS signature,
			0 AS moderator,
			NULL AS banned,
			0 AS ordering,
			u.posts AS posts,
			avatar AS avatar,
			(karma_good-karma_bad) AS karma,
			0 AS karma_time,
			1 AS group_id,
			0 AS uhits,
			u.personal_text AS personalText,
			u.gender AS gender,
			u.birthdate AS birthdate,
			u.location AS location,
			u.icq AS ICQ,
			u.aim AS AIM,
			u.yim AS YIM,
			u.msn AS MSN,
			NULL AS SKYPE,
			NULL AS TWITTER,
			NULL AS FACEBOOK,
			NULL AS GTALK,
			NULL AS MYSPACE,
			NULL AS LINKEDIN,
			NULL AS DELICIOUS,
			NULL AS FRIENDFEED,
			NULL AS DIGG,
			NULL AS BLOGSPOT,
			NULL AS FLICKR,
			NULL AS BEBO,
			u.website_title AS websitename,
			u.website_url AS websiteurl,
			0 AS rank,
			hide_email AS hideEmail,
			show_online AS showOnline
		FROM `#__members` AS u
		ORDER BY u.id_member";
		$result = $this->getExportData ( $query, $start, $limit, 'userid' );
/*
		foreach ( $result as $item ) {
			$row = & $result [$item->userid];
			// Convert bbcode in signature
			$row->signature = $this->prep ( $row->signature );
			$row->location = $this->prep ( $row->location );
		}
*/
		return $result;
	}

	function countUsers() {
		$query = "SELECT COUNT(*) FROM `#__members`";
		return $this->getCount ( $query );
	}

	function &exportUsers($start = 0, $limit = 0) {
		$query = "SELECT
			u.id_member AS extid,
			u.member_name AS extusername,
			u.real_name AS name,
			u.member_name AS username,
			u.email_address AS email,
			CONCAT(u.password_salt,':',u.passwd) AS password,
			'Registered' AS usertype,
			IF(is_activated>0,0,1) AS block,
			0 AS gid,
			FROM_UNIXTIME(u.date_registered) AS registerDate,
			IF(u.last_login>0, FROM_UNIXTIME(u.last_login), '0000-00-00 00:00:00') AS lastvisitDate,
			NULL AS params
		FROM `#__members` AS u
		ORDER BY u.id_member";
		$result = $this->getExportData ( $query, $start, $limit, 'extid' );
		foreach ( $result as $item ) {
			$row = & $result [$item->extid];
			$row->name = html_entity_decode ( $row->name );
			$row->username = html_entity_decode ( $row->username );
		}
		return $result;
	}

	function mapJoomlaUser($joomlauser) {
		$query = "SELECT id_member
			FROM #__members WHERE member_name={$this->ext_database->Quote($joomlauser->username)}";

		$this->ext_database->setQuery( $query );
		$result = intval($this->ext_database->loadResult());
		return $result;
	}

	function prep($s) {
		$s = html_entity_decode($s, ENT_COMPAT, 'UTF-8');
		$s = preg_replace ( "/\r/", '', $s );
		$s = preg_replace ( "/\n/", '', $s );
		$s = preg_replace ( '/<br \/>/', "\n", $s );
		$s = preg_replace ( '/\[s\]/', "[strike]", $s );
		$s = preg_replace ( '/\[\/s\]/', "[/strike]", $s );
		return $s;
	}
}
