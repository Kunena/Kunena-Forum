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

// Dont allow direct linking

defined( '_JEXEC' ) or die();

// Kunena wide defines

require_once (JPATH_BASE . DS . 'components' . DS . 'com_kunena' . DS . 'lib' . DS . 'kunena.defines.php');

global $message;
global $kunena_this_cat;

// Get all the variables we need and strip them in case

$action = JRequest::getCmd ( 'action', '' );
$catid = JRequest::getInt ( 'catid', 0 );
$contentURL = JRequest::getVar ( 'contentURL', '' );
$do = JRequest::getCmd ( 'do', '' );
$email = JRequest::getVar ( 'email', '' );
$favoriteMe = JRequest::getVar ( 'favoriteMe', '' );
$fb_authorname = JRequest::getVar ( 'fb_authorname', '' );
$fb_thread = JRequest::getInt ( 'fb_thread', 0 );
$func = JString::strtolower ( JRequest::getCmd ( 'func', '' ) );
$id = JRequest::getInt ( 'id', 0 );
$limit = JRequest::getInt ( 'limit', 0 );
$limitstart = JRequest::getInt ( 'limitstart', 0 );
$markaction = JRequest::getVar ( 'markaction', '' );
$message = JRequest::getVar ( 'message', '' );
$page = JRequest::getInt ( 'page', 0 );
$parentid = JRequest::getInt ( 'parentid', 0 );
$pid = JRequest::getInt ( 'pid', 0 );
$replyto = JRequest::getInt ( 'replyto', 0 );
$resubject = JRequest::getVar ( 'resubject', '' );
$return = JRequest::getVar ( 'return', '' );
$rowid = JRequest::getInt ( 'rowid', 0 );
$rowItemid = JRequest::getInt ( 'rowItemid', 0 );
$subject = JRequest::getVar ( 'subject', '' );
$subscribeMe = JRequest::getVar ( 'subscribeMe', '' );
$thread = JRequest::getInt ( 'thread', 0 );
$topic_emoticon = JRequest::getVar ( 'topic_emoticon', '' );
$userid = JRequest::getInt ( 'userid', 0 );
$no_html = JRequest::getBool ( 'no_html', 0 );

$kunena_app = JFactory::getApplication ();

// Redirect Forum Jump

if (isset ( $_POST ['func'] ) && $func == "showcat") {
	$Itemid = JRequest::getInt ( 'Itemid', 0, 'REQUEST' );
	header ( "HTTP/1.1 303 See Other" );
	header ( "Location: " . htmlspecialchars_decode ( JRoute::_ ( 'index.php?option=com_kunena&amp;Itemid=' . $Itemid . '&amp;func=showcat&amp;catid=' . $catid ) ) );
	$kunena_app->close ();
}

// Image does not work if there are included files (extra characters), so we will do it now:
if ($func == "showcaptcha") {
	include (JPATH_ROOT . '/components/com_kunena/template/default/plugin/captcha/randomImage.php');
	$kunena_app->close ();
}

// Debug helpers
include_once (KUNENA_PATH_LIB . DS . "kunena.debug.php");
// get Kunenas configuration params in

require_once (KUNENA_PATH_LIB . DS . "kunena.config.class.php");

global $kunenaProfile;
global $lang, $kunena_icons;
global $board_title;
global $kunena_systime;

// Get data about the current user - its ok to not have a userid = guest
$kunena_my = &JFactory::getUser ();
// Load configuration and personal settings for current user

$kunena_config = &CKunenaConfig::getInstance ();
$kunena_db = &JFactory::getDBO ();

// Check if we need to redirect to a different default view
if ($func == ''){
	$redirect = false;

	switch ($kunena_config->fbdefaultpage){
		case 'recent' :
			$func = 'latest';
			$redirect = true;
			break;
		case 'my' :
			$func = $kunena_my->id > 0 ? 'mylatest' : 'latest';
			$redirect = true;
			break;
		default :
			$func = 'listcat';
	}

	if ( $redirect ){
		$Itemid = JRequest::getInt ( 'Itemid', 0, 'REQUEST' );
		header ( "HTTP/1.1 303 See Other" );
		header ( "Location: " . htmlspecialchars_decode ( JRoute::_ ( 'index.php?option=com_kunena&amp;Itemid=' . $Itemid . '&amp;func=' . $func ) ) );
		$kunena_app->close ();
	}
}

