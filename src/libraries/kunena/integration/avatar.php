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
 * Class KunenaAvatar
 * @since Kunena
 */
class KunenaAvatar
{
	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected static $instance = false;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $avatarSizes = null;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $resize = false;

	/**
	 * @param   null $integration
	 *
	 * @return boolean|KunenaAvatar
	 * @since Kunena
	 */
	public static function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

			$classes    = \JFactory::getApplication()->triggerEvent('onKunenaGetAvatar');

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
				self::$instance = new KunenaAvatar;
			}
		}

		return self::$instance;
	}

	/**
	 * @param $userlist
	 *
	 * @since Kunena
	 */
	public function load($userlist)
	{

	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getEditURL()
	{
		return '';
	}

	/**
	 * @param          $user
	 * @param   string $class
	 * @param   int    $sizex
	 * @param   int    $sizey
	 *
	 * @return string|void
	 * @throws Exception
	 * @since Kunena
	 */
	public function getLink($user, $class = 'kavatar', $sizex = 90, $sizey = 90)
	{
		$size   = $this->getSize($sizex, $sizey);
		$avatar = $this->getURL($user, $size->x, $size->y);

		if (!$avatar)
		{
			return;
		}

		if ($class == 'none')
		{
			$class = ' class="kavatar"';
		}
		elseif ($class)
		{
			$class = ' class="' . $class . '"';
		}

		$link = '<img' . $class . ' src="' . $avatar . '" width="' . $size->x . '" height="' . $size->y . '"  alt="' . JText::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()) . '" />';

		return $link;
	}

	/**
	 * @param   int $sizex
	 * @param   int $sizey
	 *
	 * @return StdClass
	 * @throws Exception
	 * @since Kunena
	 */
	public function getSize($sizex = 90, $sizey = 90)
	{
		$size    = new StdClass;
		$size->x = intval($sizex);
		$size->y = intval($sizey);

		if (!intval($sizex))
		{
			$template = KunenaFactory::getTemplate();
			$name     = ucfirst(strtolower($sizex));
			$size->x  = intval($template->params->get('avatarSizeX' . $name, 90));
			$size->y  = intval($template->params->get('avatarSizeY' . $name, 90));
		}

		return $size;
	}

	/**
	 * @param       $user
	 * @param   int $sizex
	 * @param   int $sizey
	 *
	 * @return string|void
	 * @throws Exception
	 * @since Kunena
	 */
	public function getURL($user, $sizex = 90, $sizey = 90)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$size = $this->getSize($sizex, $sizey);

		if (!$size->x || !$size->y)
		{
			return;
		}

		$result = $this->_getURL($user, $size->x, $size->y);
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $result;
	}

	/**
	 * @param $user
	 * @param $sizex
	 * @param $sizey
	 *
	 * @return string
	 * @since Kunena
	 */
	protected function _getURL($user, $sizex, $sizey)
	{
		return '';
	}
}
