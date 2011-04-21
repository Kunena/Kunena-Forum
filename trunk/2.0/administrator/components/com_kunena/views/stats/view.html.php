<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * About view for Kunena stats backend
 */
class KunenaAdminViewStats extends KunenaView {
	function display($tpl = null) {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );

		$this->config = KunenaFactory::getConfig ();
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_KUNENA_STAT_FORUMSTATS') . ' - ' .      $this->config->board_title);

		$kunena_stats = KunenaForumStatistics::getInstance ( );
		$kunena_stats->loadAll();
		$this->assign($kunena_stats);

		parent::display ();
	}
}