<?php
/**
 * @version $Id$
 * KunenaStats Module
 * @package Kunena Stats
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// Include the syndicate functions only once
require_once (dirname ( __FILE__ ) . DS . 'helper.php');

if (modKStatisticsHelper::getKunenaConfigClass ()) {
	$kconfig = modKStatisticsHelper::getKunenaConfigClass ();
}

if (modKStatisticsHelper::getKunenaLinkClass ()) {
	$klink = modKStatisticsHelper::getKunenaLinkClass ();
}

$model = modKStatisticsHelper::getModel ();

$stats = modKStatisticsHelper::getDatas ( $params );
require (JModuleHelper::getLayoutPath ( 'mod_kunenastats' ));
