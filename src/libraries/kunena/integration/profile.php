<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaProfile
 * @since Kunena
 */
class KunenaProfile
{
	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected static $instance = false;

	/**
	 * @param   null $integration
	 *
	 * @return boolean|KunenaProfile
	 * @since Kunena
	 */
	public static function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

			$classes    = \JFactory::getApplication()->triggerEvent('onKunenaGetProfile');

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
				self::$instance = new KunenaProfile;
			}
		}

		return self::$instance;
	}

	/**
	 * @param   int $limit
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
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
	 * @param   int $limit
	 *
	 * @return array
	 * @since Kunena
	 */
	protected function _getTopHits($limit = 0)
	{
		return array();
	}

	/**
	 * @param   string $action
	 * @param   bool   $xhtml
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getStatisticsURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = \Joomla\CMS\Factory::getUser();

		if ($config->statslink_allowed == 0 && $my->id == 0)
		{
			return false;
		}

		return KunenaRoute::_('index.php?option=com_kunena&view=statistics' . $action, $xhtml);
	}

	/**
	 * @param   string $action
	 * @param   bool   $xhtml
	 *
	 * @since Kunena
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{

	}

	/**
	 * @param          $user
	 * @param   string $task
	 * @param   bool   $xhtml
	 *
	 * @since Kunena
	 */
	public function getProfileURL($user, $task = '', $xhtml = true)
	{

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
	 * @since Kunena
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{

	}
}
