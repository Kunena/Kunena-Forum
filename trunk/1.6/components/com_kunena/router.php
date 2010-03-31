<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
**/

require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');
require_once (KUNENA_PATH_LIB . DS . 'kunena.config.class.php');

class KunenaRouter
{
	static $catidcache = null;
	static $msgidcache = array();

	// List of reserved functions (if category name is one of these, use always catid)
	static $functions = array(
		'who','announcement','poll','stats','myprofile','userprofile','fbprofile','profile',
		'userlist','post','view','help','showcat','listcat','review','moderate','rules',
		'report','latest','mylatest','noreplies','subscriptions','favorites','search','advsearch',
		'markthisread','subscribecat','unsubscribecat','karma','bulkactions','banactions',
		'templatechooser','credits'
	);

	function loadCategories()
	{
		if (self::$catidcache !== null) return; // Already loaded

		$db =& JFactory::getDBO();

		$query = 'SELECT id, name FROM #__fb_categories WHERE published=1';
		$db->setQuery($query);
		self::$catidcache = $db->loadAssocList('id');
		check_dberror ( "Unable to load categories." );
	}

	/**
	 * Preloads messages, saves SQL queries
	 *
	 * @param $msgs Messages in form of array ('1'=>'subject', ...))
	 */
	function loadMessages($msglist)
	{
		self::$msgidcache = self::$msgidcache + $msglist;
	}

	function filterOutput($str)
	{
		return JString::trim(preg_replace(array('/\s+/','/[\$\&\+\,\/\:\;\=\?\@\'\"\<\>\#\%\{\}\|\\\^\~\[\]\`\.]/'), array('-',''), $str));
	}

