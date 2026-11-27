<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Topic.Poll
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic\Poll;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;

/**
 * Class \Kunena\Forum\Libraries\Forum\Topic\TopicPollHelper
 *
 * @since   Kunena 6.0
 */
abstract class KunenaPollHelper
{
    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected static mixed $_instances = [];

    /**
     * Returns \Kunena\Forum\Libraries\Forum\Topic\Poll\Poll object.
     *
     * @param   int   $identifier  The poll to load - Can be only an integer.
     * @param   bool  $reload      reload
     *
     * @return  KunenaPoll
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function get($identifier = null, $reload = false): KunenaPoll
    {
        if ($identifier instanceof KunenaPoll) {
            return $identifier;
        }

        $id = \intval($identifier);

        if ($id < 1) {
            return new KunenaPoll();
        }

        if ($reload || empty(self::$_instances [$id])) {
            self::$_instances [$id] = new KunenaPoll($id);
        }

        return self::$_instances [$id];
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public static function recount(): void
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->update($db->quoteName('#__kunena_topics', 'a'))
            ->innerJoin($db->quoteName('#__kunena_polls', 'b') . ' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.threadid'))
            ->set($db->quoteName('a.poll_id') . ' = ' . $db->quoteName('b.id'));

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Take the votes of a user off the options of the polls, before his rows of the voters are deleted (the delete of the user,
     * the removal of his data): the results had a vote more than the voters, and their percentages counted it. As the merge of two
     * users does it: his row has his last vote.
     *
     * @param   int  $userid  The user.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function takeOffVotesOf(int $userid): void
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->update($db->quoteName('#__kunena_polls_options', 'o'))
            ->innerJoin($db->quoteName('#__kunena_polls_users', 'u') . ' ON ' . $db->quoteName('u.pollid') . ' = ' . $db->quoteName('o.pollid') . ' AND ' . $db->quoteName('u.lastvote') . ' = ' . $db->quoteName('o.id'))
            ->set($db->quoteName('o.votes') . ' = ' . $db->quoteName('o.votes') . ' - 1')
            ->where($db->quoteName('u.userid') . ' = ' . $userid)
            ->where($db->quoteName('o.votes') . ' > 0');

        $db->setQuery($query)->execute();
    }
}
