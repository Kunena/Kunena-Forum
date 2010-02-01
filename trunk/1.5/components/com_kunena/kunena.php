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

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

@error_reporting(E_ERROR | E_WARNING | E_PARSE);

// First of all take a profiling information snapshot for JFirePHP
if(JDEBUG == 1){
	require_once (JPATH_BASE . DS . 'components' . DS . 'com_kunena' . DS . 'lib' . DS . 'kunena.profiler.php');
	$__profiler = KProfiler::GetInstance();
	$__profiler->mark('Start');
}

// Just for debugging and performance analysis
$mtime = explode(" ", microtime());
$tstart = $mtime[1] + $mtime[0];

// Kunena wide defines
require_once (JPATH_BASE  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');

global $fbIcons;
global $is_Moderator;

// ERROR: global scope mix
global $message;
global $Itemid;

// Get all the variables we need and strip them in case
$action 		= JRequest::getCmd('action', '');
$catid 			= JRequest::getInt('catid', 0);
$contentURL 	= JRequest::getVar('contentURL', '');
$do 			= JRequest::getCmd('do', '');
$email 			= JRequest::getVar('email', '');
$favoriteMe 	= JRequest::getVar('favoriteMe', '');
$fb_authorname 	= JRequest::getVar('fb_authorname', '');
$fb_thread 		= JRequest::getInt('fb_thread', 0);
$func 			= strtolower(JRequest::getCmd('func', ''));
$id 			= JRequest::getInt('id', 0);
$limit 			= JRequest::getInt('limit', 0);
$limitstart 	= JRequest::getInt('limitstart', 0);
$markaction 	= JRequest::getVar('markaction', '');
$message 		= JRequest::getVar('message', '');
$page 			= JRequest::getInt('page', 0);
$parentid 		= JRequest::getInt('parentid', 0);
$pid 			= JRequest::getInt('pid', 0);
$replyto 		= JRequest::getInt('replyto', 0);
$resubject 		= JRequest::getVar('resubject', '');
$return 		= JRequest::getVar('return', '');
$rowid 			= JRequest::getInt('rowid', 0);
$rowItemid 		= JRequest::getInt('rowItemid', 0);
$sel 			= JRequest::getVar('sel', '');
$subject 		= JRequest::getVar('subject', '');
$subscribeMe 	= JRequest::getVar('subscribeMe', '');
$thread 		= JRequest::getInt('thread', 0);
$topic_emoticon = JRequest::getVar('topic_emoticon', '');
$userid 		= JRequest::getInt('userid', 0);
$view 			= JRequest::getVar('view', '');
$msgpreview 	= JRequest::getVar('msgpreview', '');
$no_html		= JRequest::getBool('no_html', 0);

$app = JFactory::getApplication();

// Redirect Forum Jump
if (isset($_POST['func']) && $func == "showcat")
{
	header("HTTP/1.1 303 See Other");
	header("Location: " . htmlspecialchars_decode(JRoute::_('index.php?option=com_kunena&amp;Itemid=' . $Itemid . '&amp;func=showcat&amp;catid=' . $catid)));
	$app->close();
}

// Image does not work if there are included files (extra characters), so we will do it now:
if ($func == "showcaptcha") {
   include (JPATH_ROOT . '/components/com_kunena/template/default/plugin/captcha/randomImage.php');
   $app->close();
}

// Debug helpers
include_once (KUNENA_PATH_LIB .DS. "kunena.debug.php");

// get Kunenas configuration params in
require_once (KUNENA_PATH_LIB .DS. "kunena.config.class.php");

// Get CKunanaUser and CKunenaUsers
require_once (KUNENA_PATH_LIB .DS. "kunena.user.class.php");

global $fbConfig, $kunenaProfile;

// Get data about the current user - its ok to not have a userid = guest
$kunena_my = &JFactory::getUser();
$KunenaUser = new CKunenaUser($kunena_my->id);
// Load configuration and personal settings for current user
$fbConfig =& CKunenaConfig::getInstance();

// get right Language file
if (file_exists(KUNENA_FILE_LANGUAGE)) {
    include_once (KUNENA_FILE_LANGUAGE);
} else {
    include_once (KUNENA_FILE_LANGUAGE_DEFAULT);
}

$kn_tables =& CKunenaTables::getInstance();
if ($kn_tables->installed() === false) {
	$fbConfig->board_offline = 1;
}

// Permissions: Check for administrators and moderators
global $aro_group;
$kunena_acl = &JFactory::getACL();
if ($kunena_my->id != 0)
{
    $aro_group = $kunena_acl->getAroGroup($kunena_my->id);
   	$aro_group->id = $aro_group->id;
    $is_admin = (strtolower($aro_group->name) == 'super administrator' || strtolower($aro_group->name) == 'administrator');
}
else
{
    $aro_group = new StdClass();
    $aro_group->id = 0;
    $is_admin = 0;
}

//Get the userid; sometimes the new var works whilst $kunena_my->id doesn't..?!?
$my_id = $kunena_my->id;

// Check if we only allow registered users
if ($fbConfig->regonly && !$my_id)
{
    echo '<div>' . _FORUM_UNAUTHORIZIED . '</div>';
    echo '<div>' . _FORUM_UNAUTHORIZIED2 . '</div>';
}
// or if the board is offline
else if ($fbConfig->board_offline && !$is_admin)
{
    echo stripslashes($fbConfig->offline_message);
}
else
{
// =======================================================================================
// Forum is online:

global $lang, $fbIcons;
global $is_Moderator;

// ERROR: global scope mix
global $message;

// Central Location for all internal links
require_once (KUNENA_PATH_LIB .DS. "kunena.link.class.php");

// Class structure should be used after this and all the common task should be moved to this class
require_once (KUNENA_PATH .DS. "class.kunena.php");

if (file_exists(KUNENA_ABSTMPLTPATH .DS. 'smile.class.php'))
{
	require_once (KUNENA_ABSTMPLTPATH .DS. 'smile.class.php');
}
else
{
	require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'smile.class.php');
}

//intercept the RSS request; we should stop afterwards
if ($func == 'fb_rss')
{
    include (KUNENA_PATH_LIB .DS. 'kunena.rss.php');
    $app->close();
}

if ($func == 'fb_pdf')
{
    include (KUNENA_PATH_LIB .DS. 'kunena.pdf.php');
    $app->close();
}

// Include Clexus PM class file
if ($fbConfig->pm_component == "clexuspm")
{
    require_once (KUNENA_ROOT_PATH .DS. 'components/com_mypms/class.mypms.php');
    $ClexusPMconfig = new ClexusPMConfig();
}

//time format
include_once (KUNENA_PATH_LIB .DS. 'kunena.timeformat.class.php');

$systime = time() + $fbConfig->board_ofset * KUNENA_SECONDS_IN_HOUR;

// Retrieve current cookie data for session handling
$settings = !empty($_COOKIE['fboard_settings'])?$_COOKIE['fboard_settings']:'';

$board_title = $fbConfig->board_title;
$fromBot = 0;
$prefview = $fbConfig->default_view;

// JOOMLA STYLE CHECK
if ($fbConfig->joomlastyle < 1) {
    $boardclass = "fb_";
    }

// Include preview here before inclusion of other files
if ($func == "getpreview")
{
    $message = utf8_urldecode(utf8_decode(stripslashes($msgpreview)));

    $smileyList = smile::getEmoticons(1);
    $msgbody = smile::smileReplace( $message , 0, $fbConfig->disemoticons, $smileyList);
    $msgbody = nl2br($msgbody);
    $msgbody = str_replace("__FBTAB__", "\t", $msgbody);
	$msgbody = CKunenaTools::prepareContent($msgbody);
    // $msgbody = ereg_replace('%u0([[:alnum:]]{3})', '&#x1;',$msgbody);

    $msgbody = smile::htmlwrap($msgbody, $fbConfig->wrap);
    header("Content-Type: text/html; charset=utf-8");
    echo $msgbody;
    $app->close();
}

if ($no_html == 0) {
$document =& JFactory::getDocument();

// inline jscript with image location
$document->addScriptDeclaration('jr_expandImg_url = "' . KUNENA_URLIMAGESPATH . '";');

if (is_object($kunenaProfile) && $kunenaProfile->useProfileIntegration())
{
	if (defined('KUNENA_COREJSURL'))
	{
		global $_CB_framework;
		$_CB_framework->addJQueryPlugin( 'kunena_tmpl', KUNENA_COREJSPATH );
		$_CB_framework->outputCbJQuery( '', 'kunena_tmpl' );
	}
}
else
{
	// Add required header tags
	if (defined('KUNENA_JQURL') && !defined('J_JQUERY_LOADED'))
	{
		define('J_JQUERY_LOADED', 1);
		if (!defined('C_ASSET_JQUERY')) define('C_ASSET_JQUERY', 1);
		$document->addScript(KUNENA_JQURL);
	}

	if (defined('KUNENA_COREJSURL'))
	{
		$document->addScript(KUNENA_COREJSURL);
	}
}

if ($fbConfig->joomlastyle < 1) {
	if (file_exists(KUNENA_JTEMPLATEPATH.'/css/kunena.forum.css'))
	{
		$document->addStyleSheet(KUNENA_JTEMPLATEURL . '/css/kunena.forum.css');
	}
	else
	{
		$document->addStyleSheet(KUNENA_TMPLTCSSURL);
	}
}
else
{
	$document->addStyleSheet(KUNENA_DIRECTURL . '/template/default/joomla.css');
}
} // no_html == 0

// WHOIS ONLINE IN FORUM
if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/who/who.class.php')) {
    include (KUNENA_ABSTMPLTPATH . '/plugin/who/who.class.php');
    }
