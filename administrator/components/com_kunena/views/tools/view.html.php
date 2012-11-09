<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * About view for Kunena cpanel
 */
class KunenaAdminViewTools extends KunenaView {
	function displayDefault() {
		$this->setToolBarDefault();
		$this->display ();
	}

	function displayPrune() {
		$this->forumList = $this->get('PruneCategories');
		$this->listtrashdelete = $this->get('PruneListtrashdelete');
		$this->controloptions = $this->get('PruneControlOptions');
		$this->keepSticky = $this->get('PruneKeepSticky');

		$this->setToolBarPrune();
		$this->display ();
	}

	function displaySubscriptions() {
		$this->id = JRequest::getInt('id');

		$this->display ();
	}

	function displaySyncUsers() {
		$this->setToolBarSyncUsers();
		$this->display ();
	}

	function displayRecount() {
		$this->setToolBarRecount();
		$this->display ();
	}

	function displayMenu() {
		$this->legacy = KunenaMenuFix::getLegacy();
		$this->invalid = KunenaMenuFix::getInvalid();
		$this->conflicts = KunenaMenuFix::getConflicts();

		$this->setToolBarMenu();
		$this->display ();
	}

	function displayPurgeReStatements() {
		$this->setToolBarPurgeReStatements();
		$this->display ();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::back();
	}

	protected function setToolBarPrune() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('prune', 'delete.png', 'delete_f2.png', 'COM_KUNENA_PRUNE', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarSyncUsers() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('syncusers', 'apply.png', 'apply_f2.png', 'COM_KUNENA_SYNC', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarRecount() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('recount', 'apply.png', 'apply_f2.png', 'COM_KUNENA_A_RECOUNT', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarMenu() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		if (!empty($this->legacy)) JToolBarHelper::custom('fixlegacy', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_MENU_TOOLBAR_FIXLEGACY', false);
		JToolBarHelper::custom('trashmenu', 'restore.png', 'restore_f2.png', 'COM_KUNENA_A_TRASH_MENU', false);
		JToolBarHelper::spacer();
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}

	protected function setToolBarPurgeReStatements() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::trash('purgerestatements', 'COM_KUNENA_A_PURGE_RE_MENU_VALIDATE', false);
		JToolBarHelper::spacer();
		JToolBarHelper::back();
		JToolBarHelper::spacer();
	}
}
