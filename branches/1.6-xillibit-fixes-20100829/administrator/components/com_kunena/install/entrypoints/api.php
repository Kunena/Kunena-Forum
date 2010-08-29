<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

defined( '_JEXEC' ) or die();
if (defined ( 'KUNENA_LOADED' ))
	return;

// Component name amd database prefix
define ( 'KUNENA_COMPONENT_NAME', basename ( dirname ( __FILE__ ) ) );
define ( 'KUNENA_NAME', substr ( KUNENA_COMPONENT_NAME, 4 ) );

// Component location
define ( 'KUNENA_COMPONENT_LOCATION', basename ( dirname ( dirname ( __FILE__ ) ) ) );

// Component paths
define ( 'KPATH_COMPONENT_RELATIVE', KUNENA_COMPONENT_LOCATION . DS . KUNENA_COMPONENT_NAME );
define ( 'KPATH_SITE', JPATH_ROOT . DS . KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_ADMIN', JPATH_ADMINISTRATOR . DS . KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_MEDIA', JPATH_ROOT . DS . 'media' . DS . KUNENA_NAME );

/**
 * Intelligent library importer.
 *
 * @param	string	A dot syntax path.
 * @return	boolean	True on success
 * @since	1.6
 */
function kimport($path)
{
	//return JLoader::import($path, KPATH_ADMIN.'/libraries');
	require_once(KPATH_ADMIN.'/libraries/'.str_replace( '.', '/', $path).'.php');
}

// Version information
class Kunena {
	public static function isSvn() {
		if ('@kunenaversion@' == '@' . 'kunenaversion' . '@') {
			return true;
		}
		return false;
	}

	public static function version() {
		return '@kunenaversion@';
	}

	public static function versionDate() {
		return '@kunenaversiondate@';
	}

	public static function versionName() {
		return '@kunenaversionname@';
	}

	public static function versionBuild() {
		return '@kunenaversionbuild@';
	}

	public static function enabled() {
		return false;
	}
}

// Kunena has been initialized
define ( 'KUNENA_LOADED', 1 );
