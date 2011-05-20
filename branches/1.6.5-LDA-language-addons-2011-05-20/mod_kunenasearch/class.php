<?php
/**
* @package		Kunena Search
* @copyright	(C) 2010 Kunena Project. All rights reserved.
* @license		GNU/GPL
*/


// no direct access
defined('_JEXEC') or die('Restricted access');

class modKunenaSearch {
	public function __construct($params) {
	$this->document = JFactory::getDocument ();

	require_once (KUNENA_PATH_LIB . '/kunena.link.class.php');

	// Initialize session
	$session = KunenaFactory::getSession ();
	$session->updateAllowedForums();

	$this->ksearch_button			= $params->get('ksearch_button', '');
	$this->ksearch_button_pos		= $params->get('ksearch_button_pos', 'right');
	$this->ksearch_button_txt	 	= $params->get('ksearch_button_txt', JText::_('Search'));
	$this->ksearch_width			= intval($params->get('ksearch_width', 20));
	$this->ksearch_maxlength		= $this->ksearch_width > 20 ? $this->ksearch_width : 20;
	$this->ksearch_txt			 	= $params->get('ksearch_txt', JText::_('Search...'));
	$this->ksearch_moduleclass_sfx 	= $params->get('moduleclass_sfx', '');

	$this->params = $params;

	require(JModuleHelper::getLayoutPath('mod_kunenasearch'));
	}
}
