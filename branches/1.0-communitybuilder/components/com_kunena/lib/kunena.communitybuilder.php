<?php
/**
* @version $Id: kunena.user.class.php 570 2009-03-31 10:04:30Z mahagr $
* Kunena Component - Community Builder compability
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

/**
 * CB framework
 * @global CBframework $_CB_framework
 */
global $_CB_framework, $_CB_database, $ueConfig, $mainframe;

if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
        if ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) {
                echo 'CB not installed';
                return;
        }
        include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );
} else {
        if ( ! file_exists( $mainframe->getCfg( 'absolute_path' ) . '/administrator/components/com_comprofiler/plugin.foundation.php' ) ) {
                echo 'CB not installed';
                return;
        }
        include_once( $mainframe->getCfg( 'absolute_path' ) . '/administrator/components/com_comprofiler/plugin.foundation.php' );
}
cbimport( 'cb.database' );
cbimport( 'cb.tables' );
cbimport( 'language.front' );
