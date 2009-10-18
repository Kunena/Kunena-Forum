<?php
/**
* @version $Id: kunena.integration.class.php 653 2009-04-27 02:28:22Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

// Abstract base class for various 3rd party integration classes
abstract class CKunenaIntegration
{
	private static $loaded = false;
	private static $enabled = false;
	private static $error = 0;
	private static $errormsg = '';

	function __construct() { }

	// Public helper function to access our private vars
	public function IsLoaded()
	{
		return self::$loaded;
	}

	public function IsEnabled()
	{
		return self::$enabled;
	}

	public function GetError()
	{
		return self::$error;
	}

	public function GetErrorMsg()
	{
		return self::$errormsg;
	}

	// Identical to isEnabled()
	public function status()
	{
		return self::$enabled;
	}

	// Protected function to manipulate our private vars in derived classes
	protected function enable($enable = true)
	{
		self::$enabled = $enable;
	}

	protected function loaded($loaded = true)
	{
		self::$loaded = $loaded;
	}

	protected function setError($error = 0)
	{
		self::$error = $error;
	}

	protected function setErrorMsg($errormsg = '')
	{
		self::$errormsg = $errormsg;
	}

	// abstract function to be overriden in derived class
	abstract public function start();

	// Various Currently supported integration options
	// Derived class overrides those that are available
	protected function detectIntegration()
	{
		return true;
	}

	function useAvatarIntegration() {
		return false;
	}

	function useProfileIntegration() {
		return false;
	}

	function usePMSIntegration() {
		return false;
	}

}
?>