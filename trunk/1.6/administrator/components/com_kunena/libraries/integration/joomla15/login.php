<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaLoginJoomla15 extends KunenaLogin
{
	public function __construct() {
		if (is_dir(JPATH_LIBRARIES.'/joomla/access'))
			return;
		$this->priority = 25;
	}

	public function getLoginFormFields() {
		return array (
			'form'=>'login',
			'field_username'=>'username',
			'field_password'=>'passwd',
			'field_remember'=>'remember',
			'field_return'=>'return',
			'option'=>'com_user',
			'task'=>'login'
		);
	}

	public function getLogoutFormFields() {
		return array (
			'form'=>'login',
			'field_return'=>'return',
			'option'=>'com_user',
			'task'=>'logout'
		);
	}

	public function getLoginURL()
	{
		return JRoute::_('index.php?option=com_user&view=login');
	}

	public function getLogoutURL()
	{
		return JRoute::_('index.php?option=com_user&view=login');
	}

	public function getRegistrationURL()
	{
		$usersConfig = JComponentHelper::getParams ( 'com_users' );
		if ($usersConfig->get ( 'allowUserRegistration' ))
			return JRoute::_('index.php?option=com_user&view=register');
	}

	public function getResetURL()
	{
		return JRoute::_('index.php?option=com_user&view=reset');
	}

	public function getRemindURL()
	{
		return JRoute::_('index.php?option=com_user&view=remind');
	}
}
