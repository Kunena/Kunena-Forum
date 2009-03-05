<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2009 Kunena All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

require_once (JPATH_BASE.'/libraries/joomla/template/template.php');

class CKunenaTemplate extends patTemplate
{
// Place holder for future native functionality
// For now we only need to get away from patTemplate that the old FB used.

	public function __construct($templatepath)
	{
		parent::__construct();

		//set the namespace
		$this->setNamespace( 'kunena' );

		//set root template directory
		$this->setRoot( $templatepath );
	}

}
?>