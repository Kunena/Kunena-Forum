<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Message
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Message;

\defined('_JEXEC') or die();

/**
 * The part of a message that the search may look into.
 *
 * Some text of a message is hidden when the message is shown: [hide] is for members, [confidential] for the author and the
 * moderators, [private] for nobody. The search compared the words with the whole text, so a visitor who could not read that text
 * could still find out what it says: a search for a word finds the message if the word is in the hidden part, and with a few
 * searches (more and more letters, or "-word" to leave messages out, or only the number of results) the hidden text is found.
 *
 * The messages that have such a part are not searched in their text by those who may not read it. The title is still searched.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaSearchHiddenText
{
    /**
     * The condition (for the aliases t.message and m.userid of the search) that is true for the messages whose text may be searched.
     *
     * @param   boolean  $isMember           The visitor is logged in (they may read [hide]).
     * @param   boolean  $isGlobalModerator  The visitor is an administrator or a global moderator (they may read [confidential]).
     * @param   integer  $userId             The id of the visitor; the author may read [confidential] of their own messages.
     *
     * @return  string
     *
     * @since   Kunena 7.1
     */
    public static function searchableCondition(bool $isMember, bool $isGlobalModerator, int $userId): string
    {
        // Nobody reads the content of [private] in the message, so nobody searches it.
        $parts = ["t.message NOT LIKE '%[private%'"];

        if (!$isMember) {
            $parts[] = "t.message NOT LIKE '%[hide%'";
        }

        if (!$isGlobalModerator) {
            $parts[] = $isMember && $userId > 0
                ? "(t.message NOT LIKE '%[confidential%' OR m.userid = " . $userId . ')'
                : "t.message NOT LIKE '%[confidential%'";
        }

        return '(' . implode(' AND ', $parts) . ')';
    }

    /**
     * The condition of one word of the search.
     *
     * @param   string   $like       The quoted operand of LIKE, e.g. '%word%' (already escaped for the database).
     * @param   boolean  $negated    The word is to be left out ("-word").
     * @param   string   $searchable The result of searchableCondition().
     *
     * @return  string
     *
     * @since   Kunena 7.1
     */
    public static function wordCondition(string $like, bool $negated, string $searchable): string
    {
        // A message is found by its text only when the text may be searched; the same rule decides what "-word" leaves out, because
        // the messages that are left out would tell as much as the ones that are found.
        $inText = "(t.message LIKE {$like} AND {$searchable})";

        return $negated
            ? "(NOT {$inText} AND m.subject NOT LIKE {$like})"
            : "({$inText} OR m.subject LIKE {$like})";
    }
}
