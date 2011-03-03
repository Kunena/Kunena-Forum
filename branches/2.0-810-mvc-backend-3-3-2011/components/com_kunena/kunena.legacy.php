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

kimport('kunena.error');
kimport('kunena.forum.category.helper');

class KunenaApp {

	function __construct() {
		ob_start();

		$kunena_config = KunenaFactory::getConfig ();
		if ($kunena_config->debug) {
			KunenaError::initialize();
		}

// First of all take a profiling information snapshot for JFirePHP
if(JDEBUG){
	require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.profiler.php');
	$__profiler = KProfiler::GetInstance();
	$__profiler->mark('Start');
}

$func = strtolower (JRequest::getWord ( 'func', JRequest::getWord ( 'view' ) ) );
JRequest::setVar ( 'func', $func );
JRequest::setVar ( 'view' );
$format = JRequest::getCmd ( 'format', 'html' );

// SEF turns &do=xxx into &layout=xxx, so we need to get both variables
$layout = JRequest::getWord ( 'do', JRequest::getWord ( 'layout', null ) );
JRequest::setVar ( 'do', $layout );
JRequest::setVar ( 'layout' );

require_once(KUNENA_PATH . DS . 'router.php');
if ($func && !isset(KunenaRouter::$functions[$func])) {
	// If func is not legal, raise joomla error
	return JError::raiseError( 500, 'Kunena function "' . $func . '" not found' );
}

$kunena_app = JFactory::getApplication ();
$uri = KunenaRoute::current(true);
kimport ('kunena.route.legacy');
if (KunenaRouteLegacy::convert($uri)) {
	// FIXME: using wrong Itemid
	$kunena_app->redirect (KunenaRoute::_($uri, false));
}

$me = KunenaFactory::getUser();
$menu = $kunena_app->getMenu ();
$active = $menu->getActive ();

// Central Location for all internal links
require_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.link.class.php');
kimport('kunena.html.parser');

// Redirect profile (menu item) to the right component
if ($func == 'profile' && empty($_POST)) {
	$redirect = 1;
	if (!empty($active)) {
		$params = new JParameter($active->params);
		$redirect = $params->get('integration');
	}
	if ($redirect) {
		$profileIntegration = KunenaFactory::getProfile();
		if (!($profileIntegration instanceof KunenaProfileKunena)) {
			$url = CKunenaLink::GetProfileURL($me->userid, false);
			if ($url) $kunena_app->redirect($url);
		}
	}
}

// Check for JSON request
$document = JFactory::getDocument();
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
	$action = JRequest::getCmd ( 'action', '' );
	if ($action!='uploadfile') JResponse::setHeader( 'Content-Disposition', 'attachment; filename="kunena.json"' );

	$value = JRequest::getVar ( 'value', '' );

	JResponse::sendHeaders();

	if ($kunena_config->board_offline && ! $me->isAdmin ()){
		// when the forum is offline, we don't entertain json requests
		json_encode ( array(
				'status' => '0',
				'error' => @sprintf(_KUNENA_FORUM_OFFLINE)) );
	}
	else {
		// Generate reponse
		$do = JRequest::getCmd ( 'do', '' );
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
if ($kunena_config->board_offline && ! $me->isAdmin ()) {
	// if the board is offline
	echo $kunena_config->offline_message;
} else if ($kunena_config->regonly && ! $me->userid) {
	// if we only allow registered users
	echo '<div id="Kunena">';
	//KunenaForum::display('common', 'default', null, array('header'=>JText::_('COM_KUNENA_LOGIN_NOTIFICATION'), 'body'=>JText::_('COM_KUNENA_LOGIN_FORUM')));
	require_once KUNENA_PATH . '/class.kunena.php';
	CKunenaTools::loadTemplate('/login.php');
	echo '</div>';
} else {
	// =======================================================================================
	// Forum is online:

	$integration = KunenaFactory::getProfile();
	$integration->open();

	//time format
	include_once (JPATH_COMPONENT . DS . 'lib' . DS . 'kunena.timeformat.class.php');

	// Kunena Current Template Icons Pack
	$template = KunenaFactory::getTemplate();

	if(JDEBUG){
		$__profiler->mark('Session Start');
	}

	// We only save session for registered users
	$kunena_session = KunenaFactory::getSession ( true );
	if ($me->userid && !$me->exists()) {
		$me->save();

		// Assign previous visit without user offset to variable for templates to decide
		$this->prevCheck = $kunena_session->lasttime;

	} else {
		// For guests we don't show new posts
		$this->prevCheck = CKunenaTimeformat::internalTime()+60;
	}

	if(JDEBUG){
		$__profiler->mark('Session End');
	}

	/*       _\|/_
             (o o)
     +----oOO-{_}-OOo--------------------------------+
     |    Until this section we have included the    |
     |   necessary files and gathered the required   |
     |     variables. Now let's start processing     |
     |                     them                      |
     +----------------------------------------------*/

	if ($kunena_config->board_offline) {
		?><span id="fbOffline"><?php echo JText::_('COM_KUNENA_FORUM_IS_OFFLINE')?></span> <?php
	}

	if(JDEBUG){
		$__profiler->mark('$func Start');
	}

	switch ($func) {
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

$integration = KunenaFactory::getProfile();
$integration->close();

//$params = JComponentHelper::getParams( 'com_kunena' );
//if ($params->get( 'show_page_title' )) $document->setTitle ( $params->get( 'page_title' ) );

//if (empty($_POST) && $format == 'html') {
//	$default = KunenaRoute::getDefault();
//	if ($default) $menu->setActive($default->id);
//}

} // end of online

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
}

$kunena = new KunenaApp();
