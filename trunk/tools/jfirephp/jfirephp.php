<?php
/**
 * @version		$Id: mykunenatoolbar.php 1297 2009-12-16 10:51:38Z fxstein $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');

if(!class_exists('plgCommunityMyKunenaToolbar'))
{
	class plgCommunityMyKunenaToolbar extends CApplications
	{
		var $name 		= "My Kunena Toolbar";
		var $_name		= 'mykunenatoolbar';
	
	    function plgCommunityMyKunenaToolbar(& $subject, $config)
	    {
			parent::__construct($subject, $config);
	    }
		
		function onSystemStart()
		{
			//Load Language file.
			JPlugin::loadLanguage( 'plg_mykunenatoolbar', JPATH_ADMINISTRATOR );
			
			if( !file_exists( JPATH_ROOT . DS . 'components' . DS . 'com_kunena' . DS . 'class.kunena.php' ) )
			{
				trigger_error (JText::_('PLG_MYKUNENA TOOLBAR ERROR'),E_USER_WARNING);
				return;
			}
			
			if( !class_exists('CFactory'))
			{
				require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
			}
			
			//initialize the toolbar object	
			$toolbar = CFactory::getToolbar();		
			
			$db = &JFactory::getDBO();
			// Get Kunena item id
			if (!defined("KUNENA_COMPONENT_ITEMID")) 
			{
        		$db->setQuery("SELECT id FROM #__menu WHERE link='index.php?option=com_kunena' AND published='1'");
        		$kunenaItemId = $db->loadResult();

        		if ($kunenaItemId < 1) {
            		$kunenaItemId = 0;
            	}
			}			

			//adding new 'tab' 'Forum Settings' to JomSocial toolbar
			$toolbar->addGroup('MYKUNENA', JText::_('PLG_MYKUNENA TOOLBAR FORUM'), JRoute::_('index.php?option=com_kunena&amp;Itemid='.$kunenaItemId.'&amp;func=myprofile'));
			$toolbar->addItem('MYKUNENA', 'MYKUNENA_PROFILE_INFO', JText::_('PLG_MYKUNENA TOOLBAR MY PROFILE INFO'), JRoute::_('index.php?option=com_kunena&amp;Itemid='.$kunenaItemId.'&amp;func=myprofile&amp;do=profileinfo'));		 
			$toolbar->addItem('MYKUNENA', 'MYKUNENA_MY_POSTS', JText::_('PLG_MYKUNENA TOOLBAR MY POSTS'), JRoute::_('index.php?option=com_kunena&amp;Itemid='.$kunenaItemId.'&amp;func=myprofile&amp;do=showmsg'));		 
			$toolbar->addItem('MYKUNENA', 'MYKUNENA_MY_SUBSCRIBES', JText::_('PLG_MYKUNENA TOOLBAR MY SUBSCRIBES'), JRoute::_('index.php?option=com_kunena&amp;Itemid='.$kunenaItemId.'&amp;func=myprofile&amp;do=showsub'));		 
			$toolbar->addItem('MYKUNENA', 'MYKUNENA_MY_FAVORITES', JText::_('PLG_MYKUNENA TOOLBAR MY FAVORITES'), JRoute::_('index.php?option=com_kunena&amp;Itemid='.$kunenaItemId.'&amp;func=myprofile&amp;do=showfav'));		 
			$toolbar->addItem('MYKUNENA', 'MYKUNENA_LOOK_AND_LAYOUT', JText::_('PLG_MYKUNENA TOOLBAR LOOK AND LAYOUT'), JRoute::_('index.php?option=com_kunena&amp;Itemid='.$kunenaItemId.'&amp;func=myprofile&amp;do=showset'));		 
		}	
	}	
}

