<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
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

// Show error message if J!1.5 Legacy Mode is not turned on
if (!defined ('_VALID_MOS') && defined ('_JEXEC'))
{
	$mainframe->enqueueMessage('Legacy Mode has been switched off!', 'error');
	$mainframe->enqueueMessage('Because of the above errors your Forum is now Offline and Forum Administration has been disabled.', 'notice');
	$mainframe->enqueueMessage('Please enable Legacy Mode from Extensions / Plugin Manager / System - Legacy.');
	return;
}
// Dont allow direct linking
defined ('_VALID_MOS') or die('Kunena Forum cannot be run without Joomla!');

// Kill notices (we have many..)
error_reporting (E_ALL ^ E_NOTICE);

global $mainframe;

//Get right Language file
if (file_exists($mainframe->getCfg('absolute_path') . '/administrator/components/com_kunena/language/kunena.' . $mainframe->getCfg('lang') . '.php')) {
    include ($mainframe->getCfg('absolute_path') . '/administrator/components/com_kunena/language/kunena.' . $mainframe->getCfg('lang') . '.php');
}
else {
    include ($mainframe->getCfg('absolute_path') . '/administrator/components/com_kunena/language/kunena.english.php');
}

require_once ($mainframe->getCfg("absolute_path") . "/components/com_kunena/lib/kunena.debug.php");
require_once ($mainframe->getCfg("absolute_path") . "/components/com_kunena/lib/kunena.config.class.php");

global $fbConfig, $kunenaProfile;
$fbConfig =& CKunenaConfig::getInstance();
$fbConfig->load();

// Class structure should be used after this and all the common task should be moved to this class
require_once ($mainframe->getCfg("absolute_path") . "/components/com_kunena/class.kunena.php");
require_once ($mainframe->getPath('admin_html'));

$kn_tables = CKunenaTables::getInstance();
if ($kn_tables->installed() === false) {
	if (CKunenaTools::isJoomla15()) {
		$mainframe->enqueueMessage(_KUNENA_ERROR_INCOMPLETE_ERROR, 'error');
		$mainframe->enqueueMessage(_KUNENA_ERROR_INCOMPLETE_OFFLINE, 'notice');
		$mainframe->enqueueMessage(_KUNENA_ERROR_INCOMPLETE_REASONS);
		$mainframe->enqueueMessage(_KUNENA_ERROR_INCOMPLETE_1);
		$mainframe->enqueueMessage(_KUNENA_ERROR_INCOMPLETE_2);
		$mainframe->enqueueMessage(_KUNENA_ERROR_INCOMPLETE_3);
		$mainframe->enqueueMessage(_KUNENA_ERROR_INCOMPLETE_SUPPORT.' <a href="http://www.kunena.com">www.kunena.com</a>');
	}
	else
	{
		echo '<div style="background: #E6C0C0; border: #DE7A7B 3px solid;"><h2>'._KUNENA_ERROR_INCOMPLETE_ERROR.'</h2>'
			.'<p style="font-weight: bold; color: #CC0000;">'._KUNENA_ERROR_INCOMPLETE_OFFLINE.'</p>'
			.'<p>'._KUNENA_ERROR_INCOMPLETE_REASONS.'</p>'
			.'<p>'._KUNENA_ERROR_INCOMPLETE_1.'</p>'
			.'<p>'._KUNENA_ERROR_INCOMPLETE_2.'</p>'
			.'<p>'._KUNENA_ERROR_INCOMPLETE_3.'</p>'
			.'<p>'._KUNENA_ERROR_INCOMPLETE_SUPPORT.' <a href="http://www.kunena.com">www.kunena.com</a></p></div><br />';
	}
}
else
{

// Detect errors in CB integration
if (is_object($kunenaProfile)) 
{
	$kunenaProfile->enqueueErrors();
	//$kunenaProfile->close();
}
	
$cid = mosGetParam($_REQUEST, 'cid', array ( 0 ));

if (!is_array($cid)) {
    $cid = array ( 0 );
}

$uid = mosGetParam($_REQUEST, 'uid', array ( 0 ));

if (!is_array($uid)) {
    $uid = array ( $uid );
}

// ERROR: global scope mix
global $order;
$order = mosGetParam($_REQUEST, 'order');

// initialise some request directives (specifically for J1.5 compatibility)
$no_html = intval(mosGetParam($_REQUEST, 'no_html', 0));
$id = intval(mosGetParam($_REQUEST, 'id', 0));

$pt_stop = "0";

if (!$no_html)
{
	html_Kunena::showFbHeader();
}

switch ($task)
{
    case "installfb":
        $mode = mosGetParam($_REQUEST, "mode", 1);

        com_install_Kunena ($mode);
        break;

    case "new":
        editForum(0, $option);

        break;

    case "edit":
        editForum($cid[0], $option);

        break;

    case "edit2":
        editForum($uid[0], $option);

        break;

    case "save":
        saveForum ($option);

        break;

    case "cancel":
        cancelForum ($option);

        break;

    case "publish":
        publishForum($cid, 1, $option);

        break;

    case "unpublish":
        publishForum($cid, 0, $option);

        break;

    case "remove":
        deleteForum($cid, $option);

        break;

    case "orderup":
        orderForum($cid[0], -1, $option);

        break;

    case "orderdown":
        orderForum($cid[0], 1, $option);

        break;

    case "showconfig":
        showConfig ($option);

        break;

    case "saveconfig":
        saveConfig ($option);

        break;

    case "newmoderator":
        newModerator($option, $id);

        break;

    case "addmoderator":
        addModerator($option, $id, $cid, 1);

        break;

    case "removemoderator":
        addModerator($option, $id, $cid, 0);

        break;

    case "showprofiles":
        showProfiles($database, $option, $mosConfig_lang, $order);

        break;

    case "profiles":
        showProfiles($database, $option, $mosConfig_lang, $order);

        break;

    case "userprofile":
        editUserProfile($uid);

        break;

    case "showinstructions":
        showInstructions($database, $option, $mosConfig_lang);

        break;

    case "showCss":
        showCss ($option);

        break;

    case "saveeditcss":
        $file = mosGetParam($_REQUEST, "file", 1);
        $csscontent = mosGetParam($_REQUEST, "csscontent", 1);

        saveCss($file, $csscontent, $option);

        break;

    case "instructions":
        showInstructions($database, $option, $mosConfig_lang);

        break;

    case "saveuserprofile":
        saveUserProfile ($option);

        break;

    case "upgradetables":
        upgradeTables ($option);

        break;

    case "pruneforum":
        pruneforum($database, $option);

        break;

    case "doprune":
        doprune($database, $option);

        break;

    case "dousersync":
        doUserSync($database, $option);

        break;

    case "syncusers":
        syncusers($database, $option);

        break;

    case "browseImages":
        browseUploaded($database, $option, 1);

        break;

    case "browseFiles":
        browseUploaded($database, $option, 0);

        break;

    case "replaceImage":
        replaceImage($database, $option, mosGetParam($_REQUEST, 'img', ''), $OxP);

        break;

    case "deleteFile":
        deleteFile($database, $option, mosGetParam($_REQUEST, 'fileName', ''));

        break;

    case "showAdministration":
        showAdministration ($option);

        break;

    case 'recount':
    	CKunenaTools::reCountBoards();
    	// Also reset the name info stored with messages
    	CKunenaTools::updateNameInfo();
    	mosRedirect("index2.php?option=com_kunena", _KUNENA_RECOUNTFORUMS_DONE);
        break;

	case "showsmilies":
    	showsmilies($option);

        break;

    case "editsmiley":
    	editsmiley($option, $cid[0]);

        break;

    case "savesmiley":
    	savesmiley($option, $id);

        break;

    case "deletesmiley":
    	deletesmiley($option, $cid);

        break;

    case "newsmiley":
    	newsmiley($option);

        break;

    case 'ranks':
    	showRanks($option);

    	break;

    case "editRank":
    	editRank($option, $cid[0]);

        break;

    case "saveRank":
    	saveRank($option, $id);

        break;

    case "deleteRank":
    	deleteRank($option, $cid);

        break;

    case "newRank":
    	newRank($option);

        break;

    case 'cpanel':
    default:
        html_Kunena::controlPanel();
        break;
}

} // ENDIF: is installed

html_Kunena::showFbFooter();
//function showAdministration( $option,$joomla1_5 ) {
function showAdministration($option)
{
    global $database, $mainframe;
    global $mosConfig_lang;

    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
    $levellimit = $mainframe->getUserStateFromRequest("view{$option}limit", 'levellimit', 10);
    /*
    * danial
    */
    $database->setQuery("SELECT a.*, a.name AS category, u.name AS editor, g.name AS groupname, h.name AS admingroup"
    . "\nFROM #__fb_categories AS a"
    . "\nLEFT JOIN #__users AS u ON u.id = a.checked_out"
    . "\nLEFT JOIN #__core_acl_aro_groups AS g ON g.".((CKunenaTools::isJoomla15())?"":"group_")."id = a.pub_access"
    . "\nLEFT JOIN #__core_acl_aro_groups AS h ON h.".((CKunenaTools::isJoomla15())?"":"group_")."id = a.admin_access"
    . "\n GROUP BY a.id"
    . "\n ORDER BY a.ordering, a.name");

    $rows = $database->loadObjectList();
    	check_dberror("Unable to load categories.");

    // establish the hierarchy of the categories
    $children = array ();

    // first pass - collect children
    foreach ($rows as $v)
    {
        $pt = $v->parent;
        $list = isset($children[$pt]) ? $children[$pt] : array ();
        array_push($list, $v);
        $children[$pt] = $list;
    }

    // second pass - get an indent list of the items
    $list = fbTreeRecurse(0, '', array (), $children, max(0, $levellimit - 1));
    $total = count($list);
    if ($limitstart >= $total) $limitstart = 0;
    require_once ($GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total, $limitstart, $limit);
    $levellist = mosHTML::integerSelectList(1, 20, 1, 'levellimit', 'size="1" onchange="document.adminForm.submit();"', $levellimit);
    // slice out elements based on limits
    $list = array_slice($list, $pageNav->limitstart, $pageNav->limit);
    /**
    *@end
    */

    html_Kunena::showAdministration($list, $pageNav, $option);
}



