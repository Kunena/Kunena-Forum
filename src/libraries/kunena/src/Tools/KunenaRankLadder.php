<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tools;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * The ranks (tool "Rank ladder check"): the ranks of posts which share their number of posts (only one of them is ever
 * given), no rank for a user without posts, special ranks nobody has, users who have a rank which no longer exists, and
 * the ranks whose image is not in the folder of the ranks.
 *
 * @since   Kunena 8.0
 */
class KunenaRankLadder
{
    /**
     * The folder of the images of the ranks, from the root of Joomla.
     *
     * @since   Kunena 8.0
     */
    public const FOLDER = 'media/kunena/ranks';

    /**
     * The words in the image of a special rank which Kunena gives by itself (KunenaUser::getRank()): to the guests, the
     * blocked and the banned users, the administrators and the moderators. Such a rank is given to nobody by hand.
     *
     * @since   Kunena 8.0
     */
    public const AUTOMATIC = ['guest', 'disabled', 'banned', 'admin', 'rankmod', 'moderator'];

    /**
     * Does Kunena give this special rank by itself?
     *
     * @param   string  $image  The image of the rank.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isAutomatic(string $image): bool
    {
        foreach (self::AUTOMATIC as $word) {
            if (str_contains($image, $word)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The problems of the ranks.
     *
     * @param   array      $ranks   rankId, rankTitle, rankMin, rankSpecial, rankImage.
     * @param   array      $given   rankId => the users who have it as their (special) rank.
     * @param   callable   $exists  Does the image exist: fn (string): bool.
     *
     * @return  array{ladder: array, same: array<int, int[]>, noStart: bool, unused: int[], images: int[], unknown: array<int, int>}
     *          same: rankMin => the ranks with it; unknown: a rank id which users have but which does not exist => users.
     *
     * @since   Kunena 8.0
     */
    public static function check(array $ranks, array $given, callable $exists): array
    {
        $ladder = array_values(array_filter($ranks, static fn ($rank) => !(int) $rank['rankSpecial']));
        usort($ladder, static fn ($a, $b) => [(int) $a['rankMin'], (int) $a['rankId']] <=> [(int) $b['rankMin'], (int) $b['rankId']]);

        $byMin = [];

        foreach ($ladder as $rank) {
            $byMin[(int) $rank['rankMin']][] = (int) $rank['rankId'];
        }

        $known  = array_flip(array_map(static fn ($rank) => (int) $rank['rankId'], $ranks));
        $unused = [];
        $images = [];

        foreach ($ranks as $rank) {
            $id = (int) $rank['rankId'];

            if ((int) $rank['rankSpecial'] && empty($given[$id]) && !self::isAutomatic((string) $rank['rankImage'])) {
                $unused[] = $id;
            }

            if (trim((string) $rank['rankImage']) !== '' && !$exists((string) $rank['rankImage'])) {
                $images[] = $id;
            }
        }

        $unknown = [];

        foreach ($given as $id => $users) {
            if ((int) $id > 0 && !isset($known[(int) $id]) && (int) $users > 0) {
                $unknown[(int) $id] = (int) $users;
            }
        }

        return [
            'ladder'  => $ladder,
            'same'    => array_filter($byMin, static fn ($ids) => \count($ids) > 1),
            'noStart' => $ladder !== [] && (int) $ladder[0]['rankMin'] > 0,
            'unused'  => $unused,
            'images'  => $images,
            'unknown' => $unknown,
        ];
    }

    /**
     * The ranks of this site.
     *
     * @return  array  check() with the ranks by id ("ranks").
     *
     * @since   Kunena 8.0
     */
    public static function checkOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()->select($db->quoteName(['rankId', 'rankTitle', 'rankMin', 'rankSpecial', 'rankImage']))->from($db->quoteName('#__kunena_ranks'));
        $ranks = (array) $db->setQuery($query)->loadAssocList();
        $query = $db->createQuery()
            ->select($db->quoteName('rank'))
            ->select('COUNT(*) AS ' . $db->quoteName('users'))
            ->from($db->quoteName('#__kunena_users'))
            ->where($db->quoteName('rank') . ' > 0')
            ->group($db->quoteName('rank'));
        $given  = array_map('intval', (array) $db->setQuery($query)->loadAssocList('rank', 'users'));
        // A template may have its own images of the ranks (KunenaTemplate::getRankImage()).
        $folders = array_merge([JPATH_ROOT . '/' . self::FOLDER], glob(JPATH_ROOT . '/components/com_kunena/template/*/media/ranks', GLOB_ONLYDIR) ?: []);
        $exists  = static function (string $image) use ($folders): bool {
            if (basename($image) !== $image) {
                return false;
            }

            foreach ($folders as $folder) {
                if (is_file($folder . '/' . $image)) {
                    return true;
                }
            }

            return false;
        };

        return self::check($ranks, $given, $exists)
            + ['ranks' => array_column($ranks, null, 'rankId'), 'given' => $given];
    }

    /**
     * Takes a rank which no longer exists away from the users who have it: they get the rank of their posts again.
     *
     * @return  int  The users changed.
     *
     * @since   Kunena 8.0
     */
    public static function resetUnknownOnSite(): int
    {
        $ids = array_keys(self::checkOnSite()['unknown']);

        if (!$ids) {
            return 0;
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->update($db->quoteName('#__kunena_users'))
            ->set($db->quoteName('rank') . ' = 0')
            ->whereIn($db->quoteName('rank'), array_map('intval', $ids));
        $db->setQuery($query)->execute();

        return (int) $db->getAffectedRows();
    }
}
