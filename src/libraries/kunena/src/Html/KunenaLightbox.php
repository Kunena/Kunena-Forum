<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Html
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Html;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * The lightbox of the images of the messages (the option "Enable Lightbox for Images"): the attributes of the link of an image.
 *
 * - Yes: the lightbox of Kunena (media/kunena/core/js/lightbox.js, loaded by the layout Widget/Lightbox) opens the links with
 *   data-kunena-lightbox; the links of the same group are the images it goes through.
 * - Use existing one: Kunena loads no script, the links get the attributes of Fancybox for the lightbox of the site.
 * - No: the link opens the image itself.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaLightbox
{
    /**
     * No lightbox.
     *
     * @since   Kunena 8.0
     */
    public const int NONE = 0;

    /**
     * The lightbox of Kunena.
     *
     * @since   Kunena 8.0
     */
    public const int KUNENA = 1;

    /**
     * The lightbox of the site (Fancybox), which Kunena does not load.
     *
     * @since   Kunena 8.0
     */
    public const int EXISTING = 2;

    /**
     * The attributes of the link of an image, with a space in front (for the tag), or an empty string without a lightbox.
     *
     * @param   string       $group     The images the lightbox goes through: the links of the page with the same group.
     * @param   string|null  $download  The address which the download button of the lightbox of Kunena takes (an attachment: the
     *                                  address which the forum sends as a file), not escaped; without it, the address of the link.
     * @param   string|null  $filename  The name of the downloaded file (the name of the attachment as it was uploaded).
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function attributes(string $group = 'gallery', ?string $download = null, ?string $filename = null): string
    {
        $escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        switch (self::mode()) {
            case self::KUNENA:
                return ' data-kunena-lightbox="' . $escape($group) . '"'
                    . ($download ? ' data-kunena-download="' . $escape($download) . '"' : '')
                    . ($filename ? ' data-kunena-filename="' . $escape($filename) . '"' : '');

            case self::EXISTING:
                return ' data-fancybox="' . $escape($group) . '" data-type="image"';

            default:
                return '';
        }
    }

    /**
     * Whether the page loads the lightbox of Kunena.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isKunena(): bool
    {
        return self::mode() === self::KUNENA;
    }

    /**
     * The lightbox of the forum: one of the constants of the class.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function mode(): int
    {
        $mode = (int) KunenaConfig::getInstance()->lightbox;

        return \in_array($mode, [self::KUNENA, self::EXISTING], true) ? $mode : self::NONE;
    }
}
