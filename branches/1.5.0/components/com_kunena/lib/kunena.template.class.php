<?php
/**
* @version $Id: kunena.template.class.php 1082 2008-10-27 06:44:15Z fxstein $
* Kunena Component
* @package Kunena
* @Copyright (C) 2009 Kunena All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

require_once (JPATH_BASE.'/libraries/joomla/template/template.php');

class CKunenaTemplate extends JTemplate
{
// Place holder for future native functionality
// For now we only need to get away from patTemplate that the old FB used.

	public function __construct()
	{
		parent::__construct();

		//set the namespace
		$this->setNamespace( 'kunena' );

		//add module directories
		// $this->addModuleDir('Function',		dirname(__FILE__). DS. 'module'. DS .'function');
		// $this->addModuleDir('Modifier', 	dirname(__FILE__). DS. 'module'. DS .'modifier');

		//set root template directory
		//$this->setRoot( dirname(__FILE__).DS.'tmpl' );

	}

}
?>