<?php
/**
 * @version		$Id: view.html.php 1014 2009-08-17 07:18:07Z louis $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * About view for Kunena backend
 */
class KunenaViewTemplates extends JView
{

	/**
	 * Private method to set the toolbar for this view
	 *
	 * @access private
	 *
	 * @return null
	 **/
	function setToolBar()
	{
		// Set the titlebar text
		JToolBarHelper::title( JText::_( 'Templates' ), 'templates' );

		JToolBarHelper::back(JText::_( 'Home' ) , 'index.php?option=com_kunena');
	}
}