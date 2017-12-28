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
 * About view for Kunena stats backend
 *
 * @since  K1.X
 */
class KunenaAdminViewStats extends KunenaView
{
	/**
	 *
	 * @internal param null $tpl
	 */
	function displayDefault()
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
