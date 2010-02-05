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

// Display time it took to create the entire page in the footer
jimport( 'joomla.error.profiler' );
$__kstarttime = JProfiler::getmicrotime();

$lang = JFactory::getLanguage();
$lang->load('com_kunena', JPATH_COMPONENT);

// First of all take a profiling information snapshot for JFirePHP
if(JDEBUG){
	require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.profiler.php');
	$__profiler = KProfiler::GetInstance();
	$__profiler->mark('Start');
}

// Kunena wide defines
require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.defines.php');

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

// If JFirePHP is installed and enabled, leave a trace of the Kunena startup
if(JDEBUG == 1 && defined('JFIREPHP')){
	// FB::trace("Kunena Startup");
}

// Redirect Forum Jump

if (isset ( $_POST ['func'] ) && $func == "showcat") {
	$Itemid = JRequest::getInt ( 'Itemid', 0, 'REQUEST' );
	header ( "HTTP/1.1 303 See Other" );
	header ( "Location: " . htmlspecialchars_decode ( JRoute::_ ( 'index.php?option=com_kunena&amp;Itemid=' . $Itemid . '&amp;func=showcat&amp;catid=' . $catid ) ) );
	$kunena_app->close ();
}

// Debug helpers
include_once (JPATH_COMPONENT . DS . 'lib' . DS . "kunena.debug.php");
// get Kunenas configuration params in

require_once (JPATH_COMPONENT . DS . 'lib' . DS . "kunena.config.class.php");

global $kunenaProfile;
global $lang, $kunena_icons, $topic_emoticons;

$kunena_my = &JFactory::getUser ();
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

$kn_tables = & CKunenaTables::getInstance ();
if ($kn_tables->installed () === false) {
	$kunena_config->board_offline = 1;
}

// Class structure should be used after this and all the common task should be moved to this class
require_once (JPATH_COMPONENT . DS . 'class.kunena.php');

// Central Location for all internal links
require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.link.class.php');

require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.smile.class.php');

// Check for JSON request
if ($func == "json") {

	if(JDEBUG == 1 && defined('JFIREPHP')){
		FB::log('Kunena JSON request');
	}

	// URL format for JSON requests: e.g: index.php?option=com_kunena&func=json&action=autocomplete&do=getcat
	require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.ajax.helper.php');

	$ajaxHelper = &CKunenaAjaxHelper::getInstance();

	// Get the document object.
	$document =& JFactory::getDocument();

	// Set the MIME type for JSON output.
	$document->setMimeEncoding( 'application/json' );

	// Change the suggested filename.
	if ($action!='uploadfile') JResponse::setHeader( 'Content-Disposition', 'attachment; filename="kunena.json"' );

	$value = JRequest::getVar ( 'value', '' );

	JResponse::sendHeaders();

	if ($kunena_config->board_offline && ! CKunenaTools::isAdmin ()){
		// when the forum is offline, we don't entertain json requests
		json_encode ( array(
				'status' => '0',
				'error' => @sprintf(_KUNENA_FORUM_OFFLINE)) );
	}
	else {
		// Generate reponse
		echo $ajaxHelper->generateJsonResponse($action, $do, $value);
	}

	$kunena_app->close ();
}

// Check if we only allow registered users

if ($kunena_config->regonly && ! $kunena_my->id) {
	$kunena_app->enqueueMessage ( JText::_('COM_KUNENA_A_REGISTERED_ONLY') . '<br/>' . JText::_('COM_KUNENA_FORUM_UNAUTHORIZIED') . '<br/>' . JText::_('COM_KUNENA_FORUM_UNAUTHORIZIED2'), 'error' );
} // or if the board is offline

