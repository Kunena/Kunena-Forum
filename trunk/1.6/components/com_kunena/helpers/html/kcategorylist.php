<?php
/**
* @version $Id: klink.php $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
*/

// no direct access
defined('_JEXEC') or die;

/**
 * Utility class for Kunena categories
 *
 * @since		1.6
 */
abstract class JHtmlKCategoryList
{
	/**
	 * @var	array	Cached array of the category items.
	 */
	protected static $items = array();

	/**
	 * Returns an array of categories.
	 *
	 * @param	array	An array of configuration options. By default, only published categories are returned.
	 *
	 * @return	array
	 */
	public static function options($config = array('filter.published' => array(1), 'filter.id' => null))
	{
		$hash = md5(serialize($config));
		if (!isset(self::$items[$hash]))
		{
			$config	= (array) $config;
			$db		= &JFactory::getDbo();

			// TODO: merge into categories model
			
			kimport('database.query');
			$query	= new KQuery;

			$query->select('c.*');
	    	$query->from('#__kunena_categories AS c');

	    	if (!isset($config['filter.id']) || !is_array($config['filter.id']))
	    	{
				kimport('user.user');
	    		$user = KUser::getInstance(false);
	    		$config['filter.id'] = explode(',', $user->getAllowedCategories());
	    	}
	    	JArrayHelper::toInteger($config['filter.id']);
	    	$query->where('c.id IN ('.implode(',', $config['filter.id']).')');
		
			// Filter on the published state
			if (isset($config['filter.published']))
			{
				if (is_numeric($config['filter.published'])) {
					$query->where('c.published = '.(int) $config['filter.published']);
				}
				else if (is_array($config['filter.published']))
				{
					JArrayHelper::toInteger($config['filter.published']);
					$query->where('c.published IN ('.implode(',', $config['filter.published']).')');
				}
			}
	    	
			$query->order('c.parent, c.ordering');
			
			$db->setQuery($query->toString());
			$rows = $db->loadObjectList();

			$nested = array();
		    foreach ($rows as $row) {
    			$nested[$row->parent][] = $row;
		    }
			
			// Assemble the list options.
			self::$items[$hash] = array();
			self::_getItems($nested, $hash);
		}

		return self::$items[$hash];
	}
	
	protected static function _getItems(&$nested, $hash, $parent=0, $level=0)
	{
		if (empty($nested[$parent])) return '';
		foreach ($nested[$parent] as &$item)
		{
			$item->name = str_repeat('... ', $level).$item->name;
			self::$items[$hash][] = JHtml::_('select.option', $item->id, $item->name);
			self::_getItems($nested, $hash, $item->id, $level+1);
		}
	}
}
