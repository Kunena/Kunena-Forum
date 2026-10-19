<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Draft
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Draft;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Drafts: messages a user has saved to finish and post later.
 *
 * A draft keeps the posted form fields as JSON, so that it can be loaded back into the editor exactly like
 * the fields of a post which failed. The subject and message are also stored in their own columns for the list.
 *
 * @since   Kunena 7.2
 */
abstract class KunenaDraftHelper
{
    /**
     * Maximum number of drafts a user can keep.
     *
     * @since   Kunena 7.2
     */
    public const MAX_DRAFTS = 50;

    /**
     * Form fields which are kept in a draft (values are the defaults the editor needs).
     *
     * @var     array
     * @since   Kunena 7.2
     */
    protected static $defaults = [
        'catid'             => 0,
        'name'              => '',
        'email'             => null,
        'subject'           => '',
        'message'           => '',
        'modified_reason'   => '',
        'icon_id'           => null,
        'anonymous'         => 0,
        'poll_title'        => '',
        'poll_options'      => [],
        'poll_time_to_live' => 0,
        'subscribe'         => 0,
        'private'           => '',
        'rating'            => 0,
        'params'            => '',
        'quote'             => 0,
    ];

    /**
     * Save a draft, a new one or an update of an existing draft of the user.
     *
     * @param   int    $userid    Owner of the draft.
     * @param   array  $fields    Posted form fields.
     * @param   int    $parentId  Message which is replied to (0 for a new topic).
     * @param   int    $topicId   Topic which is replied to (0 for a new topic).
     * @param   int    $draftId   Existing draft to update (0 creates a new draft).
     * @param   int    $messageId Message which is being edited (0 if the draft is not an edit).
     *
     * @return  int  Id of the draft, or 0 if it was not possible to save it.
     *
     * @since   Kunena 7.2
     */
    public static function save(int $userid, array $fields, int $parentId = 0, int $topicId = 0, int $draftId = 0, int $messageId = 0): int
    {
        if ($userid < 1) {
            return 0;
        }

        $fields  = array_intersect_key($fields, static::$defaults) + static::$defaults;
        $subject = mb_substr(trim((string) $fields['subject']), 0, 255);
        $message = (string) $fields['message'];
        $catid   = (int) $fields['catid'];
        $json    = json_encode($fields, JSON_INVALID_UTF8_SUBSTITUTE);
        $now     = time();
        $db      = Factory::getContainer()->get(DatabaseInterface::class);

        if ($draftId && static::get($draftId, $userid)) {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__kunena_drafts'))
                ->set($db->quoteName('catid') . ' = :catid')
                ->set($db->quoteName('parent_id') . ' = :parent')
                ->set($db->quoteName('topic_id') . ' = :topic')
                ->set($db->quoteName('message_id') . ' = :mesid')
                ->set($db->quoteName('subject') . ' = :subject')
                ->set($db->quoteName('message') . ' = :message')
                ->set($db->quoteName('fields') . ' = :fields')
                ->set($db->quoteName('modified_time') . ' = :modified')
                ->where($db->quoteName('id') . ' = :id')
                ->where($db->quoteName('user_id') . ' = :userid')
                ->bind(':catid', $catid, ParameterType::INTEGER)
                ->bind(':parent', $parentId, ParameterType::INTEGER)
                ->bind(':topic', $topicId, ParameterType::INTEGER)
                ->bind(':mesid', $messageId, ParameterType::INTEGER)
                ->bind(':subject', $subject)
                ->bind(':message', $message)
                ->bind(':fields', $json)
                ->bind(':modified', $now, ParameterType::INTEGER)
                ->bind(':id', $draftId, ParameterType::INTEGER)
                ->bind(':userid', $userid, ParameterType::INTEGER);
            $db->setQuery($query)->execute();

            return $draftId;
        }

        if (static::count($userid) >= static::MAX_DRAFTS) {
            return 0;
        }

        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__kunena_drafts'))
            ->columns($db->quoteName(['user_id', 'catid', 'parent_id', 'topic_id', 'message_id', 'subject', 'message', 'fields', 'created_time', 'modified_time']))
            ->values(':userid, :catid, :parent, :topic, :mesid, :subject, :message, :fields, :created, :modified')
            ->bind(':userid', $userid, ParameterType::INTEGER)
            ->bind(':catid', $catid, ParameterType::INTEGER)
            ->bind(':parent', $parentId, ParameterType::INTEGER)
            ->bind(':topic', $topicId, ParameterType::INTEGER)
            ->bind(':mesid', $messageId, ParameterType::INTEGER)
            ->bind(':subject', $subject)
            ->bind(':message', $message)
            ->bind(':fields', $json)
            ->bind(':created', $now, ParameterType::INTEGER)
            ->bind(':modified', $now, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        return (int) $db->insertid();
    }

    /**
     * Get a draft of the user.
     *
     * @param   int  $id      Draft id.
     * @param   int  $userid  Owner of the draft.
     *
     * @return  object|null  The draft row with the form fields decoded into $draft->fields, or null.
     *
     * @since   Kunena 7.2
     */
    public static function get(int $id, int $userid): ?object
    {
        if ($id < 1 || $userid < 1) {
            return null;
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__kunena_drafts'))
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('user_id') . ' = :userid')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->bind(':userid', $userid, ParameterType::INTEGER);
        $draft = $db->setQuery($query)->loadObject();

        if (!$draft) {
            return null;
        }

        $fields = json_decode((string) $draft->fields, true);
        $draft->fields = array_intersect_key(\is_array($fields) ? $fields : [], static::$defaults) + static::$defaults;

        return $draft;
    }

    /**
     * Get the drafts of the user, the most recently changed first.
     *
     * @param   int  $userid  Owner of the drafts.
     *
     * @return  array
     *
     * @since   Kunena 7.2
     */
    public static function getAll(int $userid): array
    {
        if ($userid < 1) {
            return [];
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'catid', 'parent_id', 'topic_id', 'message_id', 'subject', 'message', 'created_time', 'modified_time']))
            ->from($db->quoteName('#__kunena_drafts'))
            ->where($db->quoteName('user_id') . ' = :userid')
            ->order($db->quoteName('modified_time') . ' DESC')
            ->bind(':userid', $userid, ParameterType::INTEGER);

        return (array) $db->setQuery($query, 0, static::MAX_DRAFTS)->loadObjectList();
    }

    /**
     * Count the drafts of the user.
     *
     * @param   int  $userid  Owner of the drafts.
     *
     * @return  int
     *
     * @since   Kunena 7.2
     */
    public static function count(int $userid): int
    {
        if ($userid < 1) {
            return 0;
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kunena_drafts'))
            ->where($db->quoteName('user_id') . ' = :userid')
            ->bind(':userid', $userid, ParameterType::INTEGER);

        return (int) $db->setQuery($query)->loadResult();
    }

    /**
     * Delete a draft of the user.
     *
     * @param   int  $id      Draft id.
     * @param   int  $userid  Owner of the draft.
     *
     * @return  boolean  True if a draft was deleted.
     *
     * @since   Kunena 7.2
     */
    public static function delete(int $id, int $userid): bool
    {
        if ($id < 1 || $userid < 1) {
            return false;
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__kunena_drafts'))
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('user_id') . ' = :userid')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->bind(':userid', $userid, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        return $db->getAffectedRows() > 0;
    }
}