else {
    include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/who/who.class.php');
    }

// include required libraries
if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_layout.php')) {
    require_once (KUNENA_ABSTMPLTPATH . '/fb_layout.php');
    }
else {
    require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_layout.php');
    }

require_once (KUNENA_PATH_LIB .DS. 'kunena.permissions.php');
require_once (KUNENA_PATH_LIB .DS. 'kunena.category.class.php');
require_once (JPATH_BASE.'/libraries/joomla/template/template.php');

if ($catid != '') {
    $thisCat = new jbCategory($kunena_db, $catid);
    }

$KunenaTemplate = new patTemplate();
$KunenaTemplate->setRoot( KUNENA_ABSTMPLTPATH );

$KunenaTemplate->readTemplatesFromFile("header.html");
$KunenaTemplate->readTemplatesFromFile("footer.html");

$is_Moderator = fb_has_moderator_permission($kunena_db, $thisCat, $kunena_my->id, $is_admin);

if ($func == '') // Set default start page as per config settings
{
	switch ($fbConfig->fbdefaultpage)
	{
		case 'recent':
			$func = 'latest';
			break;
		case 'my':
			$func = $kunena_my->id > 0 ? 'mylatest' : 'latest';
			break;
		default:
			$func = 'listcat';
	}
}

// Kunena Current Template Icons Pack
// See if there's an icon pack installed
$useIcons = 0; //init
$fbIcons = 0;

