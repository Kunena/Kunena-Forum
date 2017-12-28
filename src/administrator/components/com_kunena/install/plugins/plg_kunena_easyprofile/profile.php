<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Easyprofile
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaProfileEasyprofile extends KunenaProfile
{
	protected $params = null;

	/**
	 * KunenaProfileEasyprofile constructor.
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
		elseif ($this->params->get('userlist', 0) == 0)
		{
			return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
		}
		else
		{
			return JRoute::_('index.php?option=com_jsn&view=list&Itemid=' . $this->params->get('menuitem', ''), false);
		}
	}

	/**
	 * @param        $userid
	 * @param string $task
	 * @param bool   $xhtml
	 *
	 * @return bool
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
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}
}

