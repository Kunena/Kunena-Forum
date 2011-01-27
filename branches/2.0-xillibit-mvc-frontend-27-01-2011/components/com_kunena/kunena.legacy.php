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

// SEF turns &do=xxx into &layout=xxx, so we need to get both variables
$layout = JRequest::getWord ( 'do', JRequest::getWord ( 'layout', null ) );
JRequest::setVar ( 'do' );

$uri = KunenaRoute::current(true);
kimport ('kunena.route.legacy');
if (KunenaRouteLegacy::convert($uri)) {
	// FIXME: using wrong Itemid
	$kunena_app->redirect (KunenaRoute::_($uri, false));
}

// Convert layout back to do to make old code to work
JRequest::setVar ( 'do', $layout );
JRequest::setVar ( 'layout' );

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
if ($format == 'html') {
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

		case 'review' :
			require_once (KUNENA_PATH_LIB . DS . 'kunena.review.php');
			$review = new CKunenaReview($catid);
			$review->display();

			break;

		case 'rules' :
		case 'help' :
			CKunenaTools::loadTemplate('/'.$func.'.php');

			break;

		case 'search' :
		case 'advsearch' :
			require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.search.class.php');

			$kunenaSearch = new CKunenaSearch ( );
			$kunenaSearch->show ();
			break;

		case 'karma' :
			include (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.karma.php');

			break;

		case 'thankyou':
			require_once(JPATH_COMPONENT.DS.'lib'.DS.'kunena.thankyou.php');
			$thankyou = new CKunenaThankyou ();
			$thankyou->setThankyou();

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
	}

	if(JDEBUG){
		$__profiler->mark('$func End');
	}

	// Bottom Module
	CKunenaTools::showModulePosition( 'kunena_bottom' );

	// RSS
	if ($kunena_config->enablerss) {
		if ($catid>0) {
			$category = KunenaForumCategoryHelper::get($catid);
			if ($category->pub_access == 0 && $category->parent_id) $rss_params = '&amp;catid=' . (int) $catid;
		} else {
			$rss_params = '';
		}
		if (isset($rss_params)) {
			jimport ( 'joomla.version' );
			$jversion = new JVersion ();
			echo '<div class="krss-block">';
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
