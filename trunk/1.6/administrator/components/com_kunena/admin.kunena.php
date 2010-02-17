<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

defined( '_JEXEC' ) or die();

// Kunena wide defines
require_once (JPATH_ROOT . DS . 'components' . DS . 'com_kunena' . DS . 'lib' . DS . 'kunena.defines.php');

$lang = JFactory::getLanguage();
$lang->load('com_kunena', KUNENA_PATH);
$lang->load('com_kunena', KUNENA_PATH_ADMIN);

global $kunenaProfile;

$task = JRequest::getCmd ( 'task' );

if ($task == 'install') {
	require_once (KUNENA_PATH_ADMIN_INSTALL . DS . 'kunena.install.php');
	com_install ();
	return;
}

// Now that we have the global defines we can use shortcut defines
require_once (KUNENA_PATH_LIB . DS . 'kunena.debug.php');
require_once (KUNENA_PATH_LIB . DS . 'kunena.config.class.php');
require_once (KUNENA_PATH_LIB . DS . 'kunena.version.php');

$kunena_app = & JFactory::getApplication ();
$kunena_config = & CKunenaConfig::getInstance ();
$kunena_db = JFactory::getDBO ();

//$kunena_config =& CKunenaConfig::getInstance();
//$kunena_config->load();


// Class structure should be used after this and all the common task should be moved to this class
require_once (KUNENA_PATH . DS . 'class.kunena.php');
require_once (KUNENA_PATH_ADMIN . DS . 'admin.kunena.html.php');

$kn_tables = CKunenaTables::getInstance ();
if ($kn_tables->installed () === false) {
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_INCOMPLETE_ERROR'), 'error' );
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_INCOMPLETE_OFFLINE'), 'notice' );
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_INCOMPLETE_REASONS') );
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_INCOMPLETE_1') );
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_INCOMPLETE_2') );
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_INCOMPLETE_3') );
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_INCOMPLETE_SUPPORT') . ' <a href="http://www.kunena.com">www.kunena.com</a>' );
	html_Kunena::showFbFooter ();
	return;
}

$cid = JRequest::getVar ( 'cid', array (0 ) );

if (! is_array ( $cid )) {
	$cid = array (0 );
}

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

switch ($task) {
	case "new" :
		editForum ( 0, $option );

		break;

	case "edit" :
		editForum ( $cid [0], $option );

		break;

	case "edit2" :
		editForum ( $uid [0], $option );

		break;

	case "save" :
		saveForum ( $option );

		break;

	case "cancel" :
		cancelForum ( $option );

		break;

	case "publish" :
		publishForum ( $cid, 1, $option );

		break;

	case "unpublish" :
		publishForum ( $cid, 0, $option );

		break;

	case "remove" :
		deleteForum ( $cid, $option );

		break;

	case "orderup" :
		orderForum ( $cid [0], - 1, $option );

		break;

	case "orderdown" :
		orderForum ( $cid [0], 1, $option );

		break;

	case "showconfig" :
		showConfig ( $option );

		break;

	case "saveconfig" :
		saveConfig ( $option );

		break;

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

	case "userprofile" :
		editUserProfile ( $option, $uid );

		break;

	case "showinstructions" :
		showInstructions ( $kunena_db, $option );

		break;

	case "showCss" :
		showCss ( $option );

		break;

	case "saveeditcss" :
		$file = JRequest::getVar ( 'file', 1 );
		$csscontent = JRequest::getVar ( 'csscontent', 1 );

		saveCss ( $file, $csscontent, $option );

		break;

	case "instructions" :
		showInstructions ( $kunena_db, $option );

		break;

	case "saveuserprofile" :
		saveUserProfile ( $option );

		break;

	case "pruneforum" :
		pruneforum ( $kunena_db, $option );

		break;

	case "doprune" :
		doprune ( $kunena_db, $option );

		break;

	case "douserssync" :
		douserssync ( $kunena_db, $option );

		break;

	case "syncusers" :
		syncusers ( $kunena_db, $option );

		break;

	case "browseImages" :
		browseUploaded ( $kunena_db, $option, 1 );

		break;

	case "browseFiles" :
		browseUploaded ( $kunena_db, $option, 0 );

		break;

	case "replaceImage" :
		replaceImage ( $kunena_db, $option, JRequest::getVar ( 'img', '' ), JRequest::getInt ( 'OxP', 1 ) );

		break;

	case "deleteFile" :
		deleteFile ( $kunena_db, $option, JRequest::getVar ( 'fileName', '' ) );

		break;

	case "showAdministration" :
		showAdministration ( $option );

		break;

	case 'recount' :
		CKunenaTools::reCountUserPosts ();
		CKunenaTools::reCountBoards ();
		// Also reset the name info stored with messages
		//CKunenaTools::updateNameInfo();
		$kunena_app->redirect ( JURI::base () . 'index.php?option=com_kunena', JText::_('COM_KUNENA_RECOUNTFORUMS_DONE') );
		break;

	case "showsmilies" :
		showsmilies ( $option );

		break;

	case "editsmiley" :
		editsmiley ( $option, $cid [0] );

		break;

	case "savesmiley" :
		savesmiley ( $option, $id );

		break;

	case "deletesmiley" :
		deletesmiley ( $option, $cid );

		break;

	case "newsmiley" :
		newsmiley ( $option );

		break;

	case 'ranks' :
		showRanks ( $option );

		break;

	case "editRank" :
		editRank ( $option, $cid [0] );

		break;

	case "saveRank" :
		saveRank ( $option, $id );

		break;

	case "deleteRank" :
		deleteRank ( $option, $cid );

		break;

	case "newRank" :
		newRank ( $option );

		break;

	case "showtrashview" :
		showtrashview ( $option );

		break;

	case "showsystemreport" :
		showSystemReport ( $option );

		break;

	case "trashpurge" :
		trashpurge ( $option, $cid );

		break;

	case "deleteitemsnow" :
		deleteitemsnow ( $option, $cid );

		break;

	case "trashrestore" :
		trashrestore ( $option, $cid );

		break;

	case "pollpublish" :
		pollpublish ( $option, $cid, 1 );

		break;

	case "pollunpublish" :
		pollunpublish ( $option, $cid, 0 );

		break;

	case "createmenu" :
		CKunenaTools::createMenu();

		$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_MENU_CREATED') );
		// No break! Need to display the control panel
	case 'cpanel' :
	default :
		html_Kunena::controlPanel ();
		break;
}

$kn_version = CKunenaVersion::versionArray ();
if (JString::strpos ( $kn_version->version, 'SVN')) {
	$kn_version_name = JText::_('COM_KUNENA_VERSION_SVN');
	$kn_version_warning = JText::_('COM_KUNENA_VERSION_SVN_WARNING');
} else if (JString::strpos ( $kn_version->version, 'RC' ) !== false) {
	$kn_version_name = JText::_('COM_KUNENA_VERSION_RC');
	$kn_version_warning = JText::_('COM_KUNENA_VERSION_RC_WARNING');
} else if (JString::strpos ( $kn_version->version, 'BETA' ) !== false) {
	$kn_version_name = JText::_('COM_KUNENA_VERSION_BETA');
	$kn_version_warning = JText::_('COM_KUNENA_VERSION_BETA_WARNING');
} else if (JString::strpos ( $kn_version->version, 'ALPHA' ) !== false) {
	$kn_version_name = JText::_('COM_KUNENA_VERSION_ALPHA');
	$kn_version_warning = JText::_('COM_KUNENA_VERSION_ALPHA_WARNING');
} else if (JString::strpos ( $kn_version->version, 'DEV' ) !== false) {
	$kn_version_name = JText::_('COM_KUNENA_VERSION_DEV');
	$kn_version_warning = JText::_('COM_KUNENA_VERSION_DEV_WARNING');
} else if (JString::strpos ( $kn_version->version, 'SVN' ) !== false) {
	$kn_version_name = JText::_('COM_KUNENA_VERSION_DEV');
	$kn_version_warning = JText::_('COM_KUNENA_VERSION_DEV_WARNING');
}
if (! empty ( $kn_version_warning )) {
	$kunena_app->enqueueMessage ( sprintf ( JText::_('COM_KUNENA_VERSION_INSTALLED'), $kn_version->version, $kn_version_name ) . ' ' . $kn_version_warning, 'notice' );
}
if ($kn_version->versionname == 'NOT UPGRADED') {
	$kunena_app->enqueueMessage ( sprintf ( JText::_('COM_KUNENA_ERROR_UPGRADE'), $kn_version->version ), 'notice' );
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_UPGRADE_WARN') );
	$kunena_app->enqueueMessage ( sprintf ( JText::_('COM_KUNENA_ERROR_UPGRADE_AGAIN'), $kn_version->version ) );
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_INCOMPLETE_SUPPORT') . ' <a href="http://www.kunena.com">www.kunena.com</a>' );
}

// Detect errors in CB integration
if (is_object ( $kunenaProfile )) {
	$kunenaProfile->enqueueErrors ();
	//$kunenaProfile->close();
}

html_Kunena::showFbFooter ();

function showAdministration($option) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();

	$limit = $kunena_app->getUserStateFromRequest ( "global.list.limit", 'limit', $kunena_app->getCfg ( 'list_limit' ), 'int' );
	$limitstart = $kunena_app->getUserStateFromRequest ( "{$option}.limitstart", 'limitstart', 0, 'int' );
	$levellimit = $kunena_app->getUserStateFromRequest ( "{$option}.limit", 'levellimit', 10, 'int' );

	$kunena_db->setQuery ( "SELECT a.*, a.name AS category, u.name AS editor, g.name AS groupname, h.name AS admingroup
		FROM #__fb_categories AS a
		LEFT JOIN #__users AS u ON u.id = a.checked_out
		LEFT JOIN #__core_acl_aro_groups AS g ON g.id = a.pub_access
		LEFT JOIN #__core_acl_aro_groups AS h ON h.id = a.admin_access
		ORDER BY a.ordering, a.name" );

	$rows = $kunena_db->loadObjectList ('id');
	check_dberror ( "Unable to load categories." );

	// establish the hierarchy of the categories
	$children = array ();

	// first pass - collect children
	foreach ( $rows as $v ) {
		$list = array();
		$vv = $v;
		while ($vv->parent>0 && isset($rows[$vv->parent]) && !in_array($vv->parent, $list)) {
			$list[] = $vv->id;
			$vv = $rows[$vv->parent];
		}
		if ($vv->parent) {
			$v->parent = -1;
			$v->published = 0;
			$v->name = JText::_('COM_KUNENA_CATEGORY_ORPHAN').' : '.$v->name;
		}
		$children [$v->parent][] = $v;
		$v->location = count ( $children [$v->parent] )-1;
	}

	if (isset($children [-1])) {
		$children [0] = array_merge($children [-1], $children [0]);
		$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_CATEGORY_ORPHAN_DESC'), 'notice' );
	}

	// second pass - get an indent list of the items
	$list = fbTreeRecurse ( 0, '', array (), $children, max ( 0, $levellimit - 1 ) );
	$total = count ( $list );
	if ($limitstart >= $total)
		$limitstart = 0;

	jimport ( 'joomla.html.pagination' );
	$pageNav = new JPagination ( $total, $limitstart, $limit );

	$levellist = JHTML::_ ( 'select.integerList', 1, 20, 1, 'levellimit', 'size="1" onchange="document.adminForm.submit();"', $levellimit );
	// slice out elements based on limits
	$list = array_slice ( $list, $pageNav->limitstart, $pageNav->limit );
	/**
	 *@end
	 */

	html_Kunena::showAdministration ( $list, $children, $pageNav, $option );
}

