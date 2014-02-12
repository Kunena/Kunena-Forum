<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserEditSettingsDisplay
 *
 * @since  3.1
 */
class ComponentKunenaControllerUserEditSettingsDisplay extends ComponentKunenaControllerUserEditDisplay
{
	protected $name = 'User/Edit/Settings';

	public $settings;

	/**
	 * Prepare Kunena user settings.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$item = new StdClass;
		$item->name = 'messageordering';
		$item->label = JText::_('COM_KUNENA_USER_ORDER');
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[] = JHtml::_('select.option', 2, JText::_('COM_KUNENA_USER_ORDER_ASC'));
		$options[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_USER_ORDER_DESC'));
		$item->field = JHtml::_('select.genericlist', $options, 'messageordering', 'class="kinputbox" size="1"',
			'value', 'text', $this->escape($this->profile->ordering), 'kmessageordering');
		$this->settings[] = $item;

		$item = new StdClass;
		$item->name = 'hidemail';
		$item->label = JText::_('COM_KUNENA_USER_HIDEEMAIL');
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field = JHtml::_('select.genericlist', $options, 'hidemail', 'class="kinputbox" size="1"', 'value',
			'text', $this->escape($this->profile->hideEmail), 'khidemail');
		$this->settings[] = $item;

		$item = new StdClass;
		$item->name = 'showonline';
		$item->label = JText::_('COM_KUNENA_USER_SHOWONLINE');
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field = JHtml::_('select.genericlist', $options, 'showonline', 'class="kinputbox" size="1"', 'value',
			'text', $this->escape($this->profile->showOnline), 'kshowonline');
		$this->settings[] = $item;

		$this->headerText = JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS_TITLE');
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}

	/**
	 * Escape text for HTML.
	 *
	 * @param   string  $string  String to be escaped.
	 *
	 * @return  string
	 */
	protected function escape($string)
	{
		return htmlentities($string, ENT_COMPAT, 'UTF-8');
	}
}
