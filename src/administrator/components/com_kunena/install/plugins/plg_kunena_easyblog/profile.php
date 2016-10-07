<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyblog
 *
 * @copyright       Copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

class KunenaProfileEasyblog extends KunenaProfile
{
	protected $params = null;

	/**
	 * KunenaProfileEasyblog constructor.
	 *
	 * @param $params
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
		$my     = JFactory::getUser();

		if ($config->userlist_allowed == 1 && $my->id == 0)
		{
			return false;
		}

		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
	}

	/**
	 * @param          $userid
	 * @param   string $task
	 * @param   bool   $xhtml
	 *
	 * @return boolean
	 * @since Kunena
 	 */
	public function getProfileURL($userid, $task = '', $xhtml = true)
	{
		// Make sure that user profile exist.
		if (!$userid)
		{
			return false;
		}

		return JRoute::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $userid, false);
	}

	/**
	 * @param $view
	 * @param $params
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
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}
}

