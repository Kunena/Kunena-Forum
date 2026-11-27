<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\QueryInterface;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class KunenaForumDiagnostics
 *
 * @since   Kunena 6.0
 */
abstract class KunenaDiagnostics
{
    /**
     * The most rows the backend shows of one check.
     *
     * @since   Kunena 8.0
     */
    public const int ITEMS_LIMIT = 100;

    /**
     * @param   string  $function  function
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public static function canDelete(string $function): bool
    {
        $queryFunction = 'delete_' . $function;

        if (method_exists(__CLASS__, $queryFunction)) {
            return true;
        }

        return false;
    }

    /**
     * @param   string  $function  function
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public static function canFix(string $function): bool
    {
        $queryFunction = 'fix_' . $function;

        if (method_exists(__CLASS__, $queryFunction) || method_exists(__CLASS__, 'repair_' . $function)) {
            return true;
        }

        return false;
    }

    /**
     * @param   string  $function  function
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public static function canNotice(string $function): string
    {
        $queryFunction = 'notice_' . $function;

        if (method_exists(__CLASS__, $queryFunction)) {
            return self::$queryFunction();
        }

        return '';
    }

    /**
     * @param   string  $function  function
     *
     * @return  integer
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public static function count(string $function): int
    {
        $function = 'query_' . $function;

        if (method_exists(__CLASS__, $function)) {
            $query = self::$function();

            $db = Factory::getContainer()->get('DatabaseDriver');
            $query->select('COUNT(*)');
            $db->setQuery($query);

            try {
                return (int) $db->loadResult();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        return 0;
    }

    /**
     * @param   string  $function  function
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public static function delete(string $function): bool
    {
        $queryFunction = 'delete_' . $function;

        if (method_exists(__CLASS__, $queryFunction)) {
            $query = self::$queryFunction();
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $db->setQuery($query);

            try {
                return (bool) $db->execute();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        return false;
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_aliasMissingCategory(): string
    {
        return "DELETE a FROM #__kunena_aliases AS a LEFT JOIN #__kunena_categories AS c ON a.item=c.id WHERE a.type='catid' AND c.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_attachmentOrphaned(): string
    {
        return "DELETE a FROM #__kunena_attachments AS a LEFT JOIN #__kunena_messages AS m ON a.mesid=m.id WHERE a.mesid>0 AND m.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_categoryOrphaned(): string
    {
        return "DELETE a FROM #__kunena_categories AS a LEFT JOIN #__kunena_categories AS c ON a.parentid=c.id WHERE a.parentid>0 AND c.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_messageBodyMissingMessage(): string
    {
        return "DELETE a FROM #__kunena_messages_text AS a LEFT JOIN #__kunena_messages AS m ON a.mesid=m.id WHERE m.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_messageMissingMessageBody(): string
    {
        return "DELETE a FROM #__kunena_messages AS a LEFT JOIN #__kunena_messages_text AS t ON t.mesid=a.id WHERE t.mesid IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_messageOrphaned(): string
    {
        // The texts go with their messages (without "mt" they stayed, as orphans of the check messageBodyMissingMessage).
        return "DELETE a, mt FROM #__kunena_messages AS a LEFT JOIN #__kunena_topics AS t ON t.id=a.thread LEFT JOIN #__kunena_messages_text AS mt ON a.id=mt.mesid WHERE t.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_movedAndMessages(): string
    {
        return "DELETE a FROM #__kunena_topics AS a INNER JOIN #__kunena_messages AS m ON m.thread=a.id LEFT JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE a.moved_id>0";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_movedMissingTopic(): string
    {
        return "DELETE a FROM #__kunena_topics AS a LEFT JOIN #__kunena_topics AS t ON t.id=a.moved_id WHERE a.moved_id>0 AND t.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_pollOptionOrphaned(): string
    {
        return "DELETE a FROM #__kunena_polls_options AS a LEFT JOIN #__kunena_polls AS p ON p.id=a.pollid WHERE p.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_pollOrphaned(): string
    {
        return "DELETE a FROM #__kunena_polls AS a LEFT JOIN #__kunena_topics AS t ON t.id=a.threadid WHERE t.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_pollTopicMismatch(): string
    {
        // Only a poll which no topic uses: a poll which another topic shows (an old split gave the poll to the new topic and left
        // its topic as it was) was deleted, and that topic lost its poll (fix_pollTopicMismatch() gives it its topic).
        return "DELETE a FROM #__kunena_polls AS a INNER JOIN #__kunena_topics AS t ON t.id=a.threadid LEFT JOIN #__kunena_topics AS tt ON tt.poll_id=a.id WHERE t.poll_id!=a.id AND tt.id IS NULL";
    }

    /**
     * A poll which names another topic than the topic which shows it gets that topic (the same repair as fix_topicPollMismatch(),
     * from the side of the poll). A poll which no topic shows is left to the delete.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function fix_pollTopicMismatch(): QueryInterface
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        return $db->createQuery()
            ->update('#__kunena_polls AS a')
            ->innerJoin('#__kunena_topics AS t ON t.id=a.threadid')
            ->innerJoin('#__kunena_topics AS tt ON tt.poll_id=a.id')
            ->set('a.threadid=tt.id')
            ->where('t.poll_id!=a.id');
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_pollUserOrphaned(): string
    {
        return "DELETE a FROM #__kunena_polls_users AS a LEFT JOIN #__kunena_polls AS p ON p.id=a.pollid WHERE p.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_ratingOrphaned(): string
    {
        return "DELETE r FROM #__kunena_rate AS r LEFT JOIN #__kunena_topics AS t ON t.id=r.topic_id WHERE t.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_thankyouOrphaned(): string
    {
        return "DELETE a FROM #__kunena_thankyou AS a LEFT JOIN #__kunena_messages AS m ON m.id=a.postid WHERE m.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_reactionOrphaned(): string
    {
        return "DELETE a FROM #__kunena_reactions AS a LEFT JOIN #__kunena_messages AS m ON m.id=a.message_id WHERE m.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_historyOrphaned(): string
    {
        return "DELETE a FROM #__kunena_messages_history AS a LEFT JOIN #__kunena_messages AS m ON m.id=a.message_id WHERE m.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_digestOrphaned(): string
    {
        return "DELETE a FROM #__kunena_digest AS a LEFT JOIN #__kunena_messages AS m ON m.id=a.message_id WHERE m.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_bookmarkOrphaned(): string
    {
        return "DELETE a FROM #__kunena_bookmarks AS a LEFT JOIN #__kunena_messages AS m ON m.id=a.message_id WHERE m.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_topicInSection(): string
    {
        return "DELETE a FROM #__kunena_topics AS a LEFT JOIN #__kunena_categories AS c ON c.id=a.category_id WHERE c.parentid=0";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_topicMissingCategory(): string
    {
        return "DELETE a FROM #__kunena_topics AS a LEFT JOIN #__kunena_categories AS c ON c.id=a.category_id WHERE c.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_topicMissingMessages(): string
    {
        return "DELETE a FROM #__kunena_topics AS a LEFT JOIN #__kunena_messages AS m ON m.thread=a.id WHERE a.moved_id=0 AND m.id IS NULL";
    }

    /**
     * Delete the subscriptions and favorites of users who no longer exist.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_userTopicUserOrphaned(): string
    {
        return "DELETE a FROM #__kunena_user_topics AS a LEFT JOIN #__users AS u ON u.id=a.user_id WHERE u.id IS NULL";
    }

    /**
     * Delete the subscriptions and roles in categories of users who no longer exist.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_userCategoryUserOrphaned(): string
    {
        return "DELETE a FROM #__kunena_user_categories AS a LEFT JOIN #__users AS u ON u.id=a.user_id WHERE u.id IS NULL";
    }

    /**
     * Delete the follows of or by users who no longer exist.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_followOrphaned(): string
    {
        return "DELETE a FROM #__kunena_user_follow AS a LEFT JOIN #__users AS u ON u.id=a.user_id LEFT JOIN #__users AS f ON f.id=a.followed_id WHERE u.id IS NULL OR f.id IS NULL";
    }

    /**
     * Delete the ignores of or by users who no longer exist.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_ignoreOrphaned(): string
    {
        return "DELETE a FROM #__kunena_user_ignore AS a LEFT JOIN #__users AS u ON u.id=a.user_id LEFT JOIN #__users AS i ON i.id=a.ignored_id WHERE u.id IS NULL OR i.id IS NULL";
    }

    /**
     * Delete the forum profiles of Joomla users who no longer exist.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_profileOrphaned(): string
    {
        return "DELETE a FROM #__kunena_users AS a LEFT JOIN #__users AS u ON u.id=a.userid WHERE u.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_userCategoryOrphaned(): string
    {
        return "DELETE a FROM #__kunena_user_categories AS a LEFT JOIN #__kunena_categories AS c ON c.id=a.category_id WHERE a.category_id>0 AND c.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_userReadOrphaned(): string
    {
        return "DELETE a FROM #__kunena_user_read AS a LEFT JOIN #__kunena_topics AS t ON t.id=a.topic_id WHERE t.id IS NULL";
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     */
    protected static function delete_userTopicOrphaned(): string
    {
        return "DELETE a FROM #__kunena_user_topics AS a LEFT JOIN #__kunena_topics AS t ON t.id=a.topic_id WHERE t.id IS NULL";
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select("a.*");
        }

