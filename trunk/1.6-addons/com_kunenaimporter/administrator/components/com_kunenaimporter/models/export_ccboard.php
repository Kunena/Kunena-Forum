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

class KunenaimporterModelExport_ccBoard extends KunenaimporterModelExport {
	var $version;
	var $pnversion;

	function checkConfig() {
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport ( 'joomla.filesystem.file' );

		parent::checkConfig ();
		if (JError::isError ( $this->ext_database ))
			return;

		// the ccboard config is only stored in ccboard.xml file
		$xml = JPATH_ADMINISTRATOR . '/components/com_ccboard/ccboard.xml';

		if (JFile::exists ( $xml )) {
			$parser = & JFactory::getXMLParser ( 'Simple' );
			$parser->loadFile ( $xml );
			$doc = & $parser->document;
			$element = & $doc->getElementByPath ( 'version' );
			$this->version = $element->data ();
		} else {
			$this->addMessage ( '<div>Can not check the ccbaord version, the file ccboard.xml has been removed</b></div>' );
		}

		$version = substr ( $this->version, 0, 3 );
		if ($version != 1.2)
			$this->error = "Unsupported forum: ccBoard " . $this->version;
		if ($this->error) {
			$this->addMessage ( '<div>ccBoard version: <b style="color:red">' . $this->version . '</b></div>' );
			$this->addMessage ( '<div><b>Error:</b> ' . $this->error . '</div>' );
			return false;
		}
		$this->addMessage ( '<div>ccBoard version: <b style="color:green">' . $this->version . '</b></div>' );
	}

	function buildImportOps() {
		// query: (select, from, where, groupby), functions: (count, export)
		$importOps = array ();
		$importOps ['categories'] = array ('count' => 'countCategories', 'export' => 'exportCategories' );
		$importOps ['config'] = array ('count' => 'countConfig', 'export' => 'exportConfig' );
		$importOps ['attachments'] = array ('count' => 'countAttachments', 'export' => 'exportAttachments' );
		$importOps ['moderation'] = array ('count' => 'countModeration', 'export' => 'exportModeration' );
		$importOps ['messages'] = array ('count' => 'countMessages', 'export' => 'exportMessages' );
		$importOps ['ranks'] = array ('count' => 'countRanks', 'export' => 'exportRanks' );
		$importOps ['userprofile'] = array ('count' => 'countUserprofile', 'export' => 'exportUserprofile' );
		$this->importOps = & $importOps;
	}

	function countCategories() {
		$query = "SELECT count(*) FROM #__ccb_category";
		$count = $this->getCount ( $query );
		$query = "SELECT count(*) FROM #__ccb_forums";
		$count2 = $this->getCount ( $query );
		return $count + $count2;
	}

	function &exportCategories($start = 0, $limit = 0) {
		// Import the categories
		$query = "SELECT cccategory.cat_name AS name, cccategory.ordering, ccforums.forum_name AS name, ccforums.forum_desc AS description, ccforums.cat_id AS parent, ccforums.topic_count AS numTopics, ccforums.post_count AS numPosts, ccforums.last_post_user, ccforums.last_post_time AS time_last_msg, ccforums.last_post_id AS id_last_msg, ccforums.published, ccforums.locked, ccforums.ordering, ccforums.moderated, ccforums.review FROM #__ccb_category AS cccategory LEFT JOIN #__ccb_forums AS ccforums ON cccategory.id=ccforums.cat_id";
		$result = $this->getExportData ( $query, $start, $limit );
		foreach ( $result as $key => $item ) {
			$row = & $result [$key];
			$row->name = $this->prep ( $row->name );
			if (! $row->parent) {
				$row->parent = 0;
				$row->pub_access = 0;
				$row->published = 1;
			} else {
				$row->description = $this->prep ( $row->description );
			}
		}
		return $result;
	}

	function countConfig() {
		return 1;
	}

