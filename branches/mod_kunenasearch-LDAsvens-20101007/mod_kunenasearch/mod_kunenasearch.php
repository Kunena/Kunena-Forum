<?php
/**
 * @version $Id$
 * KunenaSearch Module
 * 
 * @package	Kunena Search
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );

$ksearch_button			 = $params->get('ksearch_button', '');
$ksearch_button_pos		 = $params->get('ksearch_button_pos', 'right');
$ksearch_button_txt	 	 = $params->get('ksearch_button_txt', JText::_('MOD_KUNENASEARCH_BUTTONTEXT_DEFAULT'));
$ksearch_width			 = intval($params->get('ksearch_width', 20));
$ksearch_txt			 = $params->get('ksearch_txt', JText::_('MOD_KUNENASEARCH_TEXT_DEFAULT'));
$moduleclass_sfx 		 = $params->get('moduleclass_sfx', '');

require(JModuleHelper::getLayoutPath('mod_kunena_search'));
