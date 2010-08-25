<?php
/**
 * @version $Id$
 * Kunenalogin Module
 * @package Kunena login
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

$template = $this->params->get ( 'template', '0' );
switch ($template) {
	//case "2" :
		//require_once (dirname(__FILE__).DS.'flat.php');
		//break;
	//case "1" :
		//require_once (dirname(__FILE__).DS.'horizontal.php');
		//break;
	default :
		require_once (dirname ( __FILE__ ) . DS . 'vertical.php');
}
