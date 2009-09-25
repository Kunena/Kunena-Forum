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

class KLogin 
{
	protected static $instance = null;

	protected function __construct() {}

	public function &getInstance() 
	{
		if (!self::$instance) {
			self::$instance =& new KLogin();
		}
		return self::$instance;
	}

	public function getLoginURL() 
	{
		return JRoute::_('index.php?option=com_user&view=login');
	}

	public function getLogoutURL() 
	{
		return JRoute::_('index.php?option=com_user&view=login');
	}

	public function getRegisterURL() 
	{
		return JRoute::_('index.php?option=com_user&view=register');
	}

	public function getLostPasswordURL() 
	{
		return JRoute::_('index.php?option=com_user&view=reset');
	}
	
}
