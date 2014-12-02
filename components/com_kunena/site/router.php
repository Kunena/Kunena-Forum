<?php
/**
 * Kunena Component
 * @package Kunena.Site
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.error.profiler');

/**
 * Build SEF URL
 *
 * All SEF URLs are formatted like this:
 *
 * http://site.com/menuitem/1-category-name/10-subject/[view]/[layout]/[param1]-value1/[param2]-value2?param3=value3&param4=value4
 *
 * - If catid exists, category will always be in the first segment
 * - If there is no catid, second segment for message will not be used (param-value: id-10)
 * - [view] and [layout] are the only parameters without value
 * - all other segments (task, id, userid, page, sel) are using param-value format
 *
 * NOTE! Only major variables are using SEF segments
 *
 * @param $query
 * @return array Segments
 */
function KunenaBuildRoute(&$query) {
	$segments = array ();

	// If Kunena Forum isn't installed or SEF is not enabled, do nothing
	if (!class_exists('KunenaForum') || !KunenaForum::isCompatible('3.0') || !KunenaForum::installed() || !KunenaRoute::$config->sef) {
		return $segments;
	}

	KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__FUNCTION__.'()') : null;

	// Get menu item
	$menuitem = null;
	if (isset ( $query ['Itemid'] )) {
		static $menuitems = array();
		$Itemid = $query ['Itemid'] = (int) $query ['Itemid'];
		if (!isset($menuitems[$Itemid])) {
			$menuitems[$Itemid] = JFactory::getApplication()->getMenu ()->getItem ( $Itemid );
			if (!$menuitems[$Itemid]) {
				// Itemid doesn't exist or is invalid
				unset ($query ['Itemid']);
			}
		}
		$menuitem = $menuitems[$Itemid];
	}

	// Safety check: we need view in order to create SEF URLs
	if (!isset($menuitem->query['view']) && empty($query ['view'])) {
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__FUNCTION__.'()') : null;
		return $segments;
	}

	// Get view for later use (query wins menu item)
	$view = isset ( $query ['view'] ) ? (string) preg_replace( '/[^a-z]/', '', $query ['view'] ) : $menuitem->query ['view'];

	// Get default values for URI variables
	if (isset(KunenaRoute::$views[$view])) {
		$defaults = KunenaRoute::$views[$view];
	}
	// Check all URI variables and remove those which aren't needed
	foreach ( $query as $var => $value ) {
		if (isset ( $defaults [$var] ) && !isset ( $menuitem->query [$var] ) && $value == $defaults [$var] ) {
			// Remove URI variable which has default value
			unset ( $query [$var] );
		} elseif ( isset ( $menuitem->query [$var] ) && $value == $menuitem->query [$var] && $var != 'Itemid' && $var != 'option' ) {
			// Remove URI variable which has the same value as menu item
			unset ( $query [$var] );
		}
	}

	// We may have catid also in the menu item (it will not be in URI)
	$numeric = !empty ( $menuitem->query ['catid'] );

	// Support URIs like: /forum/12-my_category
	if (!empty ( $query ['catid'] ) && ($view == 'category' || $view == 'topic' || $view == 'home')) {
		// TODO: ensure that we have view=category/topic
		$catid = ( int ) $query ['catid'];
		if ($catid) {
			$numeric = true;

			$alias = KunenaForumCategoryHelper::get($catid)->alias;
			// If category alias is empty, use category id; otherwise use alias
			$segments [] = empty ( $alias ) ? $catid : $alias;
			// This segment fully defines category view so the variable is no longer needed
			if ($view == 'category') {
				unset ( $query ['view'] );
			}
		}
		unset ( $query ['catid'] );
	}

	// Support URIs like: /forum/12-category/123-topic
	if (!empty ( $query ['id'] ) && $numeric) {
		$id = (int) $query ['id'];
		if ($id) {
			$subject = KunenaRoute::stringURLSafe ( KunenaForumTopicHelper::get($id)->subject );
			if (empty ( $subject )) {
				$segments [] = $id;
			} else {
				$segments [] = "{$id}-{$subject}";
			}
			// This segment fully defines topic view so the variable is no longer needed
			if ($view == 'topic') {
				unset ( $query ['view'] );
			}
		}
		unset ( $query ['id'] );
	} else {
		// No id available, do not use numeric variable for mesid
		$numeric = false;
	}

	// View gets added only when we do not use short URI for category/topic
	if (!empty ( $query ['view'] )) {
		// Use filtered value
		$segments [] = $view;
	}

	// Support URIs like: /forum/12-category/123-topic/reply
	if (!empty ( $query ['layout'] )) {
		// Use filtered value
		$segments [] = (string) preg_replace( '/[^a-z]/', '', $query ['layout'] );
	}

	// Support URIs like: /forum/12-category/123-topic/reply/124
	if (isset ( $query ['mesid'] ) && $numeric) {
		$segments [] = (int) $query ['mesid'];
		unset ( $query ['mesid'] );
	}

	// Support URIs like: /forum/user/128-matias
	if (isset ( $query ['userid'] ) && $view == 'user') {
		$segments [] = (int) $query ['userid'] .'-'.KunenaRoute::stringURLSafe ( KunenaUserHelper::get((int)$query ['userid'])->getName() );
		unset ( $query ['userid'] );
	}

	unset ( $query ['view'], $query ['layout'] );

	// Rest of the known parameters are in var-value form
	foreach ( KunenaRoute::$parsevars as $var=>$dummy ) {
		if (isset ( $query [$var] )) {
			$segments [] = "{$var}-{$query[$var]}";
			unset ( $query [$var] );
		}
	}

	KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__FUNCTION__.'()') : null;
	return $segments;
}