//---------------------------------------
//-E D I T   F O R U M-------------------
//---------------------------------------
function editForum($uid, $option)
{
    global $database, $my, $acl;
    $row = new fbForum($database);
    // load the row from the db table
    $row->load($uid);

    //echo "<pre>"; print_r ($row); echo "</pre>";
    if ($uid)
    {
        $row->checkout($my->id);
    }
    else
    {
        // initialise new record
        $row->parent = 0;
        $row->published = 0;
        $row->ordering = 9999;
    }

    $categoryList = showCategories($row->parent, "parent", "", "4");
    // make a standard yes/no list
    $yesno = array ();
    $yesno[] = mosHTML::makeOption('0', _ANN_NO);
    $yesno[] = mosHTML::makeOption('1', _ANN_YES);

	// make a standard no/yes list
    $noyes = array ();
    $noyes[] = mosHTML::makeOption('1', _ANN_YES);
    $noyes[] = mosHTML::makeOption('0', _ANN_NO);
    //Create all kinds of Lists
    $lists = array ();
    $accessLists = array ();
    //create custom group levels to include into the public group selectList
    $pub_groups = array ();
    $pub_groups[] = mosHTML::makeOption(0, _KUNENA_EVERYBODY);
    $pub_groups[] = mosHTML::makeOption(-1, _KUNENA_ALLREGISTERED);
    $pub_groups = array_merge($pub_groups, $acl->get_group_children_tree(null, _KUNENA_REGISTERED, true));
    //create admin groups array for use in selectList:
    $adm_groups = array ();
    $adm_groups = array_merge($adm_groups, $acl->get_group_children_tree(null, _KUNENA_PUBLICBACKEND, true));
    //create the access control list
    $accessLists['pub_access'] = mosHTML::selectList($pub_groups, 'pub_access', 'class="inputbox" size="4"', 'value', 'text', $row->pub_access);
    $accessLists['admin_access'] = mosHTML::selectList($adm_groups, 'admin_access', 'class="inputbox" size="4"', 'value', 'text', $row->admin_access);
    $lists['pub_recurse'] = mosHTML::selectList($yesno, 'pub_recurse', 'class="inputbox" size="1"', 'value', 'text', $row->pub_recurse);
    $lists['admin_recurse'] = mosHTML::selectList($yesno, 'admin_recurse', 'class="inputbox" size="1"', 'value', 'text', $row->admin_recurse);
    $lists['forumLocked'] = mosHTML::selectList($yesno, 'locked', 'class="inputbox" size="1"', 'value', 'text', $row->locked);
    $lists['forumModerated'] = mosHTML::selectList($noyes, 'moderated', 'class="inputbox" size="1"', 'value', 'text', $row->moderated);
    $lists['forumReview'] = mosHTML::selectList($yesno, 'review', 'class="inputbox" size="1"', 'value', 'text', $row->review);
    //get a list of moderators, if forum/category is moderated
    $moderatorList = array ();

    if ($row->moderated == 1)
    {
        $database->setQuery("SELECT * FROM #__fb_moderation AS a LEFT JOIN #__users as u ON a.userid=u.id where a.catid=$row->id");
        $moderatorList = $database->loadObjectList();
        	check_dberror("Unable to load moderator list.");
    }

    html_Kunena::editForum($row, $categoryList, $moderatorList, $lists, $accessLists, $option);
}

