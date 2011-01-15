<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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
require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.defines.php');

class KunenaApp {

	function __construct() {
		KunenaRoute::cacheLoad();
		ob_start();

		// Display time it took to create the entire page in the footer
		jimport( 'joomla.error.profiler' );
		$__kstarttime = JProfiler::getmicrotime();

		kimport('kunena.error');
		kimport('kunena.forum.category.helper');
		$kunena_config = KunenaFactory::getConfig ();
		if ($kunena_config->debug) {
			KunenaError::initialize();
		}

$lang = JFactory::getLanguage();
if (!$lang->load('com_kunena', JPATH_SITE, null, true)) {
	$lang->load('com_kunena', KPATH_SITE);
}

// First of all take a profiling information snapshot for JFirePHP
if(JDEBUG){
	require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.profiler.php');
	$__profiler = KProfiler::GetInstance();
	$__profiler->mark('Start');
}

$func = strtolower (JRequest::getWord ( 'func', JRequest::getWord ( 'view' ) ) );
JRequest::setVar ( 'view', $func );
JRequest::setVar ( 'func', $func );
$format = JRequest::getCmd ( 'format', 'html' );

require_once(KUNENA_PATH . DS . 'router.php');
if ($func && !isset(KunenaRouter::$functions[$func])) {
	// If func is not legal, raise joomla error
	return JError::raiseError( 500, 'Kunena function "' . $func . '" not found' );
}

$kunena_app = JFactory::getApplication ();

if (empty($_POST) && $format == 'html') {
	$me = KunenaFactory::getUser();
	$menu = $kunena_app->getMenu ();
	$active = $menu->getActive ();

	// Legacy menu item and Itemid=0 support with redirect and notice
	if (empty($active->query ['view'])) {
		$new = $menu->getItem (KunenaRoute::getItemID ());
		if ($new) {
			if ($active) {
				if ($active->route == $new->route) {
					KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_MENU_CONFLICT', $active->route, $active->id, $new->id), 'menu');
					$menu->setActive ( $new->id );
					$active = $new;
				} else {
					KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_MENU_LEGACY', $active->route, $active->id, $new->route, $new->id), 'menu');
					$kunena_app->redirect (KunenaRoute::_(null, false));
				}
			} else {
				KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_MENU_NO_ITEM_REDIRECT', $new->route, $new->id));
				$kunena_app->redirect (KunenaRoute::_(null, false));
			}
		} elseif (!$active) {
			KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_MENU_NO_ITEM'));
		}
	}
	if (!$func || $func == 'entrypage') {
		// If we are currently in entry page, we need to show and highlight default menu item
		if (!empty ( $active->query ['defaultmenu'] )) {
			$defaultitem = $active->query ['defaultmenu'];
			if ($defaultitem > 0) {
				$newitem = $menu->getItem ($defaultitem);
				if (!$newitem) {
					KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_MENU_NOT_EXISTS'), 'menu');
				} elseif (empty($newitem->component) || $newitem->component != 'com_kunena') {
					KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_MENU_NOT_KUNENA'), 'menu');
				} elseif ($active->route == $newitem->route) {
					// Special case: we are using Entry Page instead of menu alias and we have identical menu alias
					if ($active->id != $newitem->id) {
						$defaultitem = !empty ( $newitem->query ['defaultmenu'] ) ? $newitem->query ['defaultmenu'] : $newitem->id;
						$newitem2 = $menu->getItem ($defaultitem);
						if (empty($newitem2->component) || $newitem2->component != 'com_kunena') {
							$defaultitem = $newitem->id;
						}
						if ($defaultitem) {
							$menu->setActive ( $defaultitem );
							$active = $menu->getActive ();
						}
					}
				} else {
					$oldlocation = KunenaRoute::getCurrentMenu ();
					$menu->setActive ( $defaultitem );
					$active = $menu->getActive ();

					$newlocation = KunenaRoute::getCurrentMenu ();
					if (!$oldlocation || $oldlocation->id != $newlocation->id) {
						// Follow Default Menu Item if it's not in the same menu
						$kunena_app->redirect (KunenaRoute::_($defaultitem, false));
					}
				}
				if (is_object ( $active )) {
					foreach ( $active->query as $var => $value ) {
						if ($var == 'view' && $value == 'entrypage')
							$value = $func;
						JRequest::setVar ( $var, $value );
					}
					$func = JRequest::getCmd ( 'view' );
				}
			}
		}
	}
	$newItemid = KunenaRoute::getItemid();
	if ($active && $newItemid && !KunenaRoute::getCurrentMenu () && $active->id != $newItemid) {
		$kunena_app->redirect (KunenaRoute::_(null, false));
	}
}

// Convert legacy urls into new ones
$view = JRequest::getWord ( 'view' );
$layout = JRequest::getWord ( 'layout', null );
$config = KunenaFactory::getConfig ();
$redirect = false;
switch ($view) {
	case 'listcat':
		$redirect = true;
		$view = 'categories';
		break;
	case 'showcat':
		$redirect = true;
		$view = 'category';
		$page = JRequest::getInt ( 'page', 1 );
		JRequest::setVar ( 'page' );
		if ($page > 0) {
			JRequest::setVar ( 'limitstart', $config->threads_per_page * ($page - 1));
		}
		break;
	case 'latest' :
	case 'mylatest' :
	case 'noreplies' :
	case 'subscriptions' :
	case 'favorites' :
	case 'userposts' :
	case 'unapproved' :
	case 'deleted' :
		$redirect = true;
		$mode = JRequest::getWord ( 'do', $view );
		JRequest::setVar ( 'do' );
		$view = 'topics';
		switch ($mode) {
			case 'latest':
				$layout = 'default';
				$mode = 'replies';
				break;
			case 'unapproved':
				$layout = 'default';
				$mode = 'unapproved';
				break;
			case 'deleted':
				$layout = 'default';
				$mode = 'deleted';
				break;
			case 'noreplies':
				$layout = 'default';
				$mode = 'noreplies';
				break;
			case 'latesttopics':
				$layout = 'default';
				$mode = 'topics';
				break;
			case 'mylatest':
				$layout = 'user';
				$mode = 'default';
				break;
			case 'subscriptions':
				$layout = 'user';
				$mode = 'subscriptions';
				break;
			case 'favorites':
				$layout = 'user';
				$mode = 'favorites';
				break;
			case 'owntopics':
				$layout = 'user';
				$mode = 'started';
				break;
			case 'userposts':
				JRequest::setVar ( 'userid', '0' );
				// continue
			case 'latestposts':
				$layout = 'posts';
				$mode = 'recent';
				break;
			case 'saidthankyouposts':
				$layout = 'posts';
				$mode = 'mythanks';
				break;
			case 'gotthankyouposts':
				$layout = 'posts';
				$mode = 'thankyou';
				break;
			case 'catsubscriptions':
				$layout = 'category';
				break;
			default:
				$layout = 'default';
				$mode = 'default';
		}
		$page = JRequest::getInt ( 'page', 1 );
		JRequest::setVar ( 'page' );
		if ($page > 0) {
			JRequest::setVar ( 'limitstart', $config->threads_per_page * ($page - 1));
		}
		JRequest::setVar ( 'mode', $mode );
		break;
	case 'view':
		$redirect = true;
		$view = 'topic';
		break;
	case 'myprofile' :
	case 'userprofile' :
	case 'fbprofile' :
	case 'profile' :
	case 'moderateuser' :
		$redirect = true;
		$view = 'user';
		$task = JRequest::getCmd ( 'task', '' );
		if ($task) $kunena_app->enqueueMessage(JText::_('COM_KUNENA_DEPRECATED_ACTION'), 'error');
		$mode = JRequest::getWord ( 'do' );
		JRequest::setVar ( 'do' );
		switch ($mode) {
			case 'edit':
				$layout = 'edit';
				break;
			default:
				$layout = 'default';
				break;
		}
		break;
	case 'userlist':
		$redirect = true;
		$view = 'users';
		break;
	case 'rss':
		$redirect = true;
		$view = 'topics';
		$mode = $kunena_config->rss_type;
		switch ($mode) {
			case 'topic':
				$mode = 'topics';
				break;
			case 'recent':
				$mode = 'replies';
				break;
			case 'post':
			default:
				$mode = 'posts';
				break;
		}
		$limit = $kunena_config->rss_timelimit;
		switch ($limit) {
			case 'week':
				$limit = 168;
				break;
			case 'year':
				$limit = 8760;
				break;
			case 'month':
			default:
				$limit = 720;
				break;
		}
		JRequest::setVar ( 'mode', $mode );
		JRequest::setVar ( 'sel', $limit );
		JRequest::setVar ( 'type', 'rss' );
		JRequest::setVar ( 'format', 'feed' );
		break;
	case 'post':
		$redirect = true;
		$view = 'topic';

		// Support old &parentid=123 and &replyto=123 variables
		$id = JRequest::getInt ( 'id', 0 );
		if (!$id) {
			$id = JRequest::getInt ( 'parentid', 0 );
		}
		if (!$id) {
			$id = JRequest::getInt ( 'replyto', 0 );
		}
		JRequest::setVar ( 'parentid' );
		JRequest::setVar ( 'replyto' );
		JRequest::setVar ( 'id', $id );

		$mode = JRequest::getWord ( 'do' );
		JRequest::setVar ( 'do' );
		$action = JRequest::getCmd ( 'action', '' );
		if ($action) {
			$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_DEPRECATED_ACTION' ), 'error' );
		} else {
			switch ($mode) {
				case 'new' :
					$layout = 'create';
					break;

				case 'reply' :
					$layout = 'reply';
					break;

				case 'quote' :
					$layout = 'reply';
					JRequest::setVar ( 'quote', 1 );
					break;

				case 'edit' :
					$layout = 'edit';
					break;

				case 'moderate' :
					$layout = 'move';
					JRequest::setVar ( 'id' );
					JRequest::setVar ( 'mesid', $id );
					break;

				case 'moderatethread' :
					$layout = 'move';
					break;

				default :
					$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_DEPRECATED_ACTION' ), 'error' );
			}
		}
		break;
}
if ($redirect) {
	JRequest::setVar ( 'func' );
	JRequest::setVar ( 'view', $view );
	JRequest::setVar ( 'layout', $layout );
	$kunena_app->redirect (KunenaRoute::_(null, false));
}


global $message;
global $kunena_this_cat;

// Get all the variables
$action = JRequest::getCmd ( 'action', '' );
$catid = JRequest::getInt ( 'catid', 0 );
$do = JRequest::getCmd ( 'do', '' );
$task = JRequest::getCmd ( 'task', '' );
$id = JRequest::getInt ( 'id', 0 );
$userid = JRequest::getInt ( 'userid', 0 );
$limit = JRequest::getInt ( 'limit', 0 );
$limitstart = JRequest::getInt ( 'limitstart', 0 );
$markaction = JRequest::getVar ( 'markaction', '' );
$message = JRequest::getVar ( 'message', '' );
$page = JRequest::getInt ( 'page', 0 );

// If JFirePHP is installed and enabled, leave a trace of the Kunena startup
if(JDEBUG == 1 && defined('JFIREPHP')){
	// FB::trace("Kunena Startup");
}

// Redirect Forum Jump
if (isset ( $_POST ['func'] ) && $func == "showcat") {
	header ( "HTTP/1.1 303 See Other" );
	header ( "Location: " . KunenaRoute::_ ( 'index.php?option=com_kunena&func=showcat&catid=' . $catid, false ) );
	$kunena_app->close ();
}

$kunena_my = JFactory::getUser ();
$kunena_db = JFactory::getDBO ();

$document = JFactory::getDocument();
$document->addScriptDeclaration('// <![CDATA[
var kunena_toggler_close = "'.JText::_('COM_KUNENA_TOGGLER_COLLAPSE').'";
var kunena_toggler_open = "'.JText::_('COM_KUNENA_TOGGLER_EXPAND').'";
// ]]>');

// Class structure should be used after this and all the common task should be moved to this class
require_once (JPATH_COMPONENT . DS . 'class.kunena.php');

// Central Location for all internal links
require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.link.class.php');
kimport('kunena.html.parser');

// Redirect profile (menu item) to the right component
if ($func == 'profile' && !$do && empty($_POST)) {
	$redirect = 1;
	if (!empty($active)) {
		$params = new JParameter($active->params);
		$redirect = $params->get('integration');
	}
	if ($redirect) {
		$profileIntegration = KunenaFactory::getProfile();
		if (!($profileIntegration instanceof KunenaProfileKunena)) {
			$url = CKunenaLink::GetProfileURL($kunena_my->id, false);
			if ($url) $kunena_app->redirect($url);
		}
	}
}

// Check for JSON request
if ($func == "json") {

	if(JDEBUG == 1 && defined('JFIREPHP')){
		FB::log('Kunena JSON request');
	}

	// URL format for JSON requests: e.g: index.php?option=com_kunena&func=json&action=autocomplete&do=getcat
	require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.ajax.helper.php');

	$ajaxHelper = &CKunenaAjaxHelper::getInstance();

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

$format = JRequest::getCmd ( 'format', 'html' );
if ($func != 'fb_pdf' && $func != 'pdf' && $format == 'html') {
	if (file_exists ( KUNENA_ABSTMPLTPATH . '/initialize.php' )) {
		require_once ( KUNENA_ABSTMPLTPATH . '/initialize.php' );
	} else {
		require_once (KPATH_SITE . '/template/default/initialize.php');
	}
}
if ($kunena_config->board_offline && ! CKunenaTools::isAdmin ()) {
	// if the board is offline
	echo $kunena_config->offline_message;
} else if ($kunena_config->regonly && ! $kunena_my->id) {
	// if we only allow registered users
	echo '<div id="Kunena">';
	CKunenaTools::loadTemplate('/login.php');
	echo '</div>';
} else {
	// =======================================================================================
	// Forum is online:

	jimport ( 'joomla.version' );
	$jversion = new JVersion ();
	if (($func == 'fb_pdf' || $func == 'pdf') && $jversion->RELEASE != '1.6') {
		include (JPATH_COMPONENT.DS.'lib'.DS.'kunena.pdf.php');
		$kunena_app->close ();
	}

	if ($format != 'html') {
		echo "Kunena: Unsupported output format {$format}, please use only format=html or .html";
		$kunena_app->close ();
	}

	$integration = KunenaFactory::getProfile();
	$integration->open();

	//time format
	include_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.timeformat.class.php');

	// Insert WhoIsOnlineDatas
	require_once (KUNENA_PATH_LIB .DS. 'kunena.who.class.php');

	$who = CKunenaWhoIsOnline::getInstance();
	$who->insertOnlineDatas ();

	// include required libraries
	jimport('joomla.template.template');

	// Kunena Current Template Icons Pack
	$template = KunenaFactory::getTemplate();
	include (JPATH_ROOT.'/'.$template->getFile('icons.php'));

	if(JDEBUG){
		$__profiler->mark('Session Start');
	}

	// We only save session for registered users
	$kunena_session = KunenaFactory::getSession ( true );
	if ($kunena_my->id > 0) {
		// new indicator handling
		if ($markaction == "allread") {
			if (!JRequest::checkToken()) {
				$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
				$kunena_app->redirect ( CKunenaLink::GetCategoryURL('listcat', $catid, false) );
			}
			$kunena_session->markAllCategoriesRead ();
		}
		if (!$kunena_session->save ()) $kunena_app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_SESSION_SAVE_FAILED'), 'error' );

		if ($markaction == "allread") {
			$kunena_app->redirect ( CKunenaLink::GetCategoryURL('listcat', $catid, false), JText::_('COM_KUNENA_GEN_ALL_MARKED') );
		}

		$userprofile = KunenaFactory::getUser($kunena_my->id);
		if ($userprofile->posts === null) {
			$userprofile->save();
			//$userprofile = KunenaFactory::getUser($kunena_my->id, true);
		}

		// Assign previous visit without user offset to variable for templates to decide
		$this->prevCheck = $kunena_session->lasttime;

	} else {
		// For guests we don't show new posts
		$this->prevCheck = CKunenaTimeformat::internalTime()+60;
	}

	if(JDEBUG){
		$__profiler->mark('Session End');
	}

	//Get the topics this user has already read this session from #__kunena_sessions
	$this->read_topics = explode ( ',', $kunena_session->readtopics );


	/*       _\|/_
             (o o)
     +----oOO-{_}-OOo--------------------------------+
     |    Until this section we have included the    |
     |   necessary files and gathered the required   |
     |     variables. Now let's start processing     |
     |                     them                      |
     +----------------------------------------------*/

	if ($kunena_config->highlightcode) {
		$document->addStyleDeclaration('
			div.highlight pre {
				width: '.(($kunena_config->rtewidth * 9) / 10).'px;
			}
		');
	}

	//Check if the catid requested is a parent category, because if it is
	//the only thing we can do with it is 'listcat' and nothing else
	if ($func == "showcat") {
		if ($catid != 0) {
			$category = KunenaForumCategoryHelper::get($catid);
			$catParent = $category->getParent()->id;
		}
		if ($catid == 0 || $catParent == 0) {
			$kunena_app->redirect ( CKunenaLink::GetCategoryURL('listcat',$catid, false) );
		}
	}
	?>

<div id="Kunena"><?php
	if ($kunena_config->board_offline) {
		?>
<span id="fbOffline"><?php
		echo JText::_('COM_KUNENA_FORUM_IS_OFFLINE')?></span> <?php
	}
	?>
 <?php
	if(JDEBUG){
		$__profiler->mark('Profilebox Start');
	}

	// Display login box
	KunenaForum::display('common', 'menu');
	KunenaForum::display('common', 'loginbox');

 	if(JDEBUG){
		$__profiler->mark('Profilebox End');
	}

	// Handle help / rules menuitems
	if ($func == 'article') {
		$func = $do;
	}

	if(JDEBUG){
		$__profiler->mark('$func Start');
	}

	switch ($func) {
		case 'who' :
			require_once (KUNENA_PATH_LIB .DS. 'kunena.who.class.php');
			$online = CKunenaWhoIsOnline::getInstance();
			$online->displayWho();

			break;

		case 'announcement' :
			require_once (KUNENA_PATH_LIB .DS. 'kunena.announcement.class.php');
			$ann = CKunenaAnnouncement::getInstance();
			$ann->display();

			break;

        case 'poll':
  			require_once (KUNENA_PATH_LIB .DS. 'kunena.poll.class.php');
  			$kunena_polls = CKunenaPolls::getInstance();
  			$kunena_polls->display();

            break;

		case 'polls':
			require_once (KUNENA_PATH_LIB .DS. 'kunena.poll.class.php');
			$kunena_polls = CKunenaPolls::getInstance();
			$kunena_polls->polldo();

			break;

		case 'stats' :
			require_once(KUNENA_PATH_LIB .DS. 'kunena.stats.class.php');
			$kunena_stats = new CKunenaStats ( );
			$kunena_stats->showStats ();

			break;

		case 'review' :
			require_once (KUNENA_PATH_LIB . DS . 'kunena.review.php');
			$review = new CKunenaReview($catid);
			$review->display();

			break;

		case 'rules' :
		case 'help' :
			CKunenaTools::loadTemplate('/'.$func.'.php');

			break;

		case 'report' :
			require_once(KUNENA_PATH_LIB .DS. 'kunena.report.class.php');
			$report = new CKunenaReport();
			$report->display();

			break;

		case 'search' :
		case 'advsearch' :
			require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.search.class.php');

			$kunenaSearch = new CKunenaSearch ( );
			$kunenaSearch->show ();
			break;

		case 'markthisread' :
			if (!JRequest::checkToken('get')) {
				$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
				$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, false ), JText::_('COM_KUNENA_GEN_FORUM_MARKED') );
			}
			// Mark all unread topics in the category to read
			$readTopics = $kunena_session->readtopics;
			$kunena_db->setQuery ( "SELECT thread FROM #__kunena_messages WHERE catid='{$catid}' AND parent=0 AND thread NOT IN ({$readTopics})" );
			$readForum = $kunena_db->loadResultArray ();
			if (KunenaError::checkDatabaseError()) return;
			$readTopics = implode(',', array_merge(explode(',', $readTopics), $readForum));
			$kunena_db->setQuery ( "UPDATE #__kunena_sessions set readtopics='$readTopics' WHERE userid=$kunena_my->id" );
			$kunena_db->query ();
			if (KunenaError::checkDatabaseError()) return;

			$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, false ), JText::_('COM_KUNENA_GEN_FORUM_MARKED') );
			break;

		case 'subscribecat' :
			if (!JRequest::checkToken('get')) {
				$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
				if ($userid == 0) {
					$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, false ) );
				} else {
					$kunena_app->redirect ( CKunenaLink::GetProfileURL($userid, false) );
				}
			}