// get right Language file

if (file_exists ( KUNENA_FILE_LANGUAGE )) {
	include_once (KUNENA_FILE_LANGUAGE);
} else {
	include_once (KUNENA_FILE_LANGUAGE_DEFAULT);
}

$kn_tables = & CKunenaTables::getInstance ();
if ($kn_tables->installed () === false) {
	$kunena_config->board_offline = 1;
}

// Class structure should be used after this and all the common task should be moved to this class

require_once (KUNENA_PATH . DS . "class.kunena.php");

// Central Location for all internal links
require_once (KUNENA_PATH_LIB . DS . "kunena.link.class.php");

if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'smile.class.php' )) {
	require_once (KUNENA_ABSTMPLTPATH . DS . 'smile.class.php');
} else {
	require_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'smile.class.php');
}

$kunena_is_admin = CKunenaTools::isAdmin ();

// Check for JSON request
if ($func == "json") {

	// URL format for JSON requests: e.g: index.php?option=com_kunena&func=json&action=autocomplete&do=getcat
	require_once (KUNENA_PATH_LIB . DS . "kunena.ajax.helper.php");

	$ajaxHelper = &CKunenaAjaxHelper::getInstance();

	// Get the document object.
	$document =& JFactory::getDocument();

	// Set the MIME type for JSON output.
	$document->setMimeEncoding( 'application/json' );

	// Change the suggested filename.
	JResponse::setHeader( 'Content-Disposition', 'attachment; filename="kunena.json"' );

	$value = JRequest::getVar ( 'value', '' );

	JResponse::sendHeaders();

	if ($kunena_config->board_offline && ! $kunena_is_admin){
		// when the forum is offline, we don't entertain json requests
		echo '[]';
	}
	else {
		// Generate reponse
		echo $ajaxHelper->generateJsonResponse($action, $do, $value);
	}

	$kunena_app->close ();
}

// Check if we only allow registered users

if ($kunena_config->regonly && ! $kunena_my->id) {
	$kunena_app->enqueueMessage ( _COM_A_REGISTERED_ONLY . '<br/>' . _FORUM_UNAUTHORIZIED . '<br/>' . _FORUM_UNAUTHORIZIED2, 'error' );
} // or if the board is offline

