<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport('kunena.user.helper');
kimport('kunena.forum.category.helper');
kimport('kunena.forum.topic.helper');
kimport('kunena.forum.topic.user.helper');

// Kunena 2.0.0: Recount forum statistics
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