//---------------------------------------
//-E D I T   F O R U M-------------------
//---------------------------------------
function editForum($uid, $option) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_acl = &JFactory::getACL ();
	$kunena_my = &JFactory::getUser ();
	$row = new fbForum ( $kunena_db );
	// load the row from the db table
	$row->load ( $uid );

	if ($uid) {
		$row->checkout ( $kunena_my->id );
		$categories = array ();
	} else {
		// initialise new record
		$categories [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_TOPLEVEL'), 'value', 'text' );
		$row->parent = 0;
		$row->published = 0;
		$row->ordering = 9999;
	}

	// get a list of just the categories
	$kunena_db->setQuery ( "SELECT a.id AS value, a.name AS text FROM #__fb_categories AS a WHERE parent='0' AND id<>'$row->id' ORDER BY ordering" );
	$categories = array_merge ( $categories, $kunena_db->loadObjectList () );
	check_dberror ( "Unable to load categories." );

	if ($row->parent == 0) {
		//make sure the Top Level Category is available in edit mode as well:
		$kunena_db->setQuery ( "SELECT distinct '0' AS value, '" . JText::_('COM_KUNENA_TOPLEVEL') . "' AS text FROM #__fb_categories AS a WHERE parent='0' AND id<>'$row->id' ORDER BY ordering" );
		$categories = array_merge ( $categories, ( array ) $kunena_db->loadObjectList () );
		check_dberror ( "Unable to load categories." );

		//build the select list:
		$categoryList = JHTML::_ ( 'select.genericlist', $categories, 'parent', 'class="inputbox" size="1"', 'value', 'text', $row->parent );
	} else {
		$categoryList = JHTML::_ ( 'select.genericlist', $categories, 'parent', 'class="inputbox" size="1"', 'value', 'text', $row->parent );
	}

	$categoryList = showCategories ( $row->parent, "parent", "", "4" );
	// make a standard yes/no list
	$yesno = array ();
	$yesno [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_ANN_NO') );
	$yesno [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_ANN_YES') );

	// make a standard no/yes list
	$noyes = array ();
	$noyes [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_ANN_YES') );
	$noyes [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_ANN_NO') );
	//Create all kinds of Lists
	$lists = array ();
	$accessLists = array ();
	//create custom group levels to include into the public group selectList
	$pub_groups = array ();
	$pub_groups [] = JHTML::_ ( 'select.option', 0, JText::_('COM_KUNENA_EVERYBODY') );
	$pub_groups [] = JHTML::_ ( 'select.option', - 1, JText::_('COM_KUNENA_ALLREGISTERED') );

	$pub_groups = array_merge ( $pub_groups, $kunena_acl->get_group_children_tree ( null, JText::_('COM_KUNENA_REGISTERED'), true ) );
	//create admin groups array for use in selectList:
	$adm_groups = array ();
	$adm_groups = array_merge ( $adm_groups, $kunena_acl->get_group_children_tree ( null, JText::_('COM_KUNENA_PUBLICBACKEND'), true ) );
	//create the access control list
	$accessLists ['pub_access'] = JHTML::_ ( 'select.genericlist', $pub_groups, 'pub_access', 'class="inputbox" size="4"', 'value', 'text', $row->pub_access );
	$accessLists ['admin_access'] = JHTML::_ ( 'select.genericlist', $adm_groups, 'admin_access', 'class="inputbox" size="4"', 'value', 'text', $row->admin_access );
	$lists ['pub_recurse'] = JHTML::_ ( 'select.genericlist', $yesno, 'pub_recurse', 'class="inputbox" size="1"', 'value', 'text', $row->pub_recurse );
	$lists ['admin_recurse'] = JHTML::_ ( 'select.genericlist', $yesno, 'admin_recurse', 'class="inputbox" size="1"', 'value', 'text', $row->admin_recurse );
	$lists ['forumLocked'] = JHTML::_ ( 'select.genericlist', $yesno, 'locked', 'class="inputbox" size="1"', 'value', 'text', $row->locked );
	$lists ['forumModerated'] = JHTML::_ ( 'select.genericlist', $noyes, 'moderated', 'class="inputbox" size="1"', 'value', 'text', $row->moderated );
	$lists ['forumReview'] = JHTML::_ ( 'select.genericlist', $yesno, 'review', 'class="inputbox" size="1"', 'value', 'text', $row->review );
	$lists ['allow_polls'] = JHTML::_ ( 'select.genericlist', $yesno, 'allow_polls', 'class="inputbox" size="1"', 'value', 'text', $row->allow_polls );
	//get a list of moderators, if forum/category is moderated
	$moderatorList = array ();

	if ($row->moderated == 1) {
		$kunena_db->setQuery ( "SELECT * FROM #__fb_moderation AS a LEFT JOIN #__users as u ON a.userid=u.id where a.catid=$row->id" );
		$moderatorList = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to load moderator list." );
	}

	html_Kunena::editForum ( $row, $categoryList, $moderatorList, $lists, $accessLists, $option );
}

function saveForum($option) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_app = & JFactory::getApplication ();

	$kunena_my = &JFactory::getUser ();
	$row = new fbForum ( $kunena_db );
	$id = JRequest::getInt ( 'id', 0, 'post' );
	if ($id) {
		$row->load ( $id );
	}
	if (! $row->save ( $_POST, 'parent' )) {
		$kunena_app->enqueueMessage ( $row->getError (), 'error' );
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showAdministration" );
	}
	$row->reorder ();

	$kunena_db->setQuery ( "UPDATE #__fb_sessions SET allowed='na'" );
	$kunena_db->query ();
	check_dberror ( "Unable to update sessions." );

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showAdministration" );
}

function publishForum($cid = null, $publish = 1, $option) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_app = & JFactory::getApplication ();
	$kunena_my = &JFactory::getUser ();
	if (! is_array ( $cid ) || count ( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('" . JText::_('COM_KUNENA_SELECTANITEMTO') . " $action'); window.history.go(-1);</script>\n";
		exit ();
	}

	$cids = implode ( ',', $cid );
	$kunena_db->setQuery ( "UPDATE #__fb_categories SET published='$publish'" . "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$kunena_my->id'))" );
	$kunena_db->query ();
	check_dberror ( "Unable to update categories." );

	if (count ( $cid ) == 1) {
		$row = new fbForum ( $kunena_db );
		$row->checkin ( $cid [0] );
	}

	// we must reset fbSession->allowed, when forum record was changed
	$kunena_db->setQuery ( "UPDATE #__fb_sessions SET allowed='na'" );
	$kunena_db->query ();
	check_dberror ( "Unable to update sessions." );

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showAdministration" );
}

function deleteForum($cid = null, $option) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_app = & JFactory::getApplication ();
	$kunena_my = &JFactory::getUser ();
	if (! is_array ( $cid ) || count ( $cid ) < 1) {
		$action = 'delete';
		echo "<script> alert('" . JText::_('COM_KUNENA_SELECTANITEMTO') . " $action'); window.history.go(-1);</script>\n";
		exit ();
	}

	$cids = implode ( ',', $cid );
	$kunena_db->setQuery ( "DELETE FROM #__fb_categories" . "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$kunena_my->id'))" );
	$kunena_db->query ();
	check_dberror ( "Unable to delete categories." );

	$kunena_db->setQuery ( "SELECT id, parent FROM #__fb_messages where catid in ($cids)" );
	$mesList = $kunena_db->loadObjectList ();
	check_dberror ( "Unable to load messages." );

	if (count ( $mesList ) > 0) {
		foreach ( $mesList as $ml ) {
			$kunena_db->setQuery ( "DELETE FROM #__fb_messages WHERE id = $ml->id" );
			$kunena_db->query ();
			check_dberror ( "Unable to delete messages." );

			$kunena_db->setQuery ( "DELETE FROM #__fb_messages_text WHERE mesid=$ml->id" );
			$kunena_db->query ();
			check_dberror ( "Unable to delete message text." );

			//and clear up all subscriptions as well
			if ($ml->parent == 0) { //this was a topic message to which could have been subscribed
				$kunena_db->setQuery ( "DELETE FROM #__fb_subscriptions WHERE thread=$ml->id" );
				$kunena_db->query ();
				check_dberror ( "Unable to delete subscriptions." );
			}
		}
	}

	$kunena_db->setQuery ( "UPDATE #__fb_sessions SET allowed='na'" );
	$kunena_db->query ();
	check_dberror ( "Unable to update sessions." );

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showAdministration" );
}

function cancelForum($option) {
	$kunena_app = & JFactory::getApplication ();

	$kunena_db = &JFactory::getDBO ();
	$row = new fbForum ( $kunena_db );
	$row->bind ( $_POST );
	$row->checkin ();
	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showAdministration" );
}

function orderForum($uid, $inc, $option) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();
	$row = new fbForum ( $kunena_db );
	$row->load ( $uid );

	// Ensure that we have the right ordering
	$where = $row->_db->nameQuote ( 'parent' ) . '=' . $row->_db->quote ( $row->parent );
	$row->reorder ( $where );
	$row->load ( $uid );

	$row->move ( $inc, $where );
	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showAdministration" );
}

function pollpublish ( $option, $cid = null, $publish = 1 ) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_app = & JFactory::getApplication ();
	$kunena_my = &JFactory::getUser ();
	if (! is_array ( $cid ) || count ( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('" . JText::_('COM_KUNENA_SELECTANITEMTO') . " $action'); window.history.go(-1);</script>\n";
		exit ();
	}

	$cids = implode ( ',', $cid );
	$kunena_db->setQuery ( "UPDATE #__fb_categories SET allow_polls='$publish'" . "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$kunena_my->id'))" );
	$kunena_db->query ();
	check_dberror ( "Unable to update categories." );

	if (count ( $cid ) == 1) {
		$row = new fbForum ( $kunena_db );
		$row->checkin ( $cid [0] );
	}

	// we must reset fbSession->allowed, when forum record was changed
	$kunena_db->setQuery ( "UPDATE #__fb_sessions SET allowed='na'" );
	$kunena_db->query ();
	check_dberror ( "Unable to update sessions." );

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showAdministration" );
}

function pollunpublish ( $option, $cid = null, $unpublish = 0 ) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_app = & JFactory::getApplication ();
	$kunena_my = &JFactory::getUser ();
	if (! is_array ( $cid ) || count ( $cid ) < 1) {
		$action = $unpublish ? 'unpublish' : 'publish';
		echo "<script> alert('" . JText::_('COM_KUNENA_SELECTANITEMTO') . " $action'); window.history.go(-1);</script>\n";
		exit ();
	}

	$cids = implode ( ',', $cid );
	$kunena_db->setQuery ( "UPDATE #__fb_categories SET allow_polls='$unpublish'" . "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$kunena_my->id'))" );
	$kunena_db->query ();
	check_dberror ( "Unable to update categories." );

	if (count ( $cid ) == 1) {
		$row = new fbForum ( $kunena_db );
		$row->checkin ( $cid [0] );
	}

	// we must reset fbSession->allowed, when forum record was changed
	$kunena_db->setQuery ( "UPDATE #__fb_sessions SET allowed='na'" );
	$kunena_db->query ();
	check_dberror ( "Unable to update sessions." );

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showAdministration" );
}

