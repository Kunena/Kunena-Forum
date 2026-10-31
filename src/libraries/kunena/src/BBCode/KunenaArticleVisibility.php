<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\BBCode;

\defined('_JEXEC') or die();

/**
 * May the text of a Joomla article be shown in a message ([article]ID[/article])?
 *
 * The tag loaded the article by its id and looked at the view level of the article and at the state of its category. Not at the
 * state of the article (a draft, a trashed article), not at its dates (not yet published, expired), and not at the view level of
 * its category, so a member could read what the visitors of the site can not.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaArticleVisibility
{
    public const OK          = null;
    public const UNPUBLISHED = 'unpublished';
    public const CATEGORY    = 'category';

    /**
     * @param   integer      $state        State of the article: 1 published, 0 unpublished, 2 archived, -2 trashed.
     * @param   string|null  $publishUp    First moment (UTC, Y-m-d H:i:s) or null.
     * @param   string|null  $publishDown  Last moment (UTC, Y-m-d H:i:s) or null.
     * @param   integer      $catAccess    View level of the category.
     * @param   array        $viewLevels   View levels of the reader.
     * @param   boolean      $mayEdit      The reader may edit the article (so they see the drafts of it as well).
     * @param   string       $now          The current moment (UTC, Y-m-d H:i:s).
     *
     * @return  string|null  null when it may be shown, else UNPUBLISHED or CATEGORY.
     *
     * @since   Kunena 7.1
     */
    public static function check(int $state, ?string $publishUp, ?string $publishDown, int $catAccess, array $viewLevels, bool $mayEdit, string $now): ?string
    {
        // The category has its own view level, which also applies to the editors.
        if (!\in_array($catAccess, array_map('intval', $viewLevels), true)) {
            return self::CATEGORY;
        }

        if ($mayEdit) {
            return self::OK;
        }

        if ($state !== 1) {
            return self::UNPUBLISHED;
        }

        if (self::hasDate($publishUp) && $publishUp > $now) {
            return self::UNPUBLISHED;
        }

        if (self::hasDate($publishDown) && $publishDown <= $now) {
            return self::UNPUBLISHED;
        }

        return self::OK;
    }

    /**
     * Joomla stores "no date" as null, and in old sites as the zero date.
     *
     * @param   string|null  $date  The date.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    private static function hasDate(?string $date): bool
    {
        return $date !== null && $date !== '' && !str_starts_with($date, '0000-00-00');
    }
}
