<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2010 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

// no direct access
defined('_JEXEC') or die();

jimport( 'joomla.plugin.plugin' );

if(!class_exists('plgJFirePHP'))
{
	class plgJFirePHP extends JPlugin
	{

		/**
		 * Constructor
		 *
		 * For php4 compatability we must not use the __constructor as a constructor for plugins
		 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
		 * This causes problems with cross-referencing necessary for the observer design pattern.
		 *
		 * @access	protected
		 * @param	object	$subject The object to observe
		 * @param 	array   $config  An array that holds the plugin configuration
		 * @since	1.0
		 */
		function plgJFirePHP(& $subject, $config)
		{
			$this->_db = JFactory::getDBO();
			parent :: __construct($subject, $config);
		}

		/**
		 * onAfterInitialise handler
		 *
		 * Register FirePHP libraries
		 *
		 * @access	public
		 * @return null
		 */

		function onAfterInitialise()
		{
			// Sample: JHTML::addIncludePath(JPATH_PLUGINS.DS.'system'.DS.'mootools12');
		}

	}
}

