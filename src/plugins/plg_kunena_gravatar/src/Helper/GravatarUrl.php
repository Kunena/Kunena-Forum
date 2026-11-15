<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Gravatar
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Gravatar\Helper;

\defined('_JEXEC') or die();

/**
 * The address of the Gravatar image of an e-mail address (it replaces the library of Emberlabs/Pedrollo of 2011, which threw on
 * a size above 512 or on a default image it did not know, and used MD5 and http).
 *
 * The address is https://www.gravatar.com/avatar/<SHA-256 of the trimmed, lower-case address>?s=<size>&r=<rating>&d=<default>.
 * Gravatar accepts SHA-256 hashes (and MD5, which is far easier to reverse to a guessed address). Without an address (a member who
 * hides it, see GravatarPrivacy) the hash is zeros and f=y forces the default image: nothing of the address is in the page.
 *
 * @see     https://docs.gravatar.com/api/avatars/images/
 * @since   Kunena 8.0
 */
final class GravatarUrl
{
    /**
     * The address of the images.
     *
     * @since   Kunena 8.0
     */
    public const string BASE = 'https://www.gravatar.com/avatar/';

    /**
     * The sizes which Gravatar gives, in pixels.
     *
     * @since   Kunena 8.0
     */
    public const int MIN_SIZE = 1;

    public const int MAX_SIZE = 2048;

    /**
     * The built-in default images of Gravatar (mm is the former name of mp).
     *
     * @since   Kunena 8.0
     */
    public const array DEFAULTS = ['404', 'mp', 'mm', 'identicon', 'monsterid', 'wavatar', 'retro', 'robohash', 'blank', 'initials', 'color'];

    /**
     * The ratings, from the most to the least suitable for everybody.
     *
     * @since   Kunena 8.0
     */
    public const array RATINGS = ['g', 'pg', 'r', 'x'];

    /**
     * The address of the image.
     *
     * @param   string  $email    The e-mail address, or an empty string for the default image (nothing of an address in it).
     * @param   int     $size     The size in pixels (kept within MIN_SIZE and MAX_SIZE).
     * @param   string  $default  A built-in default image (DEFAULTS), the address of an image, or empty for the one of Gravatar.
     * @param   string  $rating   The highest rating which is shown (RATINGS); an unknown one is g.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function build(string $email, int $size, string $default = '', string $rating = 'g'): string
    {
        $email  = strtolower(trim($email));
        $params = [
            's' => (string) max(self::MIN_SIZE, min(self::MAX_SIZE, $size)),
            'r' => \in_array(strtolower($rating), self::RATINGS, true) ? strtolower($rating) : 'g',
        ];

        if (($image = self::defaultImage($default)) !== '') {
            $params['d'] = $image;
        }

        if ($email === '') {
            $params['f'] = 'y';
        }

        $hash = $email !== '' ? hash('sha256', $email) : str_repeat('0', 64);

        // The address of a default image is encoded once here (http_build_query would encode it again).
        $query = implode('&', array_map(static fn ($key, $value) => $key . '=' . $value, array_keys($params), $params));

        return self::BASE . $hash . '?' . $query;
    }

    /**
     * The value of d: a built-in default image, the encoded address of an image, or an empty string (the image of Gravatar).
     *
     * @param   string  $default  The setting of the plugin.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function defaultImage(string $default): string
    {
        $default = trim($default);

        if (\in_array(strtolower($default), self::DEFAULTS, true)) {
            return strtolower($default);
        }

        // An image of the site: an absolute http(s) address (Gravatar fetches it itself).
        if (preg_match('#^https?://#i', $default) && filter_var($default, FILTER_VALIDATE_URL)) {
            return rawurlencode($default);
        }

        return '';
    }
}
