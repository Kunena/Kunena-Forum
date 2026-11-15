<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Kunena
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Kunena\Helper;

\defined('_JEXEC') or die();

use Exception;
use Joomla\Registry\Registry;
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
     * @var     boolean
     * @since   Kunena 6.0
     */
    public bool $css = \true;

    /**
     * @param   Registry  $params  params
     *
     * @since   Kunena 6.0
     */
    public function __construct(Registry $params)
    {
        $this->params = $params;
        $this->resize = \true;
    }

    /**
     * Function to get the link to the User edit page
     *
     * @return  string
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getEditURL(): string
    {
        return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=edit');
    }

    /**
     * Function to get the Avatar image for the user
     *
     * @param   KunenaUser  $user   user
     * @param   int         $sizex  sizex
     * @param   int         $sizey  sizey
     *
     * @return  string
     * @since   Kunena 6.0
     * @throws  Exception
     */
    protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
    {
        $user   = KunenaFactory::getUser($user);
        $avatar = $user->avatar;
        $config = KunenaFactory::getConfig();

        if (!empty($avatar)) {
            $path     = KPATH_MEDIA . "/avatars";
            $origPath = "{$path}/{$avatar}";

            if (!is_file($origPath)) {
                // If avatar does not exist use default image: the small one only when it has the pixels which are asked for (it is
                // 48 pixels, and was stretched up to 90, blurred).
                $template = KunenaFactory::getTemplate();
                $small    = KunenaConfig::getInstance()->defaultAvatarSmall;

                if (self::usesSmallDefault($sizex, self::imageWidth(JPATH_SITE . '/' . $template->getAvatarPath($small)))) {
                    $avatar = $small;
                } else {
                    $avatar = KunenaConfig::getInstance()->defaultAvatar;
                }

                // Search from the template.
                $origPath = JPATH_SITE . '/' . $template->getAvatarPath($avatar);
                $avatar   = $template->name . '/' . $avatar;
            } elseif (str_starts_with($avatar, 'users/') && strtolower(substr($avatar, -4)) === '.svg') {
                // An uploaded SVG avatar (cleaned on upload) is shown from its own file: GD cannot resize it.
                $timestamp = $user->timestamp ? '?' . $user->timestamp : '';

                return KURL_MEDIA . "avatars/{$avatar}{$timestamp}";
            }

            $dir  = \dirname($avatar);
            $file = basename($avatar);

            if ($sizex == $sizey) {
                $resized = "resized/size{$sizex}/{$dir}";
            } else {
                $resized = "resized/size{$sizex}x{$sizey}/{$dir}";
            }

            if ($user->timestamp) {
                $timestamp = '?' . $user->timestamp;
            } else {
                $timestamp = '';
            }

            if (!is_file("{$path}/{$resized}/{$file}")) {
                try {
                    KunenaImageHelper::version(
                        $origPath,
                        "{$path}/{$resized}",
                        $file,
                        $sizex,
                        $sizey,
                        \intval($config->avatarQuality),
                        KunenaImage::SCALE_INSIDE,
                        \intval($config->avatarCrop)
                    );
                } catch (Exception $e) {
                    KunenaError::error($e->getMessage());
                }

                if ($user->timestamp) {
                    $timestamp = '?' . $user->timestamp;
                } else {
                    $timestamp = '?' . round(microtime(true));
                }
            }

            return KURL_MEDIA . "avatars/{$resized}/{$file}{$timestamp}";
        }
        // When KunenaFactory::getConfig()->avatarType is true it means Icontype
        if ($config->avatarType) {
            return KURL_MEDIA . "core/svg/person.svg";
        }

        return KURL_MEDIA . 'avatars/' . $config->defaultAvatar;
    }

    /**
     * Whether the small default avatar is used for an avatar of this width: only when it is at least as wide (it is not stretched).
     * When its width is not known, the rule of before: up to 90 pixels.
     *
     * @param   int  $sizex       The width which is asked for.
     * @param   int  $smallWidth  The width of the small default avatar (0: unknown).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function usesSmallDefault(int $sizex, int $smallWidth): bool
    {
        return $smallWidth > 0 ? $sizex <= $smallWidth : $sizex <= 90;
    }

    /**
     * The width of an image file, read once for each file (0 when it cannot be read).
     *
     * @param   string  $file  The path.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    protected static function imageWidth(string $file): int
    {
        static $widths = [];

        if (!isset($widths[$file])) {
            $info          = is_file($file) ? @getimagesize($file) : false;
            $widths[$file] = $info ? (int) $info[0] : 0;
        }

        return $widths[$file];
    }
}
