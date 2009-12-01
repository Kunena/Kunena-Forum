<?php
/**
 * @version		$Id: api.php 1014 2009-08-17 07:18:07Z louis $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined( '_JEXEC' ) or die('Restricted access');
if (defined('KUNENA_LOADED')) return;

// Component name amd database prefix
define ('KUNENA_COMPONENT_NAME', basename(dirname(__FILE__)));
define ('KUNENA_DATABASE_PREFIX', '#__kunena');

// Component location
define ('KUNENA_COMPONENT_LOCATION', basename(dirname(dirname(__FILE__))));

// Component paths
define ('KPATH_COMPONENT_RELATIVE', KUNENA_COMPONENT_LOCATION .DS. KUNENA_COMPONENT_NAME);
define ('KPATH_SITE', JPATH_ROOT .DS. KPATH_COMPONENT_RELATIVE);
define ('KPATH_ADMIN', JPATH_ADMINISTRATOR .DS. KPATH_COMPONENT_RELATIVE);
define ('KPATH_COMPONENT_MEDIA', KPATH_SITE .DS. 'media');
define ('KPATH_ADMIN_MEDIA', JPATH_ADMINISTRATOR .DS. KPATH_COMPONENT_RELATIVE .DS. 'media');
define ('KPATH_MEDIA', JPATH_ROOT .DS. 'media' .DS. KUNENA_COMPONENT_NAME);

// URLs
define('KURL_SITE', 'index.php?option=com_kunena');
define('KURL_COMPONENT_MEDIA', JURI::Base().KUNENA_COMPONENT_LOCATION.'/'.KUNENA_COMPONENT_NAME.'/media/');
define('KURL_MEDIA', JURI::Base().'media/'.KUNENA_COMPONENT_NAME.'/');

// Version information
define ('KUNENA_VERSION', ('@kunenaversion@' == '@'.'kunenaversion'.'@') ? '1.6.0-SVN' : strtoupper('@kunenaversion@'));
define ('KUNENA_VERSION_DATE', ('@kunenaversiondate@' == '@'.'kunenaversiondate'.'@') ? date('Y-m-d', filemtime(KPATH_SITE .DS. 'CHANGELOG.php')) : '@kunenaversiondate@');
define ('KUNENA_VERSION_NAME', ('@kunenaversionname@' == '@'.'kunenaversionname'.'@') ? 'SVN Revision' : '@kunenaversionname@');
define ('KUNENA_VERSION_BUILD', ('@kunenaversionbuild@' == '@'.'kunenaversionbuild'.'@') ? '0' : '@kunenaversionbuild@');

/**
 * Kunena Libraries intelligent file importer.
 *
 * @param	string	A dot syntax path.
 * @param	string	A dot syntax path.
 * @return	boolean	True on success
 * @since	1.0
 */
function kimport($path, $location='libraries')
{
	if ($location == 'admin') return JLoader::import($path, KPATH_ADMIN);
	return JLoader::import($path, KPATH_SITE .DS. $location);
}

// Import Kunena Factory
kimport('factory');

// Kunena has been initialized
define ('KUNENA_LOADED', 1);