//===============================
// Config Functions
//===============================
function showConfig($option) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_config = & CKunenaConfig::getInstance ();

	$lists = array ();

	// the default page when entering Kunena
	$defpagelist = array ();

	$defpagelist [] = JHTML::_ ( 'select.option', 'recent', JText::_('COM_KUNENA_A_FBDEFAULT_PAGE_RECENT') );
	$defpagelist [] = JHTML::_ ( 'select.option', 'my', JText::_('COM_KUNENA_A_FBDEFAULT_PAGE_MY') );
	$defpagelist [] = JHTML::_ ( 'select.option', 'categories', JText::_('COM_KUNENA_A_FBDEFAULT_PAGE_CATEGORIES') );

	// build the html select list
	$lists ['fbdefaultpage'] = JHTML::_ ( 'select.genericlist', $defpagelist, 'cfg_fbdefaultpage', 'class="inputbox" size="1" ', 'value', 'text', $kunena_config->fbdefaultpage );

	// build the html select list

	// RSS
	{
		// options to be used later
		$rss_yesno = array ();
		$rss_yesno [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_A_NO') );
		$rss_yesno [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_A_YES') );

		// ------

		$rss_type = array ();
		$rss_type [] = JHTML::_ ( 'select.option', 'thread', JText::_('COM_KUNENA_A_RSS_TYPE_THREAD') );
		$rss_type [] = JHTML::_ ( 'select.option', 'post', JText::_('COM_KUNENA_A_RSS_TYPE_POST') );
		$rss_type [] = JHTML::_ ( 'select.option', 'recent', JText::_('COM_KUNENA_A_RSS_TYPE_RECENT') );

		// build the html select list
		$lists ['rss_type'] = JHTML::_ ( 'select.genericlist', $rss_type, 'cfg_rss_type', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->rss_type );

		// ------

		$rss_timelimit = array ();
		$rss_timelimit [] = JHTML::_ ( 'select.option', 'week', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_WEEK') );
		$rss_timelimit [] = JHTML::_ ( 'select.option', 'month', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_MONTH') );
		$rss_timelimit [] = JHTML::_ ( 'select.option', 'year', JText::_('COM_KUNENA_A_RSS_TIMELIMIT_YEAR') );

		// build the html select list
		$lists ['rss_timelimit'] = JHTML::_ ( 'select.genericlist', $rss_timelimit, 'cfg_rss_timelimit', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->rss_timelimit );

		// ------

		$rss_specification = array ();

		$rss_specification [] = JHTML::_ ( 'select.option', 'rss0.91', 'RSS 0.91');
		$rss_specification [] = JHTML::_ ( 'select.option', 'rss1.0', 'RSS 1.0' );
		$rss_specification [] = JHTML::_ ( 'select.option', 'rss2.0', 'RSS 2.0' );
		$rss_specification [] = JHTML::_ ( 'select.option', 'atom1.0', 'Atom 1.0' );

		// build the html select list
		$lists ['rss_specification'] = JHTML::_ ( 'select.genericlist', $rss_specification, 'cfg_rss_specification', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->rss_specification );

		// ------

		$rss_author_format = array ();
		$rss_author_format [] = JHTML::_ ( 'select.option', 'name', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_NAME') );
		$rss_author_format [] = JHTML::_ ( 'select.option', 'email', JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_EMAIL') );

		// build the html select list
		$lists ['rss_author_format'] = JHTML::_ ( 'select.genericlist', $rss_author_format, 'cfg_rss_author_format', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->rss_author_format );

		// ------

		$rss_word_count = array ();
		$rss_word_count [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_A_RSS_WORD_COUNT_ALL') );
		$rss_word_count [] = JHTML::_ ( 'select.option', '50', '50' );
		$rss_word_count [] = JHTML::_ ( 'select.option', '100', '100' );
		$rss_word_count [] = JHTML::_ ( 'select.option', '250', '250' );
		$rss_word_count [] = JHTML::_ ( 'select.option', '500', '500' );
		$rss_word_count [] = JHTML::_ ( 'select.option', '750', '750' );
		$rss_word_count [] = JHTML::_ ( 'select.option', '1000', '1000' );

		// build the html select list
		$lists ['rss_word_count'] = JHTML::_ ( 'select.genericlist', $rss_word_count, 'cfg_rss_word_count', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->rss_word_count );

		// ------

		// build the html select list
		$lists ['rss_allow_html'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_rss_allow_html', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->rss_allow_html );

		// ------

		// build the html select list
		$lists ['rss_old_titles'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_rss_old_titles', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->rss_old_titles );

		// ------

		// build the html select list - (moved enablerss here, to keep all rss-related features together)
		$lists ['enablerss'] = JHTML::_ ( 'select.genericlist', $rss_yesno, 'cfg_enablerss', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->enablerss );
	}

	// source of avatar picture
	$avlist = array ();
	$avlist [] = JHTML::_ ( 'select.option', 'fb', JText::_('COM_KUNENA_KUNENA') );
	$avlist [] = JHTML::_ ( 'select.option', 'cb', JText::_('COM_KUNENA_CB') );
	$avlist [] = JHTML::_ ( 'select.option', 'jomsocial', JText::_('COM_KUNENA_JOMSOCIAL') );
	$avlist [] = JHTML::_ ( 'select.option', 'aup', JText::_('COM_KUNENA_AUP_ALPHAUSERPOINTS') ); // INTEGRATION ALPHAUSERPOINTS
	// build the html select list
	$lists ['avatar_src'] = JHTML::_ ( 'select.genericlist', $avlist, 'cfg_avatar_src', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->avatar_src );

	// private messaging system to use
	$pmlist = array ();
	$pmlist [] = JHTML::_ ( 'select.option', 'no', JText::_('COM_KUNENA_A_NO') );
	$pmlist [] = JHTML::_ ( 'select.option', 'cb', JText::_('COM_KUNENA_CB') );
	$pmlist [] = JHTML::_ ( 'select.option', 'jomsocial', JText::_('COM_KUNENA_JOMSOCIAL') );
	$pmlist [] = JHTML::_ ( 'select.option', 'uddeim', JText::_('COM_KUNENA_UDDEIM') );

	$lists ['pm_component'] = JHTML::_ ( 'select.genericlist', $pmlist, 'cfg_pm_component', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->pm_component );

	//redundant    $lists['pm_component'] = JHTML::_('select.genericlist',$pmlist, 'cfg_pm_component', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->pm_component);
	// Profile select
	$prflist = array ();
	$prflist [] = JHTML::_ ( 'select.option', 'fb', JText::_('COM_KUNENA_KUNENA') );
	$prflist [] = JHTML::_ ( 'select.option', 'cb', JText::_('COM_KUNENA_CB') );
	$prflist [] = JHTML::_ ( 'select.option', 'jomsocial', JText::_('COM_KUNENA_JOMSOCIAL') );
	$prflist [] = JHTML::_ ( 'select.option', 'aup', JText::_('COM_KUNENA_AUP_ALPHAUSERPOINTS') ); // INTEGRATION ALPHAUSERPOINTS


	$lists ['fb_profile'] = JHTML::_ ( 'select.genericlist', $prflist, 'cfg_fb_profile', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->fb_profile );

	// build the html select list
	// make a standard yes/no list
	$yesno = array ();
	$yesno [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_A_NO') );
	$yesno [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_A_YES') );
	/* Build the templates list*/
	// This function was modified from the one posted to PHP.net by rockinmusicgv
	// It is available under the readdir() entry in the PHP online manual
	//function get_dirs($directory, $select_name, $selected = "") {
	$listitems [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_SELECTTEMPLATE') );

	$templatelist = array();
	$imagesetlist = array();
	$dir = @opendir ( KUNENA_PATH_TEMPLATE );
	if ($dir) {
		while ( ($file = readdir ( $dir )) !== false ) {
			if ($file != ".." && $file != ".") {
				if (is_dir ( KUNENA_PATH_TEMPLATE . DS . $file )) {
					if (! ($file [0] == '.') && is_file ( KUNENA_PATH_TEMPLATE . DS. $file . DS . 'css' . DS . 'kunena.forum.css' )) {
						$templatelist [] = $file;
					}
					if (! ($file [0] == '.') && is_dir ( KUNENA_PATH_TEMPLATE . DS . $file . DS . 'images' )) {
						$imagesetlist [] = $file;
					}
				}
			}
		}

		closedir ( $dir );
	}

	asort ( $templatelist );

	foreach ( $templatelist as $key => $val ) {
		$templatelistitems [] = JHTML::_ ( 'select.option', $val, $val );
	}

	asort ( $imagesetlist );

	foreach ( $imagesetlist as $key => $val ) {
		$imagesetlistitems [] = JHTML::_ ( 'select.option', $val, $val );
	}

	$lists ['jmambot'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_jmambot', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->jmambot );
	$lists ['disemoticons'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_disemoticons', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->disemoticons );
	$lists ['template'] = JHTML::_ ( 'select.genericlist', $templatelistitems, 'cfg_template', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->template );
	$lists ['templateimagepath'] = JHTML::_ ( 'select.genericlist', $imagesetlistitems, 'cfg_templateimagepath', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->templateimagepath );
	$lists ['regonly'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_regonly', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->regonly );
	$lists ['board_offline'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_board_offline', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->board_offline );
	$lists ['pubwrite'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_pubwrite', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->pubwrite );
	$lists ['useredit'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_useredit', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->useredit );
	$lists ['showhistory'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showhistory', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showhistory );
	$lists ['showannouncement'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showannouncement', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showannouncement );
	$lists ['avataroncat'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_avataroncat', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->avataroncat );
	$lists ['showlatest'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showlatest', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showlatest );
	$lists ['latestsinglesubject'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_latestsinglesubject', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->latestsinglesubject );
	$lists ['latestreplysubject'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_latestreplysubject', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->latestreplysubject );
	$lists ['latestshowdate'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_latestshowdate', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->latestshowdate );
	$lists ['showchildcaticon'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showchildcaticon', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showchildcaticon );
	$lists ['latestshowhits'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_latestshowhits', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->latestshowhits );
	$lists ['showuserstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showuserstats', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showuserstats );
	$lists ['showwhoisonline'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showwhoisonline', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showwhoisonline );
	$lists ['showpopsubjectstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showpopsubjectstats', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showpopsubjectstats );
	$lists ['showgenstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showgenstats', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showgenstats );
	$lists ['showpopuserstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showpopuserstats', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showpopuserstats );
	$lists ['allowsubscriptions'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowsubscriptions', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->allowsubscriptions );
	$lists ['subscriptionschecked'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_subscriptionschecked', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->subscriptionschecked );
	$lists ['allowfavorites'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowfavorites', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->allowfavorites );
	$lists ['mailmod'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_mailmod', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->mailmod );
	$lists ['mailadmin'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_mailadmin', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->mailadmin );
	$lists ['showemail'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showemail', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showemail );
	$lists ['askemail'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_askemail', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->askemail );
	$lists ['changename'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_changename', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->changename );
	$lists ['allowavatar'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowavatar', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->allowavatar );
	$lists ['allowavatarupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowavatarupload', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->allowavatarupload );
	$lists ['allowavatargallery'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowavatargallery', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->allowavatargallery );
	$lists ['avatar_src'] = JHTML::_ ( 'select.genericlist', $avlist, 'cfg_avatar_src', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->avatar_src );

	$ip_opt [] = JHTML::_ ( 'select.option', 'gd2', 'GD2' );
	$ip_opt [] = JHTML::_ ( 'select.option', 'gd1', 'GD1' );
	$ip_opt [] = JHTML::_ ( 'select.option', 'none', JText::_('COM_KUNENA_IMAGE_PROCESSOR_NONE') );

	$lists ['imageprocessor'] = JHTML::_ ( 'select.genericlist', $ip_opt, 'cfg_imageprocessor', 'class="inputbox"', 'value', 'text', $kunena_config->imageprocessor );
	$lists ['showstats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showstats', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showstats );
	$lists ['showranking'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showranking', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showranking );
	$lists ['rankimages'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_rankimages', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->rankimages );
	$lists ['username'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_username', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->username );
	$lists ['shownew'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_shownew', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->shownew );
	$lists ['allowimageupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowimageupload', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->allowimageupload );
	$lists ['allowimageregupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowimageregupload', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->allowimageregupload );
	$lists ['allowfileupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowfileupload', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->allowfileupload );
	$lists ['allowfileregupload'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_allowfileregupload', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->allowfileregupload );
	$lists ['editmarkup'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_editmarkup', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->editmarkup );
	$lists ['discussbot'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_discussbot', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->discussbot );
	$lists ['showkarma'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showkarma', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showkarma );
	$lists ['enablepdf'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_enablepdf', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->enablepdf );
	$lists ['enablerulespage'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_enablerulespage', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->enablerulespage );
	$lists ['rules_infb'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_rules_infb', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->rules_infb );
	$lists ['enablehelppage'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_enablehelppage', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->enablehelppage );
	$lists ['help_infb'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_help_infb', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->help_infb );
	$lists ['enableforumjump'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_enableforumjump', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->enableforumjump );
	$lists ['userlist_online'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_online', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_online );
	$lists ['userlist_avatar'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_avatar', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_avatar );
	$lists ['userlist_name'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_name', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_name );
	$lists ['userlist_username'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_username', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_username );
	$lists ['userlist_posts'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_posts', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_posts );
	$lists ['userlist_karma'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_karma', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_karma );
	$lists ['userlist_email'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_email', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_email );
	$lists ['userlist_usertype'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_usertype', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_usertype );
	$lists ['userlist_joindate'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_joindate', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_joindate );
	$lists ['userlist_lastvisitdate'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_lastvisitdate', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_lastvisitdate );
	$lists ['userlist_userhits'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_userlist_userhits', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->userlist_userhits );
	$lists ['usernamechange'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_usernamechange', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->usernamechange );
	$lists ['reportmsg'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_reportmsg', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->reportmsg );
	$lists ['captcha'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_captcha', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->captcha );
	$lists ['mailfull'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_mailfull', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->mailfull );
	// New for 1.0.5
	$lists ['showspoilertag'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showspoilertag', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showspoilertag );
	$lists ['showvideotag'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showvideotag', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showvideotag );
	$lists ['showebaytag'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_showebaytag', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showebaytag );
	$lists ['trimlongurls'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_trimlongurls', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->trimlongurls );
	$lists ['autoembedyoutube'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_autoembedyoutube', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->autoembedyoutube );
	$lists ['autoembedebay'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_autoembedebay', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->autoembedebay );
	$lists ['highlightcode'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_highlightcode', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->highlightcode );
	// New for 1.5.7 -> integration AlphaUserPoints
	$lists ['alphauserpoints'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_alphauserpoints', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->alphauserpoints );
	$lists ['alphauserpointsrules'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_alphauserpointsrules', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->alphauserpointsrules );
	// New for 1.5.8 -> SEF
	$lists ['sef'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_sef', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->sef );
	$lists ['sefcats'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_sefcats', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->sefcats );
	$lists ['sefutf8'] = JHTML::_ ( 'select.genericlist', $yesno, 'cfg_sefutf8', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->sefutf8 );
	// New for 1.6 -> Hide images and files for guests
	$lists['showimgforguest'] = JHTML::_('select.genericlist', $yesno, 'cfg_showimgforguest', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showimgforguest);
	$lists['showfileforguest'] = JHTML::_('select.genericlist', $yesno, 'cfg_showfileforguest', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showfileforguest);
    // New for 1.6 -> Avatar Position
    $avpos = array ();
	$avpos[] = JHTML::_('select.option', 'top',JText::_('COM_KUNENA_AV_TOP'));
	$avpos[] = JHTML::_('select.option', 'left',JText::_('COM_KUNENA_AV_LEFT'));
	$avpos[] = JHTML::_('select.option', 'right',JText::_('COM_KUNENA_AV_RIGHT'));
	$avpos[] = JHTML::_('select.option', 'bottom',JText::_('COM_KUNENA_AV_BOTTOM'));
    $lists['avposition'] = JHTML::_('select.genericlist', $avpos, 'cfg_avposition', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->avposition);
	//New for 1.6 -> Poll
	$lists['pollallowvoteone'] = JHTML::_('select.genericlist', $yesno, 'cfg_pollallowvoteone', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->pollallowvoteone);
  	$lists['pollenabled'] = JHTML::_('select.genericlist', $yesno, 'cfg_pollenabled', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->pollenabled);
  	$lists['showpoppollstats'] = JHTML::_('select.genericlist', $yesno, 'cfg_showpoppollstats', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->showpoppollstats);
  	$lists['pollresultsuserslist'] = JHTML::_('select.genericlist', $yesno, 'cfg_pollresultsuserslist', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->pollresultsuserslist);
  	//New for 1.6 -> Choose ordering system
  	$ordering_system_list = array ();
  	$ordering_system_list[] = JHTML::_('select.option', 'new_ord', JText::_('COM_KUNENA_COM_A_ORDERING_SYSTEM_NEW'));
  	$ordering_system_list[] = JHTML::_('select.option', 'old_ord',JText::_('COM_KUNENA_COM_A_ORDERING_SYSTEM_OLD'));
  	$lists['ordering_system'] = JHTML::_('select.genericlist', $ordering_system_list, 'cfg_ordering_system', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->ordering_system);
	// New for 1.6: datetime
	require_once(KUNENA_PATH_LIB .DS. 'kunena.timeformat.class.php');
	$dateformatlist = array ();
	$time = CKunenaTimeformat::internalTime() - 80000;
	$dateformatlist[] = JHTML::_('select.option', 'none', JText::_('COM_KUNENA_OPTION_DATEFORMAT_NONE'));
	$dateformatlist[] = JHTML::_('select.option', 'ago', CKunenaTimeformat::showDate($time, 'ago'));
	$dateformatlist[] = JHTML::_('select.option', 'datetime_today', CKunenaTimeformat::showDate($time, 'datetime_today'));
	$dateformatlist[] = JHTML::_('select.option', 'datetime', CKunenaTimeformat::showDate($time, 'datetime'));
	$lists['post_dateformat'] = JHTML::_('select.genericlist', $dateformatlist, 'cfg_post_dateformat', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->post_dateformat);
	$lists['post_dateformat_hover'] = JHTML::_('select.genericlist', $dateformatlist, 'cfg_post_dateformat_hover', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->post_dateformat_hover);
	// New for 1.6: hide ip
	$lists['hide_ip'] = JHTML::_('select.genericlist', $yesno, 'cfg_hide_ip', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->hide_ip);
	//New for 1.6: Joomsocial Activity Stream Integration disable/enable
	$lists['js_actstr_integration'] = JHTML::_('select.genericlist', $yesno, 'cfg_js_actstr_integration', 'class="inputbox" size="1"', 'value', 'text', $kunena_config->js_actstr_integration);

	html_Kunena::showConfig($kunena_config, $lists, $option);
}

function saveConfig($option) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_config = & CKunenaConfig::getInstance ();
	$kunena_db = &JFactory::getDBO ();

	foreach ( $_POST as $postsetting => $postvalue ) {
		if (JString::strpos ( $postsetting, 'cfg_' ) === 0) {
			//remove cfg_ and force lower case
			$postname = JString::strtolower ( JString::substr ( $postsetting, 4 ) );
			$postvalue = addslashes ( $postvalue );

			// No matter what got posted, we only store config parameters defined
			// in the config class. Anything else posted gets ignored.
			if (array_key_exists ( $postname, $kunena_config->GetClassVars () )) {
				if (is_numeric ( $postvalue )) {
					eval ( "\$kunena_config->" . $postname . " = " . $postvalue . ";" );
				} else {
					// Rest is treaded as strings
					eval ( "\$kunena_config->" . $postname . " = '" . $postvalue . "';" );
				}
			} else {
				// This really should not happen if assertions are enable
				// fail it and display the current scope of variables for debugging.
				// echo debug_vars(get_defined_vars());
				trigger_error ( 'Unknown configuration variable posted.' );
				assert ( 0 );
			}
		}
	}

	$kunena_config->backup ();
	$kunena_config->remove ();
	$kunena_config->create ();

	$kunena_db->setQuery ( "UPDATE #__fb_sessions SET allowed='na'" );
	$kunena_db->query ();
	check_dberror ( "Unable to update sessions." );

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showconfig", JText::_('COM_KUNENA_CONFIGSAVED') );
}

function showInstructions($kunena_db, $option) {
	$kunena_db = &JFactory::getDBO ();
	html_Kunena::showInstructions ( $kunena_db, $option );
}

//===============================
// CSS functions
//===============================
function showCss($option) {
	require_once (KUNENA_PATH_LIB . DS . 'kunena.file.class.php');

	$kunena_config = & CKunenaConfig::getInstance ();
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

	if (CKunenaFile::write ( $file, stripslashes ( $csscontent ) )) {
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
	$kunena_db->setQuery ( "SELECT COUNT(*) FROM #__users AS a" . "\n LEFT JOIN #__fb_users AS b" . "\n ON a.id=b.userid where b.moderator=1" );
	$total = $kunena_db->loadResult ();
	check_dberror ( 'Unable to load moderators w/o limit.' );

	if ($limitstart >= $total)
		$limitstart = 0;
	if ($limit == 0 || $limit > 100)
		$limit = 100;

	$kunena_db->setQuery ( "SELECT * FROM #__users AS a" . "\n LEFT JOIN #__fb_users AS b" . "\n ON a.id=b.userid" . "\n WHERE b.moderator=1", $limitstart, $limit );
	$userList = $kunena_db->loadObjectList ();
	check_dberror ( 'Unable to load moderators.' );
	$countUL = count ( $userList );

	jimport ( 'joomla.html.pagination' );
	$pageNav = new JPagination ( $total, $limitstart, $limit );
	//$id = intval( JRequest::getVar('id') );
	//get forum name
	$forumName = '';
	$kunena_db->setQuery ( "select name from #__fb_categories where id=$id" );
	$forumName = $kunena_db->loadResult ();
	check_dberror ( 'Unable to load forum name.' );

	//get forum moderators
	$kunena_db->setQuery ( "select userid from #__fb_moderation where catid=$id" );
	$moderatorList = $kunena_db->loadObjectList ();
	check_dberror ( 'Unable to load moderator.' );
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
			$kunena_db->setQuery ( "INSERT INTO #__fb_moderation set catid='$id', userid='$cid[$i]'" );
			$kunena_db->query ();
			check_dberror ( "Unable to insert moderator." );
		}
	} else {
		for($i = 0, $n = count ( $cid ); $i < $n; $i ++) {
			$kunena_db->setQuery ( "DELETE FROM #__fb_moderation WHERE catid='$id' and userid='$cid[$i]'" );
			$kunena_db->query ();
			check_dberror ( "Unable to delete moderator." );
		}
	}

	$row = new fbForum ( $kunena_db );
	$row->checkin ( $id );

	$kunena_db->setQuery ( "UPDATE #__fb_sessions SET allowed='na'" );
	$kunena_db->query ();
	check_dberror ( "Unable to update sessions." );

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=edit2&uid=" . $id );
}

//===============================
//   User Profile functions
//===============================
function showProfiles($kunena_db, $option, $order) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();
	//$limit = intval(JRequest::getVar( 'limit', 10));
	//$limitstart = intval(JRequest::getVar( 'limitstart', 0));
	$limit = $kunena_app->getUserStateFromRequest ( "global.list.limit", 'limit', $kunena_app->getCfg ( 'list_limit' ), 'int' );
	$limitstart = $kunena_app->getUserStateFromRequest ( "{$option}.limitstart", 'limitstart', 0, 'int' );

	$search = $kunena_app->getUserStateFromRequest ( "{$option}.search", 'search', '', 'string' );
	$search = $kunena_db->getEscaped ( JString::trim ( JString::strtolower ( $search ) ) );
	$where = array ();

	if (isset ( $search ) && $search != "") {
		$where [] = "(u.username LIKE '%$search%' OR u.email LIKE '%$search%' OR u.name LIKE '%$search%')";
	}

	$kunena_db->setQuery ( "SELECT COUNT(*) FROM #__fb_users AS sbu" . "\n INNER JOIN #__users AS u" . "\n ON sbu.userid=u.id" . (count ( $where ) ? "\nWHERE " . implode ( ' AND ', $where ) : "") );
	$total = $kunena_db->loadResult ();
	check_dberror ( 'Unable to load user profiles w/o limits.' );

	if ($limitstart >= $total)
		$limitstart = 0;
	if ($limit == 0 || $limit > 100)
		$limit = 100;

	if ($order == 1) {
		$kunena_db->setQuery ( "SELECT * FROM #__fb_users AS sbu" . "\n INNER JOIN #__users AS u" . "\n ON sbu.userid=u.id " . (count ( $where ) ? "\nWHERE " . implode ( ' AND ', $where ) : "") . "\n ORDER BY sbu.moderator DESC", $limitstart, $limit );
	} else if ($order == 2) {
		$kunena_db->setQuery ( "SELECT * FROM #__fb_users AS sbu" . "\n INNER JOIN #__users AS u " . "\n ON sbu.userid=u.id " . (count ( $where ) ? "\nWHERE " . implode ( ' AND ', $where ) : "") . "\n ORDER BY u.name ASC ", $limitstart, $limit );
	} else if ($order == 3) {
		$kunena_db->setQuery ( "SELECT * FROM #__fb_users AS sbu" . "\n INNER JOIN #__users AS u " . "\n ON sbu.userid=u.id " . (count ( $where ) ? "\nWHERE " . implode ( ' AND ', $where ) : "") . "\n ORDER BY u.username ASC", $limitstart, $limit );
	} else if ($order < 1) {
		$kunena_db->setQuery ( "SELECT * FROM #__fb_users AS sbu " . "\n INNER JOIN #__users AS u" . "\n ON sbu.userid=u.id " . (count ( $where ) ? "\nWHERE " . implode ( ' AND ', $where ) : "") . "\n ORDER BY sbu.userid", $limitstart, $limit );
	}

	$profileList = $kunena_db->loadObjectList ();
	check_dberror ( 'Unable to load user profiles.' );

	$countPL = count ( $profileList );

	jimport ( 'joomla.html.pagination' );
	$pageNavSP = new JPagination ( $total, $limitstart, $limit );
	html_Kunena::showProfiles ( $option, $profileList, $countPL, $pageNavSP, $order, $search );
}

function editUserProfile($option, $uid) {
	if (empty ( $uid [0] )) {
		echo JText::_('COM_KUNENA_PROFILE_NO_USER');
		return;
	}

	$kunena_db = &JFactory::getDBO ();
	$kunena_acl = &JFactory::getACL ();

	$kunena_db->setQuery ( "SELECT * FROM #__fb_users LEFT JOIN #__users on #__users.id=#__fb_users.userid WHERE userid=$uid[0]" );
	$userDetails = $kunena_db->loadObjectList ();
	check_dberror ( 'Unable to load user profile.' );
	$user = $userDetails [0];

	//Mambo userids are unique, so we don't worry about that
	$prefview = $user->view;
	$ordering = $user->ordering;
	$moderator = $user->moderator;
	$userRank = $user->rank;

	//check to see if this is an administrator
	$result = '';
	//$kunena_db->setQuery("select usertype from #__users where id=$uid[0]");
	//$result=$kunena_db->loadResult();
	//check_dberror ( 'Unable to load user type.' );
	$result = $kunena_acl->getAroGroup ( $uid [0] );

	//grab all special ranks
	$kunena_db->setQuery ( "SELECT * FROM #__fb_ranks WHERE rank_special = '1'" );
	$specialRanks = $kunena_db->loadObjectList ();
	check_dberror ( 'Unable to load special ranks.' );

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
	$kunena_db->setQuery ( "select thread from #__fb_subscriptions where userid=$uid[0]" );
	$subslist = $kunena_db->loadObjectList ();
	check_dberror ( 'Unable to load subscriptions for user.' );

	//get all moderation category ids for this user
	$kunena_db->setQuery ( "select catid from #__fb_moderation where userid=" . $uid [0] );
	$_modCats = $kunena_db->loadResultArray ();
	check_dberror ( 'Unable to moderation category ids for user.' );

	$__modCats = array ();

	foreach ( $_modCats as $_v ) {
		$__modCats [] = JHTML::_ ( 'select.option', $_v );
	}

	$modCats = KUNENA_GetAvailableModCats ( $__modCats );

	html_Kunena::editUserProfile ( $option, $user, $subslist, $selectRank, $selectPref, $selectMod, $selectOrder, $uid [0], $modCats );
}

function saveUserProfile($option) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();
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
	$signature = addslashes ( $signature );

	$avatar = '';
	if ($deleteAvatar == 1) {
		$avatar = ",avatar=''";
	}

	$kunena_db->setQuery ( "UPDATE #__fb_users SET signature='$signature', view='$newview',moderator='$moderator', ordering='$neworder', rank='$newrank' $avatar where userid=$uid" );
	$kunena_db->query ();
	check_dberror ( "Unable to update signature." );

	//delete all moderator traces before anyway
	$kunena_db->setQuery ( "DELETE FROM #__fb_moderation WHERE userid=$uid" );
	$kunena_db->query ();
	check_dberror ( "Unable to delete moderator." );

	//if there are moderatored forums, add them all
	if ($moderator == 1) {
		if (count ( $modCatids ) > 0) {
			foreach ( $modCatids as $c ) {
				$kunena_db->setQuery ( "INSERT INTO #__fb_moderation SET catid='$c', userid='$uid'" );
				$kunena_db->query ();
				check_dberror ( "Unable to insert moderator." );
			}
		}
	}

	$kunena_db->setQuery ( "UPDATE #__fb_sessions SET allowed='na' WHERE userid='$uid'" );
	$kunena_db->query ();
	check_dberror ( "Unable to update sessions." );

	$kunena_app->redirect ( JURI::base () . "index.php?option=com_kunena&task=showprofiles" );
}

//===============================
// Prune Forum functions
//===============================
function pruneforum($kunena_db, $option) {
	$forums_list = array ();
	//get forum list; locked forums are excluded from pruning
	$kunena_db->setQuery ( "SELECT a.id as value, a.name as text" . "\nFROM #__fb_categories AS a" . "\nWHERE a.parent != '0'" . "\nAND a.locked != '1'" . "\nORDER BY parent, ordering" );
	//get all subscriptions for this user
	$forums_list = $kunena_db->loadObjectList ();
	check_dberror ( "Unable to load unlocked forums." );
	$forumList ['forum'] = JHTML::_ ( 'select.genericlist', $forums_list, 'prune_forum', 'class="inputbox" size="4"', 'value', 'text', '' );
	html_Kunena::pruneforum ( $option, $forumList );
}

function doprune($kunena_db, $option) {
	$kunena_app = & JFactory::getApplication ();

	$catid = intval ( JRequest::getVar ( 'prune_forum', - 1 ) );
	$deleted = 0;

	if ($catid == - 1) {
		echo "<script> alert('" . JText::_('COM_KUNENA_CHOOSEFORUMTOPRUNE') . "'); window.history.go(-1); </script>\n";
		$kunena_app->close ();
	}

	$prune_days = intval ( JRequest::getVar ( 'prune_days', 0 ) );
	//get the thread list for this forum
	$kunena_db->setQuery ( "SELECT DISTINCT a.thread AS thread, max(a.time) AS lastpost, c.locked AS locked " . "\n FROM #__fb_messages AS a" . "\n JOIN #__fb_categories AS b ON a.catid=b.id " . "\n JOIN #__fb_messages   AS c ON a.thread=c.thread" . "\n where a.catid=$catid " . "\n and b.locked != 1 " . "\n and a.locked != 1 " . "\n and c.locked != 1 " . "\n and c.parent = 0 " . "\n and c.ordering != 1 " . "\n group by thread" );
	$threadlist = $kunena_db->loadObjectList ();
	check_dberror ( "Unable to load thread list." );

	// Convert days to seconds for timestamp functions...
	$prune_date = CKunenaTimeformat::internalTime () - ($prune_days * 86400);

	if (count ( $threadlist ) > 0) {
		foreach ( $threadlist as $tl ) {
			//check if thread is eligible for pruning
			if ($tl->lastpost < $prune_date) {
				//get the id's for all posts belonging to this thread
				$kunena_db->setQuery ( "SELECT id from #__fb_messages WHERE thread=$tl->thread" );
				$idlist = $kunena_db->loadObjectList ();
				check_dberror ( "Unable to load thread messages." );

				if (count ( $idlist ) > 0) {
					foreach ( $idlist as $id ) {
						//prune all messages belonging to the thread
						$kunena_db->setQuery ( "DELETE FROM #__fb_messages WHERE id=$id->id" );
						$kunena_db->query ();
						check_dberror ( "Unable to delete messages." );

						$kunena_db->setQuery ( "DELETE FROM #__fb_messages_text WHERE mesid=$id->id" );
						$kunena_db->query ();
						check_dberror ( "Unable to delete message texts." );

						//delete all attachments
						$kunena_db->setQuery ( "SELECT filelocation FROM #__fb_attachments WHERE mesid=$id->id" );
						$fileList = $kunena_db->loadObjectList ();
						check_dberror ( "Unable to load attachments." );

						if (count ( $fileList ) > 0) {
							foreach ( $fileList as $fl ) {
								unlink ( $fl->filelocation );
							}

							$kunena_db->setQuery ( "DELETE FROM #__fb_attachments WHERE mesid=$id->id" );
							$kunena_db->query ();
							check_dberror ( "Unable to delete attachments." );
						}

						$deleted ++;
					}
				}
			}

			//clean all subscriptions to these deleted threads
			$kunena_db->setQuery ( "DELETE FROM #__fb_subscriptions WHERE thread=$tl->thread" );
			$kunena_db->query ();
			check_dberror ( "Unable to delete subscriptions." );
		}
	}

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=pruneforum", "" . JText::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " " . JText::_('COM_KUNENA_PRUNEDAYS') . "; " . JText::_('COM_KUNENA_PRUNEDELETED') . $deleted . " " . JText::_('COM_KUNENA_PRUNETHREADS') );
}

//===============================
// Sync users
//===============================
function syncusers($kunena_db, $option) {
	html_Kunena::syncusers ( $option );
}

function douserssync($kunena_db, $option) {
	$usercache = JRequest::getBool ( 'usercache', 0 );
	$useradd = JRequest::getBool ( 'useradd', 0 );
	$userdel = JRequest::getBool ( 'userdel', 0 );
	$userrename = JRequest::getBool ( 'userrename', 0 );

	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();

	if ($usercache) {
		//reset access rights
		$kunena_db->setQuery ( "UPDATE #__fb_sessions SET allowed='na'" );
		$kunena_db->query ();
		check_dberror ( "Unable to update sessions." );
		$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_SYNC_USERS_DO_CACHE') );
	}
	if ($useradd) {
		$kunena_db->setQuery ( "INSERT INTO #__fb_users (userid) SELECT a.id FROM #__users AS a LEFT JOIN #__fb_users AS b ON b.userid=a.id WHERE b.userid IS NULL" );
		$kunena_db->query ();
		check_dberror ( 'Unable to create user profiles.' );
		$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_SYNC_USERS_DO_ADD') . ' ' . $kunena_db->getAffectedRows () );
	}
	if ($userdel) {
		$kunena_db->setQuery ( "DELETE a FROM #__fb_users AS a LEFT JOIN #__users AS b ON a.userid=b.id WHERE b.username IS NULL" );
		$kunena_db->query ();
		check_dberror ( "Unable to delete user profiles." );
		$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_SYNC_USERS_DO_DEL') . ' ' . $kunena_db->getAffectedRows () );
	}
	if ($userrename) {
		$cnt = CKunenaTools::updateNameInfo ();
		$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_SYNC_USERS_DO_RENAME') . " $cnt" );
	}

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=syncusers" );
}

//===============================
// Uploaded Images browser
//===============================
function browseUploaded($kunena_db, $option, $type) {
	$kunena_db = &JFactory::getDBO ();
	if ($type) { //we're doing images
		$dir = @opendir ( KUNENA_PATH_UPLOADED . DS . 'images' );
		$uploaded_path = KUNENA_PATH_UPLOADED . DS . 'images';
	} else { //we're doing regular files
		$dir = @opendir ( KUNENA_PATH_UPLOADED . DS . 'files' );
		$uploaded_path = KUNENA_PATH_UPLOADED . DS . 'files';
	}

	$uploaded = array ();
	$uploaded_col_count = 0;

	$file = @readdir ( $dir );

	while ( $file ) {
		if ($file != '.' && $file != '..' && $file != 'index.php' && is_file ( $uploaded_path . DS . $file ) && ! is_link ( $uploaded_path . DS . $file )) {
			//if( preg_match('/(\.gif$|\.png$|\.jpg|\.jpeg)$/is', $file) )
			//{
			$uploaded [$uploaded_col_count] = $file;
			$uploaded_name [$uploaded_col_count] = JString::ucfirst ( str_replace ( "_", " ", preg_replace ( '/^(.*)\..*$/', '\1', $file ) ) );
			$uploaded_col_count ++;
			//}
		}
		$file = @readdir ( $dir );
	}

	@closedir ( $dir );
	@ksort ( $uploaded );
	@reset ( $uploaded );
	html_Kunena::browseUploaded ( $option, $uploaded, $uploaded_path, $type );
}

function replaceImage($kunena_db, $option, $imageName, $OxP) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();
	if (! $imageName) {
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=browseImages" );
		return;
	}

	require_once (KUNENA_PATH_LIB . DS . 'kunena.file.class.php');
	// This function will replace the selected image with a dummy (OxP=1) or delete it


	if ($OxP == "1") {
		$filename = explode ( ".", $imageName );
		$fileName = $filename [0];
		$fileExt = $filename [1];
		$ret = CKunenaFile::copy ( KUNENA_PATH_UPLOADED . DS . 'dummy.' . $fileExt, KUNENA_PATH_UPLOADED . DS . 'images' . DS . $imageName );
	} else {
		$ret = CKunenaFile::delete ( KUNENA_PATH_UPLOADED . DS . 'images' . DS . $imageName );
		//remove the database link as well
		if ($ret) {
			$kunena_db->setQuery ( "DELETE FROM #__fb_attachments where filelocation='%/images/" . $imageName . "'" );
			$kunena_db->query ();
			check_dberror ( "Unable to delete attachment." );
		}
	}
	if ($ret)
		$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_IMGDELETED') );
	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=browseImages" );
}

function deleteFile($kunena_db, $option, $fileName) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();

	if (! $fileName) {
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=browseFiles" );
		return;
	}

	require_once (KUNENA_PATH_LIB . DS . 'kunena.file.class.php');

	// step 1: Remove file
	$ret = CKunenaFile::delete ( KUNENA_PATH_UPLOADED . DS . 'files' . DS . $fileName );
	//step 2: remove the database link to the file
	if ($ret) {
		$kunena_db->setQuery ( "DELETE FROM #__fb_attachments where filelocation='%/files/" . $fileName . "'" );
		$kunena_db->query ();
		check_dberror ( "Unable to delete attachment." );
	}
	if ($ret)
		$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_FILEDELETED') );
	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=browseFiles" );
}

//===============================
// Generic Functions
//===============================


/*  danial */
#########  category functions #########
function catTreeRecurse($id, $indent = "&nbsp;&nbsp;&nbsp;", $list, &$children, $maxlevel = 9999, $level = 0, $seperator = " >> ") {
	if (@$children [$id] && $level <= $maxlevel) {
		foreach ( $children [$id] as $v ) {
			$id = $v->id;
			$txt = $v->name;
			$pt = $v->parent;
			$list [$id] = $v;
			$list [$id]->treename = "$indent$txt";
			$list [$id]->children = count ( @$children [$id] );
			$list = catTreeRecurse ( $id, "$indent$txt$seperator", $list, $children, $maxlevel, $level + 1 );
			//$list = catTreeRecurse( $id, "*", $list, $children, $maxlevel, $level+1 );
		}
	}

	return $list;
}

function showCategories($cat, $cname, $extras = "", $levellimit = "4") {
	$kunena_db = &JFactory::getDBO ();
	$kunena_db->setQuery ( "select id ,parent,name from
          #__fb_categories" . "\nORDER BY name" );
	$mitems = $kunena_db->loadObjectList ();
	check_dberror ( "Unable to load categories." );

	// establish the hierarchy of the menu
	$children = array ();

	// first pass - collect children
	foreach ( $mitems as $v ) {
		$pt = $v->parent;
		$list = @$children [$pt] ? $children [$pt] : array ();
		array_push ( $list, $v );
		$children [$pt] = $list;
	}

	// second pass - get an indent list of the items
	$list = catTreeRecurse ( 0, '', array (), $children );
	// assemble menu items to the array
	$mitems = array ();
	$mitems [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_TOPLEVEL'), 'value', 'text' );
	$this_treename = '';

	foreach ( $list as $item ) {
		if ($this_treename) {
			if ($item->id != $mitems && JString::strpos ( $item->treename, $this_treename ) === false) {
				$mitems [] = JHTML::_ ( 'select.option', $item->id, $item->treename );
			}
		} else {
			if ($item->id != $mitems) {
				$mitems [] = JHTML::_ ( 'select.option', $item->id, $item->treename );
			} else {
				$this_treename = "$item->treename/";
			}
		}
	}

	// build the html select list
	$parlist = selectList2 ( $mitems, $cname, 'class="inputbox"  ' . $extras, 'value', 'text', $cat );
	return $parlist;
}

#######################################
## multiple select list
function selectList2(&$arr, $tag_name, $tag_attribs, $key, $text, $selected) {
	reset ( $arr );
	$html = "\n<select name=\"$tag_name\" $tag_attribs>";

	for($i = 0, $n = count ( $arr ); $i < $n; $i ++) {
		$k = $arr [$i]->$key;
		$t = $arr [$i]->$text;
		$id = @$arr [$i]->id;
		$extra = '';
		$extra .= $id ? " id=\"" . $arr [$i]->id . "\"" : '';

		if (is_array ( $selected )) {
			foreach ( $selected as $obj ) {
				$k2 = $obj;

				if ($k == $k2) {
					$extra .= " selected=\"selected\"";
					break;
				}
			}
		} else {
			$extra .= ($k == $selected ? " selected=\"selected\"" : '');
		}

		$html .= "\n\t<option value=\"" . $k . "\"$extra>" . $t . "</option>";
	}

	$html .= "\n</select>\n";
	return $html;
}

function dircopy($srcdir, $dstdir, $verbose = false) {
	$num = 0;

	if (! is_dir ( $dstdir )) {
		mkdir ( $dstdir );
	}

	$curdir = opendir ( $srcdir );

	if ($curdir) {
		$file = readdir ( $curdir );
		while ( $file ) {
			if ($file != '.' && $file != '..') {
				$srcfile = $srcdir . DS . $file;
				$dstfile = $dstdir . DS . $file;

				if (is_file ( $srcfile )) {
					if (is_file ( $dstfile )) {
						$ow = filemtime ( $srcfile ) - filemtime ( $dstfile );
					} else {
						$ow = 1;
					}

					if ($ow > 0) {
						if ($verbose) {
							$tmpstr = JText::_('COM_KUNENA_COPY_FILE');
							$tmpstr = str_replace ( '%src%', $srcfile, $tmpstr );
							$tmpstr = str_replace ( '%dst%', $dstfile, $tmpstr );
							echo $tmpstr;
						}

						if (copy ( $srcfile, $dstfile )) {
							touch ( $dstfile, filemtime ( $srcfile ) );
							$num ++;

							if ($verbose) {
								echo JText::_('COM_KUNENA_COPY_OK');
							}
						} else {
							echo "" . JText::_('COM_KUNENA_DIRCOPERR') . " '$srcfile' " . JText::_('COM_KUNENA_DIRCOPERR1') . "";
						}
					}
				} else if (is_dir ( $srcfile )) {
					$num += dircopy ( $srcfile, $dstfile, $verbose );
				}
			}
		}

		closedir ( $curdir );
	}

	return $num;
}

//===============================
//   smiley functions
//===============================
//
// Read a listing of uploaded smilies for use in the add or edit smiley code...
//
function collect_smilies_ranks($path) {
  $smiley_rank_images = JFolder::Files($path,false,false,false,array('index.php'));
  return $smiley_rank_images;
}

function showsmilies($option) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_app = & JFactory::getApplication ();

	$limit = $kunena_app->getUserStateFromRequest ( "global.list.limit", 'limit', $kunena_app->getCfg ( 'list_limit' ), 'int' );
	$limitstart = $kunena_app->getUserStateFromRequest ( "{$option}.limitstart", 'limitstart', 0, 'int' );
	$kunena_db->setQuery ( "SELECT COUNT(*) FROM #__fb_smileys" );
	$total = $kunena_db->loadResult ();
	check_dberror ( "Unable to count smileys." );
	if ($limitstart >= $total)
		$limitstart = 0;
	if ($limit == 0 || $limit > 100)
		$limit = 100;

	$kunena_db->setQuery ( "SELECT * FROM #__fb_smileys", $limitstart, $limit );
	$smileytmp = $kunena_db->loadObjectList ();
	check_dberror ( "Unable to load smileys." );

	$smileypath = smileypath ();

	jimport ( 'joomla.html.pagination' );
	$pageNavSP = new JPagination ( $total, $limitstart, $limit );
	html_Kunena::showsmilies ( $option, $smileytmp, $pageNavSP, $smileypath );

}

function editsmiley($option, $id) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_db->setQuery ( "SELECT * FROM #__fb_smileys WHERE id = $id" );

	$smileytmp = $kunena_db->loadAssocList ();
	check_dberror ( "Unable to load smileys." );
	$smileycfg = $smileytmp [0];

	$smileypath = smileypath ();
	$smileypathabs = $smileypath ['abs'];
	$smileypath = $smileypath ['live'];

	$smiley_images = collect_smilies_ranks ($smileypathabs);

	$smiley_edit_img = '';

	$filename_list = "";
	for($i = 0; $i < count ( $smiley_images ); $i ++) {
		if ($smiley_images [$i] == $smileycfg ['location']) {
			$smiley_selected = "selected=\"selected\"";
			$smiley_edit_img = $smileypath . $smiley_images [$i];
		} else {
			$smiley_selected = "";
		}

		$filename_list .= '<option value="' . $smiley_images [$i] . '"' . $smiley_selected . '>' . $smiley_images [$i] . '</option>' . "\n";
	}
	html_Kunena::editsmiley ( $option, $smiley_edit_img, $filename_list, $smileypath, $smileycfg );
}

function newsmiley($option) {
	$kunena_db = &JFactory::getDBO ();

	$smileypath = smileypath ();
	$smileypathabs = $smileypath ['abs'];
	$smileypath = $smileypath ['live'];

	$smiley_images = collect_smilies_ranks ($smileypathabs);

	$filename_list = "";
	for($i = 0; $i < count ( $smiley_images ); $i ++) {
		$filename_list .= '<option value="' . $smiley_images [$i] . '">' . $smiley_images [$i] . '</option>' . "\n";
	}

	html_Kunena::newsmiley ( $option, $filename_list, $smileypath );
}

function savesmiley($option, $id = NULL) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();

	$smiley_code = JRequest::getVar ( 'smiley_code' );
	$smiley_location = JRequest::getVar ( 'smiley_url' );
	$smiley_emoticonbar = (JRequest::getVar ( 'smiley_emoticonbar' )) ? JRequest::getVar ( 'smiley_emoticonbar' ) : 0;

	if (empty ( $smiley_code ) || empty ( $smiley_location )) {
		$task = ($id == NULL) ? 'newsmiley' : 'editsmiley&id=' . $id;
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=" . $task, JText::_('COM_KUNENA_MISSING_PARAMETER') );
		$kunena_app->close ();
	}

	$kunena_db->setQuery ( "SELECT * FROM #__fb_smileys" );

	$smilies = $kunena_db->loadAssocList ();
	check_dberror ( "Unable to load smileys." );
	foreach ( $smilies as $value ) {
		if (in_array ( $smiley_code, $value ) && ! ($value ['id'] == $id)) {
			$task = ($id == NULL) ? 'newsmiley' : 'editsmiley&id=' . $id;
			$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=" . $task, JText::_('COM_KUNENA_CODE_ALLREADY_EXITS') );
			$kunena_app->close ();
		}

	}

	if ($id == NULL) {
		$kunena_db->setQuery ( "INSERT INTO #__fb_smileys SET code = '$smiley_code', location = '$smiley_location', emoticonbar = '$smiley_emoticonbar'" );
	} else {
		$kunena_db->setQuery ( "UPDATE #__fb_smileys SET code = '$smiley_code', location = '$smiley_location', emoticonbar = '$smiley_emoticonbar' WHERE id = $id" );
	}

	$kunena_db->query ();
	check_dberror ( "Unable to save smiley." );

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showsmilies", JText::_('COM_KUNENA_SMILEY_SAVED') );
}

function deletesmiley($option, $cid) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();

	$cids = implode ( ',', $cid );

	if ($cids) {
		$kunena_db->setQuery ( "DELETE FROM #__fb_smileys WHERE id IN ($cids)" );
		$kunena_db->query ();
		check_dberror ( "Unable to delete smiley." );
	}

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showsmilies", JText::_('COM_KUNENA_SMILEY_DELETED') );
}

function smileypath() {
	$kunena_config = & CKunenaConfig::getInstance ();

	if (is_dir ( KUNENA_PATH_TEMPLATE . DS . $kunena_config->template . '/images/' . KUNENA_LANGUAGE . '/emoticons' )) {
		$smiley_live_path = JURI::root () . '/components/com_kunena/template/' . $kunena_config->template . '/images/' . KUNENA_LANGUAGE . '/emoticons/';
		$smiley_abs_path = KUNENA_PATH_TEMPLATE . DS . $kunena_config->template . '/images/' . KUNENA_LANGUAGE . '/emoticons';
	} else {
		$smiley_live_path = KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'images/' . KUNENA_LANGUAGE . '/emoticons/';
		$smiley_abs_path = KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'images/' . KUNENA_LANGUAGE . '/emoticons';
	}

	$smileypath ['live'] = $smiley_live_path;
	$smileypath ['abs'] = $smiley_abs_path;

	return $smileypath;
}
//===============================
//  FINISH smiley functions
//===============================


//===============================
// Rank Administration
//===============================
//Dan Syme/IGD - Ranks Management


function showRanks($option) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();

	$order = JRequest::getVar ( 'order', '' );
	$limit = $kunena_app->getUserStateFromRequest ( "global.list.limit", 'limit', $kunena_app->getCfg ( 'list_limit' ), 'int' );
	$limitstart = $kunena_app->getUserStateFromRequest ( "{$option}.limitstart", 'limitstart', 0, 'int' );
	$kunena_db->setQuery ( "SELECT COUNT(*) FROM #__fb_ranks" );
	$total = $kunena_db->loadResult ();
	check_dberror ( "Unable to count ranks." );
	if ($limitstart >= $total)
		$limitstart = 0;
	if ($limit == 0 || $limit > 100)
		$limit = 100;

	$kunena_db->setQuery ( "SELECT * FROM #__fb_ranks", $limitstart, $limit );
	$ranks = $kunena_db->loadObjectList ();
	check_dberror ( "Unable to load ranks." );

	$rankpath = rankpath ();

	jimport ( 'joomla.html.pagination' );
	$pageNavSP = new JPagination ( $total, $limitstart, $limit );
	html_Kunena::showRanks ( $option, $ranks, $pageNavSP, $order, $rankpath );

}

function rankpath() {
	/*
	$kunena_config =& CKunenaConfig::getInstance();

    if (is_dir(JURI::root() . '/components/com_kunena/template/'.$kunena_config->template.'/images/'.KUNENA_LANGUAGE.'/ranks')) {
        $rank_live_path = JURI::root() . '/components/com_kunena/template/'.$kunena_config->template.'/images/'.KUNENA_LANGUAGE.'/ranks/';
        $rank_abs_path = 	KUNENA_PATH_TEMPLATE .DS. $kunena_config->template.'/images/'.KUNENA_LANGUAGE.'/ranks';
    }
    else {
        $rank_live_path = JURI::root() . '/components/com_kunena/template/default/images/'.KUNENA_LANGUAGE.'/ranks/';
        $rank_abs_path = 	KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'images/'.KUNENA_LANGUAGE.'/ranks';
    }

    $rankpath['live'] = $rank_live_path;
    $rankpath['abs'] = $rank_abs_path;
*/
	$rankpath ['live'] = KUNENA_URLRANKSPATH;
	$rankpath ['abs'] = KUNENA_ABSRANKSPATH;

	return $rankpath;

}

function newRank($option) {
	$kunena_db = &JFactory::getDBO ();

	$rankpath = rankpath ();
	$rankpathabs = $rankpath ['abs'];
	$rankpath = $rankpath ['live'];

	$rank_images = collect_smilies_ranks($rankpathabs);

	$filename_list = "";
	$i = 0;
	foreach ( $rank_images as $id => $row ) {
		$filename_list .= '<option value="' . $rank_images [$id] . '">' . $rank_images [$id] . '</option>' . "\n";
	}

	html_Kunena::newRank ( $option, $filename_list, $rankpath );
}

function deleteRank($option, $cid = null) {
	$kunena_db = &JFactory::getDBO ();
	$kunena_app = & JFactory::getApplication ();

	$cids = implode ( ',', $cid );
	if ($cids) {
		$kunena_db->setQuery ( "DELETE FROM #__fb_ranks WHERE rank_id IN ($cids)" );
		$kunena_db->query ();
		check_dberror ( "Unable to delete rank." );
	}

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=ranks", JText::_('COM_KUNENA_RANK_DELETED') );
}

function saveRank($option, $id = NULL) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();

	$rank_title = JRequest::getVar ( 'rank_title' );
	$rank_image = JRequest::getVar ( 'rank_image' );
	$rank_special = JRequest::getVar ( 'rank_special' );
	$rank_min = JRequest::getVar ( 'rank_min' );

	if (empty ( $rank_title ) || empty ( $rank_image )) {
		$task = ($id == NULL) ? 'newRank' : 'editRank&id=' . $id;
		$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=" . $task, JText::_('COM_KUNENA_MISSING_PARAMETER') );
		$kunena_app->close ();
	}

	$kunena_db->setQuery ( "SELECT * FROM #__fb_ranks" );
	$ranks = $kunena_db->loadAssocList ();
	check_dberror ( "Unable to load ranks." );
	foreach ( $ranks as $value ) {
		if (in_array ( $rank_title, $value ) && ! ($value ['rank_id'] == $id)) {
			$task = ($id == NULL) ? 'newRank' : 'editRank&id=' . $id;
			$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=" . $task, JText::_('COM_KUNENA_RANK_ALLREADY_EXITS') );
			$kunena_app->close ();
		}
	}

	if ($id == NULL) {
		$kunena_db->setQuery ( "INSERT INTO #__fb_ranks SET rank_title = '$rank_title', rank_image = '$rank_image', rank_special = '$rank_special', rank_min = '$rank_min'" );
	} else {
		$kunena_db->setQuery ( "UPDATE #__fb_ranks SET rank_title = '$rank_title', rank_image = '$rank_image', rank_special = '$rank_special', rank_min = '$rank_min' WHERE rank_id = $id" );
	}
	$kunena_db->query ();
	check_dberror ( "Unable to save ranks." );

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=ranks", JText::_('COM_KUNENA_RANK_SAVED') );
}

function editRank($option, $id) {
	$kunena_db = &JFactory::getDBO ();

	$kunena_db->setQuery ( "SELECT * FROM #__fb_ranks WHERE rank_id = '$id'" );
	$ranks = $kunena_db->loadObjectList ();
	check_dberror ( "Unable to load ranks." );

	$path = rankpath ();
	$pathabs = $path ['abs'];
	$path = $path ['live'];

	$rank_images = collect_smilies_ranks($pathabs);

	$edit_img = $filename_list = '';

	foreach ( $ranks as $row ) {
		foreach ( $rank_images as $img ) {
			$image = $path . $img;

			if ($img == $row->rank_image) {
				$selected = ' selected="selected"';
				$edit_img = $path . $img;
			} else {
				$selected = '';
			}

			if (JString::strlen ( $img ) > 255) {
				continue;
			}

			$filename_list .= '<option value="' . kunena_htmlspecialchars ( $img ) . '"' . $selected . '>' . $img . '</option>';
		}
	}

	html_Kunena::editRank ( $option, $edit_img, $filename_list, $path, $row );
}

//===============================
//  FINISH smiley functions
//===============================
// Dan Syme/IGD - Ranks Management

//===============================
// Trash management
//===============================
function showtrashview($option) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();
	$filter_order		= $kunena_app->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'subject', 'cmd' );
	$filter_order_Dir	= $kunena_app->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'asc',			'word' );

	$order = JRequest::getVar ( 'order', '' );
	$limit = $kunena_app->getUserStateFromRequest ( "global.list.limit", 'limit', $kunena_app->getCfg ( 'list_limit' ), 'int' );
	$limitstart = $kunena_app->getUserStateFromRequest ( "{$option}.limitstart", 'limitstart', 0, 'int' );
	$kunena_db->setQuery ( "SELECT COUNT(*) FROM #__fb_messages WHERE hold=2" );
	$total = $kunena_db->loadResult ();
	check_dberror ( "Unable to count deleted messages." );
	if ($limitstart >= $total)
		$limitstart = 0;
	if ($limit == 0 || $limit > 100)
		$limit = 100;

	$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

	$query = 'SELECT a.*, b.name AS cats_name, c.username FROM #__fb_messages AS a
	INNER JOIN #__fb_categories AS b ON a.catid=b.id
	LEFT JOIN #__users AS c ON a.userid=c.id
	WHERE hold=2'.$orderby;
	$kunena_db->setQuery ( $query, $limitstart, $limit );
	$trashitems = $kunena_db->loadObjectList ();
	check_dberror ( "Unable to load messages." );

	// table ordering
	$lists['order_Dir']	= $filter_order_Dir;
	$lists['order']		= $filter_order;

	jimport ( 'joomla.html.pagination' );
	$pageNavSP = new JPagination ( $total, $limitstart, $limit );
	html_Kunena::showtrashview ( $option, $trashitems, $pageNavSP, $lists );
}

function trashpurge($option, $cid) {
	$kunena_db = &JFactory::getDBO ();
	$return = JRequest::getCmd( 'return', 'showtrashview', 'post' );

	$cids = implode ( ',', $cid );
	if ($cids) {
		$kunena_db->setQuery ( "SELECT * FROM #__fb_messages WHERE hold=2 AND id IN ($cids)");
		$items = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to load messages." );
	}

	html_Kunena::trashpurge ( $option, $return, $cid, $items );
}

function deleteitemsnow ( $option, $cid ) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();

	$cids = implode ( ',', $cid );
	if ($cids) {
		foreach ($cid as $id ) {
			$kunena_db->setQuery ( "SELECT a.parent, a.id, b.threadid FROM #__fb_messages AS a INNER JOIN #__fb_polls AS b ON b.threadid=a.id WHERE threadid='{$id}'" );
			$mes = $kunena_db->loadObjectList ();
			check_dberror ( "Unable to load online message info." );
			if( !empty($mes[0])) {
				if ($mes[0]->parent == '0' && !empty($mes[0]->threadid) ) {
					//remove of poll
					require_once (KUNENA_PATH_LIB .DS. 'kunena.poll.class.php');
					$poll = new CKunenaPolls();
					$poll->delete_poll($mes[0]->threadid);
				}
			}
		}

		$kunena_db->setQuery ( 'SELECT userid FROM #__fb_messages WHERE id IN (' . $cids. ')' );
		$userids = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to load userids in message." );

		$kunena_db->setQuery ( 'DELETE FROM #__fb_messages WHERE id IN (' .$cids. ')' );
		$kunena_db->query ();
		check_dberror ( "Unable to delete messages." );

		$kunena_db->setQuery ( 'DELETE FROM #__fb_messages_text WHERE mesid IN (' . $cids. ')' );
		$kunena_db->query ();
		check_dberror ( "Unable to delete messages text." );
		foreach ( $userids as $line ) {
			if ($line->userid > 0) {
				$userid_array [] = $line->userid;
			}
		}

		$userids = implode ( ',', $userid_array );

		if (count ( $userid_array ) > 0) {
			$kunena_db->setQuery ( 'UPDATE #__fb_users SET posts=posts-1 WHERE userid IN (' . $userids . ')' );
			$kunena_db->query ();
			check_dberror ( "Unable to update users posts." );
		}


		$kunena_db->setQuery ( 'SELECT filelocation FROM #__fb_attachments WHERE mesid IN (' . $cids . ')' );
		$fileList = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to load attachments." );

		if (count ( $fileList ) > 0) {
			foreach ( $fileList as $fl ) {
				if (file_exists ( $fl->filelocation )) {
					unlink ( $fl->filelocation );
				}
			}

			$kunena_db->setQuery ( 'DELETE FROM #__fb_attachments WHERE mesid IN (' . $cids . ')' );
			$kunena_db->query ();
			check_dberror ( "Unable to delete attachements." );
		}
	}

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showtrashview", JText::_('COM_KUNENA_TRASH_DELETE_DONE') );
}

function trashrestore($option, $cid) {
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();
	$cids = implode ( ',', $cid );
	if ($cids) {
		$kunena_db->setQuery ( "UPDATE #__fb_messages SET hold=0 WHERE id IN ($cids)" );
		$kunena_db->query ();
		check_dberror ( "Unable to restore message(s)." );
	}

	$kunena_app->redirect ( JURI::base () . "index.php?option=$option&task=showtrashview", JText::_('COM_KUNENA_TRASH_RESTORE_DONE') );
}
//===============================
// FINISH trash management
//===============================

//===============================
// Report System
//===============================
function showSystemReport ( $option ) {
	$kunena_app = & JFactory::getApplication ();
	$return = JRequest::getCmd( 'return', 'showsystemreport', 'post' );
	$report = generateSystemReport ();
	html_Kunena::showSystemReport ( $option, $report );
}

function generateSystemReport () {
	$kunena_config =& CKunenaConfig::getInstance();
	$kunena_app = & JFactory::getApplication ();
	$kunena_db = &JFactory::getDBO ();
	$JVersion = new JVersion();
	$jversion = $JVersion->PRODUCT .' '. $JVersion->RELEASE .'.'. $JVersion->DEV_LEVEL .' '. $JVersion->DEV_STATUS.' [ '.$JVersion->CODENAME .' ] '. $JVersion->RELDATE .' '. $JVersion->RELTIME .' '. $JVersion->RELTZ;
	$JConfig = JFactory::getConfig();
	if($kunena_app->getCfg('legacy' )) {
		$jconfig_legacy = '[color=#FF0000]Enabled[/color]';
	} else {
		$jconfig_legacy = 'Disabled';
	}
	if($kunena_app->getCfg('ftp_enable' )) {
		$jconfig_ftp = 'Enabled';
	} else {
		$jconfig_ftp = 'Disabled';
	}
	if($kunena_app->getCfg('sef' )) {
		$jconfig_sef = 'Enabled';
	} else {
		$jconfig_sef = 'Disabled';
	}
	if($kunena_app->getCfg('sef_rewrite' )) {
		$jconfig_sef_rewrite = 'Enabled';
	} else {
		$jconfig_sef_rewrite = 'Disabled';
	}
	if(!JUtility::isWinOS()) {
		if (!file_exists(JPATH_ROOT. DS. '.htaccess')) {
			$htaccess = 'Exists';
		} else {
			$htaccess = 'Missing';
		}
	} else {
		$htaccess = 'Cannot test on windows system';
	}
	if(ini_get('register_globals')) {
		$register_globals = '[u]register_globals:[/u] [color=#FF0000]On[/color]';
	} else {
		$register_globals = '[u]register_globals:[/u] Off';
	}
	if(ini_get('safe_mode')) {
		$safe_mode = '[u]safe_mode:[/u] [color=#FF0000]On[/color]';
	} else {
		$safe_mode = '[u]safe_mode:[/u] Off';
	}
	if(extension_loaded('mbstring')) {
		$mbstring = '[u]mbstring:[/u] Enabled';
	} else {
		$mbstring = '[u]mbstring:[/u] [color=#FF0000]Not installed[/color]';
	}
	if(extension_loaded('gd')) {
		$gd_info = gd_info ();
		$gd_support = '[u]GD:[/u] '.$gd_info['GD Version'] ;
	} else {
		$gd_support = '[u]GD:[/u] [color=#FF0000]Not installed[/color]';
	}
	$maxExecTime = ini_get('max_execution_time');
	$maxExecMem = ini_get('memory_limit');
	$fileuploads = ini_get('upload_max_filesize');
	$kunenaVersionInfo = CKunenaVersion::versionArray ();
	$kunena_integration_type = '';
	switch ($kunena_config->fb_profile) {
    case 'fb':
        $kunena_integration_type = 'Kunena';
        break;
    case 'cb':
        $kunena_integration_type = 'Community Builder';
        break;
    case 'aup':
        $kunena_integration_type = 'Alpha User Points';
        break;
   	case 'jomsocial':
        $kunena_integration_type = 'Jomsocial';
        break;
	}
	if($kunena_config->sef) {
		$Ksef = 'Enabled';
	}else {
		$Ksef = 'Disabled';
	}
	if($kunena_config->sefcats) {
		$Ksefcats = 'Enabled';
	}else {
		$Ksefcats = 'Disabled';
	}
	if($kunena_config->sefutf8) {
		$Ksefutf8 = 'Enabled';
	}else {
		$Ksefutf8 = 'Disabled';
	}
	$databasecollation = $kunena_db->getCollation();
    $report = '[mod][quote][b]Joomla! version:[/b] '.$jversion.' [b]Platform:[/b] '.$_SERVER['SERVER_SOFTWARE'].' ('
	    .$_SERVER['SERVER_NAME'].') [b]PHP version:[/b] '.phpversion().' | '.$safe_mode.' | '.$register_globals.' | '.$mbstring
	    .' | '.$gd_support.' | [b]MySQL version:[/b] '.mysql_get_server_info().'[/quote][/mod]'
		.'[quote][b]Legacy mode:[/b] '.$jconfig_legacy.' | [b]Joomla! SEF:[/b] '.$jconfig_sef.' | [b]Joomla! SEF rewrite:[/b] '
	    .$jconfig_sef_rewrite.' | [b]FTP layer:[/b] '.$jconfig_ftp.' | [b]htaccess:[/b] '.$htaccess
	    .' | [b]PHP environment:[/b] [u]Max execution time:[/u] '.$maxExecTime.' seconds | [u]Max execution memory:[/u] '
	    .$maxExecMem.' | [u]Max file upload:[/u] '.$fileuploads.' | [u]Database collation:[/u]
		'.$databasecollation.'| [b]Kunena:[/b] [u]Installed version:[/u] '.$kunenaVersionInfo->version.' | [u]Build:[/u] '
	    .$kunenaVersionInfo->build.' | [u]Version name:[/u] '.$kunenaVersionInfo->versionname.' | [u]Kunena integration type:[/u] '
	    .$kunena_integration_type.' | [u]Kunena sef:[/u] '.$Ksef.' | [u]Kunena sefcats:[/u] '.$Ksefcats.' | [u]Kunena sefutf8:[/u] '
	    .$Ksefutf8.'[/quote]';
    return $report;
}

//===============================
// FINISH report system
//===============================

function KUNENA_GetAvailableModCats($catids) {
	$kunena_db = &JFactory::getDBO ();
	$list = JJ_categoryArray ( 1 );
	$this_treename = '';
	$catid = 0;

	foreach ( $list as $item ) {
		if ($this_treename) {
			if ($item->id != $catid && JString::strpos ( $item->treename, $this_treename ) === false) {
				$options [] = JHTML::_ ( 'select.option', $item->id, $item->treename );
			}
		} else {
			if ($item->id != $catid) {
				$options [] = JHTML::_ ( 'select.option', $item->id, $item->treename );
			} else {
				$this_treename = stripslashes ( $item->treename ) . "/";
			}
		}
	}
	$parent = JHTML::_ ( 'select.genericlist', $options, 'catid[]', 'class="inputbox fbs"  multiple="multiple"   id="FB_AvailableForums" ', 'value', 'text', $catids );
	return $parent;
}

// Grabs gd version


function KUNENA_gdVersion() {
	// Simplified GD Version check
	if (! extension_loaded ( 'gd' )) {
		return;
	}

	$phpver = JString::substr ( phpversion (), 0, 3 );
	// gd_info came in at 4.3
	if ($phpver < 4.3)
		return - 1;

	if (function_exists ( 'gd_info' )) {
		$ver_info = gd_info ();
		preg_match ( '/\d/', $ver_info ['GD Version'], $match );
		$gd_ver = $match [0];
		return $match [0];
	} else {
		return;
	}
}
?>