function KunenaParseRoute($segments) {
	// If Kunena Forum isn't installed do nothing
	if (!class_exists('KunenaForum') || !KunenaForum::isCompatible('3.0') || !KunenaForum::installed()) {
		return array();
	}

	$profiler = JProfiler::getInstance('Application');
	KUNENA_PROFILER ? $profiler->mark('kunenaRoute') : null;
	$starttime = $profiler->getmicrotime();

	// Get current menu item and get query variables from it
	$active = JFactory::getApplication()->getMenu ()->getActive ();
	$vars = isset ( $active->query ) ? $active->query : array ('view'=>'home');
	if (empty($vars['view']) || $vars['view']=='home' || $vars['view']=='entrypage') {
		$vars['view'] = '';
	}

	// Use category SEF feature?
	$sefcats = isset(KunenaRoute::$sefviews[$vars['view']]) && empty($vars ['id']);

	// Handle all segments
	while ( ($segment = array_shift ( $segments )) !== null ) {
		// Skip //
		if (!$segment) continue;

		if ($sefcats && class_exists('KunenaRoute') && method_exists('KunenaRoute', 'resolveAlias')) {
			// Find out if we have SEF alias (category, view or layout)
			$alias = strtr ( $segment, ':', '-' );
			$variables = KunenaRoute::resolveAlias($alias);
			if ($variables) {
				$sefcats = false;
				$vars = $variables + $vars;
				continue;
			}
		}

		$sefcats = false;

		// Generate variable and value
		$seg = explode ( ':', $segment );
		$var = array_shift ( $seg );
		$value = array_shift ( $seg );

		if (empty ( $var ) && empty ( $value )) {
			// Skip /-/
			continue;
		}

		if (is_numeric ( $var )) {
			// Handle variables starting by number
			$value = (int) $var;
			if ($vars['view'] == 'user') {
				// Special case: User view
				$var = 'userid';

			} elseif (empty($vars ['catid'])) {
				// First number is always category
				$var = 'catid';
				$vars ['view'] = 'category';
			} elseif (empty($vars ['id'])) {
				// Second number is always topic
				$var = 'id';
				$vars ['view'] = 'topic';
				$sefcats = false;
			} elseif (empty($vars ['mesid'])) {
				// Third number is always message
				$var = 'mesid';
				$vars ['view'] = 'topic';
			} else {
				// Invalid parameter, skip it
				continue;
			}
		} elseif ($value === null) {
			// Simple variable without value is always either view or layout
			$value = $var;
			if (empty($vars ['view']) || ($value=='topic' && $vars ['view'] == 'category')) {
				// View
				$var = 'view';
			} elseif (empty($vars ['layout'])) {
				// Layout
				$var = 'layout';
			} elseif (!empty($vars ['view'])) {
				// Unknown parameter: skip
				if (!empty($vars ['view'])) continue;
			} else {
				// Unknown view or non-existing category
				$var = 'view';
			}
		}
		$vars [$var] = $value;
	}
	if (empty($vars ['layout'])) $vars ['layout'] = 'default';
	KunenaRoute::$time = $profiler->getmicrotime() - $starttime;

	foreach ($vars as $var => $value) {
		KunenaRoute::$current->setVar($var, $value);
	}

	return $vars;
}
