<?php
/**
 * @version		$Id: view.html.php 1087 2009-09-25 21:24:07Z mahagr $
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
class KunenaViewStatistics extends KView
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
		
		//$this->assignRef ( 'items', $this->get ( 'Items' ) );
		//$this->assignRef ( 'pagination', $this->get ( 'Pagination' ) );
		
		$this->assignRef ( 'summary', $this->get ( 'Summary' ) );
		
		jimport( 'joomla.application.menu' );
		$menu = JSite::getMenu();
		$menuitem = $menu->getActive();
		
		$this->assign ( 'title', ($params->get('show_page_title') && $menuitem->query['view'] == 'recent' && $menuitem->query['type'] == $this->state->type ? 
			$params->get('page_title') : 'Forum Statistics'));
		
		parent::display ( $tpl );
	}
}