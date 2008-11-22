<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

require_once (JPATH_COMPONENT.'/libraries/view.php');

/**
 * The HTML Kunena thread view
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaViewThread extends KunenaView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 */
	function display($tpl = null)
	{
		$state = $this->get('state');

		// assign some variables to the view
		$this->assignRef('config', $state->get('config'));
		$this->assignRef('user', JFactory::getUser());
		$this->assignRef('session', $this->get('Session'));
		$this->assignRef('category', $this->get('Category'));
		$this->assignRef('thread', $this->get('Thread'));

//		//Initial:: determining what kind of view to use... from profile, cookie or default settings.
//		//pseudo: if (no view is set and the cookie_view is not set)
//		if ($view == "" && $settings['current_view'] == "") {
//			//pseudo: if there's no prefered type, use FB's default view otherwise use preferred view from profile
//			//and then set the cookie right
//			$view = $prefview == "" ? $fbConfig['default_view'] : $prefview;
//			setcookie("fboard_settings[current_view]", $view, time() + KUNENA_SECONDS_IN_YEAR, '/');
//		}
//		//pseudo: otherwise if (no view set but cookie isn't empty use view as set in cookie
//		else
//			if ($view == "" && $settings['current_view'] != "") {
//				$view = $settings['current_view'];
//			}

		parent::display($tpl);
	}
}