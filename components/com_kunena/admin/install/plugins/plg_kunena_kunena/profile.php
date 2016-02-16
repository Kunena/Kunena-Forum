<?php
/**
 * Kunena Plugin
 *
 * @package       Kunena.Plugins
 * @subpackage    Kunena
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaProfileKunena extends KunenaProfile
{
	protected $params = null;

	/**
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

		if ($config->userlist_allowed == 1 && $my->id == 0)
		{
			return false;
		}

		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
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

		if (!($user instanceof KunenaUser))
		{
			$user = KunenaUserHelper::get($user);
		}

		if ($user === false)
		{
			return false;
		}

		$userid = "&userid={$user->userid}";

		if ($task && $task != 'edit')
		{
			// TODO: remove in the future.
			$do = $task ? '&do=' . $task : '';

			return KunenaRoute::_("index.php?option=com_kunena&func=profile{$do}{$userid}", $xhtml);
		}
		else
		{
			$layout = $task ? '&layout=' . $task : '';

			if ($layout)
			{
				return KunenaRoute::_("index.php?option=com_kunena&view=user{$layout}{$userid}", $xhtml);
			}
			else
			{
				return KunenaRoute::getUserUrl($user, $xhtml);
			}
		}
	}

	/**
	 * @param int $limit
	 *
	 * @return array
	 */
	public function _getTopHits($limit = 0)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT u.id, ku.uhits AS count
			FROM #__kunena_users AS ku
			INNER JOIN #__users AS u ON u.id=ku.userid
			WHERE ku.uhits>0
			ORDER BY ku.uhits DESC";
		$db->setQuery($query, 0, $limit);
		$top = (array) $db->loadObjectList();
		KunenaError::checkDatabaseError();

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
		return $this->getProfileURL($userid, 'edit', $xhtml = true);
	}
}
