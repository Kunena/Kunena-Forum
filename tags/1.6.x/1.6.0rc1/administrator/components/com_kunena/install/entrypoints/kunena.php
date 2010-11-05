<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

defined( '_JEXEC' ) or die();

// Kunena wide defines

// Component name amd database prefix
define ( 'KUNENA_COMPONENT_NAME', basename ( dirname ( __FILE__ ) ) );
define ( 'KUNENA_NAME', substr ( KUNENA_COMPONENT_NAME, 4 ) );

// Component location
define ( 'KUNENA_COMPONENT_LOCATION', basename ( dirname ( dirname ( __FILE__ ) ) ) );

// Component paths
define ( 'KPATH_COMPONENT_RELATIVE', KUNENA_COMPONENT_LOCATION . DS . KUNENA_COMPONENT_NAME );
define ( 'KPATH_SITE', JPATH_ROOT . DS . KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_ADMIN', JPATH_ADMINISTRATOR . DS . KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_MEDIA', JPATH_ROOT . DS . 'media' . DS . KUNENA_NAME );
define ( 'KPATH_MEDIA_LEGACY', JPATH_ROOT . DS . 'images/fbfiles/' );

// Kunena has been initialized
define ( 'KUNENA_LOADED', 1 );

$lang = JFactory::getLanguage();
if (Kunena::isSVN()) {
	$lang->load('com_kunena.install',KPATH_ADMIN);
} else {
	$lang->load('com_kunena.install',JPATH_ADMINISTRATOR);
}
?>
<h2><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_TOPIC')?></h2>
<div><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_DESC')?></div>