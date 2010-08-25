<?php
/**
 * @version $Id$
 * Kunenalogin Module
 * @package Kunena login
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

// Kunena detection and version check
$minKunenaVersion = '1.6.0-RC2';
if (! class_exists ( 'Kunena' ) || Kunena::versionBuild () < 3296) {
	echo JText::sprintf ( 'MOD_KUNENALOGIN_KUNENA_NOT_INSTALLED', $minKunenaVersion );
	return;
}
// Kunena online check
if (! Kunena::enabled ()) {
	echo JText::_ ( 'MOD_KUNENALOGIN_KUNENA_OFFLINE' );
	return;
}

$params = ( object ) $params;
$modKunenaLogin = new ModKunenaLogin ( $params );
$modKunenaLogin->display();

class ModKunenaLogin {
	function __construct($params) {
		// Include the syndicate functions only once
		require_once (dirname ( __FILE__ ) . DS . 'helper.php');
		require_once (KUNENA_PATH . DS . 'class.kunena.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.link.class.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.image.class.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.login.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.timeformat.class.php');
		$this->params = $params;
	}

	function display() {
		$this->login = CKunenaLogin::getloginFields ();
		$this->logout = CKunenaLogin::getlogoutFields ();

		// load Kunena main language file so we can leverage langaueg strings from it
		KunenaFactory::loadLanguage ();

		$this->my = JFactory::getUser ();
		$this->private = KunenaFactory::getPrivateMessaging ();
		$this->PMCount = $this->private->getUnreadCount ( $this->my->id );
		$this->PMlink = $this->private->getInboxLink ( $this->PMCount ? JText::sprintf ( 'MOD_KUNENALOGIN_NEW_MESSAGE', $this->PMCount ) : JText::_ ( 'MOD_KUNENALOGIN_MYMESSAGE' ) );

		$this->params->def ( 'greeting', 1 );
		$this->type = modKunenaLoginHelper::getType ();
		$this->return = modKunenaLoginHelper::getReturnURL ( $this->params, $this->type );

		$this->loadCSS = modKunenaLoginHelper::loadCSS ( 'mod_kunenalogin' ); // load CSS stylesheet
		$this->user = JFactory::getUser ();
		require (JModuleHelper::getLayoutPath ( 'mod_kunenalogin' ));
	}
}