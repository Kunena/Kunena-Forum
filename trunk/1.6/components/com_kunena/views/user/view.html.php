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
 * The HTML Kunena user profile view.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaViewUser extends KView
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
		$this->assign ( 'title', ($params->get('show_page_title') ? 
			$params->get('page_title') : 'User Profile'));
		
		parent::display ( $tpl );
	}
}