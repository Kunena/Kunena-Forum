<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
defined( '_JEXEC' ) or die();

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

$view = JRequest::getCmd ( 'view' );
$task = JRequest::getCmd ( 'task' );

require_once(KPATH_ADMIN.'/install/version.php');
$kversion = new KunenaVersion();
if ($view != 'install' && !$kversion->checkVersion()) {
	require_once(dirname(__FILE__).'/install/install.script.php');
	Com_KunenaInstallerScript::preflight( null, null );
	Com_KunenaInstallerScript::install ( null );
	$app = JFactory::getApplication ();
	$app->redirect(JURI::root().'administrator/index.php?option=com_kunena&view=install');
}

if ($view) {
	if ($view == 'install') {
		require_once (KPATH_ADMIN . '/install/controller.php');
		$controller = new KunenaControllerInstall();
	} else {
		kimport ('kunena.controller');
		$controller = KunenaController::getInstance();
	}
	$controller->execute( $task );
	$controller->redirect();
	return;
}

// ************************************************************
//							OLD CODE
// ************************************************************

JToolBarHelper::title('&nbsp;', 'kunena.png');

kimport('kunena.error');
kimport('kunena.user.helper');
kimport('kunena.forum.category.helper');
kimport('kunena.forum.topic.helper');
kimport('kunena.forum.message.attachment.helper');
$kunena_app = JFactory::getApplication ();
require_once(KPATH_SITE.'/lib/kunena.defines.php');
$lang = JFactory::getLanguage();
$lang->load('com_kunena',JPATH_SITE);
$lang->load('com_kunena.install');

$kunena_config = KunenaFactory::getConfig ();
$kunena_db = JFactory::getDBO ();

require_once (KPATH_ADMIN . '/admin.kunena.html.php');

$cid = JRequest::getVar ( 'cid', array () );

if (! is_array ( $cid )) {
	$cid = array ();
}
$cid0 = isset($cid [0]) ? $cid [0] : 0;

$uid = JRequest::getVar ( 'uid', array (0 ) );

if (! is_array ( $uid )) {
	$uid = array ($uid );
}

$order = JRequest::getVar ( 'order', '' );

// initialise some request directives (specifically for J1.5 compatibility)
$no_html = intval ( JRequest::getVar ( 'no_html', 0 ) );
$id = intval ( JRequest::getVar ( 'id', 0 ) );

$pt_stop = "0";

if (! $no_html) {
	html_Kunena::showFbHeader ();
}

$option = JRequest::getCmd ( 'option' );

$redirect = JURI::base () . "index.php?option=com_kunena&task=showAdministration";

switch ($task) {
	case "newmoderator" :
		newModerator ( $option, $id );

		break;

	case "addmoderator" :
		addModerator ( $option, $id, $cid, 1 );

		break;

	case "removemoderator" :
		addModerator ( $option, $id, $cid, 0 );

		break;

	case "showprofiles" :
		showProfiles ( $kunena_db, $option, $order );

		break;

	case "profiles" :
		showProfiles ( $kunena_db, $option, $order );

		break;

	case "logout" :
		logout ( $option, $cid );

		break;

	case "deleteuser" :
		deleteUser ( $option, $cid );

		break;

	case "userprofile" :
		editUserProfile ( $option, $cid );

		break;

	case "userblock" :
		userban ( $option, $cid, 1 );

		break;

	case "userunblock" :
		userban ( $option, $cid, 1 );

		break;

	case "userban" :
		userban ($option, $cid, 0 );
		break;

	case "userunban" :
		userban ( $option, $cid, 0 );
		break;

	case "trashusermessages" :
		trashUserMessages ( $option, $cid );

		break;

	case "moveusermessages" :
		moveUserMessages ( $option, $cid );

		break;

	case "moveusermessagesnow" :
		moveUserMessagesNow ( $option, $cid );

		break;

	case "showCss" :
		showCss ( $option );

		break;

	case "saveeditcss" :
		$file = JRequest::getVar ( 'file', 1 );
		$csscontent = JRequest::getVar ( 'csscontent', 1 );

		saveCss ( $file, $csscontent, $option );

		break;

	case "saveuserprofile" :
		saveUserProfile ( $option );

		break;

	case 'cpanel' :
	default :
		html_Kunena::controlPanel ();
		break;
}

