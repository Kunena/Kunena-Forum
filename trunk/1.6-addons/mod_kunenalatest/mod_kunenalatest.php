<?php
/**
 * @version $Id$
 * KunenaLatest Module
 * @package Kunena Latest
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// no direct access
defined ( '_JEXEC' ) or die ( '' );

// Kunena detection and version check
$minKunenaVersion = '1.6.0-RC2';
if (!class_exists('Kunena') || Kunena::versionBuild() < 3258) {
	echo JText::sprintf ( 'MOD_KUNENALATEST_KUNENA_NOT_INSTALLED', $minKunenaVersion );
	return;
}
// Kunena online check
if (!Kunena::enabled()) {
	echo JText::_ ( 'MOD_KUNENALATEST_KUNENA_OFFLINE' );
	return;
}

$params = ( object ) $params;
$klatest = new modKunenaLatest ( $params );

class modKunenaLatest {
	public function __construct($params) {
		require_once (KUNENA_PATH_LIB . DS . 'kunena.link.class.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.image.class.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.timeformat.class.php');
		require_once (JPATH_ADMINISTRATOR . '/components/com_kunena/libraries/html/parser.php');
		$this->kunena_config = KunenaFactory::getConfig ();
		$this->document = JFactory::getDocument ();
		$this->document->addStyleSheet ( JURI::root () . 'modules/mod_kunenalatest/tmpl/klatest.css' );

		$this->latestdo = null;

		if ($params->get ( 'choosemodel' ) != 'latest') {
			$this->latestdo = $params->get ( 'choosemodel' );
		}

		// Include the kunenalatest functions only once
		require_once (dirname ( __FILE__ ) . '/helper.php');

		$this->params = $params;
		$this->ktemplate = KunenaFactory::getTemplate();
		$this->klistpost = modKunenaLatestHelper::getKunenaLatestList ( $params );

		require (JModuleHelper::getLayoutPath ( 'mod_kunenalatest' ));
	}
}