	function &exportConfig($start = 0, $limit = 0) {
		require_once (JPATH_ADMINISTRATOR . '/components/com_ccboard/ccboard-config.php');

		$ccBoardConfig = new ccboardConfig ();

		$config ['id'] = 1;
		$config ['board_title'] = $ccBoardConfig->boardname;
		$config ['board_offline'] = $ccBoardConfig->boardlocked;
		$config ['board_ofset'] = $ccBoardConfig->timeoffset;
		$config ['offline_message'] = $ccBoardConfig->lockedmsg;
		// 		$config['default_view'] = null;
		// 		$config['enablerss'] = null;
		// 		$config['enablepdf'] = null;
		//		$config['threads_per_page'] = null;
		//		$config['messages_per_page'] = null;
		// 		$config['messages_per_page_search'] = null;
		// 		$config['showhistory'] = null;
		// 		$config['historylimit'] = null;
		// 		$config['shownew'] = null;
		// 		$config['newchar'] = null;
		// 		$config['jmambot'] = null;
		//		$config['disemoticons'] = null;
		// 		$config['template'] = null;
		// 		$config['templateimagepath'] = null;
		// 		$config['joomlastyle'] = null;
		// 		$config['showannouncement'] = null;
		// 		$config['avataroncat'] = null;
		// 		$config['catimagepath'] = null;
		// 		$config['numchildcolumn'] = null;
		// 		$config['showchildcaticon'] = null;
		// 		$config['annmodid'] = null;
		$config ['rtewidth'] = $ccBoardConfig->editorwidth;
		$config ['rteheight'] = $ccBoardConfig->editorheight;
		// 		$config['enablerulespage'] = null;
		// 		$config['enableforumjump'] = null;
		// 		$config['reportmsg'] = null;
		$config ['username'] = $ccBoardConfig->showrealname;
		// 		$config['askemail'] = null;
		// 		$config['showemail'] = null;
		// 		$config['showuserstats'] = null;
		// 		$config['poststats'] = null;
		// 		$config['statscolor'] = null;
		$config ['showkarma'] = $ccBoardConfig->showkarma;
		// 		$config['useredit'] = null;
		// 		$config['useredittime'] = null;
		$config ['useredittimegrace'] = $ccBoardConfig->editgracetime;
		$config ['editmarkup'] = $ccBoardConfig->showeditmarkup;
		$config ['allowsubscriptions'] = $ccBoardConfig->emailsub;
		// 		$config['subscriptionschecked'] = null;
		$config ['allowfavorites'] = $ccBoardConfig->showfavourites;
		// 		$config['wrap'] = null;
		$config ['maxsubject'] = $ccBoardConfig->subjwidth;
		$config ['maxsig'] = $ccBoardConfig->sigmax;
		// 		$config['regonly'] = null;
		//		$config['changename'] = null;
		// 		$config['pubwrite'] = null;
		//		$config['floodprotection'] = null;
		// 		$config['mailmod'] = null;
		// 		$config['mailadmin'] = null;
		$config ['captcha'] = $ccBoardConfig->showcaptcha == 1 || $ccBoardConfig->showcaptcha == 2 ? $config ['captcha'] = 1 : $config ['captcha'] = 0;
		// 		$config['mailfull'] = null;
		//		$config['allowavatar'] = null;
		$config ['allowavatarupload'] = $ccBoardConfig->avatarupload;
		//		$config['allowavatargallery'] = null;
		// 		$config['imageprocessor'] = null;
		$config ['avatarsmallheight'] = $ccBoardConfig->smallavatarheight;
		$config ['avatarsmallwidth'] = $ccBoardConfig->smallavatarwidth;
		$config ['avatarheight'] = $ccBoardConfig->avatarheight;
		$config ['avatarwidth'] = $ccBoardConfig->avatarwidth;
		//		$config['avatarlargeheight'] = null;
		//		$config['avatarlargewidth'] = null;
		// 		$config['avatarquality'] = null;
		$config ['avatarsize'] = $ccBoardConfig->avataruploadsize;
		// 		$config['allowimageupload'] = null;
		// 		$config['allowimageregupload'] = null;
		// 		$config['imageheight'] = null;
		// 		$config['imagewidth'] = null;
		$config ['imagesize'] = $ccBoardConfig->fileuploadsize;
		// 		$config['allowfileupload'] = null;
		// 		$config['allowfileregupload'] = null;
		// 		$config['filetypes'] = null;
		$config ['filesize'] = $ccBoardConfig->fileuploadsize;
		$config ['showranking'] = $ccBoardConfig->showrank;
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
		//		$config['fbsessiontimeout'] = null;
		// 		$config['highlightcode'] = null;
		// 		$config['rsstype'] = null;
		// 		$config['rsshistory'] = null;
		//		$config['fbdefaultpage'] = null;
		$config ['default_sort'] = $ccBoardConfig->postlistorder;
		$result = array ('1' => $config );
		return $result;
	}

