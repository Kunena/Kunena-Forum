<?php
/**
 * @version $Id$
 * KunenaLatest Module
 * @package Kunena Latest
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined ( '_JEXEC' ) or die ( '' );

// Detect and load Kunena 1.6+
$kunena_api = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'api.php';
if (! is_file ( $kunena_api ))
	return;
require_once ($kunena_api);
require_once (KUNENA_PATH . DS . 'class.kunena.php');
require_once (KUNENA_PATH_LIB . DS . 'kunena.link.class.php');

// Include the syndicate functions only once
require_once (dirname ( __FILE__ ) . DS . 'helper.php');

$params = (object) $params;
$klistpost = modKunenaLatestHelper::getKunenaLatestList ( $params );

require (JModuleHelper::getLayoutPath ( 'mod_kunenalatest' ));