if (file_exists(KUNENA_ABSTMPLTPATH . '/icons.php'))
{
    include_once (KUNENA_ABSTMPLTPATH . '/icons.php');
    $useIcons = 1;
}
else
{
    include_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS.  'icons.php');
}

require_once (KUNENA_PATH_LIB .DS. 'kunena.session.class.php');

	// We only do the session handling for registered users
	// No point in keeping track of whats new for guests
	global $fbSession;
	$fbSession =& CKunenaSession::getInstance(true);
	if ($kunena_my->id > 0)
	{
		// First we drop an updated cookie, good for 1 year
		// We have consolidated multiple instances of cookie management into this single location
		// NOT SURE IF WE STILL NEED THIS ONE after session management got dbtized
		setcookie("fboard_settings[member_id]", $kunena_my->id, time() + KUNENA_SECONDS_IN_YEAR, '/');

		// new indicator handling
		if ($markaction == "allread") {
			$fbSession->markAllCategoriesRead();
		}

		$fbSession->updateAllowedForums($kunena_my->id, $aro_group, $kunena_acl);

		// save fbsession
		$fbSession->save($fbSession);

		if ($markaction == "allread") {
		        $app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)), _GEN_ALL_MARKED);
		}

		// Now lets get the view type for the forum
		$kunena_db->setQuery("SELECT view FROM #__fb_users WHERE userid='{$kunena_my->id}'");
		$prefview = $kunena_db->loadResult();
			check_dberror('Unable load default view type for user.');

		// If the prefferred view comes back empty this must be a new user
		// who does not yet have a Kunena profile -> lets create one
		if ($prefview == "")
		{
			$prefview = $fbConfig->default_view;

			$kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_users WHERE userid='{$kunena_my->id}'");
			$userexists = $kunena_db->loadResult();
			check_dberror('Unable load default view type for user.');
			if (!$userexists)
			{
				// there's no profile; set userid and the default view type as preferred view type.
				$kunena_db->setQuery("insert into #__fb_users (userid,view,moderator) values ('$kunena_my->id','$prefview','$is_admin')");
				$kunena_db->query();
					check_dberror('Unable to create user profile.');
			}
		}

	    // Assign previous visit without user offset to variable for templates to decide
		// whether or not to use the NEW indicator on forums and posts
		$prevCheck = $fbSession->lasttime; // - KUNENA_OFFSET_USER; Don't use the user offset - it throws the NEW indicator off
	}
	else
	{
		// collect accessaible categories for guest user
		$kunena_db->setQuery("SELECT id FROM #__fb_categories WHERE pub_access='0' AND published='1'");
		$fbSession->allowed =
			($arr_pubcats = $kunena_db->loadResultArray())?implode(',', $arr_pubcats):'';
			check_dberror('Unable load accessible categories for user.');

		// For guests we don't show new posts
		$prevCheck = $systime;
		$fbSession->readtopics = '';
	}

	// no access to categories?
	if (!$fbSession->allowed) $fbSession->allowed = '0';

	// Integration with GroupJive, Jomsocial:
	$params = array($kunena_my->id, &$fbSession->allowed);
	if (is_object($kunenaProfile))
		$kunenaProfile->trigger('getAllowedForumsRead', $params);

