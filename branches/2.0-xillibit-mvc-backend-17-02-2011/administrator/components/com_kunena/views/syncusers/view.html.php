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
 * Kunena Syncusers view for Kunena backend
 */
class KunenaAdminViewSyncusers extends KunenaView {
	function display() {
		$this->setToolBarDefault();

		parent::display ();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
        JToolBarHelper::custom('sync', 'apply.png', 'apply_f2.png', 'COM_KUNENA_SYNC', false);
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
	}
}

