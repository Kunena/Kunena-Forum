<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

kimport('application.view');

/**
 * The HTML Kunena recent view.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaViewRecent extends KView
{
	/**
	 * Display the view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function display($tpl = null) {
		$this->assignRef ( 'state', $this->get ( 'State' ) );

		// Create shortcut to parameters.
		$params = $this->state->get('params');
		
		$this->assign ( 'total', $this->get ( 'Total' ) );
		$this->assignRef ( 'threads', $this->get ( 'Items' ) );
		$this->assignRef ( 'pagination', $this->get ( 'Pagination' ) );
		
		$this->assignRef ( 'announcements', $this->get ( 'Announcement' ) );
		$this->assignRef ( 'statistics', $this->get ( 'Statistics' ) );
		
		if ($params->get('filter_time_override'))
		{
			$this->assign ( 'filter_time_options', array (
//				'new' => 'Only new messages',
				'session' => 'Since last visit',
				'all' => 'All messages',
				4 => '4 ' . JText::_ ( 'K_HOURS' ), 
				8 => '8 ' . JText::_ ( 'K_HOURS' ), 
				12 => '12 ' . JText::_ ( 'K_HOURS' ), 
				24 => '24 ' . JText::_ ( 'K_HOURS' ), 
				48 => '48 ' . JText::_ ( 'K_HOURS' ), 
				168 => JText::_ ( 'K_WEEK' ), 
				720 => JText::_ ( 'K_MONTH' ), 
				8760 => JText::_ ( 'K_YEAR' ) ) );
			$this->assign ( 'filter_time', $this->state->get('filter.time') );
		}
		
		jimport( 'joomla.application.menu' );
		$menu = JSite::getMenu();
		$menuitem = $menu->getActive();
		
		$this->assign ( 'title', ($params->get('show_page_title') && $menuitem->query['view'] == 'recent' && $menuitem->query['type'] == $this->state->type ? 
			$params->get('page_title') : 'Recent Discussions'));
		
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		if ($params->get('filter_time_override')) $pathway->addItem($this->escape($this->filter_time_options[$this->filter_time]));
		
		parent::display ( $tpl );
	}
}