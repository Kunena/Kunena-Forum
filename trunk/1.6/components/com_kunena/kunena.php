<?php
/**
* @version $Id: kunena.php 938 2009-07-23 23:39:48Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

// If we are doing things the new way use the MVC.
if (false)
{
	// Do the New MVC Way.
}
// Load the legacy entry point.
else {
	require (dirname(__FILE__)).'/legacy.kunena.php';
}