$kn_version_warning = $kversion->getVersionWarning('COM_KUNENA_VERSION_INSTALLED');
if (! empty ( $kn_version_warning )) {
	$kunena_app->enqueueMessage ( $kn_version_warning, 'notice' );
}
if (!$kversion->checkVersion()) {
	$kunena_app->enqueueMessage ( sprintf ( JText::_('COM_KUNENA_ERROR_UPGRADE'), KunenaForum::version() ), 'notice' );
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_UPGRADE_WARN') );
	$kunena_app->enqueueMessage ( sprintf ( JText::_('COM_KUNENA_ERROR_UPGRADE_AGAIN'), KunenaForum::version() ) );
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_INCOMPLETE_SUPPORT') . ' <a href="http://www.kunena.org">www.kunena.org</a>' );
}

// Detect errors in CB integration
// TODO: do we need to enable this?
/*
if (is_object ( $kunenaProfile )) {
	$kunenaProfile->enqueueErrors ();
}
*/

html_Kunena::showFbFooter ();


function showCss($option) {
	require_once (KUNENA_PATH_LIB . DS . 'kunena.file.class.php');

	$kunena_config = KunenaFactory::getConfig ();
	$file = KUNENA_PATH_TEMPLATE . DS . $kunena_config->template . DS .'css'. DS . "kunena.forum.css";
	$permission = CKunenaPath::isWritable ( $file );

	if (! $permission) {
		echo "<center><h1><font color=red>" . JText::_('COM_KUNENA_WARNING') . "</font></h1><br />";
		echo "<b>" . JText::_('COM_KUNENA_CFC_FILENAME') . ": " . $file . "</b><br />";
		echo "<b>" . JText::_('COM_KUNENA_CHMOD1') . "</b></center><br /><br />";
	}

	html_Kunena::showCss ( $file, $option );
}

function saveCss($file, $csscontent, $option) {
	require_once (KUNENA_PATH_LIB . DS . 'kunena.file.class.php');

	$kunena_app = & JFactory::getApplication ();
	$tmpstr = JText::_('COM_KUNENA_CSS_SAVE');
	$tmpstr = str_replace ( "%file%", $file, $tmpstr );
	echo $tmpstr;

	if (CKunenaFile::write ( $file, $csscontent )) {
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showCss", JText::_('COM_KUNENA_CFC_SAVED') );
	} else {
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showCss", JText::_('COM_KUNENA_CFC_NOTSAVED') );
	}
}

