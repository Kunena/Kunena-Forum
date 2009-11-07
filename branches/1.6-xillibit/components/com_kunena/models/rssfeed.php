<?php
/**
 * @version		$Id:  $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');
kimport('database.query');

/**
 * Statistics model for the Kunena package.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelRssFeed extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $__state_set = false;

	/**
	 * An array of totals for the lists.
	 *
	 * @var		array
	 * @since	1.6
	 */
	protected $_totals = array();

	/**
	 * Array of lists containing items.
	 *
	 * @var		array
	 * @since	1.6
	 */
	protected $_lists = array();

	/**
	 * The model context for caching.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $_context = 'com_kunena.rssfeed';

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	Optional parameter name.
	 * @param	mixed	Optional default value.
	 * @return	mixed	The property where specified, the state object where omitted.
	 * @since	1.6
	 */
	public function getState($property = null)
	{
		if (!$this->__state_set)
		{
			// Get the application object and component options.
			$app	= JFactory::getApplication();
			$params	= $app->getParams('com_kunena');

			$this->setState('list.state', 1);

			// Load model type
			// all = all announcements
			// published = published announcements
			$this->setState('type', JRequest::getCmd('type', 'all'));
			
			// Load the check parameters.
			$this->setState('check.state', true);

			// Load the parameters.
			$this->setState('params', $params);

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * Method to get a list pagination object.
	 *
	 * @return	object	A JPagination object.
	 * @since	1.6
	 */
	public function getPagination()
	{
		jimport('joomla.html.pagination');

		// Create the pagination object.
		$instance = new JPagination($this->getTotal(), (int)$this->getState('list.start'), (int)$this->getState('list.limit'));

		return $instance;
	}

	/**
	 * Method to get forum statistics
	 *
	 * @return	array	Statistics object.
	 * @since	1.6
	 */
	public function getSummary()
	{
		$key = $this->_getStoreId($this->_context);
		
		// Try to load the value from internal storage.
		if (!empty ($this->_rssfeed[$key])) {
			return $this->_rssfeed[$key];
		}		
		
		$this->_rssfeed[$key]['rssfeedresults'] = $this->getRssFeed();
		return $this->_rssfeed[$key];
	}
  /**
	 * Method to get datas for rss feed.
	 *
	 * @return	
	 * @since	1.6
	 */
	protected function _getDatasRssFeed()
	{
		$query = new KQuery();	
		$query->select('id, name, subject, userid, thread, time, message');
		$query->from('#__kunena_messages ORDER BY time DESC limit 0, 20');    	
		return $query;
	}		
	/**
	 * Method to get the datas for the rss feed.
	 *
	 * @return String	
	 * @since	1.6
	 */
	public function getRssFeed()
	{
	  // Get a unique key for the current list state.
		$key = $this->_getStoreId($this->_context);	
    
    $query = $this->_getDatasRssFeed();
		$this->_db->setQuery($query->toString());
		$datas = $this->_db->loadObjectList();
		
		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}			
    
    $return = $datas;
    
    // Push the value into internal storage.
		$this->_rssfeedresults[$key] = $return;

		// Push the value into cache.
		//$cache->store($total, $store);
    return $this->_rssfeedresults[$key];	
	}		
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	A prefix for the store id.
	 * @return	string	A store id.
	 * @since	1.6
	 */
	protected function _getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.state');
		$id	.= ':'.$this->getState('check.state');

		return md5($id);
	}
}
