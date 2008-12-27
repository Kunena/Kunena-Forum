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
	static $categories;

	// Get the relevant menu items if not loaded.
	if (empty($items))
	{
		// Get all Kunena menu items.
		$menu	= & JSite::getMenu();
		$items	= $menu->getItems('component', 'com_kunena');

		// Build an array of serialized query strings to menu item id mappings.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			// Sort the query string so they are uniform.
			ksort($items[$i]->query);

			// Setup the categories quick lookup array.
			if (!empty($items[$i]->query['cat_id'])) {
				$categories[$items[$i]->query['cat_id']] = $i;
			}
		}
	}

	if (!empty($query['view']))
	{
		switch ($query['view'])
		{
			case 'category':
				unset ($query['view']);

				// Clean up the numeric path id if it exists.
				$segments[] = preg_replace('/^[0-9]+(\:|-)/', '', $query['cat_id']);
				unset ($query['cat_id']);

				break;

			case 'thread':
				unset ($query['view']);

				// Clean up the numeric path id if it exists.
				$segments[] = preg_replace('/^[0-9]+(\:|-)/', '', $query['cat_id']);
				unset ($query['cat_id']);

				$segments[] = $query['thread_id'];
				unset ($query['thread_id']);

				break;

			case 'post':
				unset ($query['view']);
				unset ($query['layout']);
				$segments[] = 'post';
				break;
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


	if (count($segments) < 1) {
		return;
	}

	if ($segments[0] == 'post') {
		$vars['view'] = 'post';
		$vars['layout'] = 'edit';
		return $vars;
	}

	// Handle the forum thread id if present.
	$end = end($segments);
	if (is_numeric($end))
	{
		// Set the thread id from the end of the route segments.
		$vars['thread_id'] = intval($end);
		$vars['view'] = 'thread';

		// Remove the thread id from the route segments.
		array_pop($segments);
	}

	// Clean up the numeric path id if it exists.
	$segments[0] = preg_replace('/^[0-9]+(\:|-)/', '', $segments[0]);
	for($i = 0, $n = count($segments); $i < $n; $i++)
	{
		$segments[$i] = str_replace(':', '-', $segments[$i]);
	}

	// Get the category id from the categories table by path.
	$db = & JFactory::getDBO();
	$db->setQuery(
		'SELECT `id`' .
		' FROM `#__kunena_categories`' .
		' WHERE `path` = '.$db->Quote(implode('/', $segments))
	);
	$categoryId = $db->loadResult();

	// Set the category id if present.
	if ($categoryId) {
		$vars['cat_id'] = intval($categoryId);
		if (empty($vars['view'])) {
			$vars['view'] = 'category';
		}
	}

	return $vars;
}
