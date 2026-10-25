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

use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * The badges of the users: a label with a colour for the members of a Joomla user group or the users with a Kunena rank.
 * The badges are set in the configuration (Users tab), a user gets a badge when he is in the group or has the rank of the badge.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaUserBadges
{
    /**
     * The badges of the users which were asked for, by user id.
     *
     * @var     array
     * @since   Kunena 8.0
     */
    protected static mixed $cache = [];

    /**
     * The badges of a user: the ones of the Joomla user groups of the user and of the rank of the user.
     *
     * @param   KunenaUser  $user  The user.
     *
     * @return  array  Badge objects.
     *
     * @since   Kunena 8.0
     */
    public static function get(KunenaUser $user): array
    {
        $userid = (int) $user->userid;

        if (!$userid) {
            return [];
        }

        if (isset(self::$cache[$userid])) {
            return self::$cache[$userid];
        }

        $definitions          = self::getDefinitions();
        self::$cache[$userid] = [];

        if (!$definitions) {
            return [];
        }

        try {
            $groups = array_map('intval', Access::getGroupsByUser($userid, true));
            $rank   = null;

            foreach ($definitions as $badge) {
                if ($badge->usergroup && \in_array($badge->usergroup, $groups, true)) {
                    self::$cache[$userid][] = $badge;
                    continue;
                }

                if ($badge->rank) {
                    // The rank of the user: a rank which was given to the user, else the rank of the number of posts.
                    $rank ??= (int) $user->getRank(0, 'id');

                    if ($rank === $badge->rank) {
                        self::$cache[$userid][] = $badge;
                    }
                }
            }
        } catch (Exception $e) {
            self::$cache[$userid] = [];
        }

        return self::$cache[$userid];
    }

    /**
     * The defined badges.
     *
     * @return  array  Objects with title, color, usergroup and rank.
     *
     * @since   Kunena 8.0
     */
    public static function getDefinitions(): array
    {
        // The badges are kept in the parameters of the component by the view Badges of the backend (they are no field of the
        // configuration, so they are not in KunenaConfig).
        $badges = ComponentHelper::getParams('com_kunena')->get('badges', []);
        $result = [];

        // The subform of the configuration gives an object (or array) of rows.
        foreach ((array) $badges as $row) {
            $row   = (object) $row;
            $title = trim((string) ($row->title ?? ''));

            if ($title === '' || (empty($row->usergroup) && empty($row->rank))) {
                continue;
            }

            $result[] = (object) [
                'title'     => $title,
                'color'     => preg_match('/^#[0-9a-f]{6}$/i', (string) ($row->color ?? '')) ? $row->color : '#6c757d',
                'usergroup' => (int) ($row->usergroup ?? 0),
                'rank'      => (int) ($row->rank ?? 0),
            ];
        }

        return $result;
    }

    /**
     * The text colour which is readable on a background colour.
     *
     * @param   string  $color  The background colour, like #336699.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function getTextColor(string $color): string
    {
        $r = hexdec(substr($color, 1, 2));
        $g = hexdec(substr($color, 3, 2));
        $b = hexdec(substr($color, 5, 2));

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 150 ? '#000000' : '#ffffff';
    }

    /**
     * The badges of a user as html.
     *
     * @param   KunenaUser  $user   The user.
     * @param   string      $class  The class of the block.
     *
     * @return  string  The html, or an empty string when the user has no badges.
     *
     * @since   Kunena 8.0
     */
    public static function render(KunenaUser $user, string $class = ''): string
    {
        // The template can hide the badges.
        if (!KunenaFactory::getTemplate()->params->get('showBadges', 0)) {
            return '';
        }

        $badges = self::get($user);

        if (!$badges) {
            return '';
        }

        $html = '<span class="kuser-badges ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '">';

        foreach ($badges as $badge) {
            $html .= '<span class="badge me-1" style="background-color:' . $badge->color . ';color:' . self::getTextColor($badge->color) . '">'
                . htmlspecialchars(Text::_($badge->title), ENT_QUOTES, 'UTF-8') . '</span>';
        }

        return $html . '</span>';
    }
}
