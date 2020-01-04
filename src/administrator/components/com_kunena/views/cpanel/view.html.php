<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * About view for Kunena cpanel
 *
 * @since   Kunena 1.X
 */
class KunenaAdminViewCpanel extends KunenaView
{
	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayDefault()
	{
		$help_url = 'https://docs.kunena.org/en/';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_DASHBOARD'), 'dashboard');
		ToolbarHelper::link('https://www.kunena.org/bugs/changelog', Text::_('COM_KUNENA_DASHBOARD_CHANGELOG'));
		ToolbarHelper::link('https://www.kunena.org/forum', Text::_('COM_KUNENA_DASHBOARD_GET_SUPPORT'));

		if (Factory::getApplication()->getIdentity()->authorise('core.admin', 'com_kunena'))
		{
			ToolbarHelper::spacer();
			ToolbarHelper::preferences('com_kunena');
			ToolbarHelper::spacer();
		}

		$this->display();
	}
}
