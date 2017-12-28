<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaProfile
 */
class KunenaProfile
{
	protected static $instance = false;

	/**
	 * @param   null $integration
	 *
	 * @return boolean|KunenaProfile
	 */
	static public function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			JPluginHelper::importPlugin('kunena');
			$dispatcher = JEventDispatcher::getInstance();
			$classes = $dispatcher->trigger('onKunenaGetProfile');

			foreach ($classes as $class)
			{
				if (!is_object($class))
				{
					continue;
				}

				self::$instance = $class;
				break;
			}

			if (!self::$instance)
			{
				self::$instance = new KunenaProfile();
			}
		}

		return self::$instance;
	}

	/**
	 * @param   int $limit
	 *
	 * @return array
	 */
	public function getTopHits($limit = 0)
	{
		if (!$limit)
		{
			$limit = KunenaFactory::getConfig()->popusercount;
		}

		return (array) $this->_getTopHits($limit);
	}

	/**
	 * @param   string $action
	 * @param   bool   $xhtml
	 *
	 * @return boolean
	 */
	public function getStatisticsURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my = JFactory::getUser();

		if ($config->statslink_allowed == 0 && $my->id == 0)
		{
			return false;
		}

		return KunenaRoute::_('index.php?option=com_kunena&view=statistics' . $action, $xhtml);
	}

	/**
	 * @param   string $action
	 * @param   bool   $xhtml
	 */
	public function getUserListURL($action='', $xhtml = true)
	{

	}

	/**
	 * @param        $user
	 * @param   string $task
	 * @param   bool   $xhtml
	 */
	public function getProfileURL($user, $task='', $xhtml = true)
	{

	}

	/**
	 * @param $view
	 * @param $params
	 */
	public function showProfile($view, &$params)
	{

	}

	/**
	 * @param   int $limit
	 *
	 * @return array
	 */
	protected function _getTopHits($limit=0)
	{
		return array();
	}

	/**
	 * @param      $userid
	 * @param   bool $xhtml
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{

	}
}
