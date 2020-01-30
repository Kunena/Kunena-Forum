<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\User\Edit;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use StdClass;
use function defined;

/**
 * Class ComponentUserControllerEditSettingsDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentUserControllerEditSettingsDisplay extends ComponentUserControllerEditDisplay
{
	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $settings;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'User/Edit/Settings';

	/**
	 * Prepare Kunena user settings.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 */
	protected function before()
	{
		parent::before();

		$item             = new StdClass;
		$item->name       = 'messageordering';
		$item->label      = Text::_('COM_KUNENA_USER_ORDER');
		$options          = [];
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
		$options          = [];
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
		$options          = [];
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
		$options[]        = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));
		$item->field      = HTMLHelper::_('select.genericlist', $options, 'showonline', 'class="kinputbox form-control" size="1"', 'value',
			'text', $this->escape($this->profile->showOnline), 'kshowonline'
		);
		$this->settings[] = $item;

		$item             = new StdClass;
		$item->name       = 'cansubscribe';
		$item->label      = Text::_('COM_KUNENA_USER_CANSUBSCRIBE');
		$options          = [];
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
		$options          = [];
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
			$options          = [];
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
	 * Escape text for HTML.
	 *
	 * @param   string  $string  String to be escaped.
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected function escape($string)
	{
		return htmlentities($string, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareDocument()
	{
		$menu_item = $this->app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
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
}
