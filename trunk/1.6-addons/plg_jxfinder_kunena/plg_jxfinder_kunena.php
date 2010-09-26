<?php
/**
 * @version $Id: kunenadiscuss.php 3374 2010-08-31 19:01:18Z fxstein $
 * JXtended Finder Kunena  Plugin
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * @copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 * @link http://jxtended.com
 **/
defined ( '_JEXEC' ) or die ( '' );

// Load the base adapter.
require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('languages/plg_finder_jxfinder_kunena');
$lang->load('languages/plg_finder_jxfinder_kunena.custom');

/**
 * Finder adapter for Labels Labels.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderKunena_Posts
 */
class plgFinderKunena_Posts extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'jxFinder_Kunena';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'kunena';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $_type_title = 'Forum Post';

	/**
	 * @var		object		Kunena configuration object.
	 */
	private $_config;

	/**
	 * @var		array		Kunena emoticon configuration array.
	 */
	private $_emoticons;

	/**
	 * Method to reindex the link information for an item that has been saved.
	 * This event is fired before the data is actually saved so we are going
	 * to queue the item to be indexed later.
	 *
	 * @param	integer		The id of the item.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onAfterSaveKunenaPost($id)
	{
		// Queue the item to be reindexed.
		FinderIndexerQueue::add($this->_context, $id, JFactory::getDate()->toMySQL());

		return true;
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param	array		An array of item ids.
	 * @return	boolean		True on success.
	 * @throws	Exception on database error.
	 */
	public function onDeleteKunenaPost($ids)
	{
		// Remove the items.
		return $this->_remove($ids);
	}

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param	object		The item to index as an FinderIndexerResult object.
	 * @throws	Exception on database error.
	 */
	protected function _index(FinderIndexerResult $item)
	{
		// Build the necessary route and path information.
		$item->url		= $this->_getURL($item->id);
		$item->itemid	= '&Itemid='.KunenaRoute::getItemId($item->url);
		$item->route	= $item->url.'&catid='.$item->catid.$item->itemid;
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');

		// Strip slashes!
		$item->title	= stripslashes($item->title);
		$item->summary	= stripslashes($item->summary);
		$item->author	= stripslashes($item->author);

		// Process the message text.
		$item->summary	= @smile::smileReplace($item->summary, 0, $this->_config->disemoticons, $this->_emoticons);
		$item->summary	= nl2br($item->summary);
		$item->summary	= str_replace("__FBTAB__", "\t", $item->summary);
		$item->summary	= FinderIndexerHelper::prepareContent($item->summary);

		// Translate the access group to an access level.
		$item->cat_access = $this->_getAccessLevel($item->cat_access);

		// Inherit state and access form the category.
		$item->state	= $item->cat_state;
		$item->access	= $item->cat_access;

		// Set the language.
		$item->language	= FinderIndexerHelper::getDefaultLanguage();

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Forum Post');

		// Add the author taxonomy data.
		if (!empty($item->author)) {
			$item->addTaxonomy('Forum User', $item->author);
		}

		// Index the item.
		FinderIndexer::index($item);
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return	boolean		True on success.
	 */
	protected function _setup()
	{
		// Ensure Kunena 1.6 is installed and enabled.
		$KAPI = JPATH_ADMINISTRATOR.'/components/com_kunena/api.php';
		if (!JComponentHelper::isEnabled('com_kunena', true) || !is_file($KAPI)){
			return JError::raiseError (JText::_('Kunena 1.6 is not installed on your system'));
		}

		// Load dependencies.
		require_once $KAPI;
		require_once KPATH_SITE.DS.'class.kunena.php';
		require_once KPATH_SITE.DS.'lib'.DS.'kunena.smile.class.php';
		kimport('route');

		// Load the component language file.
		JFactory::getLanguage()->load('com_kunena', KPATH_SITE);

		// Load configuration.
		$this->_config = KunenaFactory::getConfig();

		// Load emoticons.
		$this->_emoticons = smile::getEmoticons(0);

		// Prime the router and application libraries.
		FinderIndexerHelper::getContentPath('');

		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param	mixed		A JDatabaseQuery object or null.
	 * @return	object		A JDatabaseQuery object.
	 */
	protected function _getListQuery($sql = null)
	{
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : new JDatabaseQuery();
		$sql->select('a.id, a.parent, a.thread, a.catid, a.subject AS title, a.topic_emoticon');
		$sql->select('a.time, FROM_UNIXTIME(a.time, \'%Y-%m-%d %H:%i:%s\') AS start_date');
		$sql->select('a.name AS author, t.message as summary');
		$sql->select('c.name AS category, c.published AS cat_state, c.pub_access AS cat_access');
		$sql->from('#__kunena_messages AS a');
		$sql->join('INNER', '#__kunena_messages_text AS t ON t.mesid = a.id');
		$sql->join('INNER', '#__kunena_categories AS c ON c.id = a.catid');
		$sql->join('LEFT', '#__users AS u ON u.id = a.userid');

		// Only include posts that have been approved.
		$sql->where('a.hold = 0');

		return $sql;
	}

	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param	mixed		The id of the item.
	 * @return	string		The URL of the item.
	 */
	protected function _getURL($id)
	{
		return 'index.php?option=com_kunena&func=view&id='.$id;
	}

	/**
	 * Method to translate a group id into an access level.
	 *
	 * @param	integer		A numeric group id.
	 * @return	integer		An access level.
	 */
	private function _getAccessLevel($groupId)
	{
		static $cache;

		// Check if public.
		if ($groupId == 0) {
			return 0;
		}

		// Check the cache.
		if (isset($cache[$groupId])) {
			return $cache[$groupId];
		}

		// Initialize the cache if necessary.
		if (!is_array($cache)) {
			$cache = array();
		}

		// Get the ACL object.
		$acl = JFactory::getACL();

		// Get the group name.
		$group = $acl->get_group_name($groupId);

		// Check if the group should have special access.
		if ($acl->is_group_child_of($group, 'Registered') || $acl->is_group_child_of($group, 'Public Backend')) {
			$cache[$groupId] = 2;
		}
		// Check if the group should have registered access.
		elseif ($acl->is_group_child_of($group, 'Public Frontend')) {
			$cache[$groupId] = 1;
		}
		// The group should only have public access.
		else {
			$cache[$groupId] = 0;
		}

		return $cache[$groupId];
	}
}