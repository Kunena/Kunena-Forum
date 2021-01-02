<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyprofile
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Easyprofile;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use function defined;

/**
 * Class KunenaProfileEasyprofile
 *
 * @since   Kunena 6.0
 */
class KunenaProfileEasyprofile extends KunenaProfile
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaProfileEasyprofile constructor.
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
	 * @return string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function getUserListURL($action = '', $xhtml = true): string
	{
		$config = KunenaFactory::getConfig();
		$my     = Factory::getApplication()->getIdentity();

		if ($config->userlist_allowed == 0 && $my->id == 0)
		{
			return false;
		}

		if ($this->params->get('userlist', 0) == 0)
		{
			return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
		}

		return Route::_('index.php?option=com_jsn&view=list&Itemid=' . $this->params->get('menuitem', ''), false);
	}

	/**
	 * @param   int     $view    view
	 * @param   object  $params  params
	 *
	 * @return   void
	 *
	 * @since   Kunena 6.0
	 */
	public function showProfile(int $view, object $params): void
	{
	}

	/**
	 * @param   int   $userid  userid
	 * @param   bool  $xhtml   xhtml
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function getEditProfileURL(int $userid, $xhtml = true): bool
	{
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}

	/**
	 * @param   int     $user   userid
	 * @param   string  $task   task
	 * @param   bool    $xhtml  xhtml
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function getProfileURL(int $user, $task = '', $xhtml = true): bool
	{
		// Make sure that user profile exist.
		if (!$user || JsnHelper::getUser($user) === null)
		{
			return false;
		}

		$user = JsnHelper::getUser($user);

		return $user->getLink();
	}
}
