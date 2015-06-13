<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * About view for Kunena cpanel
 */
class KunenaAdminViewCpanel extends KunenaView
{
	/**
	 *
	 */
	function displayDefault()
	{
		$help_url  = 'http://www.kunena.org/docs/Category:Installation';
		JToolBarHelper::help( 'COM_KUNENA', false, $help_url );
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_DASHBOARD'), 'dashboard');

		if (JFactory::getUser()->authorise('core.admin', 'com_kunena'))
		{
			JToolBarHelper::spacer();
			JToolBarHelper::preferences('com_kunena');
			JToolBarHelper::spacer();
		}

		$this->display();
	}
}
