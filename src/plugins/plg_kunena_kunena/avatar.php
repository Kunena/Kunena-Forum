<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Kunena
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Image\KunenaImage;
use Kunena\Forum\Libraries\Image\KunenaImageHelper;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class KunenaAvatar
 *
 * @since   Kunena 6.0
 */
class KunenaAvatarKunena extends KunenaAvatar
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $css = true;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * @param   object  $params  params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(object $params)
	{
		$this->params = $params;
		$this->resize = true;
	}

	/**
	 * @return string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function getEditURL(): string
	{
		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=edit');
	}

	/**
	 * @param   KunenaUser  $user   user
	 * @param   int         $sizex  sizex
	 * @param   int         $sizey  sizey
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
	{
		$user   = KunenaFactory::getUser($user);
		$avatar = $user->avatar;
		$config = KunenaFactory::getConfig();

		if (empty($avatar))
		{
			return KURL_MEDIA . "core/svg/person.svg";
		}

		$path     = KPATH_MEDIA . "/avatars";
		$origPath = "{$path}/{$avatar}";

		if (!is_file($origPath))
		{
			// If avatar does not exist use default image.
			if ($sizex <= 90)
			{
				$avatar = KunenaConfig::getInstance()->defaultAvatarSmall;
			}
			else
			{
				$avatar = KunenaConfig::getInstance()->defaultAvatar;
			}

			// Search from the template.
			$template = KunenaFactory::getTemplate();
			$origPath = JPATH_SITE . '/' . $template->getAvatarPath($avatar);
			$avatar   = $template->name . '/' . $avatar;
		}

		$dir  = dirname($avatar);
		$file = basename($avatar);

		if ($sizex == $sizey)
		{
			$resized = "resized/size{$sizex}/{$dir}";
		}
		else
		{
			$resized = "resized/size{$sizex}x{$sizey}/{$dir}";
		}

		if ($user->timestamp)
		{
			$timestamp = '?' . $user->timestamp;
		}
		else
		{
			$timestamp = '';
		}

		if (!is_file("{$path}/{$resized}/{$file}"))
		{
			try
			{
				KunenaImageHelper::version(
					$origPath,
					"{$path}/{$resized}",
					$file,
					$sizex,
					$sizey,
					intval($config->avatarQuality),
					KunenaImage::SCALE_INSIDE,
					intval($config->avatarCrop)
				);
			}
			catch (Exception $e)
			{
				KunenaError::error($e->getMessage());
			}

			if ($user->timestamp)
			{
				$timestamp = '?' . $user->timestamp;
			}
			else
			{
				$timestamp = '?' . round(microtime(true));
			}
		}

		return KURL_MEDIA . "avatars/{$resized}/{$file}{$timestamp}";
	}
}
