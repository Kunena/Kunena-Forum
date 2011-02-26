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