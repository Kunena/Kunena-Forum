<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      AltaUserPoints
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * KunenaActivityAltaUserPoints class to handle profile integration with AltaUserPoints
 *
 * @since  5.0
 */
class KunenaProfileAltaUserPoints extends KunenaProfile
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaProfileAltaUserPoints constructor.
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
	 * @return boolean
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

		return AltaUserPointsHelper::getAupUsersURL();
	}

	/**
	 * @param   int $limit
	 *
	 * @return array|boolean
	 * @since Kunena
	 */
	public function _getTopHits($limit = 0)
	{
		$db    = \Joomla\CMS\Factory::getDBO();
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
	 * @return boolean
	 * @since Kunena
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		return $this->getProfileURL($userid, '', $xhtml);
	}

	/**
	 * @param          $user
	 * @param   string $task
	 * @param   bool   $xhtml
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function getProfileURL($user, $task = '', $xhtml = true)
	{
		if ($user == 0)
		{
			return false;
		}

		$user = KunenaFactory::getUser($user);
		$my   = \Joomla\CMS\Factory::getUser();

		if ($user === false)
		{
			return false;
		}

		$userid     = $my->id != $user->userid ? '&userid=' . AltaUserPointsHelper::getAnyUserReferreID($user->userid) : '';
		$AUP_itemid = AltaUserPointsHelper::getItemidAupProfil();

		return JRoute::_('index.php?option=com_altauserpoints&view=account' . $userid . '&Itemid=' . $AUP_itemid, $xhtml);
	}
}
