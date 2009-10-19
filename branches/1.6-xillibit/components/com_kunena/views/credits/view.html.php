<?php
/**
 * @version		$Id: view.html.php 1068 2009-09-15 22:43:18Z mahagr $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

kimport('application.view');

/**
 * The HTML Kunena credits view.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaViewCredits extends KView
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
		
		$this->assignRef ( 'credits', $this->get ( 'VersionItems' ) );
		
		jimport( 'joomla.application.menu' );
		$menu = JSite::getMenu();
		$menuitem = $menu->getActive();
		
		$this->assign ( 'title', ($params->get('show_page_title') && $menuitem->query['view'] == 'credits' ? 
			$params->get('page_title') : 'Team Credits'));
		
		parent::display ( $tpl );
	}
}
?>