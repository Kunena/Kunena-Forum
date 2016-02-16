<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * User view for Kunena backend
 */
class KunenaAdminViewUser extends KunenaView
{
	public function displayEdit()
	{
		$this->setToolBarEdit();
		$this->user         = $this->get('user');
		$this->sub          = $this->get('subscriptions');
		$this->subscatslist = $this->get('catsubcriptions');
		$this->ipslist      = $this->get('IPlist');

		$avatarint        = KunenaFactory::getAvatarIntegration();
		$this->editavatar = ($avatarint instanceof KunenaAvatarKunena) ? true : false;
		$this->avatar     = $avatarint->getLink($this->user, '', 'users');

		// make the select list for the moderator flag
		$yesnoMod [] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_YES'));
		$yesnoMod [] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_NO'));

		// build the html select list
		$this->selectMod = JHtml::_('select.genericlist', $yesnoMod, 'moderator', 'class="inputbox" size="2"', 'value', 'text', $this->user->moderator);

		// make the select list for the moderator flag
		$yesnoOrder [] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_USER_ORDER_ASC'));
		$yesnoOrder [] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_USER_ORDER_DESC'));

		// build the html select list
		$this->selectOrder = JHtml::_('select.genericlist', $yesnoOrder, 'neworder', 'class="inputbox" size="2"', 'value', 'text', $this->user->ordering);
		$this->modCats     = $this->get('listmodcats');
		$this->selectRank  = $this->get('listuserranks');
		$this->display();
	}

	public function displayMove()
	{
		$this->setToolBarMove();
		$this->catslist = $this->get('movecatslist');
		$this->users    = $this->get('moveuser');
		$this->display();
	}

	protected function setToolBarEdit()
	{
		// Set the titlebar text
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'users');
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel', 'COM_KUNENA_CANCEL');
		JToolBarHelper::spacer();
		$help_url  = 'https://www.kunena.org/docs/';
		JToolBarHelper::help( 'COM_KUNENA', false, $help_url );
	}

	protected function setToolBarMove()
	{
		// Set the titlebar text
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'users');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('movemessages', 'save.png', 'save_f2.png', 'COM_KUNENA_MOVE_USERMESSAGES');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}
}
