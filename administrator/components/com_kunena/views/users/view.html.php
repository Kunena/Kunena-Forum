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
 * Users view for Kunena backend
 */
class KunenaAdminViewUsers extends KunenaView {
	function displayDefault() {
		$this->setToolBarDefault();
		$this->users = $this->get('users');
		$this->navigation = $this->get ( 'AdminNavigation' );
		$this->display();
	}

	function displayEdit() {
		$this->setToolBarEdit();
		$this->user = $this->get('user');
		$this->sub = $this->get('subscriptions');
		$this->subscatslist = $this->get('catsubcriptions');
		$this->ipslist = $this->get('IPlist');

		$avatarint = KunenaFactory::getAvatarIntegration();
		$this->editavatar = ($avatarint instanceof KunenaAvatarKunena) ? true : false;
		$this->avatar = $avatarint->getLink($this->user, '', 'profile');

		// make the select list for the moderator flag
		$yesnoMod [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_YES') );
		$yesnoMod [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_NO') );
		// build the html select list
		$this->selectMod = JHTML::_ ( 'select.genericlist', $yesnoMod, 'moderator', 'class="inputbox" size="2"', 'value', 'text', $this->user->moderator );
		// make the select list for the moderator flag
		$yesnoOrder [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_USER_ORDER_ASC') );
		$yesnoOrder [] = JHTML::_ ( 'select.option', '1', JText::_('COM_KUNENA_USER_ORDER_DESC') );
		// build the html select list
		$this->selectOrder = JHTML::_ ( 'select.genericlist', $yesnoOrder, 'neworder', 'class="inputbox" size="2"', 'value', 'text', $this->user->ordering );
		$this->modCats = $this->get('listmodcats');
		$this->selectRank = $this->get('listuserranks');
		$this->display();
	}

	function displayMove() {
		$this->setToolBarMove();
		$this->catslist = $this->get('movecatslist');
		$this->users = $this->get('moveuser');
		$this->display();
	}

	protected function setToolBarDefault() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('edit', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('logout', 'cancel.png', 'cancel_f2.png', 'COM_KUNENA_LOGOUT');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('move', 'move.png', 'move_f2.png', 'COM_KUNENA_MOVE_USERMESSAGES');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('trashusermessages', 'trash.png', 'icon-32-move.png', 'COM_KUNENA_TRASH_USERMESSAGES');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('delete','delete.png','delete_f2.png', 'COM_KUNENA_USER_DELETE');
		JToolBarHelper::spacer();
	}

	protected function setToolBarEdit() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('users', 'COM_KUNENA_CANCEL');
		JToolBarHelper::spacer();
	}

	protected function setToolBarMove() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('movemessages', 'save.png', 'save_f2.png', 'COM_KUNENA_MOVE_USERMESSAGES');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('users');
		JToolBarHelper::spacer();
	}
}
