<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

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
		JToolbarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_DASHBOARD'), 'dashboard');
		JToolbarHelper::link('https://www.kunena.org/bugs/changelog', JText::_('Changelog'));
		JToolbarHelper::link('https://www.kunena.org/forum', JText::_('Get Support'));

		if (Factory::getUser()->authorise('core.admin', 'com_kunena'))
		{
			JToolbarHelper::spacer();
			JToolbarHelper::preferences('com_kunena');
			JToolbarHelper::spacer();
		}

		$this->display();
	}
}
