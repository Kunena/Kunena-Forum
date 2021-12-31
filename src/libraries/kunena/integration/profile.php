<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

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
	 * @var boolean
	 * @since Kunena 5.2
	 */
	public $enabled = true;

	/**
	 * @param   null $integration integration
	 *
	 * @return boolean|KunenaProfile
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

			$classes = Factory::getApplication()->triggerEvent('onKunenaGetProfile');

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
				self::$instance->enabled = false;
			}
		}

		return self::$instance;
	}

	/**
	 * @param   int $limit limit
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
	 * @param   int $limit limit
	 *
	 * @return array
	 * @since Kunena
	 */
	protected function _getTopHits($limit = 0)
	{
		return array();
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
	public function getStatisticsURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = Factory::getUser();

		if ($config->statslink_allowed == 0 && $my->id == 0)
		{
			return false;
		}

		return KunenaRoute::_('index.php?option=com_kunena&view=statistics' . $action, $xhtml);
	}

	/**
	 * @param   string $action action
	 * @param   bool   $xhtml  xhtml
	 *
	 * @since Kunena
	 * @return void
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
	}

	/**
	 * @param   string $user  user
	 * @param   string $task  task
	 * @param   bool   $xhtml xhtml
	 *
	 * @since Kunena
	 * @return void
	 */
	public function getProfileURL($user, $task = '', $xhtml = true)
	{
	}

	/**
	 * @param   int   $view   view
	 * @param   mixed $params params
	 *
	 * @since Kunena
	 * @return void
	 */
	public function showProfile($view, &$params)
	{
	}

	/**
	 * @param   integer $userid userid
	 * @param   bool    $xhtml  xhtml
	 *
	 * @since Kunena
	 * @return void
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
	}
	
	/**
	 * @param   integer $userid userid
	 * @param   bool    $xhtml  xhtml
	 *
	 * @since Kunena
	 * @return void
	 */
	public function getProfileName($user, $visitorname = '', $escape = true)
	{
	}
}
