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

/**
 * Class KunenaAvatarKunena
 * @since Kunena
 */
class KunenaAvatarKunena extends KunenaAvatar
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * @var null
	 * @since Kunena
	 */
	public $css = true;

	/**
	 * @param $params
	 *
	 * @since Kunena
	 */
	public function __construct($params)
	{
		$this->params = $params;
		$this->resize = true;
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getEditURL()
	{
		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=edit');
	}

	/**
	 * @param $user
	 * @param $sizex
	 * @param $sizey
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	protected function _getURL($user, $sizex, $sizey)
	{
		$user   = KunenaFactory::getUser($user);
		$avatar = $user->avatar;
		$config = KunenaFactory::getConfig();

		$path     = KPATH_MEDIA . "/avatars";
		$origPath = "{$path}/{$avatar}";

		if (!is_file($origPath))
		{
			// If avatar does not exist use default image.
			if ($sizex <= 90)
			{
				$avatar = KunenaConfig::getInstance()->defaultavatarsmall;
			}
			else
			{
				$avatar = KunenaConfig::getInstance()->defaultavatar;
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
			KunenaImageHelper::version($origPath, "{$path}/{$resized}", $file, $sizex,
				$sizey, intval($config->avatarquality), KunenaImage::SCALE_INSIDE, intval($config->avatarcrop)
			);

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
