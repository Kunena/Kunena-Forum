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
	 * The mime type of the content the adapter indexes.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $mime = 'txt';

	/**
	 * The type of content that the adapter indexes.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $type_title = 'Forum Post';

	/**
	 * The field the published state is stored in.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $state_field = 'published';

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
	 * Method to index a batch of content items. This method can be called by
	 * the indexer many times throughout the indexing process depending on how
	 * much content is available for indexing. It is important to track the
	 * progress correctly so we can display it to the user.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on error.
	 */
	public function onBuildIndex()
	{
		JLog::add('FinderIndexerAdapter::onBuildIndex', JLog::INFO);

		// Get the indexer and adapter state.
		$iState = FinderIndexer::getState();
		$aState = $iState->pluginState[$this->context];

		// Check the progress of the indexer and the adapter.
		if ($iState->batchOffset == $iState->batchSize || $aState['offset'] == $aState['total'])
		{
			return true;
		}

		// Get the batch offset and size.
		$offset = (int) $aState['offset'];
		$limit = (int) ($iState->batchSize - $iState->batchOffset);

		// Get the content items to index.
		$items = $this->getItems($offset, $limit);

		// Iterate through the items and index them.
		foreach ($items as $item) $this->index($item);

		// Adjust the offsets.
		$iState->batchOffset = $iState->batchSize;
		$iState->totalItems -= $item->id - $offset;

		// Update the indexer state.
		$aState['offset'] = $item->id;
		$iState->pluginState[$this->context] = $aState;
		FinderIndexer::setState($iState);

		unset($items, $item);
		return true;
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
		// Initialize CLI
		$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
		if (file_exists($api)) {
			require_once $api;
		}

		// Check if Kunena has been installed.
		if (! class_exists ( 'KunenaForum' ) || ! KunenaForum::isCompatible('2.0') || ! KunenaForum::installed()) {
			return false;
		}
		KunenaForum::setup();
		return true;
	}

	/**
	 * Method to get the number of content items available to index.
	 *
	 * @return  integer  The number of content items available to index.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function getContentCount() {
		JLog::add('FinderIndexerAdapter::getContentCount', JLog::INFO);

		// Get the list query.
		$sql = $this->db->getQuery(true);
		$sql->select('MAX(id)')->from('#__kunena_messages');

		// Get the total number of content items to index.
		$this->db->setQuery($sql);
		$return = (int) $this->db->loadResult();

		// Check for a database error.
		if ($this->db->getErrorNum())
		{
			// Throw database error exception.
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		return $return;
	}


	/**
	 * Method to get a content item to index.
	 *
	 * @param   integer  $id  The id of the content item.
	 *
	 * @return  FinderIndexerResult  A FinderIndexerResult object.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function getItem($id)
	{
		JLog::add('FinderIndexerAdapter::getItem', JLog::INFO);

		$message = KunenaForumMessageHelper::get($id);

		// Convert the item to a result object.
		$item = $this->$this->createIndexerResult($message);
		unset($message);
		KunenaForumMessageHelper::clean();

		return $item;
	}

	/**
	 * Method to get a list of content items to index.
	 *
	 * @param   integer         $offset  The list offset.
	 * @param   integer         $limit   The list limit.
	 * @param   JDatabaseQuery  $sql     A JDatabaseQuery object. [optional]
	 *
	 * @return  array  An array of FinderIndexerResult objects.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function getItems($offset, $limit, $sql = null)
	{
		JLog::add("FinderIndexerAdapter::getItems({$offset}, {$limit})", JLog::INFO);

		// Get the list query.
		$sql = $this->db->getQuery(true);
		$sql->select('id')->from('#__kunena_messages')->where('id>'.$this->db->quote($offset));

		// Get the content items to index.
		$this->db->setQuery($sql, 0, $limit);
		$ids = $this->db->loadColumn();

		// Check for a database error.
		if ($this->db->getErrorNum())
		{
			// Throw database error exception.
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Convert the items to result objects.
		$messages = KunenaForumMessageHelper::getMessages($ids, 'none');
		$items = array();
		foreach ($messages as &$message)
		{
			$items[] = $this->createIndexerResult($message);
		}
		KunenaForumMessageHelper::cleanup();
		KunenaRoute::cleanup();

		return $items;
	}

	protected function createIndexerResult($message) {
		// Convert the item to a result object.
		$item = new FinderIndexerResult;
		$item->id = $message->id;
		$item->catid = $message->catid;

		// Set title context.
		$item->title = $message->subject;

		// Build the necessary url, route, path and alias information.
		$item->url = $this->getUrl($message, $this->extension, $this->layout);
//		$item->route = $item->url.'&Itemid='.KunenaRoute::getItemId($item->url);
		$item->path = FinderIndexerHelper::getContentPath($item->uri);//route);
		$item->alias = KunenaRoute::stringURLSafe($message->subject);

		// Set body context.
//		$item->body = KunenaHtmlParser::stripBBCode($message->message);

		// Set other information.
		$item->published = intval($message->hold == 1);
		// TODO: add topic state
		$item->state = intval($message->getCategory()->published == 1);
		$item->language = '*';
		$item->publish_start_date = date($this->db->getDateFormat(), $message->time);
		$item->start_date = $item->publish_start_date;
		// TODO: add access control
		$item->access = 1; // $this->getAccessLevel($item->cat_access);

		// Set the item type.
		$item->type_id = $this->type_id;

		// Set the mime type.
		$item->mime = $this->mime;

		// Set the item layout.
		$item->layout = $this->layout;

		return $item;
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

	/**
	 * Method to translate the native content states into states that the
	 * indexer can use.
	 *
	 * @param   integer  $item      The item state.
	 * @param   integer  $category  The category state. [optional]
	 *
	 * @return  integer  The translated indexer state.
	 *
	 * @since   2.5
	 */
	protected function translateState($item, $category = null)
	{
		// If category is present, factor in its states as well
		if ($category !== null)
		{
			if ($category != 1)
			{
				$item = 0;
			}
		}

		// Translate the state
		return intval($item == 1);
	}
}