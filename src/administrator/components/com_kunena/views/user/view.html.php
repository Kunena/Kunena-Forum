<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * User view for Kunena backend
 *
 * @since  K1.0
 */
class KunenaAdminViewUser extends KunenaView
{
	/**
	 *
	 * @since Kunena
	 */
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

		// Make the select list for the moderator flag
		$yesnoMod [] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_YES'));
		$yesnoMod [] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_NO'));

		// Build the html select list
		$this->selectMod = JHtml::_('select.genericlist', $yesnoMod, 'moderator', 'class="inputbox" size="2"', 'value', 'text', $this->user->moderator);

		// Make the select list for the moderator flag
		$yesnoOrder [] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_USER_ORDER_ASC'));
		$yesnoOrder [] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_USER_ORDER_DESC'));

		// Build the html select list
		$item = new StdClass;
		$item->name = 'hidemail';
		$item->label = JText::_('COM_KUNENA_USER_HIDEEMAIL');
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field = JHtml::_('select.genericlist', $options, 'hidemail', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->user->hideEmail), 'khidemail');
		$this->settings[] = $item;

		$item = new StdClass;
		$item->name = 'showonline';
		$item->label = JText::_('COM_KUNENA_USER_SHOWONLINE');
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field = JHtml::_('select.genericlist', $options, 'showonline', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->user->showOnline), 'kshowonline');
		$this->settings[] = $item;

		$item = new StdClass;
		$item->name = 'cansubscribe';
		$item->label = JText::_('COM_KUNENA_USER_CANSUBSCRIBE');
		$options = array();
		$options[] = JHtml::_('select.option', -1, JText::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field = JHtml::_('select.genericlist', $options, 'cansubscribe', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->user->canSubscribe), 'kcansubscribe');
		$this->settings[] = $item;

		$item = new StdClass;
		$item->name = 'userlisttime';
		$item->label = JText::_('COM_KUNENA_USER_USERLISTTIME');
		$options = array();
		$options[] = JHtml::_('select.option', -2, JText::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[] = JHtml::_('select.option', -1, JText::_('COM_KUNENA_SHOW_ALL'));
		$options[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_SHOW_LASTVISIT'));
		$options[] = JHtml::_('select.option', 4, JText::_('COM_KUNENA_SHOW_4_HOURS'));
		$options[] = JHtml::_('select.option', 8, JText::_('COM_KUNENA_SHOW_8_HOURS'));
		$options[] = JHtml::_('select.option', 12, JText::_('COM_KUNENA_SHOW_12_HOURS'));
		$options[] = JHtml::_('select.option', 24, JText::_('COM_KUNENA_SHOW_24_HOURS'));
		$options[] = JHtml::_('select.option', 48, JText::_('COM_KUNENA_SHOW_48_HOURS'));
		$options[] = JHtml::_('select.option', 168, JText::_('COM_KUNENA_SHOW_WEEK'));
		$options[] = JHtml::_('select.option', 720, JText::_('COM_KUNENA_SHOW_MONTH'));
		$options[] = JHtml::_('select.option', 8760, JText::_('COM_KUNENA_SHOW_YEAR'));
		$item->field = JHtml::_('select.genericlist', $options, 'userlisttime', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->user->userListtime), 'kuserlisttime');
		$this->settings[] = $item;

		$this->selectOrder = JHtml::_('select.genericlist', $yesnoOrder, 'neworder', 'class="inputbox" size="2"', 'value', 'text', $this->user->ordering);
		$this->modCats     = $this->get('listmodcats');
		$this->selectRank  = $this->get('listuserranks');
		$this->social = array('twitter', 'facebook', 'myspace', 'skype', 'linkedin', 'delicious',
			'friendfeed', 'digg', 'yim', 'aim', 'google', 'icq', 'microsoft', 'blogspot', 'flickr',
			'bebo', 'instagram', 'qq', 'qzone', 'weibo', 'wechat', 'apple', 'vk', 'telegram');
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	public function displayMove()
	{
		$this->setToolBarMove();
		$this->catslist = $this->get('movecatslist');
		$this->users    = $this->get('moveuser');
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolBarEdit()
	{
		// Set the titlebar text
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'users');
		JToolBarHelper::spacer();
		JToolBarHelper::apply('apply');
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel', 'COM_KUNENA_CANCEL');
		JToolBarHelper::spacer();
		$help_url = 'https://www.kunena.org/docs/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
	 */
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
