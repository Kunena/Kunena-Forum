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
 * The condition of the search "messages of this user".
 *
 * The search compared the name which is stored in the message (m.name) with the typed name. That column is text typed at the
 * time of the post: a guest was able to type the name of a member (before the names of accounts were refused), so the messages of
 * the guest showed up as the messages of the member, and a member who was renamed was not found by the new name.
 *
 * The messages of a member are found by the user id of the account (found by the login name or the name of the account), the
 * messages of a guest (user id 0) by the name which was typed, unless that name is one of an account: such a message was
 * written by a guest who signed with a name which is not theirs.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaSearchUserFilter
{
    /**
     * @param   string  $like    The quoted LIKE operand, e.g. '%jelle%' (already escaped for the database).
     * @param   string  $prefix  The prefix of the tables.
     *
     * @return  string  A condition for the alias m of the messages table.
     *
     * @since   Kunena 7.1
     */
    public static function condition(string $like, string $prefix = '#__'): string
    {
        $users = $prefix . 'users';

        return '(m.userid IN (SELECT su.id FROM ' . $users . ' AS su WHERE su.name LIKE ' . $like . ' OR su.username LIKE ' . $like . ')'
            . ' OR (m.userid = 0 AND m.name LIKE ' . $like
            . ' AND NOT EXISTS (SELECT 1 FROM ' . $users . ' AS gu WHERE gu.name = m.name OR gu.username = m.name)))';
    }
}
