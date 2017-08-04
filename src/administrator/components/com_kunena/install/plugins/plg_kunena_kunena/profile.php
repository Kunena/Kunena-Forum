<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Kunena
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaProfileKunena
 * @since Kunena
 */
class KunenaProfileKunena extends KunenaProfile
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
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

		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
	}

	/**
	 * @param          $user
	 * @param   string $task
	 * @param   bool   $xhtml
	 * @param   bool   $avatarTab
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function getProfileURL($user, $task = '', $xhtml = true, $avatarTab = '')
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
				return KunenaRoute::_("index.php?option=com_kunena&view=user{$layout}{$userid}{$avatarTab}", $xhtml);
			}
			else
			{
				return KunenaRoute::getUserUrl($user, $xhtml);
			}
		}
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
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('u.id', 'ku.uhits'), array(null, 'count')));
		$query->from($db->quoteName(array('#__kunena_users'), array('ku')));
		$query->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('ku.userid'));
		$query->where($db->quoteName('ku.uhits') . '>0');
		$query->order($db->quoteName('ku.uhits') . ' DESC');

		if (KunenaFactory::getConfig()->superadmin_userlist)
		{
			$filter = \Joomla\CMS\Access\Access::getUsersByGroup(8);
			$query->where('u.id NOT IN (' . implode(',', $filter) . ')');
		}

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
	 * @return boolean
	 * @since Kunena
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		$avatartab = '&avatartab=1';

		return $this->getProfileURL($userid, 'edit', $xhtml = true, $avatartab);
	}
}
