<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

\defined('_JEXEC') or die();

/**
 * The name of a gallery of avatars: one folder below media/kunena/avatars/gallery.
 *
 * The name of the request was put behind the path of the galleries as it came, so "../../.." was the list of the files of any
 * folder of the server.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaAvatarGalleryName
{
    /**
     * @param   mixed  $name  The name of the request.
     *
     * @return  string|null  The name of one folder, or null when it is none.
     *
     * @since   Kunena 7.1
     */
    public static function clean($name): ?string
    {
        if (!\is_string($name)) {
            return null;
        }

        $name = trim($name);

        // One part of a path: letters, digits, space, "_", "-" and "." (not at the start, so no "." and ".." and no hidden folder).
        if ($name === '' || \strlen($name) > 100 || !preg_match('/^[\p{L}\p{N}_\-][\p{L}\p{N}_\- .]*$/Du', $name) || str_contains($name, '..')) {
            return null;
        }

        return $name;
    }
}