//===============================
// Moderator Functions
//===============================
function newModerator($option, $id = null) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();
	//die ("New Moderator");
	//$limit = intval(JRequest::getVar( 'limit', 10));
	//$limitstart = intval(JRequest::getVar( 'limitstart', 0));
	$limit = $kunena_app->getUserStateFromRequest ( "global.list.limit", 'limit', $kunena_app->getCfg ( 'list_limit' ), 'int' );
	$limitstart = $kunena_app->getUserStateFromRequest ( "{$option}.limitstart", 'limitstart', 0, 'int' );
	$kunena_db->setQuery ( "SELECT COUNT(*) FROM #__users AS a LEFT JOIN #__kunena_users AS b ON a.id=b.userid WHERE b.moderator=1" );
	$total = $kunena_db->loadResult ();
	if (KunenaError::checkDatabaseError()) return;

	if ($limitstart >= $total)
		$limitstart = 0;
	if ($limit == 0 || $limit > 100)
		$limit = 100;

	$kunena_db->setQuery ( "SELECT * FROM #__users AS a LEFT JOIN #__kunena_users AS b ON a.id=b.userid WHERE b.moderator=1", $limitstart, $limit );
	$userList = $kunena_db->loadObjectList ();
	if (KunenaError::checkDatabaseError()) return;
	$countUL = count ( $userList );

	jimport ( 'joomla.html.pagination' );
	$pageNav = new JPagination ( $total, $limitstart, $limit );
	//$id = intval( JRequest::getVar('id') );
	//get forum name
	$forumName = '';
	$kunena_db->setQuery ( "SELECT name FROM #__kunena_categories WHERE id=$id" );
	$forumName = $kunena_db->loadResult ();
	if (KunenaError::checkDatabaseError()) return;

	//get forum moderators
	$kunena_db->setQuery ( "SELECT userid FROM #__kunena_moderation WHERE catid=$id" );
	$moderatorList = $kunena_db->loadObjectList ();
	if (KunenaError::checkDatabaseError()) return;
	$moderators = 0;
	$modIDs [] = array ();

	if (count ( $moderatorList ) > 0) {
		foreach ( $moderatorList as $ml ) {
			$modIDs [] = $ml->userid;
		}

		$moderators = 1;
	} else {
		$moderators = 0;
	}

	html_Kunena::newModerator ( $option, $id, $moderators, $modIDs, $forumName, $userList, $countUL, $pageNav );
}

function addModerator($option, $id, $cid = null, $publish = 1) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();
	$kunena_my = &JFactory::getUser ();

	$numcid = count ( $cid );
	$action = "";

	if ($publish == 1) {
		$action = 'add';
	} else {
		$action = 'remove';
	}

	if (! is_array ( $cid ) || count ( $cid ) < 1) {
		echo "<script> alert('" . JText::_('COM_KUNENA_SELECTMODTO') . " $action'); window.history.go(-1);</script>\n";
		exit ();
	}

	if ($action == 'add') {
		for($i = 0, $n = count ( $cid ); $i < $n; $i ++) {
			$kunena_db->setQuery ( "INSERT INTO #__kunena_moderation SET catid='$id', userid='$cid[$i]'" );
			$kunena_db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}
	} else {
		for($i = 0, $n = count ( $cid ); $i < $n; $i ++) {
			$kunena_db->setQuery ( "DELETE FROM #__kunena_moderation WHERE catid='$id' AND userid='$cid[$i]'" );
			$kunena_db->query ();
			if (KunenaError::checkDatabaseError()) return;
		}
	}

	$row = new TableKunenaCategories ( $kunena_db );
	$row->checkin ( $id );

	$kunena_db->setQuery ( "UPDATE #__kunena_sessions SET allowed='na'" );
	$kunena_db->query ();
	KunenaError::checkDatabaseError();

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=edit2&uid=" . $id );
}

