<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Integration;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaSvgIcons;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\User\KunenaUser;
use StdClass;

/**
 * Class \Kunena\Forum\Libraries\Integration\Avatar
 *
 * @since   Kunena 6.0
 */
class KunenaAvatar
{
	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected static $instance = false;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $avatarSizes = null;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $css = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $resize = false;

	/**
	 * @param   null  $integration  integration
	 *
	 * @return  boolean|KunenaAvatar
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			PluginHelper::importPlugin('kunena');

			$classes = Factory::getApplication()->triggerEvent('onKunenaGetAvatar');

			foreach ($classes as $class)
			{
				if (!\is_object($class))
				{
					continue;
				}

				self::$instance = $class;
				break;
			}

			if (!self::$instance)
			{
				self::$instance = new self;
			}
		}

		return self::$instance;
	}

	/**
	 * @param   array  $userlist  userlist
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function load(array $userlist): void
	{
	}

	/**
	 * @return  string
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function getEditURL(): string
	{
		return '';
	}

	/**
	 * @param   KunenaUser  $user   user
	 * @param   string      $class  class
	 * @param   int         $sizex  sizex
	 * @param   int         $sizey  sizey
	 *
	 * @return  false|string
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function getLink(KunenaUser $user, $class = 'kavatar', $sizex = 90, $sizey = 90)
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

		$ktemplate     = KunenaFactory::getTemplate();
		$topicicontype = $ktemplate->params->get('topicicontype');

		if ($topicicontype == 'svg')
		{
			if ($avatar == Uri::root() . 'media/kunena/core/svg/person.svg')
			{
				$link = '<span ' . $class . ' title="' . Text::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()) . '">'
					. KunenaSvgIcons::loadsvg('person') . '</span>';
			}
			else
			{
				$link = '<span' . $class . ' title="' . Text::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()) . '">
				<img alt="" loading=lazy src="' . $avatar . '" width="' . $size->x . '" height="' . $size->y . '"></span>';
			}
		}
		else
		{
			$link = '<img loading=lazy' . $class . ' src="' . $avatar . '" width="' . $size->x . '" height="' . $size->y . '"
			  alt="' . Text::sprintf('COM_KUNENA_LIB_AVATAR_TITLE', $user->getName()) . '" />';
		}

		return $link;
	}

	/**
	 * @param   int  $sizex  sizex
	 * @param   int  $sizey  sizey
	 *
	 * @return  StdClass
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getSize($sizex = 90, $sizey = 90): StdClass
	{
		$size    = new StdClass;
		$size->x = \intval($sizex);
		$size->y = \intval($sizey);

		if (!\intval($sizex))
		{
			$template = KunenaFactory::getTemplate();
			$name     = ucfirst(strtolower($sizex));
			$size->x  = \intval($template->params->get('avatarSizeX' . $name, 90));
			$size->y  = \intval($template->params->get('avatarSizeY' . $name, 90));
		}

		return $size;
	}

	/**
	 * @param   KunenaUser  $user   user
	 * @param   int         $sizex  sizex
	 * @param   int         $sizey  sizey
	 *
	 * @return string
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public function getURL(KunenaUser $user, $sizex = 90, int $sizey = 90): string
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$size = $this->getSize($sizex, $sizey);

		if (!$size->x || !$size->y)
		{
			return false;
		}

		$result = $this->_getURL($user, $size->x, $size->y);
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $result;
	}

	/**
	 * @param   KunenaUser  $user   user
	 * @param   integer     $sizex  sizex
	 * @param   integer     $sizey  sizey
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
	{
		return '';
	}
}
