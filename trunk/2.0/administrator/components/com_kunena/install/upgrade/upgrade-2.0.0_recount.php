<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Kunena 1.6.1: Delete view/artcile folder which is not needed anymore
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

kimport('kunena.user.helper');
kimport('kunena.forum.category.helper');
kimport('kunena.forum.topic.helper');
kimport('kunena.forum.topic.user.helper');

function kunena_upgrade_200_recount($parent) {

	// Update topic statistics
	KunenaForumTopicHelper::recount();

	// Update usertopic statistics
	KunenaForumTopicUserHelper::recount();

	// Update user statistics
	KunenaUserHelper::recount();

	// Update category statistics
	KunenaForumCategoryHelper::recount();

	return array('action'=>'', 'name'=>JText::_ ( 'COM_KUNENA_INSTALL_200_RECOUNT' ), 'success'=>true);
}