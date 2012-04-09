<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Finder
 *
 * @Copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ( '' );

jimport('joomla.application.component.helper');

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

/**
 * Finder adapter for com_kunena.
 */
class plgFinderKunena extends FinderIndexerAdapter {
	/**
	 * The plugin identifier.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $context = 'Kunena';

	/**
	 * The extension name.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $extension = 'com_kunena';

	/**
	 * The sublayout to use when rendering the results.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $layout = 'topic';

	/**
	 * The type of content that the adapter indexes.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $type_title = 'Forum Post';

	/**
	 * Method to reindex the link information for an item that has been saved.
	 * This event is fired before the data is actually saved so we are going
	 * to queue the item to be indexed later.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   JTable   $row     A JTable object
	 * @param   boolean  $isNew    If the content is just about to be created
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderBeforeSave($context, $row, $isNew) {
		// We only want to handle articles here
		if ($context == 'com_kunena.message') {
			// Query the database for the old access level if the item isn't new
			if (!$isNew) {
				$query = $this->db->getQuery(true);
				$query->select($this->db->quoteName('access'));
				$query->from($this->db->quoteName('#__content'));
				$query->where($this->db->quoteName('id') . ' = ' . (int)$row->id);
				$this->db->setQuery($query);

				// Store the access level to determine if it changes
				$this->old_access = $this->db->loadResult();
			}
		}

		return true;
	}

	/**
	 * Method to determine if the access level of an item changed.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   JTable   $row      A JTable object
	 * @param   boolean  $isNew    If the content has just been created
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterSave($context, $row, $isNew) {
		// We only want to handle Kunena messages in here
		if ($context == 'com_kunena.message') {
			// Check if the access levels are different
			if (!$isNew && $this->old_access != $row->access) {
				$sql = clone($this->_getStateQuery());
				$sql->where('a.id = ' . (int) $row->id);

				// Get the access level.
				$this->db->setQuery($sql);
				$item = $this->db->loadObject();

				// Set the access level.
				$temp = max($row->access, $item->cat_access);

				// Update the item.
				$this->change((int) $row->id, 'access', $temp);
			}

			// Run the setup method.
			$this->setup();

			// Get the item.
			$item = $this->getItem($row->id);

			// Index the item.
			$this->index($item);

		}

		return true;
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterDelete($context, $table)
	{
		if ($context == 'com_kunena.message') {
			$id = $table->id;
		} elseif ($context == 'com_finder.index') {
			$id = $table->link_id;
		} else {
			return true;
		}
		// Remove the items.
		return $this->remove($id);
	}

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param   FinderIndexerResult  $item  The item to index as an FinderIndexerResult object.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function index(FinderIndexerResult $item, $format = 'html') {
		// Check if the extension is enabled
		if (JComponentHelper::isEnabled($this->extension) == false) {
			return;
		}

		// Translate the access group to an access level.
		// FIXME:
		$item->access = $item->cat_access = 1; // $this->getAccessLevel($item->cat_access);

		// Set the language.
		$item->language = FinderIndexerHelper::getDefaultLanguage();

		// Trigger the onContentPrepare event.
		$item->body = $item->summary = FinderIndexerHelper::prepareContent(KunenaHtmlParser::parseBBCode($item->body));

		// Build the necessary route and path information.
		$item->url = $this->getUrl($item, $this->extension, $this->layout);
		$item->route = $item->url.'&Itemid='.KunenaRoute::getItemId($item->url);
		$item->path = FinderIndexerHelper::getContentPath($item->route);

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Forum Post');

		// Add the author taxonomy data.
		if (!empty($item->author)) {
			$item->addTaxonomy('Author', $item->author);
		}

		// Add the category taxonomy data.
//		$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);

		// Add the language taxonomy data.
		$item->addTaxonomy('Language', $item->language);

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		FinderIndexer::index($item);
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 */
	protected function setup() {
		// Check if Kunena has been installed.
		if (! class_exists ( 'KunenaForum' ) || ! KunenaForum::installed()) {
			return false;
		}
		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param   mixed  $sql  A JDatabaseQuery object or null.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getListQuery($sql = null)
	{
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : $this->db->getQuery(true);
		$sql->select('m.id, m.parent, m.thread, m.catid, m.subject AS title');
		$sql->select('FROM_UNIXTIME(m.time, \'%Y-%m-%d %H:%i:%s\') AS start_date');
		$sql->select('m.name AS author, t.message AS summary, t.message AS body');
		$sql->select('c.name AS category, 1 AS state, c.published AS cat_state, c.pub_access AS access, c.pub_access AS cat_access');
		$sql->from('#__kunena_messages AS m');
		$sql->join('INNER', '#__kunena_messages_text AS t ON t.mesid = m.id');
		$sql->join('INNER', '#__kunena_categories AS c ON c.id = m.catid');
		$sql->join('LEFT', '#__users AS u ON u.id = m.userid');

		// Only include posts that have been approved.
		$sql->where('m.hold=0 AND m.moved=0');

		return $sql;
	}

	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param	mixed		The id of the item.
	 * @return	string		The URL of the item.
	 */
	protected function getUrl($item, $extension, $view) {
		return "index.php?option=com_kunena&view={$view}&catid={$item->catid}&id={$item->thread}&mesid={$item->id}";
	}
}