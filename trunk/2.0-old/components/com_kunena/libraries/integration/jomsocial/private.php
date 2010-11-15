<?php
/**
* @version $Id:  $
* Kunena Component - JomSocial PMS Integration
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('integration.private');

class KPrivateMessagesJomSocial extends KPrivateMessages
{
	protected function __construct() 
	{
		kimport('integration.jomsocial.integration');
		KIntegrationJomSocial::init(); 
		if (!KIntegrationJomSocial::usePrivateMessagesIntegration()) 
		{
			return null;
		}
	}
	
	public function &getInstance() 
	{
		if (!self::$instance) {
			self::$instance = new KPrivateMessagesJomSocial();
		}
		return self::$instance;
	}	
	
	public function getOnClick($userid)
	{
		return ' onclick="'.CMessaging::getPopup($userid).'"';
	}
	
	public function getURL($userid)
	{
		return "javascript:void(0)";
	}
}