	function countAttachments() {
		$query = "SELECT count(*) FROM #__ccb_attachments";
		$count = $this->getCount ( $query );
		return $count;
	}

	function &exportAttachments($start = 0, $limit = 0) {
		$query = "SELECT post_id AS mesid, ccb_name AS userid, filesize AS size, real_name AS filename, mimetype AS filetype FROM #__ccb_attachments";
		$result = $this->getExportData ( $query, $start, $limit );
		foreach ( $result as $key => $item ) {
			$row = & $result [$key];
			$row->userid = substr ( $row->userid, 0, 2 );
		}
		return $result;
	}

	function countModeration() {
		$query = "SELECT count(*) FROM #__ccb_moderators";
		$count = $this->getCount ( $query );
		return $count;
	}

	function &exportModeration($start = 0, $limit = 0) {
		$query = "SELECT user_id AS userid, forum_id AS catid FROM #__ccb_moderators";
		$result = $this->getExportData ( $query, $start, $limit );

		return $result;
	}

	function countRanks() {
		$query = "SELECT count(*) FROM #__ccb_ranks";
		$count = $this->getCount ( $query );
		return $count;
	}

	function &exportRanks($start = 0, $limit = 0) {
		$query = "SELECT rank_title,rank_min,rank_special,rank_image FROM #__ccb_ranks";
		$result = $this->getExportData ( $query, $start, $limit );
		foreach ( $result as $rank ) {
		  if ( JFile::exists(JPATH_BASE . 'components/com_ccboard/assets/ranks/' . $rank->rank_image) ) {
        JFile::copy ( JPATH_BASE . 'components/com_ccboard/assets/ranks/' . $rank->rank_image, JPATH_BASE . 'components/com_kunena/template/default/images/ranks' );
      }			
		}

		return $result;
	}

	function countMessages() {
		$query = "SELECT count(*) FROM #__ccb_posts";
		$count = $this->getCount ( $query );
		return $count;
	}

	function &exportMessages($start = 0, $limit = 0) {
		$query = "SELECT ccposts.id, ccposts.topic_id AS thread, ccposts.forum_id AS catid, ccposts.post_subject AS subject, ccposts.post_text AS message, ccposts.post_user AS userid, ccposts.post_time AS time, ccposts.ip, ccposts.hold, ccposts.modified_by, ccposts.modified_time, ccposts.modified_reason, ccposts.post_username AS name, cctopics.id,cctopics.forum_id,cctopics.post_subject,cctopics.reply_count,cctopics.hits,cctopics.post_time,cctopics.post_user,cctopics.last_post_time,cctopics.last_post_id,cctopics.last_post_user,cctopics.start_post_id,cctopics.topic_type,cctopics.locked,cctopics.topic_email,cctopics.hold,cctopics.topic_emoticon,cctopics.post_username,cctopics.last_post_username,cctopics.topic_favourite FROM #__ccb_posts AS ccposts
		 LEFT JOIN #__ccb_topics AS cctopics ON ccposts.topic_id=cctopics.id";
		$result = $this->getExportData ( $query, $start, $limit );

		return $result;
	}

	function countUserprofile() {
		$query = "SELECT count(*) FROM #__ccb_users";
		$count = $this->getCount ( $query );
		return $count;
	}

	function &exportUserprofile($start = 0, $limit = 0) {
		$query = "SELECT user_id AS userid,location,signature,avatar,rank,post_count AS posts,gender,www,icq AS ICQ,aol AS AOL,msn AS MSN,yahoo AS YAHOO,jabber AS GTALK,skype AS SKYPE,showemail AS hideEmail,moderator,karma,karma_time,hits AS uhits FROM #__ccb_users";
		$result = $this->getExportData ( $query, $start, $limit );
		foreach ( $result as $key => $item ) {
			$row = & $result [$key];
			$row->signature = $this->prep ( $row->signature );
			$row->gender = $row->gender == 'Male' ? '1' : '2';
		}
		return $result;
	}

	function prep($s) {
		return $s;
	}

}