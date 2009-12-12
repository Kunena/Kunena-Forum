<?php
/**
* @version $Id:  $
* Kunena Component - UddeIM PMS Integration
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('integration.private');

class KPrivateMessagesUddeIM extends KPrivateMessages 
{
	protected function __construct() 
	{
		kimport('integration.uddeim.integration');
		KIntegrationUddeIM::init(); 
		if (!KIntegrationUddeIM::usePrivateMessagesIntegration()) 
		{
			return null;
		}
	}
	
	public function &getInstance() 
	{
		if (!self::$instance) {
			self::$instance = new KPrivateMessagesUddeIM();
		}
		return self::$instance;
	}
	
	function getURL($userid)
	{
		return JRoute::_('index.php?option=com_uddeim&amp;task=new&recip=' . $userid);
	}
}
