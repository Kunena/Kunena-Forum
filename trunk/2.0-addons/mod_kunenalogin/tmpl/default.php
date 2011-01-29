<?php
/**
 * @version $Id: default.php 4047 2010-12-21 07:59:21Z severdia $
 * Kunena Login Module
 * @package Kunena login
 *
 * @Copyright (C) 2010-2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

$template = $this->params->get ( 'template', '0' );
switch ($template) {
	case "horizontal" :
		require_once (dirname(__FILE__) . DS . 'horizontal.php');
		break;
	default :
		require_once (dirname ( __FILE__ ) . DS . 'vertical.php');
}