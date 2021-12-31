<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyblog
 *
 * @copyright   (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

/**
 * @package     Kunena
 *
 * @since       Kunena
 */
class KunenaProfileEasyblog extends KunenaProfile
{
	protected $params = null;

	/**
	 * KunenaProfileEasyblog constructor.
	 *
	 * @param $params
	 *
	 * @since K2.0
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
	 * @since K2.0
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

		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
	}

	/**
	 * @param          $userid
	 * @param   string $task  task
	 * @param   bool   $xhtml xhtml
	 *
	 * @return boolean
	 * @since K2.0
	 */
	public function getProfileURL($userid, $task = '', $xhtml = true)
	{
		// Make sure that user profile exist.
		if (!$userid)
		{
			return false;
		}

		return Route::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $userid, false);
	}

	/**
	 * @param $view
	 * @param $params
	 *
	 * @since K2.0
	 */
	public function showProfile($view, &$params)
	{
	}

	/**
	 * @param        $userid
	 * @param   bool $xhtml xhtml
	 *
	 * @return boolean
	 * @since K2.0
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}
}
