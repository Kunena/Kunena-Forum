<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Views
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * About view for Kunena cpanel
 *
 * @since  K1.X
 */
class KunenaAdminViewCpanel extends KunenaView
{
	/**
	 *
	 */
	function displayDefault()
	{
		$help_url  = 'https://docs.kunena.org/en/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_DASHBOARD'), 'dashboard');
		JToolBarHelper::link('https://www.kunena.org/bugs/changelog', JText::_('Changelog'));
		JToolBarHelper::link('https://www.kunena.org/forum', JText::_('Get Support'));

		if (JFactory::getUser()->authorise('core.admin', 'com_kunena'))
		{
			JToolBarHelper::spacer();
			JToolBarHelper::preferences('com_kunena');
			JToolBarHelper::spacer();
		}

		$this->display();
	}
}
