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
 * The rules of the user search which fills the list of the mentions (and of the search form): what may be asked, and what
 * may be told. Without them the search is a list of all the members of the site: with a single letter, or without any
 * letter, without a login, with the blocked and the not yet activated accounts, and with the real names.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaMentionSearch
{
    /**
     * The fewest characters of a search word.
     *
     * @var    integer
     * @since  Kunena 7.1
     */
    public const MIN_LENGTH = 2;

    /**
     * The most characters of a search word which are used.
     *
     * @var    integer
     * @since  Kunena 7.1
     */
    public const MAX_LENGTH = 50;

    /**
     * The most names in an answer.
     *
     * @var    integer
     * @since  Kunena 7.1
     */
    public const LIMIT = 10;

    /**
     * The word to search for, or null when the request is not a search yet: it is too short, or it is not text.
     *
     * @param   mixed  $term  What the visitor has typed.
     *
     * @return  string|null
     *
     * @since   Kunena 7.1
     */
    public static function normaliseTerm($term): ?string
    {
        if (!\is_string($term)) {
            return null;
        }

        // No control characters, no surrounding spaces.
        $term = trim((string) preg_replace('/[\x00-\x1f\x7f]+/u', '', $term));

        if (mb_strlen($term, 'UTF-8') < self::MIN_LENGTH) {
            return null;
        }

        return mb_substr($term, 0, self::MAX_LENGTH, 'UTF-8');
    }

    /**
     * The answer for the list: only the name which the forum shows for a member (the user name, or the name when the forum
     * shows names), nothing else, each name once, and not more than the limit.
     *
     * @param   array    $rows         Rows with the properties name and username.
     * @param   boolean  $useUsername  The forum shows the user name of the members.
     *
     * @return  array  A list of ['name' => '...'].
     *
     * @since   Kunena 7.1
     */
    public static function formatResults(array $rows, bool $useUsername): array
    {
        $names = [];

        foreach ($rows as $row) {
            $row  = (object) $row;
            $name = trim((string) ($useUsername ? ($row->username ?? '') : ($row->name ?? '')));

            if ($name === '' || isset($names[$name])) {
                continue;
            }

            $names[$name] = ['name' => $name];

            if (\count($names) >= self::LIMIT) {
                break;
            }
        }

        return array_values($names);
    }
}
