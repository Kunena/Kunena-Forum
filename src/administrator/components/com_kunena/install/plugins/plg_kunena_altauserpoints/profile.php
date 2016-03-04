<?php
/**
 * Kunena Plugin
 *
 * @package       Kunena.Plugins
 * @subpackage    AltaUserPoints
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaProfileAltaUserPoints extends KunenaProfile
{
	protected $params = null;

	public function __construct($params)
	{
		$this->params = $params;
	}

	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = JFactory::getUser();

		if ($config->userlist_allowed == 1 && $my->id == 0)
		{
			return false;
		}

		return AltaUserPointsHelper::getAupUsersURL();
	}

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
			return false;
		}

		return $top;
	}

	public function showProfile($view, &$params)
	{
	}

	public function getEditProfileURL($userid, $xhtml = true)
	{
		return $this->getProfileURL($userid, '', $xhtml);
	}
}
