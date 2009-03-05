<?php
/**
* @version $Id: admin.fireboard.php 1070 2008-10-06 08:11:18Z fxstein $
* Kunena Component
* @package Kunena
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

defined( '_JEXEC' ) or die('Restricted access');

// Kunena wide defines
require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');

// Now that we have the global defines we can use shortcut defines
require_once (KUNENA_PATH_LIB .DS. 'kunena.debug.php');
require_once (KUNENA_PATH_LIB .DS. 'kunena.config.class.php');

// get fireboards configuration params in
global $mainframe, $fbConfig, $database;

$database = JFactory::getDBO();

$fbConfig = new fb_config();
$fbConfig->load();

// Class structure should be used after this and all the common task should be moved to this class
require_once (KUNENA_PATH .DS. 'class.kunena.php');
require_once (KUNENA_PATH_ADMIN .DS. 'admin.kunena.html.php');

$langfile = KUNENA_PATH_ADMIN_LANGUAGE .DS. 'kunena.'.KUNENA_LANGUAGE.'.php';
$defaultlangfile = KUNENA_PATH_ADMIN_LANGUAGE .DS. 'kunena.english.php';

(file_exists($langfile)) ? require_once ($langfile) : require_once ($defaultlangfile);

$cid = JRequest::getVar('cid', array ( 0 ));

if (!is_array($cid)) {
    $cid = array ( 0 );
}

$uid = JRequest::getVar('uid', array ( 0 ));

if (!is_array($uid)) {
    $uid = array ( $uid );
}

// ERROR: global scope mix
global $order;
$order = JRequest::getVar('order', '');

// initialise some request directives (specifically for J1.5 compatibility)
$no_html = intval(JRequest::getVar('no_html', 0));
$id = intval(JRequest::getVar('id', 0));

$pt_stop = '0';

if (!$no_html)
{
	html_Kunena::showFbHeader();
}

switch ($task)
{
    case 'installfb':
        $mode = JRequest::getVar('mode', 1);

        com_install_fireboard ($mode);
        break;

    case 'new':
        editForum(0, $option);

        break;

    case 'edit':
        editForum($cid[0], $option);

        break;

    case 'edit2':
        editForum($uid[0], $option);

        break;

    case 'save':
        saveForum ($option);

        break;

    case 'cancel':
        cancelForum ($option);

        break;

    case 'publish':
        publishForum($cid, 1, $option);

        break;

    case 'unpublish':
        publishForum($cid, 0, $option);

        break;

    case 'remove':
        deleteForum($cid, $option);

        break;

    case 'orderup':
        orderForum($cid[0], -1, $option);

        break;

    case 'orderdown':
        orderForum($cid[0], 1, $option);

        break;

    case 'showconfig':
        showConfig ($option);

        break;

    case 'saveconfig':
        saveConfig ($option);

        break;

    case 'newmoderator':
        newModerator($option, $id);

        break;

    case 'addmoderator':
        addModerator($option, $id, $cid, 1);

        break;

    case 'removemoderator':
        addModerator($option, $id, $cid, 0);

        break;

    case 'showprofiles':
        showProfiles($database, $option, $lang, $order);

        break;

    case 'profiles':
        showProfiles($database, $option, $lang, $order);

        break;

    case 'userprofile':
        editUserProfile($uid);

        break;

    case 'showinstructions':
        showInstructions($database, $option, $lang);

        break;

    case 'showCss':
        showCss ($option);

        break;

    case 'saveeditcss':
        $file =JRequest::getVar( 'file', 1);
        $csscontent = JRequest::getVar( 'csscontent', 1);

        saveCss($file, $csscontent, $option);

        break;

    case 'instructions':
        showInstructions($database, $option, $lang);

        break;

    case 'saveuserprofile':
        saveUserProfile ($option);

        break;

    case 'upgradetables':
        upgradeTables ($option);

        break;

    case 'loadSample':
        loadSample($database, $option);

        break;

	case 'removeSample':
        removeSample($database, $option);

        break;

    case 'pruneforum':
        pruneforum($database, $option);

        break;

    case 'doprune':
        doprune($database, $option);

        break;

    case 'douserssync':
        douserssync($database, $option);

        break;

    case 'syncusers':
        syncusers($database, $option);

        break;

    case 'browseImages':
        browseUploaded($database, $option, 1);

        break;

    case 'browseFiles':
        browseUploaded($database, $option, 0);

        break;

    case 'replaceImage':
        replaceImage($database, $option, JRequest::getVar('img', ''), $OxP);

        break;

    case 'deleteFile':
        deleteFile($database, $option, JRequest::getVar('fileName', ''));


        break;

    case 'showAdministration':
        //showAdministration( $option,$joomla1_5);
        showAdministration ($option);

        break;

    case 'loadCBprofile':
        loadCBprofile($database, $option);

        break;

    case 'recount':
       FBTools::reCountBoards();
       // Also reset the name info stored with messages
       FBTools::updateNameInfo();
       $mainframe->redirect( JURI::base() .'index2.php?option=com_kunena', _KUNENA_RECOUNTFORUMS_DONE);
        break;

	case 'showsmilies':
    	showsmilies($option);

        break;

    case 'editsmiley':
    	editsmiley($option, $cid[0]);

        break;

    case 'savesmiley':
    	savesmiley($option, $id);

        break;

    case 'deletesmiley':
    	deletesmiley($option, $cid);

        break;

    case 'newsmiley':
    	newsmiley($option);

        break;

    case 'ranks':
    	showRanks($option);

    	break;

    case 'editRank':
    	editRank($option, $cid[0]);

        break;

    case 'saveRank':
    	saveRank($option, $id);

        break;

    case 'deleteRank':
    	deleteRank($option, $cid);

        break;

    case 'newRank':
    	newRank($option);

        break;

    case 'cpanel':
    default:
        html_Kunena::controlPanel();
        break;
}

html_Kunena::showFbFooter();
//function showAdministration( $option,$joomla1_5 ) {
function showAdministration($option)
{
    global $mainframe;
	$database = &JFactory::getDBO();

    $limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 10);
    $limitstart = $mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0);
    $levellimit = $mainframe->getUserStateFromRequest("view{$option}limit", 'levellimit', 10);
    /*
    * danial
    */
    $database->setQuery("SELECT a.*, a.name AS category, u.name AS editor, g.name AS groupname, h.name AS admingroup"
    . "\nFROM #__fb_categories AS a"
    . "\nLEFT JOIN #__users AS u ON u.id = a.checked_out"
    . "\nLEFT JOIN #__core_acl_aro_groups AS g ON g.id = a.pub_access"
    . "\nLEFT JOIN #__core_acl_aro_groups AS h ON h.id = a.admin_access"
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
        $list = @$children[$pt] ? $children[$pt] : array ();
        array_push($list, $v);
        $children[$pt] = $list;
    }

    // second pass - get an indent list of the items
    $list = fbTreeRecurse(0, '', array (), $children, max(0, $levellimit - 1));
    $total = count($list);
    if ($limitstart >= $total) $limitstart = 0;

	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );

	$levellist = JHTML::_('select.integerList' , 1, 20, 1, 'levellimit', 'size="1" onchange="document.adminForm.submit();"', $levellimit);
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
$database = &JFactory::getDBO();
    $acl = &JFactory::getACL();
	$my = &JFactory::getUser();
    $row = new fbForum($database);
    // load the row from the db table
    $row->load($uid);

    //echo "<pre>"; print_r ($row); echo "</pre>";
    if ($uid)
    {
        $row->checkout($my->id);
        $categories = array ();
    }
    else
    {
        // initialise new record
        $categories[] = JHTML::_('select.option', '0', JText::_( _KUNENA_TOPLEVEL), 'value', 'text');
        $row->parent = 0;
        $row->published = 0;
        $row->ordering = 9999;
    }

    // get a list of just the categories
    $database->setQuery("SELECT a.id AS value, a.name AS text FROM #__fb_categories AS a WHERE parent='0' AND id<>'$row->id' ORDER BY ordering");
    $categories = array_merge($categories, $database->loadObjectList());
    	check_dberror("Unable to load categories.");

    if ($row->parent == 0)
    {
        //make sure the Top Level Category is available in edit mode as well:
        $database->setQuery("SELECT distinct '0' AS value, '"._KUNENA_TOPLEVEL."' AS text FROM #__fb_categories AS a WHERE parent='0' AND id<>'$row->id' ORDER BY ordering");
        $categories = array_merge($categories, (array)$database->loadObjectList());
        	check_dberror("Unable to load categories.");

        //build the select list:
        $categoryList = JHTML::_('select.genericlist',$categories, 'parent', 'class="inputbox" size="1"', 'value', 'text', $row->parent);
    }
    else
    {
        $categoryList = JHTML::_('select.genericlist',$categories, 'parent', 'class="inputbox" size="1"', 'value', 'text', $row->parent);
    }

    $categoryList = showCategories($row->parent, "parent", "", "4");
    // make a standard yes/no list
    $yesno = array ();
    $yesno[] = JHTML::_('select.option', '0', _ANN_NO);
    $yesno[] = JHTML::_('select.option', '1', _ANN_YES);

	// make a standard no/yes list
    $noyes = array ();
    $noyes[] = JHTML::_('select.option', '1', _ANN_YES);
    $noyes[] = JHTML::_('select.option', '0', _ANN_NO);
    //Create all kinds of Lists
    $lists = array ();
    $accessLists = array ();
    //create custom group levels to include into the public group selectList
    $pub_groups = array ();
    $pub_groups[] = JHTML::_('select.option',0, _KUNENA_EVERYBODY);
    $pub_groups[] = JHTML::_('select.option', -1, _KUNENA_ALLREGISTERED);





    $pub_groups = array_merge($pub_groups, $acl->get_group_children_tree(null, _KUNENA_REGISTERED, true));
    //create admin groups array for use in selectList:
    $adm_groups = array ();
    $adm_groups = array_merge($adm_groups, $acl->get_group_children_tree(null, _KUNENA_PUBLICBACKEND, true));
    //create the access control list
    $accessLists['pub_access'] = JHTML::_('select.genericlist',$pub_groups, 'pub_access', 'class="inputbox" size="4"', 'value', 'text', $row->pub_access);
    $accessLists['admin_access'] = JHTML::_('select.genericlist',$adm_groups, 'admin_access', 'class="inputbox" size="4"', 'value', 'text', $row->admin_access);
    $lists['pub_recurse'] = JHTML::_('select.genericlist',$yesno, 'pub_recurse', 'class="inputbox" size="1"', 'value', 'text', $row->pub_recurse);
    $lists['admin_recurse'] = JHTML::_('select.genericlist',$yesno, 'admin_recurse', 'class="inputbox" size="1"', 'value', 'text', $row->admin_recurse);
    $lists['forumLocked'] = JHTML::_('select.genericlist',$yesno, 'locked', 'class="inputbox" size="1"', 'value', 'text', $row->locked);
    $lists['forumModerated'] = JHTML::_('select.genericlist',$noyes, 'moderated', 'class="inputbox" size="1"', 'value', 'text', $row->moderated);
    $lists['forumReview'] = JHTML::_('select.genericlist',$yesno, 'review', 'class="inputbox" size="1"', 'value', 'text', $row->review);
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
$database = &JFactory::getDBO();
    global  $my,$mainframe;
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
    //$row->updateOrder("parent='$row->parent'");
    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=showAdministration");
}

