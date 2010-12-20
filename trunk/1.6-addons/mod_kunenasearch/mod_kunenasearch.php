<?php
/**
* @package		Kunena Search
* @copyright	(C) 2010 Kunena Project. All rights reserved.
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );

$ksearch_button			 = $params->get('ksearch_button', '');
$ksearch_button_pos		 = $params->get('ksearch_button_pos', 'right');
$ksearch_button_txt	 	 = $params->get('ksearch_button_txt', JText::_('Search'));
$ksearch_width			 = intval($params->get('ksearch_width', 20));
$ksearch_maxlength		 = $ksearch-width > 20 ? $ksearch-width : 20;
$ksearch_txt			 = $params->get('ksearch_txt', JText::_('Search...'));
$moduleclass_sfx 		 = $params->get('moduleclass_sfx', '');

require(JModuleHelper::getLayoutPath('mod_kunenasearch'));