else if ($kunena_config->board_offline && ! $kunena_is_admin) {
	echo stripslashes ( $kunena_config->offline_message );
} else {
	// =======================================================================================
	// Forum is online:

	//intercept the RSS request; we should stop afterwards
	if ($func == 'fb_rss') {
		include (KUNENA_PATH_LIB . DS . 'kunena.rss.php');
		$kunena_app->close ();
	}

	if ($func == 'fb_pdf') {
		include (KUNENA_PATH_LIB . DS . 'kunena.pdf.php');
		$kunena_app->close ();
	}

	$format = JRequest::getCmd ( 'format', 'html' );
	if ($format != 'html') {
		echo "Kunena: Unsupported output format {$format}, please use only format=html or .html";
		$kunena_app->close ();
	}

	//time format

	include_once (KUNENA_PATH_LIB . DS . 'kunena.timeformat.class.php');

	$kunena_systime = time () + $kunena_config->board_ofset * KUNENA_SECONDS_IN_HOUR;

	// Retrieve current cookie data for session handling

	$this->kunena_cookie_settings = ! empty ( $_COOKIE ['fboard_settings'] ) ? $_COOKIE ['fboard_settings'] : '';

	$board_title = $kunena_config->board_title;
	$this->kunena_from_bot = 0;

	if ($no_html == 0) {
		$document = & JFactory::getDocument ();

		// inline jscript with image location

		$document->addScriptDeclaration ( 'jr_expandImg_url = "' . KUNENA_URLIMAGESPATH . '";' );

		if (is_object ( $kunenaProfile ) && $kunenaProfile->useProfileIntegration ()) {
			if (defined ( 'KUNENA_COREJSURL' )) {
				global $_CB_framework;
				$_CB_framework->addJQueryPlugin ( 'kunena_tmpl', KUNENA_COREJSPATH );
				$_CB_framework->outputCbJQuery ( '', 'kunena_tmpl' );
			}
		}

		// MooTools Libraries

		// We cannot invoke the Joomla mootools behaviors in J1.5.15 or below
		//JHTML::_('behavior.mootools');

		// Instead we require the J1.5.16 (J1.6 based) framework
		// On systems running J1.5.15 this requires the mootools12 system plugin
		JHTML::_('behavior.framework');

		// New Kunena JS for default template
		// TODO: Need to check if selected template has an override
		$document->addScript ( KUNENA_DIRECTURL . 'template/default/js/default.js' );

		if (file_exists ( KUNENA_JTEMPLATEPATH . '/css/kunena.forum.css' )) {
			$document->addStyleSheet ( KUNENA_JTEMPLATEURL . '/css/kunena.forum.css' );
		} else {
			$document->addStyleSheet ( KUNENA_TMPLTCSSURL );
		}
	} // no_html == 0



	// WHOIS ONLINE IN FORUM
	if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/who/who.class.php' )) {
		include (KUNENA_ABSTMPLTPATH . '/plugin/who/who.class.php');
	} else {
		include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/who/who.class.php');
	}

	// include required libraries

	require_once (JPATH_BASE . '/libraries/joomla/template/template.php');

	// Kunena Current Template Icons Pack

	// See if there's an icon pack installed

	$useIcons = 0; //init

	$kunena_icons = array ();

	if (file_exists ( KUNENA_ABSTMPLTPATH . '/icons.php' )) {
		include_once (KUNENA_ABSTMPLTPATH . '/icons.php');
		$useIcons = 1;
	} else {
		include_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'icons.php');
	}

	require_once (KUNENA_PATH_LIB . DS . 'kunena.session.class.php');

	// We only do the session handling for registered users

	// No point in keeping track of whats new for guests

	$kunena_session = & CKunenaSession::getInstance ( true );
	if ($kunena_my->id > 0) {
		// First we drop an updated cookie, good for 1 year

		// We have consolidated multiple instances of cookie management into this single location

		// NOT SURE IF WE STILL NEED THIS ONE after session management got dbtized

		setcookie ( "fboard_settings[member_id]", $kunena_my->id, time () + KUNENA_SECONDS_IN_YEAR, '/' );

		// new indicator handling

		if ($markaction == "allread") {
			$kunena_session->markAllCategoriesRead ();
		}

		$kunena_session->updateAllowedForums ( $kunena_my->id );

		// save fbsession

		$kunena_session->save ( $kunena_session );

		if ($markaction == "allread") {
			$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), _GEN_ALL_MARKED );
		}

		$userprofile = CKunenaUserprofile::getInstance($kunena_my->id);
		if ($userprofile->posts === null) {
			// user does not yet have a Kunena profile -> lets create one
			$kunena_db->setQuery ( "INSERT INTO #__fb_users (userid) VALUES ('$kunena_my->id')" );
			$kunena_db->query ();
			check_dberror ( 'Unable to create user profile.' );
			$userprofile = CKunenaUserprofile::getInstance($kunena_my->id, true);
		}

		// Assign previous visit without user offset to variable for templates to decide

		// whether or not to use the NEW indicator on forums and posts

		$this->prevCheck = $kunena_session->lasttime; // - KUNENA_OFFSET_USER; Don't use the user offset - it throws the NEW indicator off

	} else {
		// collect accessaible categories for guest user

		$kunena_db->setQuery ( "SELECT id FROM #__fb_categories WHERE pub_access='0' AND published='1'" );
		$kunena_session->allowed = ($arr_pubcats = $kunena_db->loadResultArray ()) ? implode ( ',', $arr_pubcats ) : '';
		check_dberror ( 'Unable load accessible categories for user.' );

		// For guests we don't show new posts

		$this->prevCheck = $kunena_systime;
		$kunena_session->readtopics = '';
	}

	// no access to categories?

	if (! $kunena_session->allowed)
		$kunena_session->allowed = '0';

	// Integration with GroupJive, Jomsocial:

	$params = array ($kunena_my->id, &$kunena_session->allowed );
	if (is_object ( $kunenaProfile ))
		$kunenaProfile->trigger ( 'getAllowedForumsRead', $params );

	//Get the topics this user has already read this session from #__fb_sessions

	$readTopics = $kunena_session->readtopics;
	$this->read_topics = explode ( ',', $readTopics );

	//Call the call for polls
	if($kunena_config->pollenabled){
  		require_once (KUNENA_PATH_LIB .DS. 'kunena.poll.class.php');
  		$poll = new CKunenaPolls();
	}

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

	if ($func == "showcat" || $func == "view") {
		if ($catid != 0) {
			$kunena_db->setQuery ( "SELECT parent FROM #__fb_categories WHERE id='{$catid}'" );
			$strCatParent = $kunena_db->loadResult ();
			check_dberror ( 'Unable to load categories.' );
		}
		if ($catid == 0 || $strCatParent === '0') {

			$kunena_app->redirect ( CKunenaLink::GetCategoryURL('listcat',$catid, true) );
		}
	}

	// display header

	?>
