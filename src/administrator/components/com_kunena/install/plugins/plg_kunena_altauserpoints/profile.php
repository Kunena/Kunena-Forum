<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  AltaUserPoints
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * KunenaActivityAltaUserPoints class to handle profile integration with AltaUserPoints
 *
 * @since  5.0
 */
class KunenaProfileAltaUserPoints extends KunenaProfile
{
	protected $params = null;

	/**
	 * KunenaProfileAltaUserPoints constructor.
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
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = JFactory::getUser();

		if ($config->userlist_allowed == 0 && $my->id == 0)
		{
			return false;
		}

		return AltaUserPointsHelper::getAupUsersURL();
	}

	/**
	 * @param        $user
	 * @param string $task
	 * @param bool   $xhtml
	 *
	 * @return bool
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

		$userid     = $my->id != $user->userid ? '&userid=' . AltaUserPointsHelper::getAnyUserReferreID($user->userid) : '';
		$AUP_itemid = AltaUserPointsHelper::getItemidAupProfil();

		return JRoute::_('index.php?option=com_altauserpoints&view=account' . $userid . '&Itemid=' . $AUP_itemid, $xhtml);
	}

	/**
	 * @param int $limit
	 *
	 * @return array|bool
	 */
	public function _getTopHits($limit = 0)
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select($db->quoteName(array('u.*', 'ju.username', 'ju.email', 'ju.lastvisitDate'), array(null, null, 'last_login')))
			->from('#__alpha_userpoints AS a')
			->innerJoin('#__users AS u ON u.id=a.userid')
			->where('a.profileviews>0')
			->order('a.profileviews DESC');

		$db->setQuery($query, 0, $limit);

		try
		{
			$top = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			return false;
		}

		return $top;
	}

	/**
	 * @param $view
	 * @param $params
	 */
	public function showProfile($view, &$params)
	{
	}

	/**
	 * @param      $userid
	 * @param bool $xhtml
	 *
	 * @return bool
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		return $this->getProfileURL($userid, '', $xhtml);
	}
}