//===============================
//   User Profile functions
//===============================
function showProfiles($kunena_db, $option, $order) {
	$kunena_app = JFactory::getApplication ();
	$kunena_db = JFactory::getDBO ();

	$filter_order = $kunena_app->getUserStateFromRequest( $option.'user_filter_order', 'filter_order', 'id', 'cmd' );
	$filter_order_Dir = $kunena_app->getUserStateFromRequest( $option.'user_filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
	if ($filter_order_Dir != 'asc') $filter_order_Dir = 'desc';
	$limit = $kunena_app->getUserStateFromRequest ( "global.list.limit", 'limit', $kunena_app->getCfg ( 'list_limit' ), 'int' );
	$limitstart = $kunena_app->getUserStateFromRequest ( "{$option}.user_limitstart", 'limitstart', 0, 'int' );

	$search = $kunena_app->getUserStateFromRequest( $option.'user_search', 'search', '', 'string' );

	$order = '';
	if ($filter_order == 'id') {
		$order = ' ORDER BY u.id '. $filter_order_Dir;
	} else if ($filter_order == 'username') {
		$order = ' ORDER BY u.username '. $filter_order_Dir ;
	} else if ($filter_order == 'name') {
		$order = ' ORDER BY u.name '. $filter_order_Dir ;
	} else if ($filter_order == 'moderator') {
		$order = ' ORDER BY ku.moderator '. $filter_order_Dir ;
	}

	$where = array ();
	if (isset ( $search ) && $search != "") {
		$searchstr = $kunena_db->getEscaped ( JString::trim ( JString::strtolower ( $search ) ) );
		$whereid = '';
		if (intval($searchstr)>0) $whereid = 'OR u.id='.intval($searchstr);
		$where [] = "(u.username LIKE '%$searchstr%' OR u.email LIKE '%$searchstr%' OR u.name LIKE '%$searchstr%' $whereid)";
	}
	$where = count ($where) ? implode ( ' AND ', $where ) : '1';

	$kunena_db->setQuery ( "SELECT COUNT(*) FROM #__kunena_users AS ku
		INNER JOIN #__users AS u ON ku.userid=u.id
		WHERE {$where}");
	$total = $kunena_db->loadResult ();
	KunenaError::checkDatabaseError();

	if ($limitstart >= $total)
		$limitstart = 0;
	if ($limit == 0 || $limit > 100)
		$limit = 100;

	$kunena_db->setQuery ( "SELECT u.id, u.username, u.name, ku.moderator
		FROM #__kunena_users AS ku
		INNER JOIN #__users AS u ON ku.userid=u.id
		WHERE {$where}
		{$order}", $limitstart, $limit );

	$users = $kunena_db->loadObjectList ();
	if (KunenaError::checkDatabaseError()) return;

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	$lists['search']= $search;

	jimport ( 'joomla.html.pagination' );
	$pageNav = new JPagination ( $total, $limitstart, $limit );
	html_Kunena::showProfiles ( $option, $users, $pageNav, $order, $lists );
}

function editUserProfile($option, $uid) {
	if (empty ( $uid [0] )) {
		echo JText::_('COM_KUNENA_PROFILE_NO_USER');
		return;
	}

	$kunena_db = &JFactory::getDBO ();
	$kunena_acl = &JFactory::getACL ();

	$kunena_db->setQuery ( "SELECT * FROM #__kunena_users LEFT JOIN #__users on #__users.id=#__kunena_users.userid WHERE userid=$uid[0]" );
	$userDetails = $kunena_db->loadObjectList ();
	if (KunenaError::checkDatabaseError()) return;
	$user = $userDetails [0];

	//Mambo userids are unique, so we don't worry about that
	$prefview = $user->view;
	$ordering = $user->ordering;
	$moderator = $user->moderator;
	$userRank = $user->rank;

	//grab all special ranks
	$kunena_db->setQuery ( "SELECT * FROM #__kunena_ranks WHERE rank_special = '1'" );
	$specialRanks = $kunena_db->loadObjectList ();
	if (KunenaError::checkDatabaseError()) return;

	//build select list options
	$yesnoRank [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_RANK_NO_ASSIGNED') );
	foreach ( $specialRanks as $ranks ) {
		$yesnoRank [] = JHTML::_ ( 'select.option', $ranks->rank_id, $ranks->rank_title );
	}
	//build special ranks select list
	$selectRank = JHTML::_ ( 'select.genericlist', $yesnoRank, 'newrank', 'class="inputbox" size="5"', 'value', 'text', $userRank );

	// make the select list for the view type
	$yesno [] = JHTML::_ ( 'select.option', 'flat', JText::_('COM_KUNENA_A_FLAT') );
	$yesno [] = JHTML::_ ( 'select.option', 'threaded', JText::_('COM_KUNENA_A_THREADED') );
	// build the html select list
	$selectPref = JHTML::_ ( 'select.genericlist', $yesno, 'newview', 'class="inputbox" size="2"', 'value', 'text', $prefview );
	// make the select list for the moderator flag
	$yesnoMod [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_ANN_YES') );
	$yesnoMod [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_ANN_NO') );
	// build the html select list
	$selectMod = JHTML::_ ( 'select.genericlist', $yesnoMod, 'moderator', 'class="inputbox" size="2"', 'value', 'text', $moderator );
	// make the select list for the moderator flag
	$yesnoOrder [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_USER_ORDER_ASC') );
	$yesnoOrder [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_USER_ORDER_DESC') );
	// build the html select list
	$selectOrder = JHTML::_ ( 'select.genericlist', $yesnoOrder, 'neworder', 'class="inputbox" size="2"', 'value', 'text', $ordering );

	//get all subscriptions for this user
	$kunena_db->setQuery ( "SELECT topic_id AS thread FROM #__kunena_user_topics WHERE user_id=$uid[0] AND subscribed=1" );
	$subslist = $kunena_db->loadObjectList ();
	if (KunenaError::checkDatabaseError()) return;

	//get all categories subscriptions for this user
	$kunena_db->setQuery ( "SELECT category_id FROM #__kunena_user_categories WHERE user_id={$uid[0]}" );
	$subscatslist = $kunena_db->loadObjectList ();
	if (KunenaError::checkDatabaseError()) return;

	//get all moderation category ids for this user
	$kunena_db->setQuery ( "SELECT catid FROM #__kunena_moderation WHERE userid=" . $uid [0] );
	$modCatList = $kunena_db->loadResultArray ();
	if (KunenaError::checkDatabaseError()) return;
	if ($moderator && empty($modCatList)) $modCatList[] = 0;

	$categoryList = array(JHTML::_('select.option', 0, JText::_('COM_KUNENA_GLOBAL_MODERATOR')));
	$params = array (
		'sections' => false,
		'action' => 'read');
	$modCats = JHTML::_('kunenaforum.categorylist', 'catid[]', 0, $categoryList, $params, 'class="inputbox" multiple="multiple"', 'value', 'text', $modCatList, 'kforums');

	//get all IPs used by this user
	$kunena_db->setQuery ( "SELECT ip FROM #__kunena_messages WHERE userid=$uid[0] GROUP BY ip" );
	$iplist = implode("','", $kunena_db->loadResultArray ());
	if (KunenaError::checkDatabaseError()) return;

	$list = array();
	if ($iplist) {
		$iplist = "'{$iplist}'";
		$kunena_db->setQuery ( "SELECT m.ip,m.userid,u.username,COUNT(*) as mescnt FROM #__kunena_messages AS m INNER JOIN #__users AS u ON m.userid=u.id WHERE m.ip IN ({$iplist}) GROUP BY m.userid,m.ip" );
		$list = $kunena_db->loadObjectlist ();
		if (KunenaError::checkDatabaseError()) return;
	}
	$useridslist = array();
	foreach ($list as $item) {
		$useridslist[$item->ip][] = $item;
	}

	html_Kunena::editUserProfile ( $option, $user, $subslist, $subscatslist, $selectRank, $selectPref, $selectMod, $selectOrder, $uid [0], $modCats, $useridslist );
}

function saveUserProfile($option) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();

	if (!JRequest::checkToken()) {
		$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showprofiles" );
	}

	$newview = JRequest::getVar ( 'newview' );
	$newrank = JRequest::getVar ( 'newrank' );
	$signature = JRequest::getVar ( 'message' );
	$deleteSig = JRequest::getVar ( 'deleteSig' );
	$moderator = JRequest::getVar ( 'moderator' );
	$uid = JRequest::getVar ( 'uid' );
	$avatar = JRequest::getVar ( 'avatar' );
	$deleteAvatar = JRequest::getVar ( 'deleteAvatar' );
	$neworder = JRequest::getVar ( 'neworder' );
	$modCatids = JRequest::getVar ( 'catid', array () );

	if ($deleteSig == 1) {
		$signature = "";
	}
	$avatar = '';
	if ($deleteAvatar == 1) {
		$avatar = ",avatar=''";
	}

	$kunena_db->setQuery ( "UPDATE #__kunena_users SET signature={$kunena_db->quote($signature)}, view='$newview',moderator='$moderator', ordering='$neworder', rank='$newrank' $avatar where userid=$uid" );
	$kunena_db->query ();
	if (KunenaError::checkDatabaseError()) return;

	//delete all moderator traces before anyway
	$kunena_db->setQuery ( "DELETE FROM #__kunena_moderation WHERE userid=$uid" );
	$kunena_db->query ();
	if (KunenaError::checkDatabaseError()) return;

	//if there are moderatored forums, add them all
	if ($moderator == 1) {
		if (!empty ( $modCatids ) && !in_array(0, $modCatids)) {
			foreach ( $modCatids as $c ) {
				$kunena_db->setQuery ( "INSERT INTO #__kunena_moderation SET catid='$c', userid='$uid'" );
				$kunena_db->query ();
				if (KunenaError::checkDatabaseError()) return;
			}
		}
	}

	$kunena_db->setQuery ( "UPDATE #__kunena_sessions SET allowed='na' WHERE userid='$uid'" );
	$kunena_db->query ();
	KunenaError::checkDatabaseError();

	$kunena_app->redirect ( JURI::base () . "index.php?option=com_kunena&task=showprofiles" );
}

function trashUserMessages ( $option, $uid ) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_app = & JFactory::getApplication ();

	if (!JRequest::checkToken()) {
		$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=profiles" );
	}

	$path = KUNENA_PATH_LIB.'/kunena.moderation.class.php';
	require_once ($path);
	$kunena_mod = CKunenaModeration::getInstance();

	$uids = implode ( ',', $uid );
	if ($uids) {
		//select only the messages which aren't already in the trash
		$kunena_db->setQuery ( "SELECT id FROM #__kunena_messages WHERE hold!=2 AND userid IN ('$uids')" );
		$idusermessages = $kunena_db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;
		foreach ($idusermessages as $messageID) {
			$kunena_mod->deleteMessage($messageID->id, $DeleteAttachments = false);
		}
	}
	$kunena_app->redirect ( JURI::base () . "index.php?option=com_kunena&task=profiles" , JText::_('COM_KUNENA_A_USERMES_TRASHED_DONE'));
}

function moveUserMessages ( $option, $uid ){
	$kunena_db = &JFactory::getDBO ();
	$return = JRequest::getCmd( 'return', 'edituserprofile', 'post' );

	$userid = implode(',', $uid);
	$kunena_db->setQuery ( "SELECT id,username FROM #__users WHERE id IN(".$userid.")" );
	$userids = $kunena_db->loadObjectList ();

	$kunena_db->setQuery ( "SELECT id,parent,name FROM #__kunena_categories" );
	$catsList = $kunena_db->loadObjectList ();
	if (KunenaError::checkDatabaseError()) return;

	foreach ($catsList as $cat) {
		if ($cat->parent) {
			$category[] = JHTML::_('select.option', $cat->id, '...'.$cat->name);
		} else {
			$category[] = JHTML::_('select.option', $cat->id, $cat->name);
		}
	}
	$lists = JHTML::_('select.genericlist', $category, 'cid[]', 'class="inputbox" multiple="multiple" size="5"', 'value', 'text');

	html_Kunena::moveUserMessages ( $option, $return, $uid, $lists, $userids );
}

function moveUserMessagesNow ( $option, $cid ) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_app = & JFactory::getApplication ();

	if (!JRequest::checkToken()) {
		$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=profiles" );
	}

	$path = KUNENA_PATH_LIB  .'/kunena.moderation.class.php';
	require_once ($path);
	$kunena_mod = CKunenaModeration::getInstance();

	$uid = JRequest::getVar( 'uid', '', 'post' );
	if ($uid) {
		$kunena_db->setQuery ( "SELECT id,thread FROM #__kunena_messages WHERE hold=0 AND userid IN ('$uid')" );
		$idusermessages = $kunena_db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;
		if ( !empty($idusermessages) ) {
			foreach ($idusermessages as $id) {
				$kunena_mod->moveMessage($id->id, $cid[0], $TargetSubject = '', $TargetMessageID = 0);
			}
		}
	}
	$kunena_app->redirect ( JURI::base () . "index.php?option=com_kunena&task=profiles", JText::_('COM_A_KUNENA_USERMES_MOVED_DONE') );
}

function logout ( $option, $userid ) {
	$app = JFactory::getApplication ();
	if (!JRequest::checkToken()) {
		$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
		$app->redirect ( JURI::base () . "index.php?option=$option&task=showprofiles" );
	}
	$options = array();
	$options['clientid'][] = 0; // site
	$app->logout( (int) $userid[0], $options);

	$app->redirect ( JURI::base () . "index.php?option=com_kunena&task=profiles", JText::_('COM_A_KUNENA_USER_LOGOUT_DONE') );
}

function deleteUser ( $option, $uid ) {
	$kunena_app = & JFactory::getApplication ();
	if (!JRequest::checkToken()) {
		$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showprofiles" );
	}
	$path = KUNENA_PATH_LIB  .'/kunena.moderation.tools.class.php';
	require_once ($path);
	$user_mod = new CKunenaModerationTools();

	foreach ($uid as $id) {
		$deleteuser = $user_mod->deleteUser($id);
		if (!$deleteuser) {
			$message = $user_mod->getErrorMessage();
		} else {
			$message = JText::_('COM_A_KUNENA_USER_DELETE_DONE');
		}
	}

	$kunena_app->redirect ( JURI::base () . "index.php?option=com_kunena&task=profiles", $message );
}

function userban($option, $userid, $block = 0) {
	$kunena_app = & JFactory::getApplication ();
	if (!JRequest::checkToken()) {
		$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showprofiles" );
	}

	kimport ( 'kunena.user.ban' );
	$userid = array_shift($userid);
	$ban = KunenaUserBan::getInstanceByUserid ( $userid, true );
	if (! $ban->id) {
		$ban->ban ( $userid, null, $block );
		$success = $ban->save ();
	} else {
		jimport ('joomla.utilities.date');
		$now = new JDate();
		$ban->setExpiration ( $now );
		$success = $ban->save ();
	}

	if ($block) {
		if ($ban->isEnabled ())
			$message = JText::_ ( 'COM_KUNENA_USER_BLOCKED_DONE' );
		else
			$message = JText::_ ( 'COM_KUNENA_USER_UNBLOCK_DONE' );
	} else {
		if ($ban->isEnabled ())
			$message = JText::_ ( 'COM_KUNENA_USER_BANNED_DONE' );
		else
			$message = JText::_ ( 'COM_KUNENA_USER_UNBAN_DONE' );
	}

	$kunena_app = JFactory::getApplication ();
	if (! $success) {
		$kunena_app->enqueueMessage ( $ban->getError (), 'error' );
	} else {
		$kunena_app->enqueueMessage ( $message );
	}
	$kunena_app->redirect ( JURI::base () . "index.php?option=com_kunena&task=profiles" );
}
//===============================
//  Get latest kunena version
//===============================

/*
 * Code originally taken from AlphaUserPoints
 * copyright Copyright (C) 2008-2010 Bernard Gilly
 * license : GNU/GPL
 * Website : http://www.alphaplug.com
 */
function getLatestKunenaVersion() {
	$kunena_app = & JFactory::getApplication ();

	$url = 'http://update.kunena.org/kunena_update.xml';
	$data = '';
	$check = array();
	$check['connect'] = 0;

	$data = $kunena_app->getUserState('com_kunena.version_check', null);
	if ( empty($data) ) {
		//try to connect via cURL
		if(function_exists('curl_init') && function_exists('curl_exec')) {
			$ch = @curl_init();

			@curl_setopt($ch, CURLOPT_URL, $url);
			@curl_setopt($ch, CURLOPT_HEADER, 0);
			//http code is greater than or equal to 300 ->fail
			@curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//timeout of 5s just in case
			@curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$data = @curl_exec($ch);
			@curl_close($ch);
		}

		//try to connect via fsockopen
		if(function_exists('fsockopen') && $data == '') {

			$errno = 0;
			$errstr = '';

			//timeout handling: 5s for the socket and 5s for the stream = 10s
			$fsock = @fsockopen("update.kunena.org", 80, $errno, $errstr, 5);

			if ($fsock) {
				@fputs($fsock, "GET /kunena_update.xml HTTP/1.1\r\n");
				@fputs($fsock, "HOST: update.kunena.org\r\n");
				@fputs($fsock, "Connection: close\r\n\r\n");

				//force stream timeout...
				@stream_set_blocking($fsock, 1);
				@stream_set_timeout($fsock, 5);

				$get_info = false;
				while (!@feof($fsock)) {
					if ($get_info) {
						$data .= @fread($fsock, 1024);
					} else {
						if (@fgets($fsock, 1024) == "\r\n") {
							$get_info = true;
						}
					}
				}
				@fclose($fsock);

				//need to check data cause http error codes aren't supported here
				if(!strstr($data, '<?xml version="1.0" encoding="utf-8"?><update>')) {
					$data = '';
				}
			}
		}

		//try to connect via fopen
		if (function_exists('fopen') && ini_get('allow_url_fopen') && $data == '') {

			//set socket timeout
			ini_set('default_socket_timeout', 5);

			$handle = @fopen ($url, 'r');

			//set stream timeout
			@stream_set_blocking($handle, 1);
			@stream_set_timeout($handle, 5);

			$data	= @fread($handle, 1000);

			@fclose($handle);
		}

		$kunena_app->setUserState('com_kunena.version_check',$data);

	}

	if( !empty($data) && strstr($data, '<?xml version="1.0" encoding="utf-8"?>') ) {
		$xml = & JFactory::getXMLparser('Simple');
		$xml->loadString($data);
		$version 				= & $xml->document->version[0];
		$check['latest_version'] = & $version->data();
		$released 				= & $xml->document->released[0];
		$check['released'] 		= & $released->data();
		$check['connect'] 		= 1;
		$check['enabled'] 		= 1;
	}

	return $check;
}

function checkLatestVersion() {
	$latestVersion = getLatestKunenaVersion();

	if ( $latestVersion['connect'] ) {
		if ( version_compare($latestVersion['latest_version'], KunenaForum::version(), '<=') ) {
			$needUpgrade = JText::sprintf('COM_KUNENA_COM_A_CHECK_VERSION_CORRECT', KunenaForum::version());
		} else {
			$needUpgrade = JText::sprintf('COM_KUNENA_COM_A_CHECK_VERSION_NEED_UPGRADE',$latestVersion['latest_version'],$latestVersion['released']);
		}
	} else {
		$needUpgrade = JText::_('COM_KUNENA_COM_A_CHECK_VERSION_CANNOT_CONNECT');
	}


	return $needUpgrade;
}

// Grabs gd version


function KUNENA_gdVersion() {
	// Simplified GD Version check
	if (! extension_loaded ( 'gd' )) {
		return;
	}

	if (function_exists ( 'gd_info' )) {
		$ver_info = gd_info ();
		preg_match ( '/\d/', $ver_info ['GD Version'], $match );
		$gd_ver = $match [0];
		return $match [0];
	} else {
		return;
	}
}

function kescape($string) {
	return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
}