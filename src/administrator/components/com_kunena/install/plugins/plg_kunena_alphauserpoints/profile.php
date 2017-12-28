<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  AlphaUserPoints
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * KunenaActivityAlphaUserPoints class to handle integration with AlphaUserPoints
 *
 * @deprecated  5.0
 */
class KunenaProfileAlphaUserPoints extends KunenaProfile
{
	protected $params = null;

	/**
	 * KunenaProfileAlphaUserPoints constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param string $action
	 * @param bool   $xhtml
	 *
	 * @return bool
	 *
	 * @deprecated  5.0
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = JFactory::getUser();

		if ($config->userlist_allowed == 0 && $my->id == 0)
		{
			return false;
		}

		return AlphaUserPointsHelper::getAupUsersURL();
	}

	/**
	 * @param        $user
	 * @param string $task
	 * @param bool   $xhtml
	 *
	 * @return bool
	 *
	 * @deprecated  5.0
	 */
	public function getProfileURL($user, $task = '', $xhtml = true)
	{
		if ($user == 0)
		{
			return false;
		}

		$user = KunenaFactory::getUser($user);
		$my   = JFactory::getUser();

		if ($user === false)
		{
			return false;
		}

		$userid     = $my->id != $user->userid ? '&userid=' . AlphaUserPointsHelper::getAnyUserReferreID($user->userid) : '';
		$AUP_itemid = AlphaUserPointsHelper::getItemidAupProfil();

		return JRoute::_('index.php?option=com_alphauserpoints&view=account' . $userid . '&Itemid=' . $AUP_itemid, $xhtml);
	}

	/**
	 * @param int $limit
	 *
	 * @return array
	 *
	 * @deprecated  5.0
	 */
	public function _getTopHits($limit = 0)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT a.userid AS id, a.profileviews AS count
			FROM #__alpha_userpoints AS a
			INNER JOIN #__users AS u ON u.id=a.userid
			WHERE a.profileviews>0
			ORDER BY a.profileviews DESC";
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
	 * @deprecated  5.0
	 */
	public function showProfile($view, &$params)
	{
	}

	/**
	 * @param      $userid
	 * @param bool $xhtml
	 *
	 * @return bool
	 *
	 * @deprecated  5.0
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		return $this->getProfileURL($userid, '', $xhtml);
	}
}
