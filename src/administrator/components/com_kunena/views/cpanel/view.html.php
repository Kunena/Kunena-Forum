<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * About view for Kunena cpanel
 *
 * @since  K1.X
 */
class KunenaAdminViewCpanel extends KunenaView
{
	/**
	 * @since Kunena
	 */
	public function displayDefault()
	{
		$help_url = 'https://docs.kunena.org/en/';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_DASHBOARD'), 'dashboard');
		JToolbarHelper::link('https://www.kunena.org/bugs/changelog', Text::_('COM_KUNENA_DASHBOARD_CHANGELOG'));
		JToolbarHelper::link('https://www.kunena.org/forum', Text::_('COM_KUNENA_DASHBOARD_GET_SUPPORT'));

		if (Factory::getUser()->authorise('core.admin', 'com_kunena'))
		{
			JToolbarHelper::spacer();
			JToolbarHelper::preferences('com_kunena');
			JToolbarHelper::spacer();
		}

		$this->display();
	}
}
