<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

$lang = JFactory::getLanguage();
$lang->load('com_kunena.install', KPATH_ADMIN, 'en-GB');
$lang->load('com_kunena.install', KPATH_ADMIN);
?>
<h2><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_TOPIC')?></h2>
<div><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_DESC')?></div>