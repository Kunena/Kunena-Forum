<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

/**
 * Function to build a Kunena URL route.
 *
 * @param	array	$query	The array of query string values for which to build a route.
 * @return	array	The URL route with segments represented as an array.
 * @version	1.0
 */
function KunenaBuildRoute(&$query)
{
	// Initialize variables.
	$segments = array();
	static $items;
	static $hashes;

	// Get the relevant menu items if not loaded.
	if (empty($items))
	{
		$menu	= &JSite::getMenu();
		$items	= $menu->getItems('component', 'com_kunena');
		$count	= count($items);

		// Build an array of serialized query strings to menu item id mappings.
		for ($i = 0; $i < $count; $i++)
		{
			// Sort the query string so they are uniform.
			ksort($items[$i]->query);

			// Serialize the query string and store for lookup.
			$hashes[serialize($items[$i]->query)] = (int)$items[$i]->id;
		}
	}

	return $segments;
}

/**
 * Function to parse a Kunena URL route.
 *
 * @param	array	$segments	The URL route with segments represented as an array.
 * @return	array	The array of variables to set in the request.
 * @version	1.0
 */
function KunenaParseRoute($segments)
{
	// Initialize variables.
	$vars = array();

	return $vars;
}