<!-- Kunena Header -->
<div id="Kunena"><?php
	if ($kunena_config->board_offline) {
		?>
<span id="fbOffline"><?php
		echo _FORUM_IS_OFFLINE?></span> <?php
	}
	?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="Kunena_top">
	<tr>
		<td align="left"><?php
	// display Kunena menu if present
	if (JDocumentHTML::countModules ( 'kunena_menu' )) {
		?>
		<!-- Kunena Menu position: kunena_menu -->
		<div id="fb_topmenu">
		<div id="Kunena_tab"><?php
		$document = &JFactory::getDocument ();
		$renderer = $document->loadRenderer ( 'modules' );
		$options = array ('style' => 'xhtml' );
		$position = 'kunena_menu';
		echo $renderer->render ( $position, $options, null );
		?>
		</div>
		</div>
		<!-- /Kunena Menu position: kunena_menu -->
		<?php
	}
	?></td>
		<td align="right" width="1%">
			<span id="kprofilebox_status"><a class="ktoggler close" rel="kprofilebox"></a></span>

			<!-- <img id="BoxSwitch_topprofilebox__topprofilebox_tbody" class="hideshow" src="<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif'?>" alt="" /> -->
		</td>
	</tr>
</table>
<!-- /Kunena Header --> <?php
	//BEGIN: PROFILEBOX

	if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/profilebox/profilebox.php' )) {
		include (KUNENA_ABSTMPLTPATH . '/plugin/profilebox/profilebox.php');
	} else {
		include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/profilebox/profilebox.php');
	}
	//FINISH: PROFILEBOX

	switch ($func) {
		case 'who' :
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/who/who.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/plugin/who/who.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/who/who.php');
			}

			break;

		#########################################################################################

		case 'announcement' :
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/announcement/announcement.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/plugin/announcement/announcement.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/announcement/announcement.php');
			}

			break;
		#########################################################################################
        case 'poll':
            if (file_exists (KUNENA_ABSTMPLTPATH . '/plugin/poll/poll.php')) {
                include (KUNENA_ABSTMPLTPATH . '/plugin/poll/poll.php');
                }
            else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/poll/poll.php');
                }

            break;

		#########################################################################################

		case 'stats' :
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/stats/stats.class.php');
			}

			$kunena_stats = new CKunenaStats ( );
			$kunena_stats->showStats ();

			break;

		#########################################################################################

		case 'myprofile' :
		case 'fbprofile' :
		case 'profile' :
			require_once ( KUNENA_PATH_VIEWS .DS. 'profile.php');
			$page = new CKunenaProfile($userid);
			$page->display();

			break;

		#########################################################################################

		case 'userlist' :
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/userlist/userlist.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/plugin/userlist/userlist.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/userlist/userlist.php');
			}

			break;

		#########################################################################################

		case 'post' :
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/post.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/post.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'post.php');
			}

			break;

		#########################################################################################

		case 'view' :
			require_once (KUNENA_PATH_VIEWS . DS . 'view.php');
			$page = new CKunenaView($catid, $id);
			$page->display();

			break;

		#########################################################################################

		case 'help' :
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/help.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/help.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'help.php');
			}

			break;

		#########################################################################################

		case 'showcat' :
			require_once (KUNENA_PATH_VIEWS . DS . 'showcat.php');
			$page = new CKunenaShowcat($catid, $page);
			$page->display();

			break;

		#########################################################################################

		case 'listcat' :
			require_once (KUNENA_PATH_VIEWS . DS . 'listcat.php');
			$page = new CKunenaListcat($catid);
			$page->display();

			break;

		#########################################################################################

		case 'review' :
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/moderate/moderate_messages.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/moderate/moderate_messages.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . '/moderate/moderate_messages.php');
			}

			break;

		#########################################################################################

		case 'moderate' :
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/moderate/moderate.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/moderate/moderate.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . '/moderate/moderate.php');
			}

			break;

		#########################################################################################

		case 'rules' :
			include (KUNENA_PATH_LIB . DS . 'kunena.rules.php');

			break;

		#########################################################################################

		case 'userprofile' :
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/plugin/myprofile/myprofile.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/myprofile/myprofile.php');
			}

			break;

		#########################################################################################

		case 'report' :
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/report/report.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/plugin/report/report.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/report/report.php');
			}

			break;

		#########################################################################################

		case 'latest' :
		case 'mylatest' :
		case 'noreplies' :
		case 'subscriptions' :
		case 'favorites' :
			require_once (KUNENA_PATH_VIEWS . DS . 'latestx.php');
			$page = new CKunenaLatestX($func, $page);
			$page->display();

			break;

		#########################################################################################

		case 'search' :
		case 'advsearch' :
			require_once (KUNENA_PATH_LIB . DS . 'kunena.search.class.php');

			$kunenaSearch = new CKunenaSearch ( );
			$kunenaSearch->show ();
			break;

		#########################################################################################

		case 'markthisread' :
			// get all already read topics

			$kunena_db->setQuery ( "SELECT readtopics FROM #__fb_sessions WHERE userid='{$kunena_my->id}'" );
			$allreadyRead = $kunena_db->loadResult ();
			check_dberror ( "Unable to load read topics." );
			/* Mark all these topics read */
			$kunena_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE catid='{$catid}' AND thread NOT IN ('{$allreadyRead}') GROUP BY thread" );
			$readForum = $kunena_db->loadObjectList ();
			check_dberror ( "Unable to load messages." );
			$readTopics = '--';

			foreach ( $readForum as $rf ) {
				$readTopics = $readTopics . ',' . $rf->thread;
			}

			$readTopics = str_replace ( '--,', '', $readTopics );

			if ($allreadyRead != "") {
				$readTopics = $readTopics . ',' . $allreadyRead;
			}

			$kunena_db->setQuery ( "UPDATE #__fb_sessions set readtopics='$readTopics' WHERE userid=$kunena_my->id" );
			$kunena_db->query ();
			check_dberror ( 'Unable to update readtopics in session table.' );

			$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, true ), _GEN_FORUM_MARKED );
			break;

		#########################################################################################
		case 'subscribecat' :

			$success_msg = '';

			if ( $catid && $kunena_my->id ) {
				$query = "INSERT INTO #__fb_subscriptions_categories (catid, userid) VALUES ('$catid','$kunena_my->id')";
				$kunena_db->setQuery ( $query );

				if (@$kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = _GEN_CATEGORY_SUBCRIBED;
				}
			}

			$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, true ), $success_msg );
			break;

		#########################################################################################
		case 'unsubscribecat' :

			$success_msg = '';
			if ($catid && $kunena_my->id ) {
				$query = "DELETE FROM #__fb_subscriptions_categories WHERE catid=$catid AND userid=$kunena_my->id";
				$kunena_db->setQuery ( $query );

				if ($kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = _GEN_CATEGORY_UNSUBCRIBED;
				}
			}

			$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, true ), $success_msg );
			break;

		#########################################################################################

		case 'karma' :
			include (KUNENA_PATH_LIB . DS . 'kunena.karma.php');

			break;

		#########################################################################################

		case 'bulkactions' :
			switch ($do) {
				case "bulkDel" :
					CKunenaTools::fbDeletePosts ( CKunenaTools::isModerator ( $kunena_my->id, $catid ), $return );

					break;

				case "bulkMove" :
					CKunenaTools::fbMovePosts ( $catid, CKunenaTools::isModerator ( $kunena_my->id, $catid ), $return );
					break;
			}

			break;

		######################



		/*    template chooser    */
		case "templatechooser" :
			$fb_user_template = strval ( JRequest::getVar ( 'kunena_user_template', '', 'COOKIE' ) );

			$fb_user_img_template = strval ( JRequest::getVar ( 'kunena_user_img_template', $fb_user_img_template ) );
			$fb_change_template = strval ( JRequest::getVar ( 'kunena_change_template', $fb_user_template ) );
			$fb_change_img_template = strval ( JRequest::getVar ( 'kunena_change_img_template', $fb_user_img_template ) );

			if ($fb_change_template) {
				// clean template name

				$fb_change_template = preg_replace ( '#\W#', '', $fb_change_template );

				if (JString::strlen ( $fb_change_template ) >= 40) {
					$fb_change_template = JString::substr ( $fb_change_template, 0, 39 );
				}

				// check that template exists in case it was deleted

				if (file_exists ( KUNENA_PATH_TEMPLATE . DS . $fb_change_template . '/css/kunena.forum.css' )) {
					$lifetime = 60 * 10;
					$fb_current_template = $fb_change_template;
					setcookie ( 'kunena_user_template', "$fb_change_template", time () + $lifetime );
				} else {
					setcookie ( 'kunena_user_template', '', time () - 3600 );
				}
			}

			if ($fb_change_img_template) {
				// clean template name

				$fb_change_img_template = preg_replace ( '#\W#', '', $fb_change_img_template );

				if (JString::strlen ( $fb_change_img_template ) >= 40) {
					$fb_change_img_template = JString::substr ( $fb_change_img_template, 0, 39 );
				}

				// check that template exists in case it was deleted

				if (file_exists ( KUNENA_PATH_TEMPLATE . DS . $fb_change_img_template . '/css/kunena.forum.css' )) {
					$lifetime = 60 * 10;
					$fb_current_img_template = $fb_change_img_template;
					setcookie ( 'kunena_user_img_template', "$fb_change_img_template", time () + $lifetime );
				} else {
					setcookie ( 'kunena_user_img_template', '', time () - 3600 );
				}
			}

			$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true) );
			break;

		#########################################################################################

		case 'credits' :
			include (KUNENA_PATH_LIB . DS . 'kunena.credits.php');

			break;

		#########################################################################################

		default :
			echo "Unknown request: $func";
			break;
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/listcat.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/listcat.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'listcat.php');
			}

			break;
	} //hctiws



	// Bottom Module
	CKunenaTools::showModulePosition( 'kunena_bottom' );

	// Credits

	echo '<div class="kcredits"> ' . CKunenaLink::GetTeamCreditsLink ( $catid, _KUNENA_POWEREDBY ) . ' ' . CKunenaLink::GetCreditsLink ();
	if ($kunena_config->enablerss) {
		$document->addCustomTag ( '<link rel="alternate" type="application/rss+xml" title="' . _LISTCAT_RSS . '" href="' . CKunenaLink::GetRSSURL() . '" />' );
		echo CKunenaLink::GetRSSLink ( '<img class="rsslink" src="' . KUNENA_URLEMOTIONSPATH . 'rss.gif" border="0" alt="' . _LISTCAT_RSS . '" title="' . _LISTCAT_RSS . '" />' );
	}
	echo '</div>';

	// display footer

	?>
<div class="fb_footer"></div>
</div>
<!-- closes Kunena div -->
<?php
} //else



if (is_object ( $kunenaProfile ))
	$kunenaProfile->close ();