<?php
/**
* @version $Id:  $
* Kunena Component - JomSocial Profile Integration
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('integration.login');

class KLoginJomSocial extends KLogin 
{
	protected function __construct() 
	{
		kimport('integration.jomsocial.integration');
		KIntegrationJomSocial::init(); 
		if (!KIntegrationJomSocial::useLoginIntegration())
		{ 
			return null;
		}
	}
	
	public function &getInstance() 
	{
		if (!self::$instance) {
			self::$instance = new KLoginJomsocial();
		}
		return self::$instance;
	}

	/*
	public function getLoginURL() 
	{
		return parent::getLoginURL();
	}

	public function getLogoutURL() 
	{
		return parent::getLogoutURL();
	}

	public function getRegisterURL() 
	{
		return parent::getRegisterURL();
	}

	public function getLostPasswordURL() 
	{
		return parent::getLostPasswordURL();
	}
	*/
	
}
