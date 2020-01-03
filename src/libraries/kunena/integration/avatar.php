<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Class KunenaAvatar
 *
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
	 * @var boolean
	 * @since Kunena
	 */
	public $css = false;

	/**
	 * @param   null  $integration  integration
	 *
	 * @return boolean|KunenaAvatar
	 * @since Kunena
	 * @throws Exception
	 */
	public static function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

			$classes = Factory::getApplication()->triggerEvent('onKunenaGetAvatar');

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
	 * @param   array  $userlist  userlist
	 *
	 * @return void
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
	 * @param   string  $user   user
	 * @param   string  $class  class
	 * @param   int     $sizex  sizex
	 * @param   int     $sizey  sizey
	 *
	 * @return string|void
	 * @since Kunena
	 * @throws Exception
	 */
	public function getLink($user, $class = 'kavatar', $sizex = 90, $sizey = 90)
	{
		$size   = $this->getSize($sizex, $sizey);
		$avatar = $this->getURL($user, $size->x, $size->y);

		if (!$avatar)
		{
			return false;
		}

		if ($class == 'none')
		{
			$class = ' class="kavatar"';
		}
		elseif ($class)
		{
			$class = ' class="' . $class . '"';
		}

		$ktemplate = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'B4')
		{
			if ($avatar == Uri::root() . 'media/kunena/core/svg/person.svg')
			{
				$link = '<span ' . $class . ' alt="' . Text::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()) . '">' . KunenaSvgIcons::loadsvg('person') . '</span>';
			}
			else
			{
				$link = '<span' . $class . ' alt="' . Text::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()) . '"><img src="' . $avatar . '" width="' . $size->x . '" height="' . $size->y . '"></span>';
			}
		}
		else
		{
			$link = '<img' . $class . ' src="' . $avatar . '" width="' . $size->x . '" height="' . $size->y . '"  alt="' . Text::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()) . '" />';
		}

		return $link;
	}

	/**
	 * @param   int  $sizex  sizex
	 * @param   int  $sizey  sizey
	 *
	 * @return StdClass
	 * @since Kunena
	 * @throws Exception
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
	 * @param   string  $user   user
	 * @param   int     $sizex  sizex
	 * @param   int     $sizey  sizey
	 *
	 * @return string|void
	 * @since Kunena
	 * @throws Exception
	 */
	public function getURL($user, $sizex = 90, $sizey = 90)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$size = $this->getSize($sizex, $sizey);

		if (!$size->x || !$size->y)
		{
			return false;
		}

		$result = $this->_getURL($user, $size->x, $size->y);
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $result;
	}

	/**
	 * @param   string   $user   user
	 * @param   integer  $sizex  sizex
	 * @param   integer  $sizey  sizey
	 *
	 * @return string
	 * @since Kunena
	 */
	protected function _getURL($user, $sizex, $sizey)
	{
		return '';
	}
}
