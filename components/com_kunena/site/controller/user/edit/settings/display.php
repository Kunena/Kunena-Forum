<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserEditSettingsDisplay
 *
 * @since  K4.0
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
		$item->field = JHtml::_('select.genericlist', $options, 'messageordering', 'class="kinputbox form-control" size="1"',
			'value', 'text', $this->escape($this->profile->ordering), 'kmessageordering');
		$this->settings[] = $item;

		$item = new StdClass;
		$item->name = 'hidemail';
		$item->label = JText::_('COM_KUNENA_USER_HIDEEMAIL');
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field = JHtml::_('select.genericlist', $options, 'hidemail', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->profile->hideEmail), 'khidemail');
		$this->settings[] = $item;

		$item = new StdClass;
		$item->name = 'showonline';
		$item->label = JText::_('COM_KUNENA_USER_SHOWONLINE');
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field = JHtml::_('select.genericlist', $options, 'showonline', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->profile->showOnline), 'kshowonline');
		$this->settings[] = $item;

		$item = new StdClass();
		$item->name = 'cansubscribe';
		$item->label = JText::_('COM_KUNENA_USER_CANSUBSCRIBE');
		$options = array();
		$options[] = JHtml::_('select.option', -1, JText::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_NO'));
		$options[] = JHtml::_('select.option', 1, JText::_('COM_KUNENA_YES'));
		$item->field = JHtml::_('select.genericlist', $options, 'cansubscribe', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->profile->canSubscribe), 'kcansubscribe');
		$this->settings[] = $item;

		$item = new StdClass();
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
			'text', $this->escape($this->profile->userListtime), 'kuserlisttime');
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
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item
		$params = $menu_item->params; // get the params
		$params_title = $params->get('page_title');
		$params_keywords = $params->get('menu-meta_keywords');
		$params_description = $params->get('menu-description');

		if (!empty($params_title))
		{
			$title = $params->get('page_title');
			$this->setTitle($title);
		}
		else
		{
			$this->setTitle($this->headerText);
		}

		if (!empty($params_keywords))
		{
			$keywords = $params->get('menu-meta_keywords');
			$this->setKeywords($keywords);
		}
		else
		{
			$this->setKeywords($this->headerText);
		}

		if (!empty($params_description))
		{
			$description = $params->get('menu-meta_description');
			$this->setDescription($description);
		}
		else
		{
			$this->setDescription($this->headerText);
		}
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
