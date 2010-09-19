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

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

$lang = JFactory::getLanguage();
if (Kunena::isSVN()) {
	$lang->load('com_kunena.install',KPATH_ADMIN);
} else {
	$lang->load('com_kunena.install',JPATH_ADMINISTRATOR);
}
?>
<h2><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_TOPIC')?></h2>
<div><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_DESC')?></div>