//Disabled threaded view option for Kunena
//    //Initial:: determining what kind of view to use... from profile, cookie or default settings.
//    //pseudo: if (no view is set and the cookie_view is not set)
//    if ($view == "" && $settings['current_view'] == "")
//    {
//        //pseudo: if there's no prefered type, use FB's default view otherwise use preferred view from profile
//        //and then set the cookie right
//        $view = $prefview == "" ? $fbConfig->default_view : $prefview;
//        setcookie("fboard_settings[current_view]", $view, time() + KUNENA_SECONDS_IN_YEAR, '/');
//    }
//    //pseudo: otherwise if (no view set but cookie isn't empty use view as set in cookie
//    else if ($view == "" && $settings['current_view'] != "")
//  	{
//        $view = $settings['current_view'];
//    }

    $view = "flat";

    //Get the max# of posts for any one user
    $kunena_db->setQuery("SELECT MAX(posts) FROM #__fb_users");
    $maxPosts = $kunena_db->loadResult();
    	check_dberror('Unable load max(posts) for user.');

    //Get the topics this user has already read this session from #__fb_sessions
    $readTopics=$fbSession->readtopics;
    $read_topics = explode(',', $readTopics);

    /*       _\|/_
             (o o)
     +----oOO-{_}-OOo--------------------------------+
     |    Until this section we have included the    |
     |   necessary files and gathered the required   |
     |     variables. Now let's start processing     |
     |                     them                      |
     +----------------------------------------------*/

    //Check if the catid requested is a parent category, because if it is
    //the only thing we can do with it is 'listcat' and nothing else
    if ($func == "showcat" || $func == "view" || $func == "post")
    {
    	if ($catid != 0) {
			$kunena_db->setQuery("SELECT parent FROM #__fb_categories WHERE id='{$catid}'");
			$strCatParent = $kunena_db->loadResult();
			check_dberror('Unable to load categories.');
    	}
        if ($catid == 0 || $strCatParent === '0')
    	{
   			$strcatid = '';
    		if ($catid) $strcatid = "&amp;catid={$catid}";
            $app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=listcat'.$strcatid)));
        }
    }

    switch ($func)
    {
        case 'view':
            $fbMenu = KUNENA_get_menu(NULL, $fbConfig, $fbIcons, $my_id, 3, $view, $catid, $id, $thread);

            break;

        case 'showcat':
            //get number of pending messages
            $kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_messages WHERE catid='$catid' AND hold='1'");
            $numPending = $kunena_db->loadResult();
            	check_dberror('Unable load pending messages.');

            $fbMenu = KUNENA_get_menu(NULL, $fbConfig, $fbIcons, $my_id, 2, $view, $catid, $id, $thread, $is_Moderator, $numPending);
            break;

        default:
            $fbMenu = KUNENA_get_menu(NULL, $fbConfig, $fbIcons, $my_id, 1, $view);

            break;
    }

    // display header
    $KunenaTemplate->addVar('kunena-header', 'menu', $fbMenu);
    $KunenaTemplate->addVar('kunena-header', 'board_title', stripslashes($board_title));
    if (file_exists(KUNENA_JTEMPLATEPATH.'/css/kunena.forum.css')) {
   		$KunenaTemplate->addVar('kunena-header', 'css_path', KUNENA_JTEMPLATEURL . '/template/' . $fbConfig->template . '/kunena.forum.css');
    } else {
   	    $KunenaTemplate->addVar('kunena-header', 'css_path', KUNENA_DIRECTURL . '/template/' . $fbConfig->template . '/kunena.forum.css');
	}

    $KunenaTemplate->addVar('kunena-header', 'offline_message', stripslashes($fbConfig->board_offline) ? '<span id="fbOffline">' . _FORUM_IS_OFFLINE . '</span>' : '');
    $KunenaTemplate->addVar('kunena-header', 'searchbox', getSearchBox());
    $KunenaTemplate->addVar('kunena-header', 'pb_imgswitchurl', KUNENA_URLIMAGESPATH . "shrink.gif");
    $KunenaTemplate->displayParsedTemplate('kunena-header');

    //BEGIN: PROFILEBOX
    if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/profilebox/profilebox.php')) {
        include (KUNENA_ABSTMPLTPATH . '/plugin/profilebox/profilebox.php');
        }
    else {
        include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/profilebox/profilebox.php');
        }
    //FINISH: PROFILEBOX

    switch (strtolower($func))
    {
        case 'who':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/who/who.php')) {
                include (KUNENA_ABSTMPLTPATH . '/plugin/who/who.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/who/who.php');
                }

            break;

        #########################################################################################
        case 'announcement':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/announcement/announcement.php')) {
                include (KUNENA_ABSTMPLTPATH . '/plugin/announcement/announcement.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/announcement/announcement.php');
                }

            break;

        #########################################################################################
        case 'stats':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php')) {
                include (KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/stats/stats.class.php');
                }

            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.php')) {
                include (KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/stats/stats.php');
                }

            break;

        #########################################################################################
        case 'fbprofile':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/fbprofile/fbprofile.php')) {
                include (KUNENA_ABSTMPLTPATH . '/plugin/fbprofile/fbprofile.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/fbprofile/fbprofile.php');
                }

            break;

        #########################################################################################
        case 'userlist':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/userlist/userlist.php')) {
                include (KUNENA_ABSTMPLTPATH . '/plugin/userlist/userlist.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/userlist/userlist.php');
                }

            break;

        #########################################################################################
        case 'post':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/post.php')) {
                include (KUNENA_ABSTMPLTPATH . '/post.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'post.php');
                }

            break;

        #########################################################################################
        case 'view':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/view.php')) {
                include (KUNENA_ABSTMPLTPATH . '/view.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'view.php');
                }

            break;

        #########################################################################################
        case 'faq':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/faq.php')) {
                include (KUNENA_ABSTMPLTPATH . '/faq.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'faq.php');
                }

            break;

        #########################################################################################
        case 'showcat':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/showcat.php')) {
                include (KUNENA_ABSTMPLTPATH . '/showcat.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'showcat.php');
                }

            break;

        #########################################################################################
        case 'listcat':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/listcat.php')) {
                include (KUNENA_ABSTMPLTPATH . '/listcat.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'listcat.php');
                }

            break;

        #########################################################################################
        case 'review':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/moderate_messages.php')) {
                include (KUNENA_ABSTMPLTPATH . '/moderate_messages.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'moderate_messages.php');
                }

            break;

        #########################################################################################
        case 'rules':
            include (KUNENA_PATH_LIB .DS. 'kunena.rules.php');

            break;

        #########################################################################################

        case 'userprofile':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile.php')) {
                include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile.php');
                }

            break;

        #########################################################################################
        case 'myprofile':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile.php')) {
                include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/myprofile/myprofile.php');
                }

            break;

        #########################################################################################
        case 'report':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/report/report.php')) {
                include (KUNENA_ABSTMPLTPATH . '/plugin/report/report.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/report/report.php');
                }

            break;

        #########################################################################################
        case 'latest':
        case 'mylatest':
            if (file_exists(KUNENA_ABSTMPLTPATH . '/latestx.php')) {
                include (KUNENA_ABSTMPLTPATH . '/latestx.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'latestx.php');
                }

            break;

        #########################################################################################
        case 'search':
        case 'advsearch':
            require_once (KUNENA_PATH_LIB .DS. 'kunena.search.class.php');

            $kunenaSearch = &new CKunenaSearch();
            $kunenaSearch->show();
            break;

        #########################################################################################
        case 'markthisread':
            // get all already read topics
            $kunena_db->setQuery("SELECT readtopics FROM #__fb_sessions WHERE userid='{$kunena_my->id}'");
            $allreadyRead = $kunena_db->loadResult();
            	check_dberror("Unable to load read topics.");
            /* Mark all these topics read */
            $kunena_db->setQuery("SELECT thread FROM #__fb_messages WHERE catid='{$catid}' AND thread NOT IN ('{$allreadyRead}') GROUP BY thread");
            $readForum = $kunena_db->loadObjectList();
            	check_dberror("Unable to load messages.");
            $readTopics = '--';

            foreach ($readForum as $rf) {
                $readTopics = $readTopics . ',' . $rf->thread;
                }

            $readTopics = str_replace('--,', '', $readTopics);

            if ($allreadyRead != "") {
                $readTopics = $readTopics . ',' . $allreadyRead;
                }

            $kunena_db->setQuery("UPDATE #__fb_sessions set readtopics='$readTopics' WHERE userid=$kunena_my->id");
            $kunena_db->query();
            	check_dberror('Unable to update readtopics in session table.');

            $app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL.'&amp;func=showcat&amp;catid='.$catid)), _GEN_FORUM_MARKED);
            break;

        #########################################################################################
        case 'karma':
            include (KUNENA_PATH_LIB .DS. 'kunena.karma.php');

            break;

        #########################################################################################
        case 'bulkactions':
            switch ($do)
            {
                case "bulkDel":
                    CKunenaTools::fbDeletePosts( $is_Moderator, $return);

                    break;

                case "bulkMove":
                    CKunenaTools::fbMovePosts($catid, $is_Moderator, $return);
                    break;
            }

            break;

        ######################

        /*    template chooser    */
        case "templatechooser":
            $fb_user_template = strval(JRequest::getVar('fb_user_template', '','COOKIE'));

            $fb_user_img_template = strval(JRequest::getVar('fb_user_img_template', $fb_user_img_template));
            $fb_change_template = strval(JRequest::getVar('fb_change_template', $fb_user_template));
            $fb_change_img_template = strval(JRequest::getVar('fb_change_img_template', $fb_user_img_template));

            if ($fb_change_template)
            {
                // clean template name
                $fb_change_template = preg_replace('#\W#', '', $fb_change_template);

                if (strlen($fb_change_template) >= 40) {
                    $fb_change_template = substr($fb_change_template, 0, 39);
                    }

                // check that template exists in case it was deleted
                if (file_exists(KUNENA_PATH_TEMPLATE .DS. $fb_change_template . '/kunena.forum.css'))
                {
                    $lifetime = 60 * 10;
                    $fb_current_template = $fb_change_template;
                    setcookie('fb_user_template', "$fb_change_template", time() + $lifetime);
                }
                else {
                    setcookie('fb_user_template', '', time() - 3600);
                    }
            }

            if ($fb_change_img_template)
            {
                // clean template name
                $fb_change_img_template = preg_replace('#\W#', '', $fb_change_img_template);

                if (strlen($fb_change_img_template) >= 40) {
                    $fb_change_img_template = substr($fb_change_img_template, 0, 39);
                    }

                // check that template exists in case it was deleted
                if (file_exists(KUNENA_PATH_TEMPLATE .DS. $fb_change_img_template . '/kunena.forum.css'))
                {
                    $lifetime = 60 * 10;
                    $fb_current_img_template = $fb_change_img_template;
                    setcookie('fb_user_img_template', "$fb_change_img_template", time() + $lifetime);
                }
                else {
                    setcookie('fb_user_img_template', '', time() - 3600);
                    }
            }

            $app->redirect(htmlspecialchars_decode(JRoute::_(KUNENA_LIVEURLREL)));
            break;

        #########################################################################################
        case 'credits':
            include (KUNENA_PATH_LIB .DS. 'kunena.credits.php');

            break;

        #########################################################################################
        default:
            if (file_exists(KUNENA_ABSTMPLTPATH . '/listcat.php')) {
                include (KUNENA_ABSTMPLTPATH . '/listcat.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'listcat.php');
                }

            break;
    } //hctiws

    // Bottom Module

    if (JDocumentHTML::countModules('kunena_bottom'))
    {
?>

        <div class = "bof-bottom-modul">
            <?php
            	$document	= &JFactory::getDocument();
            	$renderer	= $document->loadRenderer('modules');
            	$options	= array('style' => 'xhtml');
            	$position	= 'kunena_bottom';
            	echo $renderer->render($position, $options, null);
            ?>
        </div>

<?php
    }

    // Credits
    echo '<div class="fb_credits"> ' . CKunenaLink::GetTeamCreditsLink($catid, _KUNENA_POWEREDBY) . ' ' . CKunenaLink::GetCreditsLink();
    if ($fbConfig->enablerss)
    {
    	$document->addCustomTag('<link rel="alternate" type="application/rss+xml" title="'._LISTCAT_RSS.'" href="'.JRoute::_(KUNENA_LIVEURLREL.'&amp;func=fb_rss&amp;no_html=1').'" />');
        echo CKunenaLink::GetRSSLink('<img class="rsslink" src="' . KUNENA_URLEMOTIONSPATH . 'rss.gif" border="0" alt="' . _LISTCAT_RSS . '" title="' . _LISTCAT_RSS . '" />');
    }
    echo '</div>';

    // display footer
    $KunenaTemplate->displayParsedTemplate('kunena-footer');
} //else

if (is_object($kunenaProfile)) $kunenaProfile->close();

if(JDEBUG == 1){
	$__profiler->mark('Done');
	$__queries = $__profiler->getQueryCount();
	if(defined('JFIREPHP')){
		FB::log($__profiler->getBuffer(), 'Kunena Profiler');
		if($__queries>50){
			FB::error($__queries, 'Kunena Queries');
		} else if($__queries>35){
			FB::warn($__queries, 'Kunena Queries');
		} else {
			FB::log($__queries, 'Kunena Queries');
		}
	}
}