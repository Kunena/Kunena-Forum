<?php
/**
 * @version $Id: $
 * Kunena Component - Base classes for integration
 * @package Kunena
 *
 * @Copyright (C) 2009 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

class KPrivateMessages 
{
	protected static $instance = null;

	protected function __construct() {}
	
	public function &getInstance() 
	{
		if (!self::$instance) {
			self::$instance = new KPrivateMessages();
		}
		return self::$instance;
	}	
	
	function getOnClick($userid)
	{
		return '';
	}
	
	function getURL($userid)
	{
		return '';
	}
	
	function showSendPMIcon($userid) 
	{
		$my = &JFactory::getUser();
		
		// Don't send messages from/to anonymous and to yourself
		if ($my->id == 0 || $userid == 0 || $userid == $my->id) return '';
		
		$url = $this->getURL($userid);
		$onclick = $this->getOnClick($userid);
		// No PMS system enabled or PM not allowed
		if (empty($url)) return '';
		
		// We should offer the user a PM link
		return '<a href="' . $url . '"' .$onclick. ' title="'.JText::_('K_PM_SEND_TITLE').'">' .$this->_getIcon() . '</a>';
	}
	
	protected function _getIcon() 
	{
		global $kunenaIcons;
		
		if ($kunenaIcons['pms']) {
			$html = '<img src="' . KUNENA_URLICONSPATH . $kunenaIcons['pms'] . '" alt="' .JText::_('K_PM_ICONTEXT'). '" />';
		}
		else {
			$html = JText::_('K_PM_ICONTEXT');
		}

		return $html;
	}
}
