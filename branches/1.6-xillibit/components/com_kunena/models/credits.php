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
 * User profile model for the Kunena package.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelCredits extends JModel
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
	protected $_context = 'com_kunena.user';

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

			// If the limit is set to -1, use the global config list_limit value.
			$limit	= JRequest::getInt('limit', $params->get('list_limit', 0));
			$limit	= ($limit === -1) ? $app->getCfg('list_limit', 20) : $limit;

			// Load the list state.
			$this->setState('list.start', JRequest::getInt('limitstart'));
			$this->setState('list.limit', $limit);
			$this->setState('list.state', 1);

			// Load model type
			// all = all announcements
			// published = published announcements
			$this->setState('type', JRequest::getCmd('type', 'published'));

			// Load the check parameters.
			$this->setState('check.state', true);

			// Load the parameters.
			$this->setState('params', $params);

			$this->__state_set = true;
		}

		return parent::getState($property);
	}

	/**
	 * Method to get a list of items.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 * @since	1.6
	 */
	public function getContributors()
	{
		$array = array(
			array ('name'=>'fxstein', 'title'=>'Kunena developer', 'url'=>'http://www.starvmax.com/'),
//			array ('name'=>'johnnydement', 'title'=>'Kunena moderator', 'url'=>'http://www.kunena.com/community/profile?userid=66'),
//			array ('name'=>'LDA', 'title'=>'Kunena moderator', 'url'=>'http://www.kunena.com/community/profile?userid=2171'),
			array ('name'=>'Matias', 'title'=>'Kunena developer', 'url'=>'http://www.herppi.net/'),
//			array ('name'=>'Noel Hunter', 'title'=>'Kunena developer', 'url'=>'http://www.camelcity.com/'),
//			array ('name'=>'Roland76', 'title'=>'Kunena developer', 'url'=>'http://www.kunena.com/community/profile?userid=122'),
			array ('name'=>'severdia', 'title'=>'Kunena developer', 'url'=>'http://www.kunena.com/community/profile?userid=114'),
//			array ('name'=>'Spock', 'title'=>'Kunena moderator', 'url'=>'http://www.kunena.com/community/profile?userid=314'),
//			array ('name'=>'whouse', 'title'=>'Kunena developer', 'url'=>'http://www.kunena.com/community/profile?userid=148'),
			array ('name'=>'xillibit', 'title'=>'Kunena developer', 'url'=>'http://www.kunena.com/community/profile?userid=1288'),
//			array ('name'=>'@quila', 'title'=>'Kunena moderator', 'url'=>'http://www.kunena.com/community/profile?userid=447'),
		);

		return $array;
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
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.state');
		$id	.= ':'.$this->getState('check.state');

		return md5($id);
	}
}
?>