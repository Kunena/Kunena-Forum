<?php
/**
* @version $Id: kunena.link.class.php 822 2009-06-08 00:06:22Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
**/

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
function KunenaBuildRoute(&$query)
{
	static $catcache = array();
	static $msgcache = array();
	$parsevars = array('task', 'id', 'userid', 'page', 'sel');
	$segments = array();
	
	$db =& JFactory::getDBO();
	jimport('joomla.filter.output');
	
	$catfound = false;
	if(isset($query['catid']))
	{
		$catid = (int) $query['catid'];
		if($catid != 0)
		{
			$catfound = true;
			if (!isset($catcache[$catid]))
			{
				$quesql = 'SELECT name, id FROM #__fb_categories WHERE id='.(int) $catid;
				$db->setQuery($quesql);
				$catcache[$catid] = $db->loadResult();
			}
			$suf = '-'.JFilterOutput::stringURLSafe($catcache[$catid]);
			$segments[] = $query['catid'].$suf;
		}
		unset($query['catid']);
	}

	if($catfound && isset($query['id']))
	{
		$id = $query['id'];
		if (!isset($msgcache[$id]))
		{
			$quesql = 'SELECT subject, id FROM #__fb_messages WHERE id='.(int) $id;
			$db->setQuery($quesql);
			$msgcache[$id] = $db->loadResult();
		}
		$suf = JFilterOutput::stringURLSafe($msgcache[$id]);
		$segments[] = $query['id'].'-'.$suf;
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

function KunenaParseRoute($segments)
{
	$funcitems = array(
		array('func'=>'showcat', 'var'=>'catid'), 
		array('func'=>'view', 'var'=>'id')
	);
	$doitems = array('func', 'do');
	$funcpos = $dopos = 0;
	
	$vars = array();
	while (($segment = array_shift($segments)) !== null) 
	{
		$segment = explode(':', $segment);
		$var = array_shift($segment);
		$value = array_shift($segment);
		if (empty($var)) continue; // Empty parameter
		if (is_numeric($var)) 
		{
			if ($funcpos > count($funcitems)) continue; // Unknown parameter
			$vars['func'] = $funcitems[$funcpos]['func'];
			$value = $var; 
			$var = $funcitems[$funcpos++]['var'];
		} else if ($value === null) 
		{
			if ($dopos > count($doitems)) continue; // Unknown parameter
			$value = $var;
			$var = $doitems[$dopos++];
		}
		$vars[$var] = $value;
	}
	// Check if we should use listcat instead of showcat
	if ($vars['func'] == 'showcat') 
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
		}
		if (!$parent) $vars['func'] = 'listcat';
	}
	
	return $vars;
}
?>