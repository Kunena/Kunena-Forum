<?php
/**
 * Kunena Plugin
 *
 * @package          Kunena.Plugins
 * @subpackage       Community
 *
 * @copyright   (C)  2008 - 2017 Kunena Team. All rights reserved.
 * @copyright   (C)  2013 - 2014 iJoomla, Inc. All rights reserved.
 * @license          https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link             https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaProfileCommunity
 * @since Kunena
 */
class KunenaProfileCommunity extends KunenaProfile
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaProfileCommunity constructor.
	 *
	 * @param $params
	 *
	 * @since Kunena
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param   string $action
	 * @param   bool   $xhtml
	 *
	 * @return boolean|string
	 * @since Kunena
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = \Joomla\CMS\Factory::getUser();

		if ($config->userlist_allowed == 0 && $my->id == 0)
		{
			return false;
		}

		return CRoute::_('index.php?option=com_community&view=search&task=browse', $xhtml);
	}

	/**
	 * @param          $userid
	 * @param   string $task
	 * @param   bool   $xhtml
	 *
	 * @return boolean|string
	 * @since Kunena
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

	/**
	 * @param   int $limit
	 *
	 * @return array
	 * @since Kunena
	 */
	public function _getTopHits($limit = 0)
	{
		$db    = \Joomla\CMS\Factory::getDBO();
		$query = "SELECT cu.userid AS id, cu.view AS count
			FROM #__community_users AS cu
			INNER JOIN #__users AS u ON u.id=cu.userid
			WHERE cu.view>0
			ORDER BY cu.view DESC";
		$db->setQuery($query, 0, $limit);

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
	 * @param $params
	 *
	 * @since Kunena
	 */
	public function showProfile($view, &$params)
	{
	}

	/**
	 * @param        $userid
	 * @param   bool $xhtml
	 *
	 * @return boolean|string
	 * @since Kunena
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}
}
