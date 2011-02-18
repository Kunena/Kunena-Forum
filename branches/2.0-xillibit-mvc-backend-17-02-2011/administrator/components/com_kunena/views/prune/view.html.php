<?php
/**
 * @version $Id: view.html.php 4381 2011-02-05 20:55:31Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * Kunena report view for Kunena backend
 */
class KunenaAdminViewPrune extends KunenaView {
	function display() {
		$this->setToolBarDefault();
		$this->forumList = $this->get('Forumlist');
		$this->listtrashdelete = $this->get('Listtrashdelete');

		parent::display ();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::spacer();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('doprune', 'delete.png', 'delete_f2.png', 'COM_KUNENA_PRUNE', false);
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();

	}
}
