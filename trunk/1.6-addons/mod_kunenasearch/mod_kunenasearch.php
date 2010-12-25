<?php
/**
* @package		Kunena Search
* @copyright	(C) 2010 Kunena Project. All rights reserved.
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Kunena detection and version check
$minKunenaVersion = '1.6.2';
if (!class_exists('Kunena') || Kunena::versionBuild() < 3892) {
	echo JText::sprintf ( 'MOD_KUNENASEARCH_KUNENA_NOT_INSTALLED', $minKunenaVersion );
	return;
}
// Kunena online check
if (!Kunena::enabled()) {
	echo JText::_ ( 'MOD_KUNENASEARCH_KUNENA_OFFLINE' );
	return;
}

// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );

// prevent IDE warnings
$params = ( object ) $params;

$this->ksearch_button			= $params->get('ksearch_button', '');
$this->ksearch_button_pos		= $params->get('ksearch_button_pos', 'right');
$this->ksearch_button_txt	 	= $params->get('ksearch_button_txt', JText::_('Search'));
$this->ksearch_width			= intval($params->get('ksearch_width', 20));
$this->ksearch_maxlength		= $this->ksearch_width > 20 ? $this->ksearch_width : 20;
$this->ksearch_txt			 	= $params->get('ksearch_txt', JText::_('Search...'));
$this->ksearch_moduleclass_sfx 	= $params->get('moduleclass_sfx', '');

require(JModuleHelper::getLayoutPath('mod_kunenasearch'));
