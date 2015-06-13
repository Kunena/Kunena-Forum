<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * About view for Kunena stats backend
 */
class KunenaAdminViewStats extends KunenaView
{
	/**
	 * @param null $tpl
	 */
	function displayDefault($tpl = null)
	{
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'kunena.png');

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_KUNENA_STAT_FORUMSTATS') . ' - ' . $this->config->board_title);

		$kunena_stats = KunenaForumStatistics::getInstance();
		$kunena_stats->loadAll(true);
		$this->kunena_stats = $kunena_stats;

		$this->display();
	}
}
