<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

// Define the Kunena Libraries path constant.
if (!defined('KPATH_LIBRARIES')) {
	define('KPATH_LIBRARIES', realpath(dirname(__FILE__)));
}

/**
 * Kunena Libraries intelligent file importer.
 *
 * @param	string	A dot syntax path.
 * @return	boolean	True on success
 * @since	1.0
 */
function kimport($path)
{
	return JLoader::import($path, KPATH_LIBRARIES);
}
