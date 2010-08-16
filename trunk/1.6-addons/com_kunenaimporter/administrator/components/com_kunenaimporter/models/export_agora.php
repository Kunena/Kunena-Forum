<?php
/**
 * Joomla! 1.5 component: Kunena Forum Importer
 *
 * @version $Id: export_agora.php 2311 2010-04-18 10:43:48Z mahagr $
 * @author Kunena Team
 * @package Joomla
 * @subpackage Kunena Forum Importer
 * @license GNU/GPL
 *
 * Imports forum data from Agora
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');
jimport('joomla.application.application');

require_once( JPATH_COMPONENT.DS.'models'.DS.'export.php' );

class KunenaimporterModelExport_Agora extends KunenaimporterModelExport {
	var $version;
	var $pnversion;

	function checkConfig() {
		parent::checkConfig();
		if (JError::isError($this->ext_database)) return;

	}

	function buildImportOps() {
		// query: (select, from, where, groupby), functions: (count, export)
		$importOps = array();
		$importOps['categories'] = array('count'=>'countCategories', 'export'=>'exportCategories');
		$this->importOps =& $importOps;
	}

	function countConfig() {
		return 1;
	}

	function &exportConfig($start=0, $limit=0) {
		$config = array();
		if ($start) return $config;

		$query="SELECT conf_name, conf_value AS value FROM #__agora_config";
		$result = $this->getExportData($query, 0, 1000, 'conf_name');

		if (!$result) return $config;

		$config['id'] = 1;
		$config['board_title'] = $result['sitename']->value;
		$config['email'] = $result['board_email']->value;
		$config['board_offline'] = $result['board_disable']->value;
		$config['board_ofset'] = $result['board_timezone']->value;
// 		$config['offline_message'] = null;
// 		$config['default_view'] = null;
// 		$config['enablerss'] = null;
// 		$config['enablepdf'] = null;
		$config['threads_per_page'] = $result['topics_per_page']->value;
		$config['messages_per_page'] = $result['posts_per_page']->value;
// 		$config['messages_per_page_search'] = null;
// 		$config['showhistory'] = null;
// 		$config['historylimit'] = null;
// 		$config['shownew'] = null;
// 		$config['newchar'] = null;
// 		$config['jmambot'] = null;
		$config['disemoticons'] = $result['allow_smilies']->value ^ 1;
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
 		$config['allowsubscriptions'] = $result['o_auto_subscriptions'];
// 		$config['subscriptionschecked'] = null;
// 		$config['allowfavorites'] = null;
// 		$config['wrap'] = null;
// 		$config['maxsubject'] = null;
		$config['maxsig'] = $result['allow_sig']->value ? $result['max_sig_chars']->value : 0;
// 		$config['regonly'] = null;
		$config['changename'] = $result['allow_namechange']->value;
// 		$config['pubwrite'] = null;
		$config['floodprotection'] = $result['flood_interval']->value;
// 		$config['mailmod'] = null;
// 		$config['mailadmin'] = null;
// 		$config['captcha'] = null;
// 		$config['mailfull'] = null;
		$config['allowavatar'] = $result['allow_avatar_upload']->value || $result['allow_avatar_local']->value;
		$config['allowavatarupload'] = $result['allow_avatar_upload']->value;
		$config['allowavatargallery'] = $result['allow_avatar_local']->value;
// 		$config['imageprocessor'] = null;
		$config['avatarsmallheight'] = $result['avatar_max_height']->value > 50 ? 50 : $result['avatar_max_height']->value;
		$config['avatarsmallwidth'] = $result['avatar_max_width']->value > 50 ? 50 : $result['avatar_max_width']->value;
		$config['avatarheight'] = $result['o_avatars_dheight']->value;
		$config['avatarwidth'] = $result['o_avatars_dwidth']->value;
		$config['avatarlargeheight'] = $result['avatar_max_height']->value;
		$config['avatarlargewidth'] = $result['avatar_max_width']->value;
// 		$config['avatarquality'] = null;
		$config['avatarsize'] = (int)($result['avatar_filesize']->value / 1000);
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
		$config['fbsessiontimeout'] = $result['session_length']->value;
// 		$config['highlightcode'] = null;
// 		$config['rsstype'] = null;
// 		$config['rsshistory'] = null;
		$config['fbdefaultpage'] = 'categories';
// 		$config['default_sort'] = null;
		$result = array('1'=>$config);
		return $result;

	}

	function countCategories() {
		$query="SELECT count(*) FROM #__agora_categories";
		$count = $this->getCount($query);
		$query="SELECT count(*) FROM #__agora_forums";
		return $count + $this->getCount($query);
	}

	function &exportCategories($start=0, $limit=0) {
		// Import the categories
		$query="(SELECT cat_name AS name, disp_position AS ordering, enable AS published FROM #__agora_categories) UNION ALL
		(SELECT enable AS published, forum_name AS name, forum_desc AS description, forum_mdesc AS headerdesc, moderators, num_topics AS numTopics, num_posts AS numPosts, last_post_id AS id_last_msg, cat_id AS id, parent_forum_id AS parent FROM #__agora_forums)
		ORDER BY id";
		$result = $this->getExportData($query, $start, $limit);
		foreach ($result as $key=>$item) {
			$row =& $result[$key];
			$row->name = prep($row->name);
			$row->description = prep($row->description);
		}
		return $result;
	}

	function countSmilies() {
		return false;

		$query="SELECT count(*) FROM #__agora_smilies";
		return $this->getCount($query);
	}

	function &exportSmilies($start=0, $limit=0)
	{
		$query="SELECT image AS location,text FROM `#__agora_smilies` ";
		$result = $this->getExportData($query, $start, $limit);
		return $result;
	}

	function countRanks() {
		return false;

		$query="SELECT count(*) FROM #__agora_ranks";
		return $this->getCount($query);
	}

	function &exportRanks($start=0, $limit=0)
	{
		$query="SELECT rank AS rank_title, min_posts AS rank_min, image AS rank_image, user_type AS rank_special FROM `#__agora_ranks` ";
		$result = $this->getExportData($query, $start, $limit);
		return $result;
	}

	function countUsers() {
		$query="SELECT count(*) FROM #__agora_users";
		return $this->getCount($query);
	}

	function &exportUsers($start=0, $limit=0) {
		$query="SELECT url AS websiteurl, icq AS ICQ, msn AS MSN, aim AS AIM, yahoo AS YAHOO, skype AS SKYPE, location, signature, gender, birthday AS birhtdate, aboutme AS personnalText FROM #__agora_users";
		$result = $this->getExportData($query, $start, $limit);
	}

	function countPolls() {
		$query="SELECT count(*) FROM #__agora_polls";
		return $this->getCount($query);
	}

	function &exportPolls($start=0, $limit=0) {
		$query="SELECT options,voters,votes FROM #__agora_polls";
		$result = $this->getExportData($query, $start, $limit);
	}

	function prep($s) {
		return $s;
	}
}