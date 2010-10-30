<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ();
if (defined ( 'KUNENA_LOADED' ))
	return;

// Component name amd database prefix
define ( 'KUNENA_COMPONENT_NAME', basename ( dirname ( __FILE__ ) ) );
define ( 'KUNENA_NAME', substr ( KUNENA_COMPONENT_NAME, 4 ) );

// Component location
define ( 'KUNENA_COMPONENT_LOCATION', basename ( dirname ( dirname ( __FILE__ ) ) ) );

// Component paths
define ( 'KPATH_COMPONENT_RELATIVE', KUNENA_COMPONENT_LOCATION . '/' . KUNENA_COMPONENT_NAME );
define ( 'KPATH_SITE', JPATH_ROOT . DS . KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_ADMIN', JPATH_ADMINISTRATOR . DS . KPATH_COMPONENT_RELATIVE );
define ( 'KPATH_MEDIA', JPATH_ROOT . DS . 'media' . DS . KUNENA_NAME );

// URLs
define ( 'KURL_COMPONENT', 'index.php?option=' . KUNENA_COMPONENT_NAME );
define ( 'KURL_SITE', JURI::Root () . KPATH_COMPONENT_RELATIVE . '/' );
define ( 'KURL_MEDIA', JURI::Root () . 'media/' . KUNENA_NAME . '/' );

/**
 * Intelligent library importer.
 *
 * @param	string	A dot syntax path.
 * @return	boolean	True on success
 * @since	1.6
 */
function kimport($path)
{
	static $paths = array();
	if (isset($paths[$path])) return true;

	$res = false;
	if (substr($path, 0, 7) == 'kunena.') {
		$file = KPATH_ADMIN . '/libraries/' . str_replace( '.', '/', substr($path, 7));
		if (is_dir($file)) {
			$parts = explode( '/', $file );
			$file .= '/'.array_pop( $parts );
		}
		$file .= '.php';
		if (file_exists($file)) {
			$class = str_replace( '.', '', $path);
			JLoader::register($class, $file);
			$paths[$path] = 1;
			$res = true;
		}
	} else {
		$res = JLoader::import($path, dirname(__FILE__).'/libraries');
	}
	return $res;
}

// Give access to all Kunena tables
jimport('joomla.database.table');
JTable::addIncludePath(KPATH_ADMIN.'/libraries/tables');
// Give access to all JHTML functions
jimport('joomla.html.html');
JHTML::addIncludePath(KPATH_ADMIN.'/libraries/html/html');

kimport('kunena.forum');
kimport('kunena.factory');
kimport('kunena.route');

// Legacy support
$file = JPATH_ROOT . '/components/com_kunena/lib/kunena.defines.php';
if (is_file($file))
	require_once ($file);

// Kunena has been initialized
define ( 'KUNENA_LOADED', 1 );
