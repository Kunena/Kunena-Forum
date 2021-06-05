<?php
/**
 * Kunena Plugin
 *
 * @package          Kunena.Plugins
 * @subpackage       Community
 *
 * @copyright   (C)  2008 - 2021 Kunena Team. All rights reserved.
 * @copyright   (C)  2013 - 2014 iJoomla, Inc. All rights reserved.
 * @license          https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link             https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Community;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaUser;
use RuntimeException;
use function defined;

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
	 * @since   Kunena 6.0
	 */
	public function __construct(object $params)
	{
		$this->params = $params;
	}

	/**
	 * @param   string  $action  action
	 * @param   bool    $xhtml   xhtml
	 *
	 * @return  boolean|string
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function getUserListURL($action = '', $xhtml = true): string
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
	 * @since   Kunena 6.0
	 *
	 */
	public function _getTopHits($limit = 0): array
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
	 * @param   KunenaLayout  $view    view
	 * @param   object        $params  params
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function showProfile(KunenaLayout $view, object $params)
	{
	}

	/**
	 * @param   int   $userid
	 * @param   bool  $xhtml  xhtml
	 *
	 * @return  boolean|string
	 * @since   Kunena 6.0
	 */
	public function getEditProfileURL(int $userid, $xhtml = true): bool
	{
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}

	/**
	 * @param   KunenaUser  $user
	 * @param   string      $task   task
	 * @param   bool        $xhtml  xhtml
	 *
	 * @return  boolean|string
	 * @since   Kunena 6.0
	 */
	public function getProfileURL(KunenaUser $user, $task = '', $xhtml = true): bool
	{
		// Make sure that user profile exist.
		if (!$user || CFactory::getUser($user) === null)
		{
			return false;
		}

		return CRoute::_('index.php?option=com_community&view=profile&userid=' . (int) $user, $xhtml);
	}

	/**
	 * Return username of user
	 *
	 * @param           $user
	 * @param   string  $visitorname
	 * @param   bool    $escape
	 *
	 * @return string
	 * @since Kunena 5.2
	 */
	public function getProfileName($user, $visitorname = '', $escape = true)
	{
		$cconfig         = CFactory::getConfig();
		$displayusername = $cconfig->get('displayname');

		if ($displayusername == 'name')
		{
			return CFactory::getUser($user->id)->name;
		}
		else
		{
			return CFactory::getUser($user->id)->username;
		}
	}
}
