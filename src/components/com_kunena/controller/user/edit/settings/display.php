<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerUserEditSettingsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserEditSettingsDisplay extends ComponentKunenaControllerUserEditDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'User/Edit/Settings';

	/**
	 * @var
	 * @since Kunena
	 */
	public $settings;

	/**
	 * Prepare Kunena user settings.
	 *
	 * @return void
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$item             = new StdClass;
		$item->name       = 'messageordering';
		$item->label      = Text::_('COM_KUNENA_USER_ORDER');
		$options          = array();
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[]        = HTMLHelper::_('select.option', 2, Text::_('COM_KUNENA_USER_ORDER_ASC'));
		$options[]        = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_USER_ORDER_DESC'));
		$item->field      = HTMLHelper::_('select.genericlist', $options, 'messageordering', 'class="kinputbox form-control" size="1"',
			'value', 'text', $this->escape($this->profile->ordering), 'kmessageordering'
		);
		$this->settings[] = $item;

		$item             = new StdClass;
		$item->name       = 'hidemail';
		$item->label      = Text::_('COM_KUNENA_USER_HIDEEMAIL');
		$options          = array();
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
		$options[]        = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));
		$options[]        = HTMLHelper::_('select.option', 2, Text::_('COM_KUNENA_A_ONLY_REGISTERED'));
		$item->field      = HTMLHelper::_('select.genericlist', $options, 'hidemail', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->profile->hideEmail), 'khidemail'
		);
		$this->settings[] = $item;

		$item             = new StdClass;
		$item->name       = 'showonline';
		$item->label      = Text::_('COM_KUNENA_USER_SHOWONLINE');
		$options          = array();
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
		$options[]        = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));
		$item->field      = HTMLHelper::_('select.genericlist', $options, 'showonline', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->profile->showOnline), 'kshowonline'
		);
		$this->settings[] = $item;

		$item             = new StdClass;
		$item->name       = 'cansubscribe';
		$item->label      = Text::_('COM_KUNENA_USER_CANSUBSCRIBE');
		$options          = array();
		$options[]        = HTMLHelper::_('select.option', -1, Text::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
		$options[]        = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));
		$item->field      = HTMLHelper::_('select.genericlist', $options, 'cansubscribe', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->profile->canSubscribe), 'kcansubscribe'
		);
		$this->settings[] = $item;

		$item             = new StdClass;
		$item->name       = 'userlisttime';
		$item->label      = Text::_('COM_KUNENA_USER_USERLISTTIME');
		$options          = array();
		$options[]        = HTMLHelper::_('select.option', -2, Text::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[]        = HTMLHelper::_('select.option', -1, Text::_('COM_KUNENA_SHOW_ALL'));
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_SHOW_LASTVISIT'));
		$options[]        = HTMLHelper::_('select.option', 4, Text::_('COM_KUNENA_SHOW_4_HOURS'));
		$options[]        = HTMLHelper::_('select.option', 8, Text::_('COM_KUNENA_SHOW_8_HOURS'));
		$options[]        = HTMLHelper::_('select.option', 12, Text::_('COM_KUNENA_SHOW_12_HOURS'));
		$options[]        = HTMLHelper::_('select.option', 24, Text::_('COM_KUNENA_SHOW_24_HOURS'));
		$options[]        = HTMLHelper::_('select.option', 48, Text::_('COM_KUNENA_SHOW_48_HOURS'));
		$options[]        = HTMLHelper::_('select.option', 168, Text::_('COM_KUNENA_SHOW_WEEK'));
		$options[]        = HTMLHelper::_('select.option', 720, Text::_('COM_KUNENA_SHOW_MONTH'));
		$options[]        = HTMLHelper::_('select.option', 8760, Text::_('COM_KUNENA_SHOW_YEAR'));
		$item->field      = HTMLHelper::_('select.genericlist', $options, 'userlisttime', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->profile->userListtime), 'kuserlisttime'
		);
		$this->settings[] = $item;

		$this->ktemplate = KunenaFactory::getTemplate();
		$social          = $this->ktemplate->params->get('socialshare');

		if ($social != 0)
		{
			$item             = new StdClass;
			$item->name       = 'socialshare';
			$item->label      = Text::_('COM_KUNENA_USER_SOCIALSHARE');
			$options          = array();
			$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
			$options[]        = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));
			$item->field      = HTMLHelper::_('select.genericlist', $options, 'socialshare', 'class="kinputbox form-control" size="1"', 'value',
				'text', $this->escape($this->profile->socialshare), 'ksocialshare'
			);
			$this->settings[] = $item;
		}

		$this->headerText = Text::_('COM_KUNENA_PROFILE_EDIT_SETTINGS_TITLE');
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

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
	}

	/**
	 * Escape text for HTML.
	 *
	 * @param   string $string String to be escaped.
	 *
	 * @return  string
	 * @since Kunena
	 */
	protected function escape($string)
	{
		return htmlentities($string, ENT_COMPAT, 'UTF-8');
	}
}
