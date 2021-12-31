<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyprofile
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

/**
 * Class KunenaProfileEasyprofile
 * @since Kunena
 */
class KunenaProfileEasyprofile extends KunenaProfile
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaProfileEasyprofile constructor.
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
	 * @param   string $action action
	 * @param   bool   $xhtml  xhtml
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = Factory::getUser();

		if ($config->userlist_allowed == 0 && $my->id == 0)
		{
			return false;
		}
		elseif ($this->params->get('userlist', 0) == 0)
		{
			return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
		}
		else
		{
			return Route::_('index.php?option=com_jsn&view=list&Itemid=' . $this->params->get('menuitem', ''), false);
		}
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
	 * @param   bool $xhtml xhtml
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}

	/**
	 * @param          $userid
	 * @param   string $task  task
	 * @param   bool   $xhtml xhtml
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function getProfileURL($userid, $task = '', $xhtml = true)
	{
		// Make sure that user profile exist.
		if (!$userid || JsnHelper::getUser($userid) === null)
		{
			return false;
		}

		$user = JsnHelper::getUser($userid);

		return $user->getLink();
	}
	
	/**
	 * Return username of user
	 *
	 * @param   integer $userid userid
	 * @param   bool    $xhtml  xhtml
	 *
	 * @since Kunena 5.2
	 * @return string
	 */
	public function getProfileName($user, $visitorname = '', $escape = true)
	{
		$config = JComponentHelper::getParams('com_jsn');
		$formatName = $config->get('formatname', 'NAME');

		if($formatName == 'NAME')
		{
			return JsnHelper::getUser($user->id)->name;
		}
		else if ($formatName == 'USERNAME')
		{
			return JsnHelper::getUser($user->id)->username;
		}
		else if ($formatName == 'NAMEUSERNAME')
		{
			return JsnHelper::getUser($user->id)->name . ' (' . JsnHelper::getUser($user->id)->username . ')';
		}
		else
		{
			return JsnHelper::getUser($user->id)->username . ' (' . JsnHelper::getUser($user->id)->name . ')';
		}
	}
}