	function stringURLSafe($str)
	{
		$kunena_config =& CKunenaConfig::getInstance();
		if ($kunena_config->sefutf8) {
			$str = self::filterOutput($str);
			return urlencode($str);
		}
		return JFilterOutput::stringURLSafe($str);
	}
/**
 * Build SEF URL
 *
 * All SEF URLs are formatted like this:
 *
 * http://site.com/menuitem/1-category-name/10-subject/[func]/[do]/[param1]-value1/[param2]-value2?param3=value3&param4=value4
 *
 * - If catid exists, category will always be in the first segment
 * - If there is no catid, second segment for message will not be used (param-value: id-10)
 * - [func] and [do] are the only parameters without value
 * - all other segments (task, id, userid, page, sel) are using param-value format
 *
 * NOTE! Only major variables are using SEF segments
 *
 * @param $query
 * @return segments
 */
function BuildRoute(&$query)
{
	$parsevars = array('task', 'id', 'userid', 'page', 'sel');
	$segments = array();

	$kunena_config = CKunenaConfig::getInstance();
	if (!$kunena_config->sef) return $segments;

	if (isset($query['Itemid'])) {
		// If we have Itemid, make sure that we remove identical parameters
		$menu = JSite::getMenu();
		$menuitem = (object) $menu->getItem($query['Itemid']);
		foreach ($menuitem->query as $var=>$value) {
			if ($var == 'Itemid' || $var == 'option') continue;
			if (isset($query[$var]) && $value == $query[$var]) {
				unset($query[$var]);
			}
		}
	}

	$db =& JFactory::getDBO();
	jimport('joomla.filter.output');

	// We may have catid also in the menu item
	$catfound = isset($menuitem->query['catid']);
	// If we had identical catid in menuitem, this one will be skipped:
	if(isset($query['catid']))
	{
		$catid = (int) $query['catid'];
		if($catid != 0)
		{
			$catfound = true;

			if (self::$catidcache === null) self::loadCategories();
			if (isset(self::$catidcache[$catid])) $suf = self::stringURLSafe(self::$catidcache[$catid]['name']);
			if (empty($suf)) $segments[] = $query['catid'];
			else if ($kunena_config->sefcats && !in_array($suf, self::$functions)) $segments[] = $suf;
			else $segments[] = $query['catid'].'-'.$suf;
		}
		unset($query['catid']);
	}

	if($catfound && isset($query['id']))
	{
		$id = $query['id'];
		if (!isset(self::$msgidcache[$id]))
		{
			$quesql = 'SELECT subject, id FROM #__fb_messages WHERE id='.(int) $id;
			$db->setQuery($quesql);
			self::$msgidcache[$id] = $db->loadResult();
			check_dberror ( "Unable to load subject." );
		}
		$suf = self::stringURLSafe(self::$msgidcache[$id]);
		if (empty($suf)) $segments[] = $query['id'];
		else $segments[] = $query['id'].'-'.$suf;
		unset($query['id']);
	}

	if(isset($query['func']))
	{
		if ($query['func'] != 'showcat' && $query['func'] != 'view' && !($query['func'] == 'listcat' && $catfound)) {
			$segments[] = $query['func'];
		}
		unset($query['func']);
	}

	if(isset($query['do']))
	{
		$segments[] = $query['do'];
		unset($query['do']);
	}

	foreach ($parsevars as $var)
	{
		if(isset($query[$var]))
		{
			$segments[] = "{$var}-{$query[$var]}";
			unset($query[$var]);
		}
	}

	return $segments;
}

function ParseRoute($segments)
{
	$funcitems = array(
		array('func'=>'showcat', 'var'=>'catid'),
		array('func'=>'view', 'var'=>'id')
	);
	$doitems = array('func', 'do');
	$funcpos = $dopos = 0;
	$vars = array();

	$kunena_config =& CKunenaConfig::getInstance();

	// Get current menu item
	$menu = JSite::getMenu();
	$active = $menu->getActive();
	$menuquery = isset($active->query) ? $active->query : array();
	if (isset($menuquery['func'])) {
		$funcinmenu=true;
		$vars['func'] = $menuquery['func'];
		$dopos++;
	}
	if (isset($menuquery['catid'])) {
		$funcinmenu=true;
		$vars['catid'] = $menuquery['catid'];
		$funcpos++;
	}

	while (($segment = array_shift($segments)) !== null)
	{
		$seg = explode(':', $segment);
		$var = array_shift($seg);
		$value = array_shift($seg);

		// If SEF categories are allowed: Translate category name to catid
		if ($kunena_config->sefcats && $funcpos==0 && $dopos==0 && ($value !== null || !in_array($var, self::$functions)))
		{
			self::loadCategories();
			$catname = strtr($segment, ':', '-');
			foreach (self::$catidcache as $cat)
			{
				if ($catname == self::filterOutput($cat['name']) || $catname == JFilterOutput::stringURLSafe($cat['name']))
				{
					$var = $cat['id'];
					break;
				}
			}
		}

		if (empty($var)) continue; // Empty parameter

		if (is_numeric($var)) // Numeric value is always listcat, showcat or view
		{
			if ($funcpos > count($funcitems)) continue; // Unknown parameter
			$vars['func'] = $funcitems[$funcpos]['func'];
			$value = $var;
			$var = $funcitems[$funcpos++]['var'];
		} else if ($value === null) // Value must be either func or do
		{
			// Do we need to override func from menuitem?
			if (!empty($funcinmenu) && in_array($var, self::$functions)) {
				unset($funcinmenu);
				$dopos = 0;
			}
			if ($dopos > count($doitems)) continue; // Unknown parameter
			$value = $var;
			$var = $doitems[$dopos++];
		}
		$vars[$var] = $value;
	}
	// Check if we should use listcat instead of showcat
	if (isset($vars['func']) && $vars['func'] == 'showcat')
	{
		if (empty($vars['catid']))
	 	{
			$parent = 0;
		}
		else
		{
			$db =& JFactory::getDBO();
			$quesql = 'SELECT parent FROM #__fb_categories WHERE id='.(int) $vars['catid'];
			$db->setQuery($quesql);
			$parent = $db->loadResult();
			check_dberror ( "Unable to load category parent." );
		}
		if (!$parent) $vars['func'] = 'listcat';
	}

	return $vars;
}

}

function KunenaBuildRoute(&$query)
{
	return KunenaRouter::BuildRoute($query);
}

function KunenaParseRoute($segments)
{
	return KunenaRouter::ParseRoute($segments);
}
?>