else if ($kunena_config->board_offline && ! CKunenaTools::isAdmin ()) {
	echo stripslashes ( $kunena_config->offline_message );
} else {
	// =======================================================================================
	// Forum is online:

	//intercept the RSS request; we should stop afterwards
	if ($func == 'fb_rss') {
		include (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.rss.php');
		$kunena_app->close ();
	}

	if ($func == 'fb_pdf') {
		include (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.pdf.php');
		$kunena_app->close ();
	}

	$format = JRequest::getCmd ( 'format', 'html' );
	if ($format != 'html') {
		echo "Kunena: Unsupported output format {$format}, please use only format=html or .html";
		$kunena_app->close ();
	}

	//time format
	include_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.timeformat.class.php');

	$document = & JFactory::getDocument ();

	// We require Mootools 1.2 framework
	// On systems running < J1.5.16 this requires the mootools12 system plugin
	JHTML::_ ( 'behavior.framework' );

	// New Kunena JS for default template
	// TODO: Need to check if selected template has an override
	$document->addScript ( KUNENA_DIRECTURL . 'template/default/js/default.js' );

	if (file_exists ( KUNENA_JTEMPLATEPATH .DS. 'css' .DS. 'kunena.forum.css' )) {
		$document->addStyleSheet ( KUNENA_JTEMPLATEURL . '/css/kunena.forum.css' );
	} else {
		$document->addStyleSheet ( KUNENA_TMPLTCSSURL );
	}

	// WHOIS ONLINE IN FORUM
	if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/who/who.class.php' )) {
		include (KUNENA_ABSTMPLTPATH . '/plugin/who/who.class.php');
	} else {
		include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/who/who.class.php');
	}

	// include required libraries
	jimport('joomla.template.template');

	// Kunena Current Template Icons Pack
	$kunena_icons = array ();
	if (file_exists ( KUNENA_ABSTMPLTPATH . '/icons.php' )) {
		include_once (KUNENA_ABSTMPLTPATH . '/icons.php');
	} else {
		include_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'icons.php');
	}

	require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.session.class.php');

	// We only do the session handling for registered users

	// No point in keeping track of whats new for guests

	$kunena_session = & CKunenaSession::getInstance ( true );
	if ($kunena_my->id > 0) {
		// new indicator handling
		if ($markaction == "allread") {
			$kunena_session->markAllCategoriesRead ();
		}

		$kunena_session->updateAllowedForums ( $kunena_my->id );
		$kunena_session->save ( $kunena_session );

		if ($markaction == "allread") {
			$kunena_app->redirect ( CKunenaLink::GetKunenaURL(true), JText::_('COM_KUNENA_GEN_ALL_MARKED') );
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
		$this->prevCheck = $kunena_session->lasttime;

	} else {
		// collect accessaable categories for guest user
		$kunena_db->setQuery ( "SELECT id FROM #__fb_categories WHERE pub_access='0' AND published='1'" );
		$kunena_session->allowed = ($arr_pubcats = $kunena_db->loadResultArray ()) ? implode ( ',', $arr_pubcats ) : '';
		check_dberror ( 'Unable load accessible categories for user.' );

		// For guests we don't show new posts
		$this->prevCheck = CKunenaTimeformat::internalTime()+60;
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
	$this->read_topics = explode ( ',', $kunena_session->readtopics );

	//Call the call for polls
	if($kunena_config->pollenabled){
  		require_once (JPATH_COMPONENT . DS . 'lib' .DS. 'kunena.poll.class.php');
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
	?>
<!-- Kunena Header -->
<div id="Kunena"><?php
	if ($kunena_config->board_offline) {
		?>
<span id="fbOffline"><?php
		echo JText::_('COM_KUNENA_FORUM_IS_OFFLINE')?></span> <?php
	}
	?>
<table width="100%" border="0" cellspacing="0" cellpadding="0"
	id="Kunena_top">
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
		<td align="right" width="1%"><span id="kprofilebox_status"><a
			class="ktoggler close" rel="kprofilebox"></a></span>
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
			$page = new CKunenaView($func, $catid, $id);
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
			if (file_exists ( KUNENA_ABSTMPLTPATH . 'rules.php' )) {
				include (KUNENA_ABSTMPLTPATH . 'rules.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'rules.php');
			}

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
			require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.search.class.php');

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

			$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, true ), JText::_('COM_KUNENA_GEN_FORUM_MARKED') );
			break;

		#########################################################################################
		case 'subscribecat' :

			$success_msg = '';

			if ( $catid && $kunena_my->id ) {
				$query = "INSERT INTO #__fb_subscriptions_categories (catid, userid) VALUES ('$catid','$kunena_my->id')";
				$kunena_db->setQuery ( $query );

				if (@$kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = JText::_('COM_KUNENA_GEN_CATEGORY_SUBCRIBED');
				}
				check_dberror ( "Unable to subscribe to category." );
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
					$success_msg = JText::_('COM_KUNENA_GEN_CATEGORY_UNSUBCRIBED');
				}
				check_dberror ( "Unable to unsubscribe from category." );
			}

			$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, true ), $success_msg );
			break;

		#########################################################################################

		case 'karma' :
			include (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.karma.php');

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
			include (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.credits.php');

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

	echo '<div class="kcredits"> ' . CKunenaLink::GetTeamCreditsLink ( $catid, JText::_('COM_KUNENA_POWEREDBY') ) . ' ' . CKunenaLink::GetCreditsLink ();
	if ($kunena_config->enablerss) {
		$document->addCustomTag ( '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_KUNENA_LISTCAT_RSS') . '" href="' . CKunenaLink::GetRSSURL() . '" />' );
		echo CKunenaLink::GetRSSLink ( '<img class="rsslink" src="' . KUNENA_URLICONSPATH . 'rss.gif" border="0" alt="' . JText::_('COM_KUNENA_LISTCAT_RSS') . '" title="' . JText::_('COM_KUNENA_LISTCAT_RSS') . '" />' );
	}
	echo '</div>';

	// display footer

	// Show total time it took to create the page
	$__ktime = JProfiler::getmicrotime() - $__kstarttime;
?>
<div class="kfooter"><span class="kfooter-time"><?php echo JText::_('COM_KUNENA_FOOTER_TIME_TO_CREATE').'&nbsp;'.sprintf('%0.2f', $__ktime).'&nbsp;'.JText::_('COM_KUNENA_FOOTER_TIME_SECONDS');?></span></div>
</div>
<!-- closes Kunena div -->
<?php
} //else

if (is_object ( $kunenaProfile ))
	$kunenaProfile->close ();

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