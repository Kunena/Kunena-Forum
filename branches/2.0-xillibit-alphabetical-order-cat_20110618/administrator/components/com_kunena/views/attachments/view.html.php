<?php
/**
 * @version $Id: view.html.php 4469 2011-02-21 16:55:20Z xillibit $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );
kimport ('kunena.forum.message.attachment.helper');

/**
 * Attachments view for Kunena backend
 */
class KunenaAdminViewAttachments extends KunenaView {
	function displayDefault() {
		$this->items = $this->get('Items');
		$this->navigation = $this->get ( 'AdminNavigation' );
		$this->setToolBarDefault();

		$this->display ();
	}

	protected function setToolBarDefault() {
		// Set the titlebar text
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('delete','restore.png','restore_f2.png', 'COM_KUNENA_GEN_DELETE');
		JToolBarHelper::spacer();
	}
}