function saveForum($option)
{
    global $database, $my;
    $row = new fbForum($database);

    if (!$row->bind($_POST))
    {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if (!$row->check())
    {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    if (!$row->store())
    {
        echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $row->checkin();
    $row->updateOrder("parent='$row->parent'");

    $database->setQuery("UPDATE #__fb_sessions SET allowed='na'");
	$database->query() or trigger_dberror("Unable to update sessions.");
    
	mosRedirect ("index2.php?option=$option&task=showAdministration");
}

function publishForum($cid = null, $publish = 1, $option)
{
    global $database, $my;

    if (!is_array($cid) || count($cid) < 1)
    {
        $action = $publish ? 'publish' : 'unpublish';
        echo "<script> alert('" . _KUNENA_SELECTANITEMTO . " $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $cids = implode(',', $cid);
    $database->setQuery("UPDATE #__fb_categories SET published='$publish'" . "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$my->id'))");
    $database->query() or trigger_dberror("Unable to update categories.");

    if (count($cid) == 1)
    {
        $row = new fbForum($database);
        $row->checkin($cid[0]);
    }

	// we must reset fbSession->allowed, when forum record was changed
    $database->setQuery("UPDATE #__fb_sessions SET allowed='na'");
	$database->query() or trigger_dberror("Unable to update sessions.");

    mosRedirect ("index2.php?option=$option&task=showAdministration");
}

function deleteForum($cid = null, $option)
{
    global $database, $my;

    if (!is_array($cid) || count($cid) < 1)
    {
        $action = 'delete';
        echo "<script> alert('" . _KUNENA_SELECTANITEMTO . " $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $cids = implode(',', $cid);
    $database->setQuery("DELETE FROM #__fb_categories" . "\nWHERE id IN ($cids) AND (checked_out=0 OR (checked_out='$my->id'))");
    $database->query() or trigger_dberror("Unable to delete categories.");

    $database->setQuery("SELECT id, parent FROM #__fb_messages where catid in ($cids)");
    $mesList = $database->loadObjectList();
    	check_dberror("Unable to load messages.");

    if (count($mesList) > 0)
    {
    	foreach ($mesList as $ml)
    	{
    		$database->setQuery("DELETE FROM #__fb_messages WHERE id = $ml->id");
    		$database->query() or trigger_dberror("Unable to delete messages.");

    		$database->setQuery("DELETE FROM #__fb_messages_text WHERE mesid=$ml->id");
    		$database->query() or trigger_dberror("Unable to delete message text.");

    		//and clear up all subscriptions as well
    		if ($ml->parent == 0)
    		{ //this was a topic message to which could have been subscribed
    			$database->setQuery("DELETE FROM #__fb_subscriptions WHERE thread=$ml->id");
    			$database->query() or trigger_dberror("Unable to delete subscriptions.");
    		}
    	}
    }

	$database->setQuery("UPDATE #__fb_sessions SET allowed='na'");
	$database->query() or trigger_dberror("Unable to update sessions.");
    
	mosRedirect ("index2.php?option=$option&task=showAdministration");
}

function cancelForum($option)
{
    global $database;
    $row = new fbForum($database);
    $row->bind($_POST);
    $row->checkin();
    mosRedirect ("index2.php?option=$option&task=showAdministration");
}

function orderForum($uid, $inc, $option)
{
    global $database;
    $row = new fbForum($database);
    $row->load($uid);
    $row->move($inc, "parent='$row->parent'");
    mosRedirect ("index2.php?option=$option&task=showAdministration");
}

//===============================
// Config Functions
//===============================
function showConfig($option)
{
    global $database;
    global $mosConfig_lang;
    global $mosConfig_admin_template;
    global $mainframe;
    $fbConfig =& CKunenaConfig::getInstance();

    $lists = array ();

    // the default page when entering Kunena
    $defpagelist = array ();

    $defpagelist[] = mosHTML::makeOption('recent', _COM_A_FBDEFAULT_PAGE_RECENT);
    $defpagelist[] = mosHTML::makeOption('my', _COM_A_FBDEFAULT_PAGE_MY);
    $defpagelist[] = mosHTML::makeOption('categories', _COM_A_FBDEFAULT_PAGE_CATEGORIES);

    // build the html select list
    $lists['fbdefaultpage'] = mosHTML::selectList($defpagelist, 'cfg_fbdefaultpage', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->fbdefaultpage);

//Threaded view option removed from Kunena
//    // the default view
//    $list = array ();
//    $list[] = mosHTML::makeOption('flat', _COM_A_FLAT);
//    $list[] = mosHTML::makeOption('threaded', _COM_A_THREADED);
//    // build the html select list
//    $lists['default_view'] = mosHTML::selectList($list, 'cfg_default_view', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->default_view);
    $fbConfig->default_view = 'flat';

    $rsslist = array ();

    $rsslist[] = mosHTML::makeOption('thread', _COM_A_RSS_BY_THREAD);
    $rsslist[] = mosHTML::makeOption('post', _COM_A_RSS_BY_POST);
    // build the html select list
    $lists['rsstype'] = mosHTML::selectList($rsslist, 'cfg_rsstype', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->rsstype);

    $rsshistorylist = array ();

    $rsshistorylist[] = mosHTML::makeOption('week', _COM_A_RSS_HISTORY_WEEK);
    $rsshistorylist[] = mosHTML::makeOption('month', _COM_A_RSS_HISTORY_MONTH);
    $rsshistorylist[] = mosHTML::makeOption('year', _COM_A_RSS_HISTORY_YEAR);
    // build the html select list
    $lists['rsshistory'] = mosHTML::selectList($rsshistorylist, 'cfg_rsshistory', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->rsshistory);

    // source of avatar picture
    $avlist = array ();
    $avlist[] = mosHTML::makeOption('fb', _KUNENA_KUNENA);
    $avlist[] = mosHTML::makeOption('cb', _KUNENA_CB);
    $avlist[] = mosHTML::makeOption('jomsocial', _KUNENA_JOMSOCIAL);
    $avlist[] = mosHTML::makeOption('clexuspm', _KUNENA_CLEXUS);
    // build the html select list
    $lists['avatar_src'] = mosHTML::selectList($avlist, 'cfg_avatar_src', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->avatar_src);
    // private messaging system to use
    $pmlist = array ();
    $pmlist[] = mosHTML::makeOption('no', _COM_A_NO);
    $pmlist[] = mosHTML::makeOption('cb', _KUNENA_CB);
    $pmlist[] = mosHTML::makeOption('jomsocial', _KUNENA_JOMSOCIAL);
    $pmlist[] = mosHTML::makeOption('pms', _KUNENA_MYPMS);
    $pmlist[] = mosHTML::makeOption('clexuspm', _KUNENA_CLEXUS);
    $pmlist[] = mosHTML::makeOption('uddeim', _KUNENA_UDDEIM);
    $pmlist[] = mosHTML::makeOption('jim', _KUNENA_JIM);
    $pmlist[] = mosHTML::makeOption('missus', _KUNENA_MISSUS);
    $lists['pm_component'] = mosHTML::selectList($pmlist, 'cfg_pm_component', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->pm_component);
//redundant    $lists['pm_component'] = mosHTML::selectList($pmlist, 'cfg_pm_component', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->pm_component);
    // Profile select
    $prflist = array ();
    $prflist[] = mosHTML::makeOption('fb', _KUNENA_KUNENA);
    $prflist[] = mosHTML::makeOption('cb', _KUNENA_CB);
    $prflist[] = mosHTML::makeOption('jomsocial', _KUNENA_JOMSOCIAL);
    $prflist[] = mosHTML::makeOption('clexuspm', _KUNENA_CLEXUS);
    $lists['fb_profile'] = mosHTML::selectList($prflist, 'cfg_fb_profile', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->fb_profile);
    // build the html select list
    // make a standard yes/no list
    $yesno = array ();
    $yesno[] = mosHTML::makeOption('0', _COM_A_NO);
    $yesno[] = mosHTML::makeOption('1', _COM_A_YES);
    /* Build the templates list*/
    // This function was modified from the one posted to PHP.net by rockinmusicgv
    // It is available under the readdir() entry in the PHP online manual
    //function get_dirs($directory, $select_name, $selected = "") {
    $listitems[] = mosHTML::makeOption('0', _KUNENA_SELECTTEMPLATE);

    if ($dir = @opendir($mainframe->getCfg('absolute_path') . "/components/com_kunena/template"))
    {
        while (($file = readdir($dir)) !== false)
        {
            if ($file != ".." && $file != ".")
            {
                if (is_dir($mainframe->getCfg('absolute_path') . "/components/com_kunena/template/" . $file))
                {
                    if (!($file[0] == '.') && is_file($mainframe->getCfg('absolute_path') . "/components/com_kunena/template/$file/kunena.forum.css")) {
                        $templatelist[] = $file;
                    }
                    if (!($file[0] == '.') && is_dir($mainframe->getCfg('absolute_path') . "/components/com_kunena/template/$file/images/english")) {
                        $imagesetlist[] = $file;
                    }
                }
            }
        }

        closedir ($dir);
    }

    asort ($templatelist);
    asort ($imagesetlist);
    
    while (list($key, $val) = each($templatelist)) {
		$templatelistitems[] = mosHTML::makeOption($val, $val);
    }
    while (list($key, $val) = each($imagesetlist)) {
		$imagesetlistitems[] = mosHTML::makeOption($val, $val);
    }

	$lists['jmambot'] = mosHTML::selectList($yesno, 'cfg_jmambot', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->jmambot);
    $lists['disemoticons'] = mosHTML::selectList($yesno, 'cfg_disemoticons', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->disemoticons);
    $lists['template'] = mosHTML::selectList($templatelistitems, 'cfg_template', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->template);
    $lists['templateimagepath'] = mosHTML::selectList($imagesetlistitems, 'cfg_templateimagepath', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->templateimagepath);
    $lists['regonly'] = mosHTML::selectList($yesno, 'cfg_regonly', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->regonly);
    $lists['board_offline'] = mosHTML::selectList($yesno, 'cfg_board_offline', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->board_offline);
    $lists['pubwrite'] = mosHTML::selectList($yesno, 'cfg_pubwrite', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->pubwrite);
    $lists['useredit'] = mosHTML::selectList($yesno, 'cfg_useredit', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->useredit);
    $lists['showhistory'] = mosHTML::selectList($yesno, 'cfg_showhistory', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showhistory);
    $lists['joomlastyle'] = mosHTML::selectList($yesno, 'cfg_joomlastyle', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->joomlastyle);
    $lists['showannouncement'] = mosHTML::selectList($yesno, 'cfg_showannouncement', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showannouncement);
    $lists['avataroncat'] = mosHTML::selectList($yesno, 'cfg_avataroncat', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->avataroncat);
    $lists['showlatest'] = mosHTML::selectList($yesno, 'cfg_showlatest', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showlatest);
    $lists['latestsinglesubject'] = mosHTML::selectList($yesno, 'cfg_latestsinglesubject', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->latestsinglesubject);
    $lists['latestreplysubject'] = mosHTML::selectList($yesno, 'cfg_latestreplysubject', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->latestreplysubject);
    $lists['latestshowdate'] = mosHTML::selectList($yesno, 'cfg_latestshowdate', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->latestshowdate);
    $lists['showchildcaticon'] = mosHTML::selectList($yesno, 'cfg_showchildcaticon', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showchildcaticon);
    $lists['latestshowhits'] = mosHTML::selectList($yesno, 'cfg_latestshowhits', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->latestshowhits);
    $lists['showuserstats'] = mosHTML::selectList($yesno, 'cfg_showuserstats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showuserstats);
    $lists['showwhoisonline'] = mosHTML::selectList($yesno, 'cfg_showwhoisonline', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showwhoisonline);
    $lists['showpopsubjectstats'] = mosHTML::selectList($yesno, 'cfg_showpopsubjectstats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showpopsubjectstats);
    $lists['showgenstats'] = mosHTML::selectList($yesno, 'cfg_showgenstats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showgenstats);
    $lists['showpopuserstats'] = mosHTML::selectList($yesno, 'cfg_showpopuserstats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showpopuserstats);
    $lists['allowsubscriptions'] = mosHTML::selectList($yesno, 'cfg_allowsubscriptions', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowsubscriptions);
    $lists['subscriptionschecked'] = mosHTML::selectList($yesno, 'cfg_subscriptionschecked', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->subscriptionschecked);
    $lists['allowfavorites'] = mosHTML::selectList($yesno, 'cfg_allowfavorites', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowfavorites);
    $lists['mailmod'] = mosHTML::selectList($yesno, 'cfg_mailmod', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->mailmod);
    $lists['mailadmin'] = mosHTML::selectList($yesno, 'cfg_mailadmin', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->mailadmin);
    $lists['showemail'] = mosHTML::selectList($yesno, 'cfg_showemail', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showemail);
    $lists['askemail'] = mosHTML::selectList($yesno, 'cfg_askemail', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->askemail);
    $lists['changename'] = mosHTML::selectList($yesno, 'cfg_changename', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->changename);
    $lists['allowavatar'] = mosHTML::selectList($yesno, 'cfg_allowavatar', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowavatar);
    $lists['allowavatarupload'] = mosHTML::selectList($yesno, 'cfg_allowavatarupload', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowavatarupload);
    $lists['allowavatargallery'] = mosHTML::selectList($yesno, 'cfg_allowavatargallery', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowavatargallery);
    $lists['avatar_src'] = mosHTML::selectList($avlist, 'cfg_avatar_src', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->avatar_src);

    $ip_opt[] = mosHTML::makeOption( 'gd2', 'GD2' );
    $ip_opt[] = mosHTML::makeOption( 'gd1', 'GD1' );
    $ip_opt[] = mosHTML::makeOption( 'none', _KUNENA_IMAGE_PROCESSOR_NONE );

    $lists['imageprocessor'] = mosHTML::selectList( $ip_opt, 'cfg_imageprocessor', 'class="inputbox"', 'value', 'text', $fbConfig->imageprocessor );
    $lists['showstats'] = mosHTML::selectList($yesno, 'cfg_showstats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showstats);
    $lists['showranking'] = mosHTML::selectList($yesno, 'cfg_showranking', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showranking);
    $lists['rankimages'] = mosHTML::selectList($yesno, 'cfg_rankimages', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->rankimages);
    $lists['username'] = mosHTML::selectList($yesno, 'cfg_username', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->username);
    $lists['shownew'] = mosHTML::selectList($yesno, 'cfg_shownew', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->shownew);
    $lists['allowimageupload'] = mosHTML::selectList($yesno, 'cfg_allowimageupload', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowimageupload);
    $lists['allowimageregupload'] = mosHTML::selectList($yesno, 'cfg_allowimageregupload', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowimageregupload);
    $lists['allowfileupload'] = mosHTML::selectList($yesno, 'cfg_allowfileupload', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowfileupload);
    $lists['allowfileregupload'] = mosHTML::selectList($yesno, 'cfg_allowfileregupload', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowfileregupload);
    $lists['editmarkup'] = mosHTML::selectList($yesno, 'cfg_editmarkup', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->editmarkup);
    $lists['discussbot'] = mosHTML::selectList($yesno, 'cfg_discussbot', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->discussbot);
    $lists['enablerss'] = mosHTML::selectList($yesno, 'cfg_enablerss', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->enablerss);
    $lists['poststats'] = mosHTML::selectList($yesno, 'cfg_poststats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->poststats);
    $lists['showkarma'] = mosHTML::selectList($yesno, 'cfg_showkarma', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showkarma);
    $lists['enablepdf'] = mosHTML::selectList($yesno, 'cfg_enablepdf', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->enablepdf);
    $lists['enablerulespage'] = mosHTML::selectList($yesno, 'cfg_enablerulespage', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->enablerulespage);
	$lists['rules_infb'] = mosHTML::selectList($yesno, 'cfg_rules_infb', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->rules_infb);
	$lists['enablehelppage'] = mosHTML::selectList($yesno, 'cfg_enablehelppage', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->enablehelppage);
	$lists['help_infb'] = mosHTML::selectList($yesno, 'cfg_help_infb', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->help_infb);
    $lists['enableforumjump'] = mosHTML::selectList($yesno, 'cfg_enableforumjump', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->enableforumjump);
    $lists['userlist_online'] = mosHTML::selectList($yesno, 'cfg_userlist_online', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_online);
    $lists['userlist_avatar'] = mosHTML::selectList($yesno, 'cfg_userlist_avatar', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_avatar);
    $lists['userlist_name'] = mosHTML::selectList($yesno, 'cfg_userlist_name', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_name);
    $lists['userlist_username'] = mosHTML::selectList($yesno, 'cfg_userlist_username', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_username);
    $lists['userlist_group'] = mosHTML::selectList($yesno, 'cfg_userlist_group', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_group);
    $lists['userlist_posts'] = mosHTML::selectList($yesno, 'cfg_userlist_posts', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_posts);
    $lists['userlist_karma'] = mosHTML::selectList($yesno, 'cfg_userlist_karma', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_karma);
    $lists['userlist_email'] = mosHTML::selectList($yesno, 'cfg_userlist_email', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_email);
    $lists['userlist_usertype'] = mosHTML::selectList($yesno, 'cfg_userlist_usertype', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_usertype);
    $lists['userlist_joindate'] = mosHTML::selectList($yesno, 'cfg_userlist_joindate', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_joindate);
    $lists['userlist_lastvisitdate'] = mosHTML::selectList($yesno, 'cfg_userlist_lastvisitdate', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_lastvisitdate);
	$lists['userlist_userhits'] = mosHTML::selectList($yesno, 'cfg_userlist_userhits', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_userhits);
	$lists['usernamechange'] = mosHTML::selectList($yesno, 'cfg_usernamechange', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->usernamechange);
	$lists['reportmsg'] = mosHTML::selectList($yesno, 'cfg_reportmsg', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->reportmsg);
	$lists['captcha'] = mosHTML::selectList($yesno, 'cfg_captcha', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->captcha);
	$lists['mailfull'] = mosHTML::selectList($yesno, 'cfg_mailfull', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->mailfull);
	// New for 1.0.5
	$lists['showspoilertag'] = mosHTML::selectList($yesno, 'cfg_showspoilertag', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showspoilertag);
	$lists['showvideotag'] = mosHTML::selectList($yesno, 'cfg_showvideotag', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showvideotag);
	$lists['showebaytag'] = mosHTML::selectList($yesno, 'cfg_showebaytag', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showebaytag);
	$lists['trimlongurls'] = mosHTML::selectList($yesno, 'cfg_trimlongurls', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->trimlongurls);
	$lists['autoembedyoutube'] = mosHTML::selectList($yesno, 'cfg_autoembedyoutube', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->autoembedyoutube);
	$lists['autoembedebay'] = mosHTML::selectList($yesno, 'cfg_autoembedebay', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->autoembedebay);
	$lists['highlightcode'] = mosHTML::selectList($yesno, 'cfg_highlightcode', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->highlightcode);

	require_once($mainframe->getCfg("absolute_path") . "/components/com_kunena/lib/kunena.timeformat.class.php");
	// Date formats for the site
	$dateformatlist = array ();
	$time = CKunenaTimeformat::internalTime() - 80000;
	$dateformatlist[] = mosHTML::makeOption('none', _KUNENA_OPTION_DATEFORMAT_NONE);
	$dateformatlist[] = mosHTML::makeOption('ago', CKunenaTimeformat::showDate($time, 'ago'));
	$dateformatlist[] = mosHTML::makeOption('datetime_today', CKunenaTimeformat::showDate($time, 'datetime_today'));
	$dateformatlist[] = mosHTML::makeOption('datetime', CKunenaTimeformat::showDate($time, 'datetime'));
	// build the html select list
	$lists['post_dateformat'] = mosHTML::selectList($dateformatlist, 'cfg_post_dateformat', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->post_dateformat);
	$lists['post_dateformat_hover'] = mosHTML::selectList($dateformatlist, 'cfg_post_dateformat_hover', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->post_dateformat_hover);

	// Stats user count
        $stats_countuserslist = array ();
        $stats_countuserslist[] = mosHTML::makeOption('all', _KUNENA_OPTION_STATS_COUNTUSERS_ALL);
        $stats_countuserslist[] = mosHTML::makeOption('registered', _KUNENA_OPTION_STATS_COUNTUSERS_REGISTERED);
        $stats_countuserslist[] = mosHTML::makeOption('forum', _KUNENA_OPTION_STATS_COUNTUSERS_FORUM);
        // build the html select list
        $lists['stats_countusers'] = mosHTML::selectList($stats_countuserslist, 'cfg_stats_countusers', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->stats_countusers);

	$lists['forumtools'] = mosHTML::selectList($yesno, 'cfg_forumtools', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->forumtools);
	$lists['pathway'] = mosHTML::selectList($yesno, 'cfg_pathway', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->pathway);
	$lists['listcat_moderators'] = mosHTML::selectList($yesno, 'cfg_listcat_moderators', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->listcat_moderators);
	$lists['usertopicicons'] = mosHTML::selectList($yesno, 'cfg_usertopicicons', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->usertopicicons);

    html_Kunena::showConfig($fbConfig, $lists, $option);
}

function saveConfig($option)
{
	global $database;
	$fbConfig =& CKunenaConfig::getInstance();

	foreach ($_POST as $postsetting => $postvalue)
    {
        if (strpos($postsetting, 'cfg_') === 0)
        {
        	//remove cfg_ and force lower case
        	$postname = strtolower(substr($postsetting, 4));
            $postvalue = addslashes($postvalue);

            // No matter what got posted, we only store config parameters defined
            // in the config class. Anything else posted gets ignored.
            if(array_key_exists($postname , $fbConfig->GetClassVars()))
            {
            	if (is_numeric($postvalue))
	            {
					eval ("\$fbConfig->".$postname." = ".$postvalue.";");
	            }
	            else
	            {
	            	// Rest is treaded as strings
					eval ("\$fbConfig->".$postname." = '".$postvalue."';");
	            }
            }
            else
            {
            	// This really should not happen if assertions are enable
            	// fail it and display the current scope of variables for debugging.
            	// echo debug_vars(get_defined_vars());
            	trigger_error ('Unknown configuration variable posted.');
            	assert(0);
            }
        }
    }

	$fbConfig->backup();
	$fbConfig->remove();
	$fbConfig->create();

    // Legacy support
    // To enable legacy 3rd party modules to 'see' our config
	global $mainframe;
    $configfile = $mainframe->getCfg('absolute_path') . "/administrator/components/com_kunena/Kunena_config.php";

    $txt = "<?php\n";
    $txt .= "require_once (\$mainframe->getCfg('absolute_path') . '/components/com_kunena/lib/kunena.config.class.php');\n";
    $txt .= "global \$fbConfig;\n";
    $txt .= "\$fbConfig = get_object_vars(CKunenaConfig::getInstance());\n\n";
    $txt .= "?>";

	if (CKunenaTools::isJoomla15())
	{
		jimport('joomla.filesystem.file');
		JFile::write($configfile, $txt);
	}
	else
	{
		if ($fp = fopen($configfile, "w"))
		{
			fputs($fp, $txt, strlen($txt));
			fclose ($fp);
		}
	}
    // end legacy support

	$database->setQuery("UPDATE #__fb_sessions SET allowed='na'");
	$database->query() or trigger_dberror("Unable to update sessions.");
	
	mosRedirect("index2.php?option=$option&task=showconfig", _KUNENA_CONFIGSAVED);
}

function showInstructions($database, $option, $mosConfig_lang) {
    html_Kunena::showInstructions($database, $option, $mosConfig_lang);
}

//===============================
// CSS functions
//===============================
function showCss($option)
{
	global $mainframe;
    $fbConfig =& CKunenaConfig::getInstance();
    $file = $mainframe->getCfg('absolute_path') . "/components/com_kunena/template/" . $fbConfig->template . "/kunena.forum.css";
    $permission = is_writable($file);

    if (!$permission)
    {
        echo "<center><h1><font color=red>" . _KUNENA_WARNING . "</font></h1><br />";
        echo "<b>" . _KUNENA_CFC_FILENAME . ": " . $file . "</b><br />";
        echo "<b>" . _KUNENA_CHMOD1 . "</b></center><br /><br />";
    }

    html_Kunena::showCss($file, $option);
}

function saveCss($file, $csscontent, $option)
{
    $tmpstr = _KUNENA_CSS_SAVE;
    $tmpstr = str_replace("%file%", $file, $tmpstr);
    echo $tmpstr;

    if (is_writable($file) == false)
    {
        echo "<script>alert('" . _KUNENA_TFINW . "')</script>";
        echo "<script>document.location.href='index2.php?option=com_kunena&task=showCss'</script>\n";
    }

    echo "<script>alert('" . _KUNENA_FBCFS . "')</script>";
    echo "<script>document.location.href='index2.php?option=com_kunena&task=showCss'</script>\n";

    if ($fp = fopen($file, "w"))
    {
        fputs($fp, stripslashes($csscontent));
        fclose ($fp);
        mosRedirect("index2.php?option=$option&task=showCss", _KUNENA_CFC_SAVED);
    }
    else {
        mosRedirect("index2.php?option=$option&task=showCss", _KUNENA_CFC_NOTSAVED);
    }
}

//===============================
// Moderator Functions
//===============================
function newModerator($option, $id = null)
{
    global $database;
    //die ("New Moderator");
    //$limit = intval(mosGetParam($_POST, 'limit', 10));
    //$limitstart = intval(mosGetParam($_POST, 'limitstart', 0));
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
    $database->setQuery("SELECT COUNT(*) FROM #__users AS a" . "\n LEFT JOIN #__fb_users AS b" . "\n ON a.id=b.userid where b.moderator=1");
    $database->query() or trigger_dberror('Unable to load moderators w/o limit.');

    $total = $database->loadResult();
	if ($limitstart >= $total) $limitstart = 0;
    $limit_sql = (($limit>0)?" LIMIT $limitstart,$limit":"");

    $database->setQuery("SELECT * FROM #__users AS a" . "\n LEFT JOIN #__fb_users AS b" . "\n ON a.id=b.userid" . "\n WHERE b.moderator=1" . $limit_sql);
    $userList = $database->loadObjectList();
    	check_dberror('Unable to load moderators.');
    $countUL = count($userList);

    require_once ("includes/pageNavigation.php");
    $pageNav = new mosPageNav($total, $limitstart, $limit);
    //$id = intval( mosGetParam( $_POST, 'id') );
    //get forum name
    $forumName = '';
    $database->setQuery("select name from #__fb_categories where id=$id");
    $database->query() or trigger_dberror('Unable to load forum name.');
    $forumName = $database->loadResult();

    //get forum moderators
    $database->setQuery("select userid from #__fb_moderation where catid=$id");
    $moderatorList = $database->loadObjectList();
    	check_dberror('Unable to load moderator.');
    $moderators = 0;
    $modIDs[] = array ();

    if (count($moderatorList) > 0)
    {
        foreach ($moderatorList as $ml)
        {
            $modIDs[] = $ml->userid;
        }

        $moderators = 1;
    }
    else
    {
        $moderators = 0;
    }

    html_Kunena::newModerator($option, $id, $moderators, $modIDs, $forumName, $userList, $countUL, $pageNav);
}

function addModerator($option, $id, $cid = null, $publish = 1)
{
    global $database, $my;
    $numcid = count($cid);
    $action = "";

    if ($publish == 1)
    {
        $action = 'add';
    }
    else {
        $action = 'remove';
    }

    if (!is_array($cid) || count($cid) < 1)
    {
        echo "<script> alert('" . _KUNENA_SELECTMODTO . " $action'); window.history.go(-1);</script>\n";
        exit;
    }

    if ($action == 'add')
    {
        for ($i = 0, $n = count($cid); $i < $n; $i++)
        {
            $database->setQuery("INSERT INTO #__fb_moderation set catid='$id', userid='$cid[$i]'");
            $database->query() or trigger_dberror("Unable to insert moderator.");
        }
    }
    else
    {
        for ($i = 0, $n = count($cid); $i < $n; $i++)
        {
            $database->setQuery("DELETE FROM #__fb_moderation WHERE catid='$id' and userid='$cid[$i]'");
            $database->query() or trigger_dberror("Unable to delete moderator.");
        }
    }

    $row = new fbForum($database);
    $row->checkin($id);
    
    $database->setQuery("UPDATE #__fb_sessions SET allowed='na'");
	$database->query() or trigger_dberror("Unable to update sessions.");
    
    mosRedirect ("index2.php?option=$option&task=edit2&uid=" . $id);
}

//===============================
//   User Profile functions
//===============================
function showProfiles($database, $option, $mosConfig_lang, $order)
{
    global $mainframe;
    //$limit = intval(mosGetParam($_POST, 'limit', 10));
    //$limitstart = intval(mosGetParam($_POST, 'limitstart', 0));
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);

    $search = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    $search = $database->getEscaped(trim(strtolower($search)));
    $where = array ();

    if (isset($search) && $search != "") {
        $where[] = "(u.username LIKE '%$search%' OR u.email LIKE '%$search%' OR u.name LIKE '%$search%')";
    }

    $database->setQuery("SELECT COUNT(*) FROM #__fb_users AS sbu" . "\n LEFT JOIN #__users AS u" . "\n ON sbu.userid=u.id" . (count($where) ? "\nWHERE " . implode(' AND ', $where) : ""));
    $database->query() or trigger_dberror('Unable to load user profiles w/o limits.');
    $total = $database->loadResult();

    if ($limitstart >= $total) $limitstart = 0;
    $limit_sql = (($limit>0)?" LIMIT $limitstart,$limit":"");

    if ($order == 1)
    {
        $database->setQuery(
            "select * from #__fb_users AS sbu" . "\n LEFT JOIN #__users AS u" . "\n ON sbu.userid=u.id " . (count($where) ? "\nWHERE " . implode(' AND ', $where) : "") . "\n ORDER BY sbu.moderator DESC" . $limit_sql);
    }
    else if ($order == 2)
    {
        $database->setQuery("SELECT * FROM #__fb_users AS sbu" . "\n LEFT JOIN #__users AS u " . "\n ON sbu.userid=u.id " . (count($where) ? "\nWHERE " . implode(' AND ', $where) : "") . "\n ORDER BY u.name ASC " . $limit_sql);
    }
    else if ($order < 1)
    {
        $database->setQuery("SELECT * FROM #__fb_users AS sbu " . "\n LEFT JOIN #__users AS u" . "\n ON sbu.userid=u.id " . (count($where) ? "\nWHERE " . implode(' AND ', $where) : "") . "\n ORDER BY sbu.userid" . $limit_sql);
    }

    $profileList = $database->loadObjectList();
    	check_dberror('Unable to load user profiles.');

    $countPL = count($profileList);

    require_once ("includes/pageNavigation.php");
    $pageNavSP = new mosPageNav($total, $limitstart, $limit);
    html_Kunena::showProfiles($option, $mosConfig_lang, $profileList, $countPL, $pageNavSP, $order, $search);
}

function editUserProfile($uid)
{
    global $acl, $database;

    $database->setQuery("SELECT * FROM #__fb_users LEFT JOIN #__users on #__users.id=#__fb_users.userid WHERE userid=$uid[0]");
    $userDetails = $database->loadObjectList();
    	check_dberror('Unable to load user profile.');
    $user = $userDetails[0];

    //Mambo userids are unique, so we don't worry about that
    $prefview = $user->view;
    $ordering = $user->ordering;
    $moderator = $user->moderator;
    $userRank = $user->rank;

    //check to see if this is an administrator
    $result =  '';
    //$database->setQuery("select usertype from #__users where id=$uid[0]");
    //$result=$database->loadResult();
    $result = $acl->getAroGroup($uid[0]);

    //grab all special ranks
    $database->setQuery("SELECT * FROM #__fb_ranks WHERE rank_special = '1'");
    $specialRanks = $database->loadObjectList();
    	check_dberror('Unable to load special ranks.');

    //build select list options
    $yesnoRank[] = mosHTML::makeOption('0', 'No Rank Assigned');
    foreach ($specialRanks as $ranks)
    {
    	$yesnoRank[] = mosHTML::makeOption($ranks->rank_id, $ranks->rank_title);
    }
    //build special ranks select list
    $selectRank = mosHTML::selectList($yesnoRank, 'newrank', 'class="inputbox" size="5"', 'value', 'text', $userRank);

    // make the select list for the view type
    $yesno[] = mosHTML::makeOption('flat', _COM_A_FLAT);
    $yesno[] = mosHTML::makeOption('threaded', _COM_A_THREADED);
    // build the html select list
    $selectPref = mosHTML::selectList($yesno, 'newview', 'class="inputbox" size="2"', 'value', 'text', $prefview);
    // make the select list for the moderator flag
    $yesnoMod[] = mosHTML::makeOption('1', _ANN_YES);
    $yesnoMod[] = mosHTML::makeOption('0', _ANN_NO);
    // build the html select list
    $selectMod = mosHTML::selectList($yesnoMod, 'moderator', 'class="inputbox" size="2"', 'value', 'text', $moderator);
    // make the select list for the moderator flag
    $yesnoOrder[] = mosHTML::makeOption('0', _USER_ORDER_ASC);
    $yesnoOrder[] = mosHTML::makeOption('1', _USER_ORDER_DESC);
    // build the html select list
    $selectOrder = mosHTML::selectList($yesnoOrder, 'neworder', 'class="inputbox" size="2"', 'value', 'text', $ordering);

    //get all subscriptions for this user
    $database->setQuery("select thread from #__fb_subscriptions where userid=$uid[0]");
    $subslist = $database->loadObjectList();
    	check_dberror('Unable to load subscriptions for user.');

    //get all moderation category ids for this user
    $database->setQuery("select catid from #__fb_moderation where userid=". $uid[0]);
    $_modCats = $database->loadResultArray();
    	check_dberror('Unable to moderation category ids for user.');

    foreach ($_modCats as $_v) {
        $__modCats[] = mosHTML::makeOption( $_v );
    }

    $modCats = KUNENA_GetAvailableModCats($__modCats);

    html_Kunena::editUserProfile($user, $subslist, $selectRank, $selectPref, $selectMod, $selectOrder, $uid[0], $modCats);
}

function saveUserProfile($option)
{
    global $database;
    $newview = mosGetParam($_POST, 'newview', 'flat');
    $newrank = mosGetParam($_POST, 'newrank', '');
    $signature = mosGetParam($_POST, 'message', '');
    $deleteSig = mosGetParam($_POST, 'deleteSig', 0);
    $moderator = mosGetParam($_POST, 'moderator', 0);
    $uid = mosGetParam($_POST, 'uid');
    $deleteAvatar = mosGetParam($_POST, 'deleteAvatar', 0);
    $neworder = mosGetParam($_POST, 'neworder', 'asc');
    $modCatids = mosGetParam($_POST, 'catid', array());

    if ($deleteSig == 1) {
        $signature = "";
    }
    $signature = addslashes($signature);

    $avatar = '';
    if ($deleteAvatar == 1) {
        $avatar = ",avatar=''";
    }

    $database->setQuery("UPDATE #__fb_users set signature='$signature', view='$newview',moderator='$moderator', ordering='$neworder', rank='$newrank' $avatar where userid=$uid");
    $database->query() or trigger_dberror("Unable to update signature.");

    //delete all moderator traces before anyway
    $database->setQuery("delete from #__fb_moderation where userid=$uid");
    $database->query() or trigger_dberror("Unable to delete moderator.");

    //if there are moderatored forums, add them all
    if ($moderator == 1)
    {
    	if (count($modCatids) > 0)
    	{
    		foreach ($modCatids as $c)
    		{
                $database->setQuery("INSERT INTO #__fb_moderation set catid='$c', userid='$uid'");
                $database->query() or trigger_dberror("Unable to insert moderator.");
            }
    	}
    }

	$database->setQuery("UPDATE #__fb_sessions SET allowed='na' WHERE userid='$uid'");
	$database->query() or trigger_dberror("Unable to update sessions.");

	mosRedirect ("index2.php?option=com_kunena&task=showprofiles");
}

//===============================
// Prune Forum functions
//===============================
function pruneforum($database, $option)
{
    $forums_list = array ();
    //get forum list; locked forums are excluded from pruning
    $database->setQuery("SELECT a.id as value, a.name as text" . "\nFROM #__fb_categories AS a" . "\nWHERE a.parent != '0'" . "\nAND a.locked != '1'" . "\nORDER BY parent, ordering");
    //get all subscriptions for this user
    $forums_list = $database->loadObjectList();
    	check_dberror("Unable to load unlocked forums.");
    $forumList['forum'] = mosHTML::selectList($forums_list, 'prune_forum', 'class="inputbox" size="4"', 'value', 'text', '');
    html_Kunena::pruneforum($option, $forumList);
}

function doprune($database, $option)
{
    $catid = intval(mosGetParam($_POST, 'prune_forum', -1));
    $deleted = 0;

    if ($catid == -1)
    {
        echo "<script> alert('" . _KUNENA_CHOOSEFORUMTOPRUNE . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $prune_days = intval(mosGetParam($_POST, 'prune_days', 0));
    //get the thread list for this forum
    $database->setQuery("SELECT DISTINCT a.thread AS thread, max(a.time) AS lastpost, c.locked AS locked " . "\n FROM #__fb_messages AS a" . "\n JOIN #__fb_categories AS b ON a.catid=b.id " . "\n JOIN #__fb_messages   AS c ON a.thread=c.thread"
                            . "\n where a.catid=$catid " . "\n and b.locked != 1 " . "\n and a.locked != 1 " . "\n and c.locked != 1 " . "\n and c.parent = 0 " . "\n and c.ordering != 1 " . "\n group by thread");
    $threadlist = $database->loadObjectList();
        check_dberror("Unable to load thread list.");

    // Convert days to seconds for timestamp functions...
    $prune_date = CKunenaTimeformat::internalTime() - ($prune_days * 86400);

    if (count($threadlist) > 0)
    {
        foreach ($threadlist as $tl)
        {
            //check if thread is eligible for pruning
            if ($tl->lastpost < $prune_date)
            {
                //get the id's for all posts belonging to this thread
                $database->setQuery("SELECT id from #__fb_messages WHERE thread=$tl->thread");
                $idlist = $database->loadObjectList();
                        check_dberror("Unable to load thread messages.");

                if (count($idlist) > 0)
                {
                    foreach ($idlist as $id)
                    {
                        //prune all messages belonging to the thread
                        $database->setQuery("DELETE FROM #__fb_messages WHERE id=$id->id");
                        $database->query() or trigger_dberror("Unable to delete messages.");

                        $database->setQuery("DELETE FROM #__fb_messages_text WHERE mesid=$id->id");
                        $database->query() or trigger_dberror("Unable to delete message texts.");

                        //delete all attachments
                        $database->setQuery("SELECT filelocation FROM #__fb_attachments WHERE mesid=$id->id");
                        $fileList = $database->loadObjectList();
                                check_dberror("Unable to load attachments.");

                        if (count($fileList) > 0)
                        {
                            foreach ($fileList as $fl) {
                                unlink ($fl->filelocation);
                            }

                            $database->setQuery("DELETE FROM #__fb_attachments WHERE mesid=$id->id");
                            $database->query() or trigger_dberror("Unable to delete attachments.");
                        }

                        $deleted++;
                    }
                }
            }

            //clean all subscriptions to these deleted threads
            $database->setQuery("DELETE FROM #__fb_subscriptions WHERE thread=$tl->thread");
            $database->query() or trigger_dberror("Unable to delete subscriptions.");
        }
    }

    mosRedirect("index2.php?option=$option&task=pruneforum", "" . _KUNENA_FORUMPRUNEDFOR . " " . $prune_days . " " . _KUNENA_PRUNEDAYS . "; " . _KUNENA_PRUNEDELETED . "" . $deleted . " " . _KUNENA_PRUNETHREADS . "");
}

//===============================
// Sync users
//===============================
function syncusers($database, $option) {
    html_Kunena::syncusers($option);
}

function doUserSync($database, $option)
{
	//reset access rights
	$database->setQuery("UPDATE #__fb_sessions SET allowed='na'");
	$database->query() or trigger_dberror("Unable to update sessions.");

    //get userlist to remove from Kunena users list
    $database->setQuery("SELECT a.userid from #__fb_users as a left join #__users as b on a.userid=b.id where b.username is null");
    $idlistR = $database->loadObjectList();
            check_dberror("Unable to load users.");

    $allIDsR = array ();
    $cidsR = count($idlistR);

    if ($cidsR > 0)
    {
        foreach ($idlistR as $idR) {
            $allIDsR[] = $idR->userid;
        }

        $idsR = implode(',', $allIDsR);
    }

    //get userlist to add into Kunena users list
    $database->setQuery("SELECT a.id from #__users as a left join #__fb_users as b on b.userid=a.id where b.userid is null");
    $idlistA = $database->loadObjectList();
            check_dberror("Unable to load users.");

    $allIDsA = array ();
    $cidsA = count($idlistA);

    if ($cidsA > 0) {
        foreach ($idlistA as $idA) {
            $allIDsA[] = $idA->id;
        }
    }

	//fb_users update
    if ($cidsR or $cidsA) {
		// delete old users
		if ($cidsR)
		{
			$database->setQuery("DELETE FROM #__fb_users WHERE userid in ($idsR)");
			$database->query() or trigger_dberror("Unable to delete old users.");
		}

		// add new users
		if ($cidsA)
		{
			for ($j = 0, $m = count($allIDsA); $j < $m; $j ++)
			{
				$database->setQuery("INSERT INTO #__fb_users (userid) "."\nVALUES ($allIDsA[$j])");
				$database->query() or trigger_dberror("Unable to add new users.");
			}
		}
        mosRedirect("index2.php?option=$option&task=pruneusers", "" . _KUNENA_USERSSYNCDELETED . "" . $cids . " " . _KUNENA_SYNCUSERPROFILES . "");
    }
    else
    {
        $cids = 0;
        mosRedirect("index2.php?option=$option&task=pruneusers", _KUNENA_NOPROFILESFORSYNC);
    }
}

//===============================
// Uploaded Images browser
//===============================
function browseUploaded($database, $option, $type)
{
    if ($type)
    { //we're doing images
        $dir = @opendir(KUNENA_ABSUPLOADEDPATH. '/images');
        $uploaded_path = KUNENA_ABSUPLOADEDPATH. '/images';
    }
    else
    { //we're doing regular files
        $dir = @opendir(KUNENA_ABSUPLOADEDPATH.'/files');
        $uploaded_path = KUNENA_ABSUPLOADEDPATH.'/files';
    }

    $uploaded = array ();
    $uploaded_col_count = 0;

    while ($file = @readdir($dir))
    {
        if ($file != '.' && $file != '..' && is_file($uploaded_path . '/' . $file) && !is_link($uploaded_path . '/' . $file))
        {
            //if( preg_match('/(\.gif$|\.png$|\.jpg|\.jpeg)$/is', $file) )
            //{
            $uploaded[$uploaded_col_count] = $file;
            $uploaded_name[$uploaded_col_count] = ucfirst(str_replace("_", " ", preg_replace('/^(.*)\..*$/', '\1', $file)));
            $uploaded_col_count++;
        //}
        }
    }

    @closedir ($dir);
    @ksort ($uploaded);
    @reset ($uploaded);
    html_Kunena::browseUploaded($option, $uploaded, $uploaded_path, $type);
}

function replaceImage($database, $option, $imageName, $OxP)
{

	if (!$imageName) {
		mosRedirect("index2.php?option=$option&task=browseImages");
		return;
	}

    // This function will replace the selected image with a dummy (OxP=1) or delete it
    // step 1: Remove image that must be replaced:
    unlink (KUNENA_ABSUPLOADEDPATH.'/images/' . $imageName);

    if ($OxP == "1")
    {
        // step 2: the file name, without the extension:
        $filename = split("\.", $imageName);
        $fileName = $filename[0];
        $fileExt = $filename[1];
        // step 3: copy the dummy and give it the old file name:
        copy(KUNENA_ABSUPLOADEDPATH.'/dummy.' . $fileExt, KUNENA_ABSUPLOADEDPATH.'/images/' . $imageName);
    }
    else
    {
        //remove the database link as well
        $database->setQuery("DELETE FROM #__fb_attachments where filelocation='" . KUNENA_ABSUPLOADEDPATH . "/images/" . $imageName . "'");
        $database->query() or trigger_dberror("Unable to delete attachment.");
    }

    mosRedirect("index2.php?option=$option&task=browseImages", _KUNENA_IMGDELETED);
}

function deleteFile($database, $option, $fileName)
{
    global $mosConfig_admin_template;

    if (!$fileName) {
    	mosRedirect("index2.php?option=$option&task=browseFiles");
    	return;
    }

    // step 1: Remove file
    unlink (KUNENA_ABSUPLOADEDPATH.'/files/' . $fileName);
    //step 2: remove the database link to the file
    $database->setQuery("DELETE FROM #__fb_attachments where filelocation='" . KUNENA_ABSUPLOADEDPATH . "/files/" . $fileName . "'");
    $database->query() or trigger_dberror("Unable to delete attachment.");
    mosRedirect("index2.php?option=$option&task=browseFiles", _KUNENA_FILEDELETED);
}

//===============================
// Generic Functions
//===============================

/*  danial */
#########  category functions #########
function catTreeRecurse($id, $indent = "&nbsp;&nbsp;&nbsp;", $list, &$children, $maxlevel = 9999, $level = 0, $seperator = " >> ")
{
    if (@$children[$id] && $level <= $maxlevel)
    {
        foreach ($children[$id] as $v)
        {
            $id = $v->id;
            $txt = $v->name;
            $pt = $v->parent;
            $list[$id] = $v;
            $list[$id]->treename = "$indent$txt";
            $list[$id]->children = count(@$children[$id]);
            $list = catTreeRecurse($id, "$indent$txt$seperator", $list, $children, $maxlevel, $level + 1);
        //$list = catTreeRecurse( $id, "*", $list, $children, $maxlevel, $level+1 );
        }
    }

    return $list;
}

function showCategories($cat, $cname, $extras = "", $levellimit = "4")
{
    global $database, $mosConfig_lang;
    $database->setQuery("select id ,parent,name from
          #__fb_categories" . "\nORDER BY name");
    $mitems = $database->loadObjectList();
            check_dberror("Unable to load categories.");

    // establish the hierarchy of the menu
    $children = array ();

    // first pass - collect children
    foreach ($mitems as $v)
    {
        $pt = $v->parent;
        $list = @$children[$pt] ? $children[$pt] : array ();
        array_push($list, $v);
        $children[$pt] = $list;
    }

    // second pass - get an indent list of the items
    $list = catTreeRecurse(0, '', array (), $children);
    // assemble menu items to the array
    $mitems = array ();
    $mitems[] = mosHTML::makeOption('0', _KUNENA_NOPARENT);
    $this_treename = '';

    foreach ($list as $item)
    {
        if ($this_treename)
        {
            if ($item->id != $mitems && strpos($item->treename, $this_treename) === false) {
                $mitems[] = mosHTML::makeOption($item->id, stripslashes($item->treename));
            }
        }
        else
        {
            if ($item->id != $mitems) {
                $mitems[] = mosHTML::makeOption($item->id, stripslashes($item->treename));
            }
            else {
                $this_treename = stripslashes($item->treename)."/";
            }
        }
    }

    // build the html select list
    $parlist = selectList2($mitems, $cname, 'class="inputbox"  ' . $extras, 'value', 'text', $cat);
    return $parlist;
}

#######################################
## multiple select list
function selectList2(&$arr, $tag_name, $tag_attribs, $key, $text, $selected)
{
    reset ($arr);
    $html = "\n<select name=\"$tag_name\" $tag_attribs>";

    for ($i = 0, $n = count($arr); $i < $n; $i++)
    {
        $k = $arr[$i]->$key;
        $t = $arr[$i]->$text;
        $id = @$arr[$i]->id;
        $extra = '';
        $extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';

        if (is_array($selected))
        {
            foreach ($selected as $obj)
            {
                $k2 = $obj;

                if ($k == $k2)
                {
                    $extra .= " selected=\"selected\"";
                    break;
                }
            }
        }
        else {
            $extra .= ($k == $selected ? " selected=\"selected\"" : '');
        }

        $html .= "\n\t<option value=\"" . $k . "\"$extra>" . $t . "</option>";
    }

    $html .= "\n</select>\n";
    return $html;
}

function dircopy($srcdir, $dstdir, $verbose = false) {
    $num = 0;

    if (!is_dir($dstdir)) {
        mkdir($dstdir);
    }

    if ($curdir = opendir($srcdir)) {
        while ($file = readdir($curdir)) {
            if ($file != '.' && $file != '..') {
                $srcfile = $srcdir . '/' . $file;
                $dstfile = $dstdir . '/' . $file;

                if (is_file($srcfile)) {
                    if (is_file($dstfile)) {
                        $ow = filemtime($srcfile) - filemtime($dstfile);
                    }
                    else {
                        $ow = 1;
                    }

                    if ($ow > 0) {
                        if ($verbose) {
                            $tmpstr = _KUNENA_COPY_FILE;
                            $tmpstr = str_replace('%src%', $srcfile, $tmpstr);
                            $tmpstr = str_replace('%dst%', $dstfile, $tmpstr);
                            echo $tmpstr;
                        }

                        if (copy($srcfile, $dstfile)) {
                            touch($dstfile, filemtime($srcfile));
                            $num++;

                            if ($verbose) {
                                echo _KUNENA_COPY_OK;
                            }
                        }
                        else {
                            echo "" . _KUNENA_DIRCOPERR . " '$srcfile' " . _KUNENA_DIRCOPERR1 . "";
                        }
                    }
                }
                else if (is_dir($srcfile)) {
                    $num += dircopy($srcfile, $dstfile, $verbose);
                }
            }
        }

        closedir ($curdir);
    }

    return $num;
}

//===============================
//   smiley functions
//===============================
function showsmilies($option)
{
    global $database, $mainframe, $mosConfig_lang;
    //$limit = intval(mosGetParam($_POST, 'limit', 10));
    //$limitstart = intval(mosGetParam($_POST, 'limitstart', 0));
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
	$database->setQuery("SELECT COUNT(*) FROM #__fb_smileys");
	$total = $database->loadResult();
	if ($limitstart >= $total) $limitstart = 0;
    $limit_sql = (($limit>0)?" LIMIT $limitstart,$limit":"");

    $database->setQuery("SELECT * FROM #__fb_smileys" . $limit_sql);
    $smileytmp = $database->loadObjectList();
            check_dberror("Unable to load smileys.");

	$smileypath = smileypath();

    require_once ("includes/pageNavigation.php");
    $pageNavSP = new mosPageNav($total, $limitstart, $limit);
    html_Kunena::showsmilies($option, $mosConfig_lang, $smileytmp, $pageNavSP, $smileypath);

}

function editsmiley($option, $id)
{
	global $database, $mainframe, $mosConfig_lang;
    $database->setQuery("SELECT * FROM #__fb_smileys WHERE id = $id");

    $smileytmp = $database->loadAssocList();
    $smileycfg = $smileytmp[0];

    $smiley_images = collect_smilies();

    $smileypath = smileypath();
    $smileypath = $smileypath['live'].'/';

    $filename_list = "";
	for( $i = 0; $i < count($smiley_images); $i++ )
	{
		if( $smiley_images[$i] == $smileycfg['location'] )
		{
			$smiley_selected = "selected=\"selected\"";
			$smiley_edit_img = $smileypath . $smiley_images[$i];
		}
		else
		{
			$smiley_selected = "";
		}

		$filename_list .= '<option value="' . $smiley_images[$i] . '"' . $smiley_selected . '>' . $smiley_images[$i] . '</option>'."\n";
    }
    html_Kunena::editsmiley($option, $mosConfig_lang, $smiley_edit_img, $filename_list, $smileypath, $smileycfg);
}

function newsmiley($option)
{
	global $database, $mainframe;

    $smiley_images = collect_smilies();
    $smileypath = smileypath();
    $smileypath = $smileypath['live'].'/';

	$smiley_edit_img = '';

    $filename_list = "";
	for( $i = 0; $i < count($smiley_images); $i++ )
	{
		$filename_list .= '<option value="' . $smiley_images[$i] . '">' . $smiley_images[$i] . '</option>'."\n";
    }

    html_Kunena::newsmiley($option, $filename_list, $smileypath);
}

function savesmiley($option, $id = NULL)
{
	global $database;

    $smiley_code = mosGetParam($_POST, 'smiley_code');
    $smiley_location = mosGetParam($_POST, 'smiley_url');
    $smiley_emoticonbar = (mosGetParam($_POST, 'smiley_emoticonbar')) ? mosGetParam($_POST, 'smiley_emoticonbar') : 0;

    if (empty($smiley_code) || empty($smiley_location))
    {
    	$task = ($id == NULL) ? 'newsmiley' : 'editsmiley&id='.$id;
        mosRedirect ("index2.php?option=$option&task=".$task, _KUNENA_MISSING_PARAMETER);
        exit();
    }

    $database->setQuery("SELECT * FROM #__fb_smileys");

    $smilies = $database->loadAssocList();
    foreach ($smilies as $value)
    {
    	if (in_array($smiley_code, $value) && !($value['id'] == $id))
    	{
            $task = ($id == NULL) ? 'newsmiley' : 'editsmiley&id='.$id;
        	mosRedirect ("index2.php?option=$option&task=".$task, _KUNENA_CODE_ALLREADY_EXITS);
        	exit();
    	}

    }

    if ($id == NULL)
    {
    	$database->setQuery("INSERT INTO #__fb_smileys SET code = '$smiley_code', location = '$smiley_location', emoticonbar = '$smiley_emoticonbar'");
    }
    else
    {
    	$database->setQuery("UPDATE #__fb_smileys SET code = '$smiley_code', location = '$smiley_location', emoticonbar = '$smiley_emoticonbar' WHERE id = $id");
    }

    $database->query() or trigger_dberror("Unable to save smiley.");

    mosRedirect ("index2.php?option=$option&task=showsmilies", _KUNENA_SMILEY_SAVED);
}

function deletesmiley($option, $cid)
{
	global $database, $mainframe;

	if ($cids = implode(',', $cid)) {
		$database->setQuery("DELETE FROM #__fb_smileys WHERE id IN ($cids)");
		$database->query() or trigger_dberror("Unable to delete smiley.");
	}

    mosRedirect ("index2.php?option=$option&task=showsmilies", _KUNENA_SMILEY_DELETED);
}

function smileypath()
{
    global $mainframe, $mosConfig_lang;
	$fbConfig =& CKunenaConfig::getInstance();

    if (is_dir($mainframe->getCfg('absolute_path') . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.$mosConfig_lang.'/emoticons')) {
        $smiley_live_path = $mainframe->getCfg('live_site') . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.$mosConfig_lang.'/emoticons';
        $smiley_abs_path = $mainframe->getCfg('absolute_path') . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.$mosConfig_lang.'/emoticons';
    }
    else {
        $smiley_live_path = $mainframe->getCfg('live_site') . '/components/com_kunena/template/default/images/'.$mosConfig_lang.'/emoticons';
        $smiley_abs_path = $mainframe->getCfg('absolute_path') . '/components/com_kunena/template/default/images/'.$mosConfig_lang.'/emoticons';
    }

    $smileypath['live'] = $smiley_live_path;
    $smileypath['abs'] = $smiley_abs_path;

    return $smileypath;
}
//
// Read a listing of uploaded smilies for use in the add or edit smiley code...
//
function collect_smilies()
{
	$smileypath = smileypath();
	$smiley_images = array();

    $dir = @opendir($smileypath['abs']);

	while($file = @readdir($dir))
	{
		if( !@is_dir($smiley_abs_path . '/' . $file) )
		{
			$img_size = @getimagesize($smileypath['abs'] . '/' . $file);

			if( $img_size[0] && $img_size[1] )
			{
				$smiley_images[] = $file;
			}
		}
	}

	@closedir($dir);
	return $smiley_images;
}
//===============================
//  FINISH smiley functions
//===============================


//===============================
// Rank Administration
//===============================
//Dan Syme/IGD - Ranks Management

function showRanks($option)
{
    global $mainframe, $database, $mosConfig_lang, $order;

	//$limit = intval(mosGetParam($_POST, 'limit', 10));
	//$limitstart = intval(mosGetParam($_POST, 'limitstart', 0));
    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
	$limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
	$database->setQuery("SELECT COUNT(*) FROM #__fb_ranks");
	$total = $database->loadResult();
	if ($limitstart >= $total) $limitstart = 0;
	$limit_sql = (($limit>0)?" LIMIT $limitstart,$limit":"");

	$database->setQuery("SELECT * FROM #__fb_ranks" . $limit_sql);
	$ranks = $database->loadObjectList();
	        check_dberror("Unable to load ranks.");


	$rankpath = rankpath();

	require_once( "includes/pageNavigation.php" );
	$pageNavSP = new mosPageNav( $total,$limitstart,$limit );
	html_Kunena::showRanks( $option,$mosConfig_lang,$ranks,$pageNavSP,$order,$rankpath );

}

function rankpath()
{
/*
    global $mainframe, $mosConfig_lang;
	$fbConfig =& CKunenaConfig::getInstance();

    if (is_dir($mainframe->getCfg('absolute_path') . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.$mosConfig_lang.'/ranks')) {
        $rank_live_path = $mainframe->getCfg('live_site') . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.$mosConfig_lang.'/ranks';
        $rank_abs_path = $mainframe->getCfg('absolute_path') . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.$mosConfig_lang.'/ranks';
    }
    else {
        $rank_live_path = $mainframe->getCfg('live_site') . '/components/com_kunena/template/default/images/'.$mosConfig_lang.'/ranks';
        $rank_abs_path = $mainframe->getCfg('absolute_path') . '/components/com_kunena/template/default/images/'.$mosConfig_lang.'/ranks';
    }

    $rankpath['live'] = $rank_live_path;
    $rankpath['abs'] = $rank_abs_path;
*/
    $rankpath['live'] = KUNENA_URLRANKSPATH;
    $rankpath['abs'] = KUNENA_ABSRANKSPATH;

    return $rankpath;

}

function collectRanks()
{
	$rankpath = rankpath();

	$dir = @opendir($rankpath['abs']);

	$rank_images = array();
	while($file = @readdir($dir))
	{
		if( !@is_dir($rankpath['abs'] . '/' . $file) )
		{
			$img_size = @getimagesize($rankpath['abs'] . '/' . $file);

			if( $img_size[0] && $img_size[1] )
			{
				$rank_images[] = $file;
			}
		}
	}

	@closedir($dir);
	return $rank_images;
}

function newRank($option)
{
	global $database, $mainframe;

	$rank_images = collectRanks();
	$rankpath = rankpath();
	$rankpath = $rankpath['live'].'/';

	$filename_list = "";
	$i = 0;
	foreach ($rank_images as $id=>$row)
	{
		$filename_list .= '<option value="' . $rank_images[$id] . '">' . $rank_images[$id] . '</option>'."\n";
	}

    html_Kunena::newRank($option, $filename_list, $rankpath);
}

function deleteRank($option, $cid = null)
{
	global $database, $mainframe;

	if ($cids = implode(',', $cid)) {
		$database->setQuery("DELETE FROM #__fb_ranks WHERE rank_id IN ($cids)");
		$database->query() or trigger_dberror("Unable to delete rank.");
	}

    mosRedirect ("index2.php?option=$option&task=ranks", _KUNENA_RANK_DELETED);
}

function saveRank($option, $id = NULL)
{
	global $database;

    $rank_title = mosGetParam($_POST, 'rank_title');
    $rank_image = mosGetParam($_POST, 'rank_image');
    $rank_special = mosGetParam($_POST, 'rank_special');
    $rank_min = mosGetParam($_POST, 'rank_min');

    if (empty($rank_title) || empty($rank_image))
    {
    	$task = ($id == NULL) ? 'newRank' : 'editRank&id='.$id;
        mosRedirect ("index2.php?option=$option&task=".$task, _KUNENA_MISSING_PARAMETER);
        exit();
    }

    $database->setQuery("SELECT * FROM #__fb_ranks");
    $database->query() or trigger_dberror("Unable to load ranks.");

    $ranks = $database->loadAssocList();
    foreach ($ranks as $value)
    {
    	if (in_array($rank_title, $value) && !($value['rank_id'] == $id))
    	{
            $task = ($id == NULL) ? 'newRank' : 'editRank&id='.$id;
        	mosRedirect ("index2.php?option=$option&task=".$task, _KUNENA_RANK_ALLREADY_EXITS);
        	exit();
    	}
    }

    if ($id == NULL)
    {
    	$database->setQuery("INSERT INTO #__fb_ranks SET rank_title = '$rank_title', rank_image = '$rank_image', rank_special = '$rank_special', rank_min = '$rank_min'");
    }
    else
    {
    	$database->setQuery("UPDATE #__fb_ranks SET rank_title = '$rank_title', rank_image = '$rank_image', rank_special = '$rank_special', rank_min = '$rank_min' WHERE rank_id = $id");
    }
    $database->query() or trigger_dberror("Unable to save ranks.");

    mosRedirect ("index2.php?option=$option&task=ranks", _KUNENA_RANK_SAVED);
}

function editRank($option, $id)
{
	global $database, $mainframe, $mosConfig_lang;

	$database->setQuery("SELECT * FROM #__fb_ranks WHERE rank_id = '$id'");
    $database->query() or trigger_dberror("Unable to load ranks.");

	$ranks = $database->loadObjectList();
	        check_dberror("Unable to load ranks.");

    $rank_images = collectRanks();

    $path = rankpath();
    $path = $path['live'].'/';

    $edit_img = $filename_list = '';

	foreach ($ranks as $row)
	{
		foreach ($rank_images as $img)
		{
			$image = $path . $img;

			if ($img == $row->rank_image)
			{
				$selected = ' selected="selected"';
				$edit_img = $path . $img;
			}
			else
			{
				$selected = '';
			}

			if (strlen($img) > 255)
			{
				continue;
			}

			$filename_list .= '<option value="' . kunena_htmlspecialchars($img) . '"' . $selected . '>' . $img . '</option>';
		}
	}

    html_Kunena::editRank($option, $mosConfig_lang, $edit_img, $filename_list, $path, $row);
}

//===============================
//  FINISH smiley functions
//===============================
// Dan Syme/IGD - Ranks Management

function KUNENA_GetAvailableModCats($catids) {
    global $database;
    $list = JJ_categoryArray(1);
    $this_treename = '';
    $catid = 0;

    foreach ($list as $item) {
        if ($this_treename) {
            if ($item->id != $catid && strpos($item->treename, $this_treename) === false) {
                $options[] = mosHTML::makeOption($item->id, stripslashes($item->treename));
                }
            }
        else {
            if ($item->id != $catid) {
                $options[] = mosHTML::makeOption($item->id, stripslashes($item->treename));
                }
            else {
                $this_treename = stripslashes($item->treename)."/";
                }
            }
        }
    $parent = mosHTML::selectList($options, 'catid[]', 'class="inputbox fbs"  multiple="multiple"   id="KUNENA_AvailableForums" ', 'value', 'text', $catids);
    return $parent;
}

// Grabs gd version

 function KUNENA_gdVersion() {
  // Simplified GD Version check
  if (!extension_loaded('gd')) {
    return;
  }

  $phpver = substr(phpversion(), 0, 3);
  // gd_info came in at 4.3
  if ($phpver < 4.3)
    return -1;

  if (function_exists('gd_info')) {
    $ver_info = gd_info();
    preg_match('/\d/', $ver_info['GD Version'], $match);
    $gd_ver = $match[0];
    return $match[0];
  } else {
    return;
  }
}
?>
