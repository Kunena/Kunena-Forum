<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Kunena 1.6.1: Delete view/artcile folder which is not needed anymore
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

function kunena_upgrade_161_delfiles($parent) {
	if (KunenaForum::isSVN()) return;

	//Import filesystem libraries.
	jimport ( 'joomla.filesystem.folder' );

	$path = JPATH_COMPONENT.DS.'views'.DS.'article';
	if(JFolder::exists($path)) JFolder::delete($path);

	return array('action'=>'', 'name'=>JText::_ ( 'COM_KUNENA_INSTALL_161_DELFILES' ), 'success'=>true);
}