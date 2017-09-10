<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      AlphaUserPoints
 *
 * @copyright       Copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * KunenaActivityAlphaUserPoints class to handle integration with AlphaUserPoints
 *
 * @deprecated  6.0
 * @since       Kunena
 */
class KunenaProfileAlphaUserPoints extends KunenaProfile
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaProfileAlphaUserPoints constructor.
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
	 *
	 * @throws Exception
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = \Joomla\CMS\Factory::getUser();

		if ($config->userlist_allowed == 1 && $my->id == 0)
		{
			return false;
		}

		return AlphaUserPointsHelper::getAupUsersURL();
	}

	/**
	 * @param   int $limit
	 *
	 * @return array
	 *
	 * @throws Exception
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	public function _getTopHits($limit = 0)
	{
		$db    = \Joomla\CMS\Factory::getDBO();
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
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	public function showProfile($view, &$params)
	{
	}

	/**
	 * @param        $userid
	 * @param   bool $xhtml
	 *
	 * @return boolean
	 *
	 * @deprecated  6.0
	 * @since       Kunena
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
	 *
	 * @deprecated  6.0
	 * @since       Kunena
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

		$userid     = $my->id != $user->userid ? '&userid=' . AlphaUserPointsHelper::getAnyUserReferreID($user->userid) : '';
		$AUP_itemid = AlphaUserPointsHelper::getItemidAupProfil();

		return JRoute::_('index.php?option=com_alphauserpoints&view=account' . $userid . '&Itemid=' . $AUP_itemid, $xhtml);
	}
}
