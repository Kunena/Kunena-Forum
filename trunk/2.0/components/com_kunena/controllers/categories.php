<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );
kimport ( 'kunena.forum.category.helper' );

/**
 * Kunena Categories Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaControllerCategories extends KunenaController {

	function markread() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$session = KunenaFactory::getSession();
		$session->markAllCategoriesRead ();
		if (!$session->save ()) {
			$app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_SESSION_SAVE_FAILED'), 'error' );
		} else {
			$app->enqueueMessage ( JText::_('COM_KUNENA_GEN_ALL_MARKED') );
		}
		$this->redirectBack ();
	}
}