function publishForum($cid = null, $publish = 1, $option)
{
$database = &JFactory::getDBO();
    global  $mainframe;
$my =  &JFactory::getUser();
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

    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=showAdministration");
}

function deleteForum($cid = null, $option)
{
$database = &JFactory::getDBO();
    global  $mainframe;
$my =  &JFactory::getUser();
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

    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=showAdministration");
}

function cancelForum($option)
{
    global  $mainframe;

    $database = &JFactory::getDBO();
    $row = new fbForum($database);
    $row->bind($_POST);
    $row->checkin();
    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=showAdministration");
}

function orderForum($uid, $inc, $option)
{
    global  $mainframe;
    $database = &JFactory::getDBO();
    $row = new fbForum($database);
    $row->load($uid);
    $row->move($inc, "parent='$row->parent'");
    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=showAdministration");
}

//===============================
// Config Functions
//===============================
function showConfig($option)
{
    $database = &JFactory::getDBO();
    global $mosConfig_admin_template;
    global $mainframe;
    global $fbConfig;

    $lists = array ();

    // the default page when entering Kunena
    $defpagelist = array ();
	$defpagelist[] = JHTML::_('select.option', JText::_('recent'), _COM_A_FBDEFAULT_PAGE_RECENT);



	$defpagelist[] = JHTML::_('select.option', JText::_('my'), _COM_A_FBDEFAULT_PAGE_MY);
	$defpagelist[] = JHTML::_('select.option', JText::_('categories'),_COM_A_FBDEFAULT_PAGE_CATEGORIES);

    // build the html select list
    $lists['fbdefaultpage'] = JHTML::_('select.genericlist', $defpagelist ,'cfg_fbdefaultpage', 'class="inputbox" size="1" ','value', 'text', $fbConfig->fbdefaultpage);


    // the default view
    $list = array ();

	$list[] =JHTML::_('select.option', JText::_('flat'),_COM_A_FLAT);
	$list[] =JHTML::_('select.option', JText::_('threaded'),_COM_A_THREADED);

    // build the html select list
    $lists['default_view'] = JHTML::_('select.genericlist', $list ,'cfg_default_view', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->default_view);




    $rsslist = array ();
	$rsslist[] = JHTML::_('select.option', JText::_('thread'),_COM_A_RSS_BY_THREAD);
	$rsslist[] = JHTML::_('select.option', JText::_('post'),_COM_A_RSS_BY_POST);

    // build the html select list
	$lists['rsstype'] = JHTML::_('select.genericlist', $list ,'cfg_rsstype', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->rsstype);




    $rsshistorylist = array ();
	$rsshistorylist[] =JHTML::_('select.option', JText::_('Week'),_COM_A_RSS_HISTORY_WEEK);
	$rsshistorylist[] =JHTML::_('select.option', JText::_('Month'),_COM_A_RSS_HISTORY_MONTH);
	$rsshistorylist[] =JHTML::_('select.option', JText::_('Year'),_COM_A_RSS_HISTORY_YEAR);


    // build the html select list
    $lists['rsshistory'] = JHTML::_('select.genericlist', $list ,'cfg_rsshistory', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->rsshistory);



    // source of avatar picture
    $avlist = array ();
	$avlist[] = JHTML::_('select.option', JText::_('fb'),_KUNENA_KUNENA);
	$avlist[] = JHTML::_('select.option', JText::_('clexuspm'),_KUNENA_CLEXUS);
	$avlist[] = JHTML::_('select.option', JText::_('cb'),_KUNENA_CB);
    // build the html select list
    $lists['avatar_src'] = JHTML::_('select.genericlist', $avlist,'cfg_avatar_src', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->rsshistory);

    // private messaging system to use
    $pmlist = array ();
	$pmlist[] = JHTML::_('select.option', JText::_('no'),_COM_A_NO);
	$pmlist[] = JHTML::_('select.option', JText::_('pms'),_KUNENA_MYPMS);
	$pmlist[] = JHTML::_('select.option', JText::_('clexuspm'),_KUNENA_CLEXUS);
	$pmlist[] = JHTML::_('select.option', JText::_('uddeim'),_KUNENA_UDDEIM);
	$pmlist[] = JHTML::_('select.option', JText::_('jim'),_KUNENA_JIM);
	$pmlist[] = JHTML::_('select.option', JText::_('missus'),_KUNENA_MISSUS);

    $lists['pm_component'] = JHTML::_('select.genericlist', $pmlist, 'cfg_pm_component', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->pm_component);


//redundant    $lists['pm_component'] = JHTML::_('select.genericlist',$pmlist, 'cfg_pm_component', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->pm_component);
    // Profile select
    $prflist = array ();
	$prflist[] = JHTML::_('select.option', JText::_('fb'),_KUNENA_KUNENA);
	$prflist[] = JHTML::_('select.option', JText::_('clexuspm'),_KUNENA_CLEXUS);
	$prflist[] = JHTML::_('select.option', JText::_('cb'),_KUNENA_CB);

    $lists['fb_profile'] = JHTML::_('select.genericlist', $prflist, 'cfg_fb_profile', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->fb_profile);



    // build the html select list
    // make a standard yes/no list
    $yesno = array ();
	$yesno[] = JHTML::_('select.option',JText::_('0'), _COM_A_NO);
	$yesno[] = JHTML::_('select.option', JText::_('1'), _COM_A_YES);
    /* Build the templates list*/
    // This function was modified from the one posted to PHP.net by rockinmusicgv
    // It is available under the readdir() entry in the PHP online manual
    //function get_dirs($directory, $select_name, $selected = "") {
    $listitems[] = JHTML::_('select.option',  JText::_('1'),_KUNENA_SELECTTEMPLATE);

    if ($dir = @opendir(JPATH_ROOT . "/components/com_kunena/template"))
    {
        while (($file = readdir($dir)) !== false)
        {
            if ($file != ".." && $file != ".")
            {
                if (is_dir(JPATH_ROOT . "/components/com_kunena/template" . "/" . $file))
                {
                    if (!($file[0] == '.')) {
                        $filelist[] = $file;
                    }
                }
            }
        }

        closedir ($dir);
    }

    asort ($filelist);

    while (list($key, $val) = each($filelist)) {
        //echo "<option value=\"$val\"";
        //if ($selected == $val) {
        //    echo " selected";
        //}
        //echo ">$val Gallery</option>\n";
        //$listitems[] = JHTML::_('select.option',$val, $val);

		$listitems[] = JHTML::_('select.option',  JText::_($val),$val);
    }

	$lists['badwords'] = JHTML::_('select.genericlist', $yesno, 'cfg_badwords', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->badwords);
	$lists['jmambot'] = JHTML::_('select.genericlist', $yesno, 'cfg_jmambot', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->jmambot);
    $lists['disemoticons'] = JHTML::_('select.genericlist', $yesno, 'cfg_disemoticons', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->disemoticons);
    $lists['template'] = JHTML::_('select.genericlist', $listitems, 'cfg_template', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->template);
    $lists['templateimagepath'] = JHTML::_('select.genericlist', $listitems, 'cfg_templateimagepath', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->templateimagepath);
    $lists['regonly'] = JHTML::_('select.genericlist', $yesno, 'cfg_regonly', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->regonly);
    $lists['board_offline'] = 	JHTML::_('select.genericlist', $yesno, 'cfg_board_offline', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->board_offline);
    $lists['pubwrite'] = JHTML::_('select.genericlist', $yesno, 'cfg_pubwrite', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->pubwrite);
    $lists['useredit'] = JHTML::_('select.genericlist', $yesno, 'cfg_useredit', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->useredit);
    $lists['showhistory'] = JHTML::_('select.genericlist', $yesno, 'cfg_showhistory', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showhistory);
    $lists['joomlastyle'] = JHTML::_('select.genericlist', $yesno,'cfg_joomlastyle', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->joomlastyle);
    $lists['showannouncement'] = JHTML::_('select.genericlist', $yesno,'cfg_showannouncement', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showannouncement);
    $lists['avataroncat'] =	JHTML::_('select.genericlist', $yesno,'cfg_avataroncat', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->avataroncat);
    $lists['showlatest'] = 		JHTML::_('select.genericlist', $yesno,'cfg_showlatest', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showlatest);
    $lists['latestsinglesubject'] =			JHTML::_('select.genericlist', $yesno,'cfg_latestsinglesubject', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->latestsinglesubject);
    $lists['latestreplysubject'] = 	JHTML::_('select.genericlist', $yesno,'cfg_latestreplysubject', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->latestreplysubject);
    $lists['latestshowdate'] = JHTML::_('select.genericlist', $yesno,'cfg_latestshowdate', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->latestshowdate);
    $lists['showchildcaticon'] =	JHTML::_('select.genericlist', $yesno,'cfg_showchildcaticon', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showchildcaticon);
    $lists['latestshowhits'] = JHTML::_('select.genericlist', $yesno,'cfg_latestshowhits', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->latestshowhits);
    $lists['showuserstats'] = JHTML::_('select.genericlist', $yesno, 'cfg_showuserstats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showuserstats);
    $lists['showwhoisonline'] = JHTML::_('select.genericlist', $yesno, 'cfg_showwhoisonline', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showwhoisonline);
    $lists['showpopsubjectstats'] = JHTML::_('select.genericlist', $yesno, 'cfg_showpopsubjectstats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showpopsubjectstats);
    $lists['showgenstats'] = JHTML::_('select.genericlist', $yesno, 'cfg_showgenstats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showgenstats);
    $lists['showpopuserstats'] = JHTML::_('select.genericlist', $yesno, 'cfg_showpopuserstats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showpopuserstats);
    $lists['allowsubscriptions'] = JHTML::_('select.genericlist', $yesno, 'cfg_allowsubscriptions', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowsubscriptions);
    $lists['subscriptionschecked'] = JHTML::_('select.genericlist', $yesno, 'cfg_subscriptionschecked', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->subscriptionschecked);
    $lists['allowfavorites'] = JHTML::_('select.genericlist', $yesno, 'cfg_allowfavorites', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowfavorites);
    $lists['mailmod'] = JHTML::_('select.genericlist', $yesno, 'cfg_mailmod', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->mailmod);
    $lists['mailadmin'] = JHTML::_('select.genericlist', $yesno, 'cfg_mailadmin', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->mailadmin);
    $lists['showemail'] = JHTML::_('select.genericlist', $yesno, 'cfg_showemail', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showemail);
    $lists['askemail'] = JHTML::_('select.genericlist', $yesno, 'cfg_askemail', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->askemail);
    $lists['changename'] = JHTML::_('select.genericlist', $yesno, 'cfg_changename', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->changename);
    $lists['allowavatar'] = JHTML::_('select.genericlist', $yesno, 'cfg_allowavatar', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowavatar);
    $lists['allowavatarupload'] = JHTML::_('select.genericlist', $yesno, 'cfg_allowavatarupload', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowavatarupload);
    $lists['allowavatargallery'] = JHTML::_('select.genericlist', $yesno, 'cfg_allowavatargallery', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowavatargallery);
	$lists['avatar_src'] = JHTML::_('select.genericlist', $avlist, 'cfg_avatar_src', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->avatar_src);

	$ip_opt[] = JHTML::_('select.option', 'gd2', 'GD2');
	$ip_opt[] = JHTML::_('select.option', 'gd1', 'GD1');
	$ip_opt[] = JHTML::_('select.option', 'none', _KUNENA_IMAGE_PROCESSOR_NONE);
    //$ip_opt[] = mosHTML::makeOption( 'none', _KUNENA_IMAGE_PROCESSOR_NONE );

    $lists['imageprocessor'] = JHTML::_('select.genericlist', $ip_opt, 'cfg_imageprocessor', 'class="inputbox"', 'value', 'text', $fbConfig->imageprocessor );
    $lists['showstats'] = JHTML::_('select.genericlist', $yesno, 'cfg_showstats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showstats);
    $lists['showranking'] = JHTML::_('select.genericlist', $yesno, 'cfg_showranking', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showranking);
    $lists['rankimages'] = JHTML::_('select.genericlist', $yesno, 'cfg_rankimages', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->rankimages);
    $lists['username'] = JHTML::_('select.genericlist', $yesno, 'cfg_username', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->username);
    $lists['shownew'] = JHTML::_('select.genericlist', $yesno, 'cfg_shownew', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->shownew);
    $lists['allowimageupload'] = JHTML::_('select.genericlist', $yesno, 'cfg_allowimageupload', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowimageupload);
    $lists['allowimageregupload'] = JHTML::_('select.genericlist', $yesno, 'cfg_allowimageregupload', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowimageregupload);
    $lists['allowfileupload'] = JHTML::_('select.genericlist', $yesno, 'cfg_allowfileupload', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowfileupload);
    $lists['allowfileregupload'] = JHTML::_('select.genericlist', $yesno, 'cfg_allowfileregupload', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->allowfileregupload);
    $lists['editmarkup'] = JHTML::_('select.genericlist', $yesno, 'cfg_editmarkup', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->editmarkup);
    $lists['discussbot'] = JHTML::_('select.genericlist', $yesno, 'cfg_discussbot', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->discussbot);
    $lists['enablerss'] = JHTML::_('select.genericlist', $yesno, 'cfg_enablerss', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->enablerss);
    $lists['poststats'] = JHTML::_('select.genericlist', $yesno, 'cfg_poststats', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->poststats);
    $lists['showkarma'] = JHTML::_('select.genericlist', $yesno, 'cfg_showkarma', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showkarma);
    $lists['cb_profile'] = JHTML::_('select.genericlist', $yesno, 'cfg_cb_profile', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->cb_profile);
    $lists['enablepdf'] = JHTML::_('select.genericlist', $yesno, 'cfg_enablepdf', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->enablepdf);
    $lists['enablerulespage'] = JHTML::_('select.genericlist', $yesno, 'cfg_enablerulespage', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->enablerulespage);
	$lists['rules_infb'] = JHTML::_('select.genericlist', $yesno, 'cfg_rules_infb', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->rules_infb);
	$lists['enablehelppage'] = JHTML::_('select.genericlist', $yesno, 'cfg_enablehelppage', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->enablehelppage);
	$lists['help_infb'] = JHTML::_('select.genericlist', $yesno, 'cfg_help_infb', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->help_infb);
    $lists['enableforumjump'] = JHTML::_('select.genericlist', $yesno, 'cfg_enableforumjump', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->enableforumjump);
    $lists['userlist_online'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_online', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_online);
    $lists['userlist_avatar'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_avatar', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_avatar);
    $lists['userlist_name'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_name', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_name);
    $lists['userlist_username'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_username', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_username);
    $lists['userlist_group'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_group', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_group);
    $lists['userlist_posts'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_posts', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_posts);
    $lists['userlist_karma'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_karma', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_karma);
    $lists['userlist_email'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_email', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_email);
    $lists['userlist_usertype'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_usertype', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_usertype);
    $lists['userlist_joindate'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_joindate', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_joindate);
    $lists['userlist_lastvisitdate'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_lastvisitdate', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_lastvisitdate);
	$lists['userlist_userhits'] = JHTML::_('select.genericlist', $yesno, 'cfg_userlist_userhits', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->userlist_userhits);
	$lists['usernamechange'] = JHTML::_('select.genericlist', $yesno, 'cfg_usernamechange', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->usernamechange);
	$lists['reportmsg'] = JHTML::_('select.genericlist', $yesno, 'cfg_reportmsg', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->reportmsg);
	$lists['captcha'] = JHTML::_('select.genericlist', $yesno, 'cfg_captcha', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->captcha);
	$lists['mailfull'] = JHTML::_('select.genericlist', $yesno, 'cfg_mailfull', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->mailfull);
	// New for 1.0.5
	$lists['showspoilertag'] = JHTML::_('select.genericlist', $yesno, 'cfg_showspoilertag', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showspoilertag);
	$lists['showvideotag'] = JHTML::_('select.genericlist', $yesno, 'cfg_showvideotag', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showvideotag);
	$lists['showebaytag'] = JHTML::_('select.genericlist', $yesno, 'cfg_showebaytag', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->showebaytag);
	$lists['trimlongurls'] = JHTML::_('select.genericlist', $yesno, 'cfg_trimlongurls', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->trimlongurls);
	$lists['autoembedyoutube'] = JHTML::_('select.genericlist', $yesno, 'cfg_autoembedyoutube', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->autoembedyoutube);
	$lists['autoembedebay'] = JHTML::_('select.genericlist', $yesno, 'cfg_autoembedebay', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->autoembedebay);
	$lists['highlightcode'] = JHTML::_('select.genericlist', $yesno, 'cfg_highlightcode', 'class="inputbox" size="1"', 'value', 'text', $fbConfig->highlightcode);

    html_Kunena::showConfig($fbConfig, $lists, $option);
}

function saveConfig($option)
{
	global $mainframe, $fbConfig;

    $database = &JFactory::getDBO();

	$fbConfig->backup();
	$fbConfig->remove();

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

    $fbConfig->create();

    $mainframe->redirect( JURI::base() . "index2.php?option=$option&task=showconfig", _KUNENA_CONFIGSAVED);
}

function showInstructions($database, $option, $lang) {
$database = &JFactory::getDBO();
    html_Kunena::showInstructions($database, $option, $lang);
}

//===============================
// CSS functions
//===============================
function showCss($option)
{
    global $fbConfig;
    $file = "../components/com_kunena/template/" . $fbConfig->template . "/forum.css";
    @chmod($file, 0766);
    $permission = is_writable($file);

    if (!$permission)
    {
        echo "<center><h1><font color=red>" . _KUNENA_WARNING . "</FONT></h1><BR>";
        echo "<B>Your css file is <#__root>/components/com_kunena/template/" . $fbConfig->template . "/forum.css</b><BR>";
        echo "<B>" . _KUNENA_CHMOD1 . "</B></center><BR><BR>";
    }

    html_Kunena::showCss($file, $option);
}

function saveCss($file, $csscontent, $option)
{global $mainframe;
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
        $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=showCss", _KUNENA_CFS);
    }
    else {
        $mainframe->redirect( JURI::base() ."index2.php?option=$option", _KUNENA_CFCNBO);
    }
}

//===============================
// Moderator Functions
//===============================
function newModerator($option, $id = null)
{
    $database = &JFactory::getDBO();
    //die ("New Moderator");
    //$limit = intval(JRequest::getVar( 'limit', 10));
    //$limitstart = intval(JRequest::getVar( 'limitstart', 0));
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
$database = &JFactory::getDBO();
    global  $my;
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
    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=edit2&uid=" . $id);
}

//===============================
//   User Profile functions
//===============================
function showProfiles($database, $option, $lang, $order)
{
    global $mainframe;
    $database = &JFactory::getDBO();
    //$limit = intval(JRequest::getVar( 'limit', 10));
    //$limitstart = intval(JRequest::getVar( 'limitstart', 0));
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

    jimport('joomla.html.pagination');
    $pageNavSP = new JPagination( $total, $limitstart, $limit );
    html_Kunena::showProfiles($option, $lang, $profileList, $countPL, $pageNavSP, $order, $search);
}

function editUserProfile($uid)
{
   $database = &JFactory::getDBO();
    $acl = &JFactory::getACL();

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
    $yesnoRank[] = JHTML::_('select.option','0', 'No Rank Assigned');
    foreach ($specialRanks as $ranks)
    {
    	$yesnoRank[] = JHTML::_('select.option',$ranks->rank_id, $ranks->rank_title);
    }
    //build special ranks select list
    $selectRank = JHTML::_('select.genericlist',$yesnoRank, 'newrank', 'class="inputbox" size="5"', 'value', 'text', $userRank);

    // make the select list for the view type
    $yesno[] = JHTML::_('select.option','flat', _COM_A_FLAT);
    $yesno[] = JHTML::_('select.option','threaded', _COM_A_THREADED);
    // build the html select list
    $selectPref = JHTML::_('select.genericlist',$yesno, 'newview', 'class="inputbox" size="2"', 'value', 'text', $prefview);
    // make the select list for the moderator flag
    $yesnoMod[] = JHTML::_('select.option','1', _ANN_YES);
    $yesnoMod[] = JHTML::_('select.option','0', _ANN_NO);
    // build the html select list
    $selectMod = JHTML::_('select.genericlist',$yesnoMod, 'moderator', 'class="inputbox" size="2"', 'value', 'text', $moderator);
    // make the select list for the moderator flag
    $yesnoOrder[] = JHTML::_('select.option','0', _USER_ORDER_ASC);
    $yesnoOrder[] = JHTML::_('select.option','1', _USER_ORDER_DESC);
    // build the html select list
    $selectOrder = JHTML::_('select.genericlist',$yesnoOrder, 'neworder', 'class="inputbox" size="2"', 'value', 'text', $ordering);

    //get all subscriptions for this user
    $database->setQuery("select thread from #__fb_subscriptions where userid=$uid[0]");
    $subslist = $database->loadObjectList();
    	check_dberror('Unable to load subscriptions for user.');

    //get all moderation category ids for this user
    $database->setQuery("select catid from #__fb_moderation where userid=". $uid[0]);
    $_modCats = $database->loadResultArray();
    	check_dberror('Unable to moderation category ids for user.');

    $__modCats = array();

    foreach ($_modCats as $_v) {
        $__modCats[] = JHTML::_('select.option', $_v );
    }

    $modCats = KUNENA_GetAvailableModCats($__modCats);

    html_Kunena::editUserProfile($user, $subslist, $selectRank, $selectPref, $selectMod, $selectOrder, $uid[0], $modCats);
}

function saveUserProfile($option)
{
global $mainframe;
    $database = &JFactory::getDBO();
    $newview = JRequest::getVar( 'newview');
    $newrank = JRequest::getVar( 'newrank');
    $signature = JRequest::getVar( 'message');
    $deleteSig = JRequest::getVar( 'deleteSig');
    $moderator = JRequest::getVar( 'moderator');
    $uid = JRequest::getVar( 'uid');
    $avatar = JRequest::getVar( 'avatar');
    $deleteAvatar = JRequest::getVar( 'deleteAvatar');
    $neworder = JRequest::getVar( 'neworder');
    $modCatids = JRequest::getVar( 'catid', array());

    if ($deleteSig == 1) {
        $signature = "";
    }

    if ($deleteAvatar == 1) {
        $avatar = "";
    }

    $database->setQuery("UPDATE #__fb_users set signature='$signature', view='$newview',moderator='$moderator', avatar='$avatar', ordering='$neworder', rank='$newrank' where userid=$uid");
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

    $mainframe->redirect( JURI::base() ."index2.php?option=com_kunena&task=showprofiles");
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
    $forumList['forum'] = JHTML::_('select.genericlist',$forums_list, 'prune_forum', 'class="inputbox" size="4"', 'value', 'text', '');
    html_Kunena::pruneforum($option, $forumList);
}

function doprune($database, $option)
{
global $mainframe ;
    $catid = intval(JRequest::getVar( 'prune_forum', -1));
    $deleted = 0;

    if ($catid == -1)
    {
        echo "<script> alert('" . _KUNENA_CHOOSEFORUMTOPRUNE . "'); window.history.go(-1); </script>\n";
        exit();
    }

    $prune_days = intval(JRequest::getVar( 'prune_days', 0));
    //get the thread list for this forum
    $database->setQuery("SELECT DISTINCT a.thread AS thread, max(a.time) AS lastpost, c.locked AS locked " . "\n FROM #__fb_messages AS a" . "\n JOIN #__fb_categories AS b ON a.catid=b.id " . "\n JOIN #__fb_messages   AS c ON a.thread=c.thread"
                            . "\n where a.catid=$catid " . "\n and b.locked != 1 " . "\n and a.locked != 1 " . "\n and c.locked != 1 " . "\n and c.parent = 0 " . "\n and c.ordering != 1 " . "\n group by thread");
    $threadlist = $database->loadObjectList();
        check_dberror("Unable to load thread list.");

    // Convert days to seconds for timestamp functions...
    $prune_date = FBTools::fbGetInternalTime() - ($prune_days * 86400);

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

    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=pruneforum", "" . _KUNENA_FORUMPRUNEDFOR . " " . $prune_days . " " . _KUNENA_PRUNEDAYS . "; " . _KUNENA_PRUNEDELETED . "" . $deleted . " " . _KUNENA_PRUNETHREADS . "");
}

//===============================
// Sync users
//===============================
function syncusers($database, $option) {
    html_Kunena::syncusers($option);
}

function douserssync($database, $option)
{
    global $mainframe;

    $database = &JFactory::getDBO();
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
        $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=pruneusers", "" . _KUNENA_USERSSYNCDELETED . "" . $cids . " " . _KUNENA_SYNCUSERPROFILES . "");
    }
    else
    {
        $cids = 0;
        $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=pruneusers", _KUNENA_NOPROFILESFORSYNC);
    }
}

//===============================
// Load Sample Data
//===============================
function loadSample($database, $option)
{
$database = &JFactory::getDBO();
global $mainframe;
 	// Load Sample Categories
	$query = "INSERT INTO `#__fb_categories` "
                                . "\n (`id`, `parent`, `name`, `cat_emoticon`, `locked`, `alert_admin`, `moderated`, `moderators`, `pub_access`, `pub_recurse`, `admin_access`, `admin_recurse`, `ordering`, `future2`, `published`, `checked_out`, `checked_out_time`, `review`, `hits`, `description`, `id_last_msg`, `time_last_msg`, `numTopics`, `numPosts`)"
				. "\n VALUES (1, 0, 'Sample Board (Level 1 Category)', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 'Description for level 1 Category board.', 0, 0, 0, 0),"
				. "\n (2, 1, 'Level 2 Category', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 'Level 2 Category description.', 0, 0, 0, 0),"
				. "\n (3, 2, 'Level 3 Category A', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 3, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '', 0, 0, 0, 0),"
				. "\n (4, 2, 'Level 3 Category B', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 2, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '', 0, 0, 0, 0),"
				. "\n (5, 2, 'Level 3 Category C', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 1, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '', 0, 0, 0, 0),"
				. "\n (6, 1, 'Sample Locked Forum', 0, 1, 0, 1, NULL, 0, 0, 0, 0, 2, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 'Nobody, except Moderators and Admins can create new topics or replies in a locked forum (or move posts to it).', 0, 0, 0, 0),"
				. "\n (7, 1, 'Sample Review On Forum', 0, 0, 0, 1, NULL, 0, 0, 0, 0, 3, 0, 1, 0, '0000-00-00 00:00:00', 1, 0, 'Posts to be reviewed by Moderators prior to publishing them in this forum. This is useful in a Moderated forum only! If you set this without any Moderators specified, the Site Admin is solely responsible for approving/deleting submitted posts as these will be kept ''on hold''!', 0, 0, 0, 0)";

	$database->setQuery( $query );
	$database->query() or trigger_dbwarning("Unable to insert sample categories.");

	// Load Sample Messages
	$query = "INSERT INTO `#__fb_messages` "
				. "\n VALUES (1, 0, 1, 2, 'Kunena', 0, 'anonymous@forum.here', 'Sample Post', 1178882702, '127.0.0.1', 0, 0, 0, 0, 1, 0, 0, 0, 0)";

	$database->setQuery( $query );
	$database->query() or trigger_dbwarning("Unable to insert sample messages.");

	// Load Sample Messages Text
	$query = "INSERT INTO `#__fb_messages_text` "
				. "\n VALUES (1, 'Kunena is fully integrated forum solution for joomla, no bridges, no hacking core files: It can be installed just like any other component with only a few clicks.\r\n\r\nThe administration backend is fully integrated, native ACL implemented, and it has all the capabilities one would have come to expect from a mature, full-fledged forum solution!')";
	$database->query() or trigger_dbwarning("Unable to insert sample messages texts.");

    $mainframe->redirect( JURI::base() ."index2.php?option=$option", _KUNENA_SAMPLESUCCESS);
}

//===============================
// Remove Sample Data
//===============================
function removeSample($database, $option)
{
global $mainframe;
$database = &JFactory::getDBO();
 	// Remove Sample Categories
	$database->setQuery("DELETE FROM #__fb_categories WHERE id BETWEEN 1 AND 7");
	$database->query();
		check_dberror("Unable to remove sample categories.");
	// Remove Sample Messages
	$database->setQuery("DELETE FROM #__fb_messages WHERE id = 1");
	$database->query();
		check_dberror("Unable to remove sample messages.");
	// Remove Sample Messages Text
	$database->setQuery("DELETE FROM #__fb_messages_text WHERE id = 1");
	$database->query();
		check_dberror("Unable to remove sample messages texts.");

	$mainframe->redirect( JURI::base() ."index2.php?option=$option", _KUNENA_SAMPLEREMOVED);
}

//===============================
// Create Community Builder profile
//===============================
function loadCBprofile($database, $option)
{
global $mainframe;
$database = &JFactory::getDBO();
    // First remove any existing entries
    // Not the most elegant way to deal with duplicate entries, but certainly the most foolproof
    // No checking for non existing DB content
    $database->setQuery("ALTER TABLE #__comprofiler DROP fbviewtype, DROP fbordering, DROP fbsignature");
    $database->query() or trigger_dbwarning("Unable to drop comprofiler columns.");

    $database->setQuery("DELETE FROM #__comprofiler_field_values WHERE fieldtitle IN ".
    	"('_UE_KUNENA_VIEWTYPE_FLAT','_UE_KUNENA_VIEWTYPE_THREADED','_UE_KUNENA_ORDERING_OLDEST','_UE_KUNENA_ORDERING_LATEST')");
    $database->query() or trigger_dberror("Unable to delete comprofiler field values.");

    $database->setQuery("DELETE FROM #__comprofiler_fields WHERE name IN ('fbviewtype','fbordering','fbsignature')");
    $database->query() or trigger_dberror("Unable to delete comprofiler fields.");

    $database->setQuery("DELETE FROM #__comprofiler_tabs WHERE title = '_UE_KUNENA_TABTITLE'");
    $database->query() or trigger_dberror("Unable to delete comprofiler field.");

    // Now let's create the requires entries
    $database->setQuery("INSERT INTO #__comprofiler_tabs SET title='_UE_KUNENA_TABTITLE', description='_UE_KUNENA_TABDESC'");
    $database->query() or trigger_dberror("Unable to insert comprofiler tab.");

    $database->setQuery("SELECT tabid FROM #__comprofiler_tabs WHERE title='_UE_KUNENA_TABTITLE'");
    $database->query() or trigger_dberror("Unable to load comprofiler tab.");
    $tabid = $database->loadResult();

    $cols = $database->getTableFields(array('#__comprofiler_fields'));
    $isCB12 = isset($cols['#__comprofiler_fields']['tablecolumns']);

    $database->setQuery("INSERT INTO #__comprofiler_fields (name,".($isCB12?"tablecolumns,":"")."title,type,maxlength,cols,rows,ordering,published,profile,calculated,sys,tabid) VALUES ".
    	"('fbviewtype',".($isCB12?"'fbviewtype',":"")."'_UE_KUNENA_VIEWTYPE_TITLE','select',0,0,0,1,1,0,0,0,$tabid),".
    	"('fbordering',".($isCB12?"'fbordering',":"")."'_UE_KUNENA_ORDERING_TITLE','select',0,0,0,2,1,0,0,0,$tabid),".
    	"('fbsignature',".($isCB12?"'fbsignature',":"")."'_UE_KUNENA_SIGNATURE','textarea',300,60,5,3,1,0,0,0,$tabid)");
    $database->query() or trigger_dberror("Unable to insert comprofiler fields.");

    $database->setQuery("SELECT name,fieldid FROM #__comprofiler_fields WHERE name IN ('fbviewtype','fbordering')");
    $database->query() or trigger_dberror("Unable to load comprofiler fields.");
    $fieldid = $database->loadObjectList('name');

    $database->setQuery("INSERT INTO #__comprofiler_field_values (fieldid,fieldtitle,ordering) VALUES ".
    	"(".$fieldid['fbviewtype']->fieldid.",'_UE_KUNENA_VIEWTYPE_FLAT',1),".
    	"(".$fieldid['fbviewtype']->fieldid.",'_UE_KUNENA_VIEWTYPE_THREADED',2),".
    	"(".$fieldid['fbordering']->fieldid.",'_UE_KUNENA_ORDERING_OLDEST',1),".
    	"(".$fieldid['fbordering']->fieldid.",'_UE_KUNENA_ORDERING_LATEST',2)");
    $database->query() or trigger_dberror("Unable to insert comprofiler field values.");

    $database->setQuery("ALTER TABLE #__comprofiler ".
    	"ADD fbviewtype varchar(255) DEFAULT '_UE_KUNENA_VIEWTYPE_FLAT' NOT NULL, ".
		"ADD fbordering varchar(255) DEFAULT '_UE_KUNENA_ORDERING_OLDEST' NOT NULL, ".
		"ADD fbsignature mediumtext");
    $database->query() or trigger_dberror("Unable to add signature column.");

    $mainframe->redirect( JURI::base() ."index2.php?option=$option", _KUNENA_CBADDED);
}

//===============================
// Uploaded Images browser
//===============================
function browseUploaded($database, $option, $type)
{
$database = &JFactory::getDBO();
    if ($type)
    { //we're doing images
        $dir = @opendir(KUNENA_PATH_UPLOADED. '/images');
        $uploaded_path = KUNENA_PATH_UPLOADED. '/images';
    }
    else
    { //we're doing regular files
        $dir = @opendir(KUNENA_PATH_UPLOADED.'/files');
        $uploaded_path = KUNENA_PATH_UPLOADED.'/files';
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
$database = &JFactory::getDBO();
	if (!$imageName) {
		$mainframe->redirect( JURI::base() ."index2.php?option=$option&task=browseImages");
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

    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=browseImages", _KUNENA_IMGDELETED);
}

function deleteFile($database, $option, $fileName)
{
$database = &JFactory::getDBO();
    global $mosConfig_admin_template;

    if (!$fileName) {
    	$mainframe->redirect( JURI::base() ."index2.php?option=$option&task=browseFiles");
    	return;
    }

    // step 1: Remove file
    unlink (KUNENA_ABSUPLOADEDPATH.'/files/' . $fileName);
    //step 2: remove the database link to the file
    $database->setQuery("DELETE FROM #__fb_attachments where filelocation='" . KUNENA_ABSUPLOADEDPATH . "/files/" . $fileName . "'");
    $database->query() or trigger_dberror("Unable to delete attachment.");
    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=browseFiles", _KUNENA_FILEDELETED);
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
    $database = &JFactory::getDBO();
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
    $mitems[] = JHTML::_('select.option', '0', JText::_(_KUNENA_NOPARENT), 'value', 'text');
    $this_treename = '';

    foreach ($list as $item)
    {
        if ($this_treename)
        {
            if ($item->id != $mitems && strpos($item->treename, $this_treename) === false) {
                $mitems[] = JHTML::_('select.option',$item->id, $item->treename);
            }
        }
        else
        {
            if ($item->id != $mitems) {
                $mitems[] = JHTML::_('select.option', $item->id, $item->treename);
            }
            else {
                $this_treename = "$item->treename/";
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
        chmod($dstdir, 0777);
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
$database = &JFactory::getDBO();
    global $mainframe;

    $limit = intval(JRequest::getVar('limit', 10));
    $limitstart = intval(JRequest::getVar('limitstart', 0));
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
    html_Kunena::showsmilies($option, KUNENA_LANGUAGE, $smileytmp, $pageNavSP, $smileypath);

}

function editsmiley($option, $id)
{
	global  $mainframe;

	$database = &JFactory::getDBO();
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
    html_Kunena::editsmiley($option, KUNENA_LANGUAGE, $smiley_edit_img, $filename_list, $smileypath, $smileycfg);
}

function newsmiley($option)
{
	global  $mainframe;

	$database = &JFactory::getDBO();

	$smiley_images = collect_smilies();
    $smileypath = smileypath();
    $smileypath = $smileypath['live'].'/';

    $filename_list = "";
	for( $i = 0; $i < count($smiley_images); $i++ )
	{
		$filename_list .= '<option value="' . $smiley_images[$i] . '">' . $smiley_images[$i] . '</option>'."\n";
    }

    html_Kunena::newsmiley($option, $filename_list, $smileypath);
}

function savesmiley($option, $id = NULL)
{
    global  $mainframe;
	$database = &JFactory::getDBO();

    $smiley_code = JRequest::getVar( 'smiley_code');
    $smiley_location = JRequest::getVar( 'smiley_url');
    $smiley_emoticonbar = (JRequest::getVar( 'smiley_emoticonbar')) ? JRequest::getVar( 'smiley_emoticonbar') : 0;

    if (empty($smiley_code) || empty($smiley_location))
    {
    	$task = ($id == NULL) ? 'newsmiley' : 'editsmiley&id='.$id;
        $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=".$task, _KUNENA_MISSING_PARAMETER);
        exit();
    }

    $database->setQuery("SELECT * FROM #__fb_smileys");

    $smilies = $database->loadAssocList();
    foreach ($smilies as $value)
    {
    	if (in_array($smiley_code, $value) && !($value['id'] == $id))
    	{
            $task = ($id == NULL) ? 'newsmiley' : 'editsmiley&id='.$id;
        	$mainframe->redirect( JURI::base() ."index2.php?option=$option&task=".$task, _KUNENA_CODE_ALLREADY_EXITS);
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

    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=showsmilies", _KUNENA_SMILEY_SAVED);
}

function deletesmiley($option, $cid)
{
	global $mainframe;

	$database = &JFactory::getDBO();

	if ($cids = implode(',', $cid)) {
		$database->setQuery("DELETE FROM #__fb_smileys WHERE id IN ($cids)");
		$database->query() or trigger_dberror("Unable to delete smiley.");
	}

    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=showsmilies", _KUNENA_SMILEY_DELETED);
}

function smileypath()
{
    global $mainframe;
	global $fbConfig;

	if (is_dir(JPATH_ROOT . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.KUNENA_LANGUAGE.'/emoticons')) {
        $smiley_live_path = $mainframe->getCfg('live_site') . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.KUNENA_LANGUAGE.'/emoticons';
        $smiley_abs_path = JPATH_ROOT . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.KUNENA_LANGUAGE.'/emoticons';
    }
    else {
        $smiley_live_path = JPATH_ROOT . '/components/com_kunena/template/default/images/'.KUNENA_LANGUAGE.'/emoticons';
        $smiley_abs_path = JPATH_ROOT . '/components/com_kunena/template/default/images/'.KUNENA_LANGUAGE.'/emoticons';
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
    global $mainframe, $order;

    $database = &JFactory::getDBO();

    //$limit = intval(JRequest::getVar( 'limit', 10));
	//$limitstart = intval(JRequest::getVar( 'limitstart', 0));
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
	html_Kunena::showRanks( $option,KUNENA_LANGUAGE,$ranks,$pageNavSP,$order,$rankpath );

}

function rankpath()
{
    global $mainframe;
	global $fbConfig;

    if (is_dir(JURI::root() . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.KUNENA_LANGUAGE.'/ranks')) {
        $rank_live_path = JURI::root() . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.KUNENA_LANGUAGE.'/ranks';
        $rank_abs_path = 	JPATH_ROOT . '/components/com_kunena/template/'.$fbConfig->template.'/images/'.KUNENA_LANGUAGE.'/ranks';
    }
    else {
        $rank_live_path = JURI::root() . '/components/com_kunena/template/default/images/'.KUNENA_LANGUAGE.'/ranks';
        $rank_abs_path = 	JPATH_ROOT . '/components/com_kunena/template/default/images/'.KUNENA_LANGUAGE.'/ranks';
    }

    $rankpath['live'] = $rank_live_path;
    $rankpath['abs'] = $rank_abs_path;

    return $rankpath;

}

function collectRanks()
{
	$rankpath = rankpath();

    $dir = @opendir($rankpath['abs']);

	while($file = @readdir($dir))
	{
		if( !@is_dir($rank_abs_path . '/' . $file) )
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
$database = &JFactory::getDBO();
	global  $mainframe;

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
$database = &JFactory::getDBO();
	global $mainframe;

	if ($cids = implode(',', $cid)) {
		$database->setQuery("DELETE FROM #__fb_ranks WHERE rank_id IN ($cids)");
		$database->query() or trigger_dberror("Unable to delete rank.");
	}

    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=ranks", _KUNENA_RANK_DELETED);
}

function saveRank($option, $id = NULL)
{
	global $mainframe;
	$database = &JFactory::getDBO();

    $rank_title = JRequest::getVar( 'rank_title');
    $rank_image = JRequest::getVar( 'rank_image');
    $rank_special = JRequest::getVar( 'rank_special');
    $rank_min = JRequest::getVar( 'rank_min');

    if (empty($rank_title) || empty($rank_image))
    {
    	$task = ($id == NULL) ? 'newRank' : 'editRank&id='.$id;
        $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=".$task, _KUNENA_MISSING_PARAMETER);
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
        	$mainframe->redirect( JURI::base() ."index2.php?option=$option&task=".$task, _KUNENA_RANK_ALLREADY_EXITS);
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

    $mainframe->redirect( JURI::base() ."index2.php?option=$option&task=ranks", _KUNENA_RANK_SAVED);
}

function editRank($option, $id)
{
    $database = &JFactory::getDBO();
	global $mainframe;

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

			$filename_list .= '<option value="' . htmlspecialchars($img) . '"' . $selected . '>' . $img . '</option>';
		}
	}

    html_Kunena::editRank($option, KUNENA_LANGUAGE, $edit_img, $filename_list, $path, $row);
}

//===============================
//  FINISH smiley functions
//===============================
// Dan Syme/IGD - Ranks Management

function KUNENA_GetAvailableModCats($catids) {
    $database = &JFactory::getDBO();
    $list = JJ_categoryArray(1);
    $this_treename = '';
    $catid = 0;

    foreach ($list as $item) {
        if ($this_treename) {
            if ($item->id != $catid && strpos($item->treename, $this_treename) === false) {
                $options[] = JHTML::_('select.option',$item->id, $item->treename);
                }
            }
        else {
            if ($item->id != $catid) {
                $options[] = JHTML::_('select.option',$item->id, $item->treename);
                }
            else {
                $this_treename = "$item->treename/";
                }
            }
        }
    $parent = JHTML::_('select.genericlist',$options, 'catid[]', 'class="inputbox fbs"  multiple="multiple"   id="FB_AvailableForums" ', 'value', 'text', $catids);
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
