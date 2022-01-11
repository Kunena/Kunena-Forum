<?php
/**
 * Kunena Plugin
 *
 * @package          Kunena.Plugins
 * @subpackage       Community
 *
 * @copyright   (C)  2008 - 2022 Kunena Team. All rights reserved.
 * @copyright   (C)  2013 - 2014 iJoomla, Inc. All rights reserved.
 * @license          https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link             https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Layout\KunenaLayout;

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
	 * @param   object  $params  params
	 *
	 * @since   Kunena 5.0
	 */
	public function __construct(object $params)
	{
		$this->params = $params;
	}

	/**
	 * @param   string  $action  action
	 * @param   bool    $xhtml   xhtml
	 *
	 * @return string
	 * @since   Kunena 5.0
	 * @throws \Exception
	 */
	public function getUserListURL(string $action = '', bool $xhtml = true): string
	{
		$config = KunenaFactory::getConfig();
		$my     = Factory::getApplication()->getIdentity();

		if ($config->userlistAllowed == 0 && $my->id == 0)
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
	 * @throws  Exception
	 *
	 * @since   Kunena 5.0
	 */
	public function getTopHits(int $limit = 0): array
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
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
	 * @param   KunenaLayout  $view    view
	 * @param   object        $params  params
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.0
	 */
	public function showProfile(KunenaLayout $view, object $params)
	{
	}

	/**
	 * @param   int   $userid  userid
	 * @param   bool  $xhtml   xhtml
	 *
	 * @return bool
	 *
	 * @since   Kunena 5.0
	 */
	public function getEditProfileURL(int $userid, bool $xhtml = true): bool
	{
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}

	/**
	 * @param   int     $userid     userid
	 * @param   string  $task       task
	 * @param   bool    $xhtml      xhtml
	 * @param   string  $avatarTab  avatarTab
	 *
	 * @return  boolean|string
	 *
	 * @since   Kunena 5.0
	 */
	public function getProfileURL(int $userid, string $task = '', bool $xhtml = true, string $avatarTab = '')
	{
		// Make sure that user profile exist.
		if (!$userid || CFactory::getUser($userid) === null)
		{
			return false;
		}

		return CRoute::_('index.php?option=com_community&view=profile&userid=' . (int) $userid, $xhtml);
	}

	/**
	 * Return username of user
	 *
	 * @param   int     $userid       userid
	 * @param   string  $visitorname  name
	 * @param   bool    $escape       escape
	 *
	 * @return string
	 *
	 * @since Kunena 5.2
	 */
	public function getProfileName(int $userid, $visitorname = '', $escape = true)
	{
		$cconfig         = CFactory::getConfig();
		$displayusername = $cconfig->get('displayname');

		if ($displayusername == 'name')
		{
			return CFactory::getUser($userid)->name;
		}
		else
		{
			return CFactory::getUser($userid)->username;
		}
	}
}
