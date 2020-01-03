<?php
/**
 * Kunena Plugin
 *
 * @package          Kunena.Plugins
 * @subpackage       Community
 *
 * @copyright   (C)  2008 - 2020 Kunena Team. All rights reserved.
 * @copyright   (C)  2013 - 2014 iJoomla, Inc. All rights reserved.
 * @license          https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link             https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Class KunenaProfileCommunity
 *
 * @since   Kunena 6.0
 */
class KunenaProfileCommunity extends KunenaProfile
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaProfileCommunity constructor.
	 *
	 * @param   object  $params params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param   string  $action  action
	 * @param   bool    $xhtml   xhtml
	 *
	 * @return  boolean|string
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = Factory::getApplication()->getIdentity();

		if ($config->userlist_allowed == 0 && $my->id == 0)
		{
			return false;
		}

		return CRoute::_('index.php?option=com_community&view=search&task=browse', $xhtml);
	}

	/**
	 * @param   int  $limit  limit
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function _getTopHits($limit = 0)
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('cu.userid AS id, cu.view AS count')
			->from($db->quoteName('#__community_users', 'cu'))
			->innerJoin($db->quoteName('#__users', 'u') . ' ON u.id = cu.userid')
			->where('cu.view > 0')
			->order('cu.view DESC');
		$query->setLimit($limit);
		$db->setQuery($query);

		try
		{
			$top = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $top;
	}

	/**
	 * @param $view
	 * @param   object  $params params
	 *
	 * @since   Kunena 6.0
	 */
	public function showProfile($view, &$params)
	{
	}

	/**
	 * @param         $userid
	 * @param   bool  $xhtml  xhtml
	 *
	 * @return  boolean|string
	 * @since   Kunena 6.0
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}

	/**
	 * @param           $userid
	 * @param   string  $task   task
	 * @param   bool    $xhtml  xhtml
	 *
	 * @return  boolean|string
	 * @since   Kunena 6.0
	 */
	public function getProfileURL($userid, $task = '', $xhtml = true)
	{
		// Make sure that user profile exist.
		if (!$userid || CFactory::getUser($userid) === null)
		{
			return false;
		}

		return CRoute::_('index.php?option=com_community&view=profile&userid=' . (int) $userid, $xhtml);
	}
}