			$success_msg = '';

			if ( $catid && $kunena_my->id ) {
				$query = "INSERT INTO #__kunena_user_categories (user_id,category_id,subscribed) VALUES ('$kunena_my->id','$catid',1)
					ON DUPLICATE KEY UPDATE subscribed=1;";
				$kunena_db->setQuery ( $query );

				if (@$kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = JText::_('COM_KUNENA_GEN_CATEGORY_SUBCRIBED');
				}
				KunenaError::checkDatabaseError();
			}

			$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, false ), $success_msg );
			break;

		case 'unsubscribecat' :
			if (!JRequest::checkToken('get')) {
				$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
				if ($userid == 0) {
					$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, false ), $success_msg );
				} else {
					$kunena_app->redirect ( CKunenaLink::GetProfileURL($userid, false), $success_msg );
				}
			}
			$success_msg = '';
			if ($catid && $kunena_my->id ) {
				$query = "UPDATE #__kunena_user_categories SET subscribed=0 WHERE user_id={$kunena_my->id} AND category_id={$catid};";
				$kunena_db->setQuery ( $query );

				if ($kunena_db->query () && $kunena_db->getAffectedRows () == 1) {
					$success_msg = JText::_('COM_KUNENA_GEN_CATEGORY_UNSUBCRIBED');
				}
				KunenaError::checkDatabaseError();
			}

			if ($userid == 0) {
				$kunena_app->redirect ( CKunenaLink::GetCategoryURL('showcat' , $catid, false ), $success_msg );
			} else {
				$kunena_app->redirect ( CKunenaLink::GetProfileURL($userid, false), $success_msg );
			}
			break;

		case 'karma' :
			include (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.karma.php');

			break;

		case 'thankyou':
			require_once(JPATH_COMPONENT.DS.'lib'.DS.'kunena.thankyou.php');
			$thankyou = new CKunenaThankyou ();
			$thankyou->setThankyou();

			break;

		case 'bulkactions' :
			kimport('kunena.forum.topic.helper');
			$backUrl = $kunena_app->getUserState ( "com_kunena.ActionBulk" );
			if (!JRequest::checkToken()) {
				$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
				$kunena_app->redirect ( $backUrl );
			}
			$topics = KunenaForumTopicHelper::getTopics(JRequest::getVar('cb', array ( 0 ), 'post', 'array'));
			$message = '';
			switch ($do) {
				case "bulkDel" :
					foreach ( $topics as $topic ) {
						if ($topic->authorise('delete') && $topic->publish(KunenaForum::TOPIC_DELETED)) {
							$message = JText::_ ( 'COM_KUNENA_BULKMSG_DELETED' );
						} else {
							$kunena_app->enqueueMessage ( $topic->getError (), 'notice' );
						}
					}
					break;

				case "bulkDelPerm" :
					foreach ( $topics as $topic ) {
						if ($topic->authorise('permdelete') && $topic->delete()) {
							$message = JText::_ ( 'COM_KUNENA_BULKMSG_DELETED' );
						} else {
							$kunena_app->enqueueMessage ( $topic->getError (), 'notice' );
						}
					}
					break;

				case "bulkRestore" :
					foreach ( $topics as $topic ) {
						if ($topic->authorise('undelete') && $topic->publish(KunenaForum::PUBLISHED)) {
							$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_UNDELETE' );
						} else {
							$kunena_app->enqueueMessage ( $topic->getError (), 'notice' );
						}
					}
					break;

				case "bulkMove" :
					kimport('kunena.forum.category.helper');
					$target = KunenaForumCategoryHelper::get($catid);
					if (!$target->authorise('read')) {
						$kunena_app->enqueueMessage ( $target->getError(), 'error' );
						$kunena_app->redirect ( $backUrl );
					}
					foreach ( $topics as $topic ) {
						if ($topic->authorise('move') && $topic->move($target)) {
							$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_MOVE' );
						} else {
							$kunena_app->enqueueMessage ( $topic->getError (), 'notice' );
						}
					}
					break;

				case "bulkFavorite" :
					// Unfavorite topics
					$result = KunenaForumTopicHelper::favorite(array_keys($topics), 0);
					if ( $result ) {
						$message = JText::_('COM_KUNENA_USER_UNFAVORITE_YES');
					} else {
						$message = JText::_('COM_KUNENA_POST_UNFAVORITED_TOPIC');
					}
					break;

				case "bulkSub" :
					// Unsubscribe topics
					$result = KunenaForumCategoryHelper::subscribe(array_keys($topics), 0);
					if ( $result ) {
						$message = JText::_('COM_KUNENA_USER_UNSUBSCRIBE_YES');
					} else {
						$message = JText::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC');
					}
					break;
			}
			$kunena_app->redirect($backUrl, $message);

			break;

		case 'templatechooser' :
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

			$kunena_app->redirect ( CKunenaLink::GetKunenaURL(false) );
			break;

		case 'credits' :
			include (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.credits.php');

			break;
	}

	if(JDEBUG){
		$__profiler->mark('$func End');
	}

	// Bottom Module
	CKunenaTools::showModulePosition( 'kunena_bottom' );

	// PDF and RSS
	if ($kunena_config->enablerss || $kunena_config->enablepdf) {
		if ($catid>0) {
			$category = KunenaForumCategoryHelper::get($catid);
			if ($category->pub_access == 0 && $category->parent_id) $rss_params = '&amp;catid=' . (int) $catid;
		} else {
			$rss_params = '';
		}
		if (isset($rss_params) || $kunena_config->enablepdf) {
			jimport ( 'joomla.version' );
			$jversion = new JVersion ();
			echo '<div class="krss-block">';
			if ($kunena_config->enablepdf && $func == 'view' && $jversion->RELEASE != '1.6') {
				// FIXME: add better translation:
				echo CKunenaLink::GetPDFLink($catid, $limit, $limitstart, $id, CKunenaTools::showIcon ( 'kpdf', JText::_('PDF') ), 'nofollow', '', JText::_('PDF'));
			}
			if ($kunena_config->enablerss && isset($rss_params)) {
				$document->addCustomTag ( '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_KUNENA_LISTCAT_RSS') . '" href="' . CKunenaLink::GetRSSURL($rss_params) . '" />' );
				echo CKunenaLink::GetRSSLink ( CKunenaTools::showIcon ( 'krss', JText::_('COM_KUNENA_LISTCAT_RSS') ), 'follow', $rss_params );
			}
			echo '</div>';
		}
	}

	$template = KunenaFactory::getTemplate();
	$this->params = $template->params;
	// Credits
	echo '<div class="kcredits kms"> ' . CKunenaLink::GetTeamCreditsLink ( $catid, JText::_('COM_KUNENA_POWEREDBY') ) . ' ' . CKunenaLink::GetCreditsLink ();
	if ($this->params->get('templatebyText') !=''):
	echo ' :: <a href ="'. $this->params->get('templatebyLink').'" rel="follow">' . $this->params->get('templatebyText') .' '. $this->params->get('templatebyName') ? $this->params->get('templatebyName') : '' .'</a>';
	endif;
	echo '</div>';

	// display footer

	// Show total time it took to create the page
	$__ktime = JProfiler::getmicrotime() - $__kstarttime;
?>
	<div class="kfooter"><span class="kfooter-time"><?php echo JText::_('COM_KUNENA_FOOTER_TIME_TO_CREATE').'&nbsp;'.sprintf('%0.3f', $__ktime).'&nbsp;'.JText::_('COM_KUNENA_FOOTER_TIME_SECONDS');?></span>
</div>
</div>
<!-- closes Kunena div -->
<?php
$integration = KunenaFactory::getProfile();
$integration->close();

if (empty($_POST) && $format == 'html') {
	$default = KunenaRoute::getDefault();
	if ($default) $menu->setActive($default->id);
}

} // end of online

	KunenaRoute::cacheStore();
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
	ob_end_flush();
	}
	/**
	* Escapes a value for output in a view script.
	*
	* If escaping mechanism is one of htmlspecialchars or htmlentities, uses
	* {@link $_encoding} setting.
	*
	* @param  mixed $var The output to escape.
	* @return mixed The escaped value.
	*/
	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}
}

$kunena = new KunenaApp();