        return [];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_aliasMissingCategory(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['item' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_attachmentOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.mesid, a.userid, a.folder, a.filename');
        }

        return ['mesid' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_categoryMissingAlias(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.parentid, a.name, a.alias, a.description');
        }

        return ['name' => 'link', 'alias' => 'invalid', '_link' => '&view=categories&layout=edit&catid={$id}'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_categoryOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.parentid, a.name, a.alias, a.description');
        }

        return ['name' => 'link', 'parentid' => 'invalid', '_link' => '&view=categories&layout=edit&catid={$id}'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_categoryWrongAlias(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.parentid, a.name, a.alias, a.description');
        }

        return ['name' => 'link', 'alias' => 'invalid', '_link' => '&view=categories&layout=edit&catid={$id}'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_channelOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('*');
        }

        return ['channels' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_messageBodyMissingMessage(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['mesid' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_messageMissingMessageBody(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select("a.id, a.parent, a.thread, a.catid, a.hold, a.name, a.userid, a.subject, FROM_UNIXTIME(a.time) AS time, 'MISSING' AS message");
        }

        return ['message' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_messageOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select("a.id, a.parent, a.thread, a.catid, a.hold, a.name, a.userid, a.subject, FROM_UNIXTIME(a.time) AS time, mt.message");
        }

        return ['thread' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_messageWrongCategory(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select("a.id, a.parent, a.thread, CONCAT(a.catid, ' != ', t.category_id) AS catid, a.hold, a.name, a.userid, a.subject, FROM_UNIXTIME(a.time) AS time, mt.message");
        }

        return ['catid' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_movedAndMessages(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.category_id, a.hold, a.subject, m.id AS mesid, m.subject AS message_subject, t.message');
        }

        return ['mesid' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_movedMissingTopic(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.category_id, a.hold, a.subject, a.moved_id');
        }

        return ['moved_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_ownerOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('t.id, t.first_post_userid, 1');
        }

        return ['channels' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_pollOptionOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['pollid' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_pollOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['threadid' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_pollTopicMismatch(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select("a.id, a.title, CONCAT(a.threadid, ' != ', IF(tt.id,tt.id,'0')) AS threadid, t.subject AS topic1_subject, tt.subject AS topic2_subject");
        }

        return ['threadid' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_pollUserOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['pollid' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_ratingOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('r.*');
        }

        return ['topic_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_thankyouOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['postid' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_reactionOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['message_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_historyOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.message_id, a.edited_by, a.edited_time');
        }

        return ['message_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_digestOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['message_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_bookmarkOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['message_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_topicInSection(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.category_id, a.hold, a.subject');
        }

        return ['category_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_topicMissingCategory(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.category_id, a.hold, a.subject');
        }

        return ['category_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_topicMissingMessages(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select("a.id, a.category_id, a.hold, a.subject, 'MISSING' AS messages");
        }

        return ['messages' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_topicMissingPoll(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select("a.id, a.category_id, a.hold, a.subject, poll_id");
        }

        return ['poll_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_topicPollMismatch(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select("a.id, a.category_id, a.hold, a.subject, p.title AS poll_title, CONCAT(a.poll_id, ' != ', p.threadid) AS poll_id, t.subject AS real_topic_subject");
        }

        return ['poll_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_userAvatarOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('*');
        }

        return ['channels' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_userTopicUserOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['user_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_userCategoryUserOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['user_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_followOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['followed_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_ignoreOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['ignored_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_profileOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['userid' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_userCategoryOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['category_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_userReadOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.*');
        }

        return ['topic_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_userReadWrongCategory(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select("a.user_id, a.topic_id, CONCAT(a.category_id, ' != ', t.category_id) AS category_id, a.message_id, FROM_UNIXTIME(a.time) AS time");
        }

        return ['category_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_userTopicOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.user_id, a.topic_id, a.category_id, a.posts, a.last_post_id, a.owner, a.favorite, a.subscribed');
        }

        return ['topic_id' => 'invalid'];
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    protected static function fields_userTopicWrongCategory(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select("a.user_id, a.topic_id, CONCAT(a.category_id, ' != ', t.category_id) AS category_id, a.posts, a.last_post_id, a.owner, a.favorite, a.subscribed");
        }

        return ['category_id' => 'invalid'];
    }

    /**
     * @param   string  $function  function
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public static function fix(string $function): bool
    {
        // A fix which needs more than one statement (a recount): its own method.
        $repairFunction = 'repair_' . $function;

        if (method_exists(__CLASS__, $repairFunction)) {
            try {
                return self::$repairFunction();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);

                return false;
            }
        }

        $queryFunction = 'fix_' . $function;

        if (method_exists(__CLASS__, $queryFunction)) {
            $query = self::$queryFunction();
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $db->setQuery($query);

            try {
                return (bool) $db->execute();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        return false;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function fix_channelOrphaned(): QueryInterface
    {
        return self::query_channelOrphaned()->update('#__kunena_categories')->set("channels='THIS'")->where("channels='none' OR channels IS NULL");
    }

    /**
     * @return  QueryInterface
     * @since   Kunena 6.0
     */
    protected static function fix_messageWrongCategory(): QueryInterface
    {
        return self::query_messageWrongCategory()->update('#__kunena_messages AS a')->set('a.catid=t.category_id');
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function fix_ownerOrphaned(): QueryInterface
    {
        return self::query_channelOrphaned()->update('#__kunena_categories')->set("channels='THIS'")->where("channels='none' OR channels=NULL");
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    /**
     * A topic whose poll is missing: the topic forgets the poll. The topic was deleted (its messages then were orphans).
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function fix_topicMissingPoll(): QueryInterface
    {
        return self::query_topicMissingPoll()->update('#__kunena_topics AS a')->set('a.poll_id=0');
    }

    /**
     * A topic whose poll belongs to another topic: when that topic does not use the poll (a split gave the poll to the new topic
     * without moving it), the poll goes to this topic; when it does, this topic forgets the poll. The topic was deleted.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function fix_topicPollMismatch(): QueryInterface
    {
        return self::query_topicPollMismatch()->update('#__kunena_topics AS a')
            ->set('p.threadid=IF(t.id IS NULL OR t.poll_id!=p.id, a.id, p.threadid)')
            ->set('a.poll_id=IF(t.id IS NOT NULL AND t.poll_id=p.id, 0, a.poll_id)');
    }

    protected static function fix_userAvatarOrphaned(): QueryInterface
    {
        return self::query_useravatarOrphaned()->update('#__kunena_users')->set("avatar=NULL")->where("avatar=''");
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function fix_userReadWrongCategory(): QueryInterface
    {
        return self::query_userReadWrongCategory()->update('#__kunena_user_read AS a')->set('a.category_id=t.category_id');
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function fix_userTopicWrongCategory(): QueryInterface
    {
        return self::query_userTopicWrongCategory()->update('#__kunena_user_topics AS a')->set('a.category_id=t.category_id');
    }

    /**
     * @param   string  $function  function
     *
     * @return  array
     *
     * @since   Kunena 6.0
     */
    public static function getFieldInfo(string $function): array
    {
        static $fields = [];

        if (!isset($fields[$function])) {
            $fieldsFunction = 'fields_' . $function;

            if (!method_exists(__CLASS__, $fieldsFunction)) {
                $fieldsFunction = 'fields';
            }

            $fields[$function] = (array) self::$fieldsFunction();
        }

        return $fields[$function];
    }

    /**
     * @param   string  $function  function
     * @param   int     $limit     The most rows to load (0: all of them)
     *
     * @return  array
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public static function getItems(string $function, int $limit = 0): array
    {
        $queryFunction = 'query_' . $function;

        if (method_exists(__CLASS__, $queryFunction)) {
            $query          = self::$queryFunction();
            $fieldsFunction = 'fields_' . $function;

            if (!method_exists(__CLASS__, $fieldsFunction)) {
                $fieldsFunction = 'fields';
            }

            self::$fieldsFunction($query);
            $db = Factory::getContainer()->get('DatabaseDriver');
            $db->setQuery($query, 0, $limit);

            try {
                return (array) $db->loadAssocList();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        return [];
    }

    /**
     * Query to find the warnings of users who no longer exist.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_warningOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_warnings AS a')->leftJoin('#__users AS u ON u.id=a.user_id')->where('u.id IS NULL');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_warningOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.user_id, a.points, a.reason, a.created_time');
        }

        return ['user_id' => 'invalid'];
    }

    /**
     * Delete the warnings of users who no longer exist.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_warningOrphaned(): string
    {
        return 'DELETE a FROM #__kunena_warnings AS a LEFT JOIN #__users AS u ON u.id=a.user_id WHERE u.id IS NULL';
    }

    /**
     * Query to find the private posts whose post of the forum no longer exists: every private post is made with its post, and
     * until Kunena 8.0 a post deleted for good left its private post (the text) behind.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_privateOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_private AS a')
            ->where('NOT EXISTS (SELECT 1 FROM #__kunena_private_post_map AS pm INNER JOIN #__kunena_messages AS m ON m.id=pm.message_id WHERE pm.private_id=a.id)');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_privateOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.author_id, a.created_at');
        }

        return ['id' => 'invalid', 'author_id' => 'valid', 'created_at' => 'valid'];
    }

    /**
     * Delete the private posts whose post of the forum no longer exists (their recipients and links are the next checks).
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_privateOrphaned(): string
    {
        return 'DELETE a FROM #__kunena_private AS a WHERE NOT EXISTS (SELECT 1 FROM #__kunena_private_post_map AS pm'
            . ' INNER JOIN #__kunena_messages AS m ON m.id=pm.message_id WHERE pm.private_id=a.id)';
    }

    /**
     * Query to find the recipients of private posts whose private post or user no longer exists.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_privateUserOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_private_user_map AS a')->leftJoin('#__kunena_private AS p ON p.id=a.private_id')
            ->leftJoin('#__users AS u ON u.id=a.user_id')->where('p.id IS NULL OR u.id IS NULL');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_privateUserOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.private_id, a.user_id');
        }

        return ['private_id' => 'invalid', 'user_id' => 'invalid'];
    }

    /**
     * Delete the recipients of private posts whose private post or user no longer exists.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_privateUserOrphaned(): string
    {
        return 'DELETE a FROM #__kunena_private_user_map AS a LEFT JOIN #__kunena_private AS p ON p.id=a.private_id'
            . ' LEFT JOIN #__users AS u ON u.id=a.user_id WHERE p.id IS NULL OR u.id IS NULL';
    }

    /**
     * Query to find the links of private posts to posts of the forum when one of the two no longer exists.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_privatePostOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_private_post_map AS a')->leftJoin('#__kunena_private AS p ON p.id=a.private_id')
            ->leftJoin('#__kunena_messages AS m ON m.id=a.message_id')->where('p.id IS NULL OR m.id IS NULL');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_privatePostOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.private_id, a.message_id');
        }

        return ['private_id' => 'invalid', 'message_id' => 'invalid'];
    }

    /**
     * Delete the links of private posts to posts of the forum when one of the two no longer exists.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_privatePostOrphaned(): string
    {
        return 'DELETE a FROM #__kunena_private_post_map AS a LEFT JOIN #__kunena_private AS p ON p.id=a.private_id'
            . ' LEFT JOIN #__kunena_messages AS m ON m.id=a.message_id WHERE p.id IS NULL OR m.id IS NULL';
    }

    /**
     * Query to find the links of private posts to attachments when one of the two no longer exists.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_privateAttachmentOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_private_attachment_map AS a')->leftJoin('#__kunena_private AS p ON p.id=a.private_id')
            ->leftJoin('#__kunena_attachments AS t ON t.id=a.attachment_id')->where('p.id IS NULL OR t.id IS NULL');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_privateAttachmentOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.private_id, a.attachment_id');
        }

        return ['private_id' => 'invalid', 'attachment_id' => 'invalid'];
    }

    /**
     * Delete the links of private posts to attachments when one of the two no longer exists.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_privateAttachmentOrphaned(): string
    {
        return 'DELETE a FROM #__kunena_private_attachment_map AS a LEFT JOIN #__kunena_private AS p ON p.id=a.private_id'
            . ' LEFT JOIN #__kunena_attachments AS t ON t.id=a.attachment_id WHERE p.id IS NULL OR t.id IS NULL';
    }

    /**
     * Query to find the drafts of users who no longer exist, or of a reply to a topic which no longer exists.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_draftOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_drafts AS a')->leftJoin('#__users AS u ON u.id=a.user_id')->leftJoin('#__kunena_topics AS t ON t.id=a.topic_id')
            ->where('u.id IS NULL OR (a.topic_id>0 AND t.id IS NULL)');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_draftOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.user_id, a.topic_id, a.subject, a.modified_time');
        }

        return ['user_id' => 'invalid', 'topic_id' => 'invalid'];
    }

    /**
     * Delete the drafts of users who no longer exist, or of a reply to a topic which no longer exists.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_draftOrphaned(): string
    {
        return 'DELETE a FROM #__kunena_drafts AS a LEFT JOIN #__users AS u ON u.id=a.user_id LEFT JOIN #__kunena_topics AS t ON t.id=a.topic_id'
            . ' WHERE u.id IS NULL OR (a.topic_id>0 AND t.id IS NULL)';
    }

    /**
     * Query to find the bans of users who no longer exist. A ban of an IP address stays: it still bans the address.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_banOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_users_banned AS a')->leftJoin('#__users AS u ON u.id=a.userid')
            ->where("a.userid>0 AND u.id IS NULL AND (a.ip IS NULL OR a.ip='')");

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_banOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.userid, a.expiration, a.reason_private');
        }

        return ['userid' => 'invalid'];
    }

    /**
     * Delete the bans of users who no longer exist (not a ban of an IP address).
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_banOrphaned(): string
    {
        return "DELETE a FROM #__kunena_users_banned AS a LEFT JOIN #__users AS u ON u.id=a.userid WHERE a.userid>0 AND u.id IS NULL AND (a.ip IS NULL OR a.ip='')";
    }

    /**
     * Query to find the translations of categories which no longer exist.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_categoryTranslationOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_categories_translations AS a')->leftJoin('#__kunena_categories AS c ON c.id=a.category_id')->where('c.id IS NULL');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_categoryTranslationOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.category_id, a.language, a.name');
        }

        return ['category_id' => 'invalid'];
    }

    /**
     * Delete the translations of categories which no longer exist.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_categoryTranslationOrphaned(): string
    {
        return 'DELETE a FROM #__kunena_categories_translations AS a LEFT JOIN #__kunena_categories AS c ON c.id=a.category_id WHERE c.id IS NULL';
    }

    /**
     * Query to find the ratings of topics by users who no longer exist (ratingOrphaned: of topics which no longer exist).
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_ratingUserOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_rate AS a')->leftJoin('#__users AS u ON u.id=a.userid')->where('u.id IS NULL');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_ratingUserOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.topic_id, a.userid, a.rate');
        }

        return ['userid' => 'invalid'];
    }

    /**
     * Delete the ratings by users who no longer exist.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_ratingUserOrphaned(): string
    {
        return 'DELETE a FROM #__kunena_rate AS a LEFT JOIN #__users AS u ON u.id=a.userid WHERE u.id IS NULL';
    }

    /**
     * Query to find the karma given by or to users who no longer exist.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_karmaOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_karma AS a')->leftJoin('#__users AS u ON u.id=a.userid')->leftJoin('#__users AS t ON t.id=a.target_userid')
            ->where('u.id IS NULL OR t.id IS NULL');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_karmaOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.userid, a.target_userid, a.time');
        }

        return ['userid' => 'invalid', 'target_userid' => 'invalid'];
    }

    /**
     * Delete the karma given by or to users who no longer exist.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_karmaOrphaned(): string
    {
        return 'DELETE a FROM #__kunena_karma AS a LEFT JOIN #__users AS u ON u.id=a.userid LEFT JOIN #__users AS t ON t.id=a.target_userid'
            . ' WHERE u.id IS NULL OR t.id IS NULL';
    }

    /**
     * Query to find the sessions of users who no longer exist (a guest has no session row).
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_sessionOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_sessions AS a')->leftJoin('#__users AS u ON u.id=a.userid')->where('a.userid>0 AND u.id IS NULL');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_sessionOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.userid, a.lasttime, a.currvisit');
        }

        return ['userid' => 'invalid'];
    }

    /**
     * Delete the sessions of users who no longer exist.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function delete_sessionOrphaned(): string
    {
        return 'DELETE a FROM #__kunena_sessions AS a LEFT JOIN #__users AS u ON u.id=a.userid WHERE a.userid>0 AND u.id IS NULL';
    }

    /**
     * Query to find the topics whose number of posts or time of the last post differ from their posts, as the recount of the
     * topics counts them (the posts in the state of the topic which were not moved away).
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_topicWrongCounts(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_topics AS a')
            ->innerJoin('(SELECT thread, hold, COUNT(*) AS posts, MAX(time) AS maxtime FROM #__kunena_messages WHERE moved=0 GROUP BY thread, hold) AS c'
                . ' ON c.thread=a.id AND c.hold=a.hold')
            ->where('a.moved_id=0 AND (a.posts!=c.posts OR a.last_post_time!=c.maxtime)');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_topicWrongCounts(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.category_id, a.subject, a.posts, c.posts AS counted, a.last_post_time, c.maxtime AS counted_time');
        }

        return ['posts' => 'invalid', 'last_post_time' => 'invalid'];
    }

    /**
     * Count the topics again (and their categories).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected static function repair_topicWrongCounts(): bool
    {
        return self::recountTopics(self::ids('topicWrongCounts', 'a.id'));
    }

    /**
     * Query to find the categories whose numbers of topics and posts differ from their published topics.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_categoryWrongCounts(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_categories AS a')
            ->leftJoin('(SELECT category_id, COUNT(*) AS topics, SUM(posts) AS posts FROM #__kunena_topics WHERE hold=0 AND moved_id=0 GROUP BY category_id) AS c'
                . ' ON c.category_id=a.id')
            ->where('(a.numTopics!=COALESCE(c.topics, 0) OR a.numPosts!=COALESCE(c.posts, 0))');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_categoryWrongCounts(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.name, a.numTopics, COALESCE(c.topics, 0) AS counted_topics, a.numPosts, COALESCE(c.posts, 0) AS counted_posts');
        }

        return ['numTopics' => 'invalid', 'numPosts' => 'invalid'];
    }

    /**
     * Count the categories again.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected static function repair_categoryWrongCounts(): bool
    {
        $ids = self::ids('categoryWrongCounts', 'a.id');

        return !$ids || KunenaCategoryHelper::recountBatch($ids) !== false;
    }

    /**
     * Query to find the users whose number of posts differs from the posts in their topics.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_userWrongPosts(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_users AS a')
            ->leftJoin('(SELECT user_id, SUM(posts) AS posts FROM #__kunena_user_topics GROUP BY user_id) AS c ON c.user_id=a.userid')
            ->where('a.posts!=COALESCE(c.posts, 0)');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_userWrongPosts(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.userid, a.posts, COALESCE(c.posts, 0) AS counted');
        }

        return ['posts' => 'invalid'];
    }

    /**
     * Count the posts of the users again.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected static function repair_userWrongPosts(): bool
    {
        return KunenaUserHelper::recount() !== false;
    }

    /**
     * Query to find the categories whose parents lead back to themselves: the tree has a loop and they are shown nowhere.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_categoryLoop(): QueryInterface
    {
        // Each category walks up its parents (at most 100 steps); a walk which comes back to its start is a loop.
        $walk = 'WITH RECURSIVE walk (start, id, parentid, depth) AS ('
            . ' SELECT id, id, parentid, 0 FROM #__kunena_categories'
            . ' UNION ALL SELECT w.start, c.id, c.parentid, w.depth + 1 FROM walk AS w INNER JOIN #__kunena_categories AS c ON c.id=w.parentid'
            . ' WHERE w.depth < 100 AND w.parentid > 0 AND (w.depth = 0 OR w.id != w.start))'
            . ' SELECT DISTINCT start FROM walk WHERE depth > 0 AND id = start';

        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_categories AS a')->innerJoin('(' . $walk . ') AS l ON l.start=a.id');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_categoryLoop(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.name, a.parentid');
        }

        return ['parentid' => 'invalid'];
    }

    /**
     * Query to find the published topics whose posts are all deleted or wait for approval: they are listed and open empty.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_topicShownEmpty(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_topics AS a')
            ->innerJoin('(SELECT thread, MIN(hold) AS hold FROM #__kunena_messages WHERE moved=0 GROUP BY thread) AS c ON c.thread=a.id')
            ->where('a.hold=0 AND a.moved_id=0 AND c.hold>0');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_topicShownEmpty(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.category_id, a.subject, a.hold, c.hold AS posts_hold');
        }

        return ['hold' => 'invalid'];
    }

    /**
     * Count the topics again: the recount gives a topic the state of its posts.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected static function repair_topicShownEmpty(): bool
    {
        return self::recountTopics(self::ids('topicShownEmpty', 'a.id'));
    }

    /**
     * Query to find the posts which reply to a post which no longer exists or is in another topic.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_messageWrongParent(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_messages AS a')->leftJoin('#__kunena_messages AS p ON p.id=a.parent')
            ->innerJoin('#__kunena_topics AS t ON t.id=a.thread')
            ->where('a.parent>0 AND (p.id IS NULL OR p.thread!=a.thread)');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_messageWrongParent(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.thread, a.subject, a.parent, p.thread AS parent_thread');
        }

        return ['parent' => 'invalid'];
    }

    /**
     * A post which replies to a post which no longer exists or is in another topic replies to the first post of its topic (the
     * first post itself replies to nothing).
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function fix_messageWrongParent(): QueryInterface
    {
        return self::query_messageWrongParent()->update('#__kunena_messages AS a')->set('a.parent=IF(t.first_post_id=a.id, 0, t.first_post_id)');
    }

    /**
     * The ids of the rows of a check.
     *
     * @param   string  $function  The check.
     * @param   string  $column    The column of the id.
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    protected static function ids(string $function, string $column): array
    {
        $method = 'query_' . $function;
        $query  = self::$method();
        $query->select('DISTINCT ' . $column);

        return array_map('intval', (array) Factory::getContainer()->get('DatabaseDriver')->setQuery($query)->loadColumn());
    }

    /**
     * Count topics again, with their categories.
     *
     * @param   int[]  $ids  The topics.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected static function recountTopics(array $ids): bool
    {
        if (!$ids) {
            return true;
        }

        $db         = Factory::getContainer()->get('DatabaseDriver');
        $categories = $db->setQuery('SELECT DISTINCT category_id FROM #__kunena_topics WHERE id IN (' . implode(',', array_map('intval', $ids)) . ')')->loadColumn();

        if (KunenaTopicHelper::recount($ids) === false) {
            return false;
        }

        return !$categories || KunenaCategoryHelper::recountBatch(array_map('intval', $categories)) !== false;
    }

    /**
     * The checks by what they are about, for the page of the diagnostics: heading (a language key) => checks.
     *
     * @return  array<string, string[]>
     *
     * @since   Kunena 8.0
     */
    public static function getGroups(): array
    {
        return [
            'COM_KUNENA_DIAGNOSTICS_GROUP_CATEGORIES' => [
                'categoryOrphaned', 'categoryMissingAlias', 'categoryWrongAlias', 'aliasMissingCategory', 'channelOrphaned',
                'categoryLoop', 'categoryWrongCounts', 'categoryTranslationOrphaned',
            ],
            'COM_KUNENA_DIAGNOSTICS_GROUP_TOPICS' => [
                'topicInSection', 'topicMissingCategory', 'topicMissingMessages', 'topicMissingPoll', 'topicPollMismatch',
                'movedMissingTopic', 'movedAndMessages', 'topicsOwnersOrphaned', 'ratingOrphaned',
                'topicWrongCounts', 'topicShownEmpty', 'ratingUserOrphaned',
            ],
            'COM_KUNENA_DIAGNOSTICS_GROUP_POSTS' => [
                'messageBodyMissingMessage', 'messageMissingMessageBody', 'messageWrongCategory', 'messageOrphaned', 'attachmentOrphaned',
                'thankyouOrphaned', 'reactionOrphaned', 'historyOrphaned', 'digestOrphaned', 'bookmarkOrphaned',
                'messageWrongParent', 'draftOrphaned', 'privateOrphaned', 'privateUserOrphaned', 'privatePostOrphaned', 'privateAttachmentOrphaned',
            ],
            'COM_KUNENA_DIAGNOSTICS_GROUP_POLLS' => [
                'pollOrphaned', 'pollTopicMismatch', 'pollOptionOrphaned', 'pollUserOrphaned',
            ],
            'COM_KUNENA_DIAGNOSTICS_GROUP_USERS' => [
                'userCategoryOrphaned', 'userReadOrphaned', 'userReadWrongCategory', 'userTopicOrphaned', 'userTopicWrongCategory',
                'userAvatarOrphaned', 'userTopicUserOrphaned', 'userCategoryUserOrphaned', 'followOrphaned', 'ignoreOrphaned', 'profileOrphaned',
                'userWrongPosts', 'warningOrphaned', 'banOrphaned', 'karmaOrphaned', 'sessionOrphaned',
            ],
        ];
    }

    /**
     * The language key of the title of a check: COM_KUNENA_DIAGNOSTICS_CHECK_ and the name of the check in capitals, its words
     * separated by an underscore (categoryOrphaned: COM_KUNENA_DIAGNOSTICS_CHECK_CATEGORY_ORPHANED).
     *
     * @param   string  $function  The name of the check.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function titleKey(string $function): string
    {
        return 'COM_KUNENA_DIAGNOSTICS_CHECK_' . strtoupper(preg_replace('/(?<=[a-z0-9])(?=[A-Z])/', '_', $function));
    }

    /**
     * @return  array
     *
     * @since   Kunena 6.0
     */
    public static function getList(): array
    {
        return [
            'categoryOrphaned',
            'categoryMissingAlias',
            'categoryWrongAlias',
            'aliasMissingCategory',
            'messageBodyMissingMessage',
            'messageMissingMessageBody',
            'topicInSection',
            'topicMissingCategory',
            'topicMissingMessages',
            'topicMissingPoll',
            'topicPollMismatch',
            'movedMissingTopic',
            'movedAndMessages',
            'messageWrongCategory',
            'messageOrphaned',
            'attachmentOrphaned',
            'pollOrphaned',
            'pollTopicMismatch',
            'pollOptionOrphaned',
            'pollUserOrphaned',
            'thankyouOrphaned',
            'reactionOrphaned',
            'historyOrphaned',
            'digestOrphaned',
            'bookmarkOrphaned',
            'userCategoryOrphaned',
            'userReadOrphaned',
            'userReadWrongCategory',
            'userTopicOrphaned',
            'userTopicWrongCategory',
            'ratingOrphaned',
            'channelOrphaned',
            'userAvatarOrphaned',
            'topicsOwnersOrphaned',
            'userTopicUserOrphaned',
            'userCategoryUserOrphaned',
            'followOrphaned',
            'ignoreOrphaned',
            'profileOrphaned',
            'categoryLoop',
            'categoryWrongCounts',
            'categoryTranslationOrphaned',
            'topicWrongCounts',
            'topicShownEmpty',
            'ratingUserOrphaned',
            'messageWrongParent',
            'draftOrphaned',
            'privateOrphaned',
            'privateUserOrphaned',
            'privatePostOrphaned',
            'privateAttachmentOrphaned',
            'userWrongPosts',
            'warningOrphaned',
            'banOrphaned',
            'karmaOrphaned',
            'sessionOrphaned',
        ];
    }

    /**
     * @param   string  $function  function
     *
     * @return string
     *
     * @since   Kunena 6.0
     */
    public static function getQuery(string $function): string
    {
        $function = 'query_' . $function;

        if (method_exists(__CLASS__, $function)) {
            $query = self::$function();

            $query->select("COUNT(*)");

            return (string) $query;
        }

        return false;
    }

    /**
     * Whether the name is one of the checks of getList() (a name from the request is checked with it first).
     *
     * @param   string  $name  The name of the check
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isCheck(string $name): bool
    {
        return \in_array($name, self::getList(), true);
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected static function notice_categoryWrongAlias(): string
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->select('*')
            ->from($db->quoteName('#__kunena_categories', 'a'))
            ->leftJoin($db->quoteName('#__kunena_aliases', 'c') . ' ON a.alias=c.alias')
            ->where("c.type!='catid' OR c.item!=a.id");
        $db->setQuery((string) $query);

        $list = (array) $db->loadObjectList();

        $ids = [];

        foreach ($list as $item) {
            $ids[] = $item->id;
        }

        if (empty($ids)) {
            return '';
        }

        return 'Please fix the type of alias in #__kunena_aliases for category with id(s): ' . implode(', ', $ids);
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_aliasMissingCategory(): QueryInterface
    {
        // Query to find orphaned aliases
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_aliases AS a")->leftJoin("#__kunena_categories AS c ON a.item=c.id")->where("a.type='catid' AND c.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_attachmentOrphaned(): QueryInterface
    {
        // Query to find attachments which do not belong in any existing message. An upload which waits for its message (mesid 0:
        // the editor of a post, a private message) is no orphan: it was listed and deleted, and the post then lost its file.
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_attachments AS a")->leftJoin("#__kunena_messages AS m ON a.mesid=m.id")->where("a.mesid>0 AND m.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_categoryMissingAlias(): QueryInterface
    {
        // Query to find categories with missing alias
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_categories AS a")->leftJoin("#__kunena_aliases AS c ON a.alias=c.alias")->where("c.alias IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_categoryOrphaned(): QueryInterface
    {
        // Query to find orphaned categories
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_categories AS a")->leftJoin("#__kunena_categories AS c ON a.parentid=c.id")->where("a.parentid>0 AND c.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_categoryWrongAlias(): QueryInterface
    {
        // Query to find categories with wrong alias
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_categories AS a")->innerJoin("#__kunena_aliases AS c ON a.alias=c.alias")->where("c.type!='catid' OR c.item!=a.id");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_channelOrphaned(): QueryInterface
    {
        // Query to find user read which do not belong in any existing topic
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_categories")->where("channels IS NULL OR channels='none'");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_messageBodyMissingMessage(): QueryInterface
    {
        // Query to find broken messages (orphan message text)
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_messages_text AS a")->leftJoin("#__kunena_messages AS m ON a.mesid=m.id")->where("m.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_messageMissingMessageBody(): QueryInterface
    {
        // Query to find broken messages (message is missing body)
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_messages AS a")->leftJoin("#__kunena_messages_text AS t ON t.mesid=a.id")->where("t.mesid IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_messageOrphaned(): QueryInterface
    {
        // Query to find messages which do not belong in any existing topic
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_messages AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.thread")->leftJoin("#__kunena_messages_text AS mt ON a.id=mt.mesid")->where("t.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     * @since   Kunena 6.0
     */
    protected static function query_messageWrongCategory(): QueryInterface
    {
        // Query to find messages which have wrong category id
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_messages AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.thread")->leftJoin("#__kunena_messages_text AS mt ON a.id=mt.mesid")->where("t.category_id!=a.catid");

        return $query;
    }

    /**
     * @return  QueryInterface
     * @since   Kunena 6.0
     */
    protected static function query_movedAndMessages(): QueryInterface
    {
        // Query to find topics without messages
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_topics AS a")->innerJoin("#__kunena_messages AS m ON m.thread=a.id")->leftJoin("#__kunena_messages_text AS t ON m.id=t.mesid")->where("a.moved_id>0");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_movedMissingTopic(): QueryInterface
    {
        // Query to find moved topics pointing to non-existent topic
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_topics AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.moved_id")->where("a.moved_id>0 AND t.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_ownerOrphaned(): QueryInterface
    {
        // Query to find user read which do not belong in any existing topic
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_topics AS t")->leftJoin("#__kunena_user_topics AS j ON j.topic_id=t.id")->where("t.first_post_userid > 0");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_pollOptionOrphaned(): QueryInterface
    {
        // Query to find poll options which do not belong in any existing poll
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_polls_options AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.pollid")->where("p.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_pollOrphaned(): QueryInterface
    {
        // Query to find polls which do not belong in any existing topic
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_polls AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.threadid")->where("t.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_pollTopicMismatch(): QueryInterface
    {
        // Query to find polls which do not belong in any existing topic
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_polls AS a")->innerJoin("#__kunena_topics AS t ON t.id=a.threadid")->leftJoin("#__kunena_topics AS tt ON tt.poll_id=a.id")->where("t.poll_id!=a.id");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_pollUserOrphaned(): QueryInterface
    {
        // Query to find poll users which do not belong in any existing poll
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_polls_users AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.pollid")->where("p.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_ratingOrphaned(): QueryInterface
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();

        // Query to find orphaned ratings
        $query->from("#__kunena_rate AS r")->leftJoin("#__kunena_topics AS t ON t.id=r.topic_id")->where("t.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_thankyouOrphaned(): QueryInterface
    {
        // Query to find thankyous which do not belong in any existing message
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_thankyou AS a")->leftJoin("#__kunena_messages AS m ON m.id=a.postid")->where("m.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_reactionOrphaned(): QueryInterface
    {
        // Query to find reactions which do not belong in any existing message
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_reactions AS a")->leftJoin("#__kunena_messages AS m ON m.id=a.message_id")->where("m.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_historyOrphaned(): QueryInterface
    {
        // Query to find versions of the edit history which do not belong in any existing message
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_messages_history AS a")->leftJoin("#__kunena_messages AS m ON m.id=a.message_id")->where("m.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_digestOrphaned(): QueryInterface
    {
        // Query to find posts collected for the digests which do not belong in any existing message
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_digest AS a")->leftJoin("#__kunena_messages AS m ON m.id=a.message_id")->where("m.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_bookmarkOrphaned(): QueryInterface
    {
        // Query to find bookmarks which do not belong in any existing message
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_bookmarks AS a")->leftJoin("#__kunena_messages AS m ON m.id=a.message_id")->where("m.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_topicInSection(): QueryInterface
    {
        // Query to find topics which are located in section, not in category
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_topics AS a")->innerJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("c.parentid=0");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_topicMissingCategory(): QueryInterface
    {
        // Query to find topics which do not have existing category
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_topics AS a")->leftJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("c.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_topicMissingMessages(): QueryInterface
    {
        // Query to find topics without messages
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_topics AS a")->leftJoin("#__kunena_messages AS m ON m.thread=a.id")->where("a.moved_id=0 AND m.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_topicMissingPoll(): QueryInterface
    {
        // Query to find topics which have missing poll
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_topics AS a")->leftJoin("#__kunena_polls AS p ON p.id=a.poll_id")->where("a.moved_id=0 AND a.poll_id>0 AND p.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_topicPollMismatch(): QueryInterface
    {
        // Query to find polls which have wrong topic
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_topics AS a")->innerJoin("#__kunena_polls AS p ON p.id=a.poll_id")->leftJoin("#__kunena_topics AS t ON p.threadid=t.id")->where("a.moved_id=0 AND a.poll_id>0 AND p.threadid!=a.id");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_userAvatarOrphaned(): QueryInterface
    {
        // Query to find user read which do not belong in any existing topic
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_users")->where("avatar=''");

        return $query;
    }

    /**
     * Query to find the subscriptions and favorites of users who no longer exist.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_userTopicUserOrphaned(): QueryInterface
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_user_topics AS a")->leftJoin("#__users AS u ON u.id=a.user_id")->where("u.id IS NULL");

        return $query;
    }

    /**
     * Query to find the subscriptions and roles in categories of users who no longer exist.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_userCategoryUserOrphaned(): QueryInterface
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_user_categories AS a")->leftJoin("#__users AS u ON u.id=a.user_id")->where("u.id IS NULL");

        return $query;
    }

    /**
     * Query to find the follows of or by users who no longer exist.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_followOrphaned(): QueryInterface
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_user_follow AS a")->leftJoin("#__users AS u ON u.id=a.user_id")->leftJoin("#__users AS f ON f.id=a.followed_id")->where("u.id IS NULL OR f.id IS NULL");

        return $query;
    }

    /**
     * Query to find the ignores of or by users who no longer exist.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_ignoreOrphaned(): QueryInterface
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_user_ignore AS a")->leftJoin("#__users AS u ON u.id=a.user_id")->leftJoin("#__users AS i ON i.id=a.ignored_id")->where("u.id IS NULL OR i.id IS NULL");

        return $query;
    }

    /**
     * Query to find the forum profiles of Joomla users who no longer exist.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_profileOrphaned(): QueryInterface
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_users AS a")->leftJoin("#__users AS u ON u.id=a.userid")->where("u.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_userCategoryOrphaned(): QueryInterface
    {
        // Query to find user categories which do not belong in any existing category
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_user_categories AS a")->leftJoin("#__kunena_categories AS c ON c.id=a.category_id")->where("a.category_id>0 AND c.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_userReadOrphaned(): QueryInterface
    {
        // Query to find user read which do not belong in any existing topic
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_user_read AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("t.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_userReadWrongCategory(): QueryInterface
    {
        // Query to find user read which wrong category information
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_user_read AS a")->innerJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("a.category_id!=t.category_id");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_userTopicOrphaned(): QueryInterface
    {
        // Query to find user topics which do not belong in any existing topic
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_user_topics AS a")->leftJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("t.id IS NULL");

        return $query;
    }

    /**
     * @return  QueryInterface
     *
     * @since   Kunena 6.0
     */
    protected static function query_userTopicWrongCategory(): QueryInterface
    {
        // Query to find user topic which wrong category information
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->from("#__kunena_user_topics AS a")->innerJoin("#__kunena_topics AS t ON t.id=a.topic_id")->where("a.category_id!=t.category_id");

        return $query;
    }

    /**
     * Query to find the topics of a registered author which have no owner (no row of a user of the topic is the owner): the
     * author does not see the topic as his, and cannot edit it as its owner.
     *
     * @return  QueryInterface
     *
     * @since   Kunena 8.0
     */
    protected static function query_topicsOwnersOrphaned(): QueryInterface
    {
        $query = Factory::getContainer()->get('DatabaseDriver')->createQuery();
        $query->from('#__kunena_topics AS a')
            ->innerJoin('#__users AS u ON u.id=a.first_post_userid')
            ->leftJoin('(SELECT topic_id, MAX(owner) AS owner FROM #__kunena_user_topics GROUP BY topic_id) AS j ON j.topic_id=a.id')
            ->where('a.moved_id=0 AND COALESCE(j.owner, 0)=0');

        return $query;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected static function fields_topicsOwnersOrphaned(?QueryInterface $query = null): array
    {
        if ($query) {
            $query->select('a.id, a.category_id, a.subject, a.first_post_userid');
        }

        return ['first_post_userid' => 'invalid'];
    }

    /**
     * The author of the first post becomes the owner of the topic (his row of the topic is made, or marked as the owner).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    protected static function repair_topicsOwnersOrphaned(): bool
    {
        $select = self::query_topicsOwnersOrphaned();
        $select->select('a.id, a.first_post_userid, a.category_id, 1');
        $db = Factory::getContainer()->get('DatabaseDriver');
        $db->setQuery('INSERT INTO #__kunena_user_topics (topic_id, user_id, category_id, owner) ' . $select . ' ON DUPLICATE KEY UPDATE owner=1');

        return (bool) $db->execute();
    }
}
