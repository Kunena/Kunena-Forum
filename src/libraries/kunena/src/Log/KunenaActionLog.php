<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Libraries
 * @subpackage    Log
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Log;

\defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use ReflectionClass;

/**
 * Copies the moderation entries of the Kunena log to the User Actions Log of Joomla (com_actionlogs).
 *
 * Only an allow-list of the entry goes there: the actor, the operation, and the ids and names of the topic, the category and
 * the target user. The data of the entry (reasons, comments, options) and the IP address never do.
 *
 * @since   Kunena 8.0
 */
class KunenaActionLog
{
    /**
     * Write the moderation entries to the action log of Joomla, when the option of Kunena is on, com_actionlogs is enabled and
     * com_kunena is one of its "Events To Log".
     *
     * @param   KunenaEntry[]  $entries  The entries.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function write(array $entries): void
    {
        if (!KunenaFactory::getConfig()->logModerationActionlog || !ComponentHelper::isEnabled('com_actionlogs')) {
            return;
        }

        if (!\in_array('com_kunena', (array) ComponentHelper::getParams('com_actionlogs')->get('loggable_extensions', []), true)) {
            return;
        }

        $model = null;

        foreach ($entries as $entry) {
            $data    = $entry->getData();
            $message = static::buildMessage($data, $entry->getNames());

            if ($message === null) {
                continue;
            }

            $model ??= Factory::getApplication()->bootComponent('com_actionlogs')->getMVCFactory()
                ->createModel('Actionlog', 'Administrator', ['ignore_request' => true]);

            $model->addLog([$message['message']], $message['key'], 'com_kunena', (int) $data['user_id']);
        }
    }

    /**
     * The message of the action log for an entry of the Kunena log, or null when the entry is no moderation entry.
     *
     * @param   array  $data   The data of the entry (KunenaEntry::getData()).
     * @param   array  $names  The names of the entry (KunenaEntry::getNames()).
     *
     * @return  array|null  ['key' => the language key, 'message' => the values of its placeholders]
     *
     * @since   Kunena 8.0
     */
    public static function buildMessage(array $data, array $names): ?array
    {
        if ((int) ($data['type'] ?? -1) !== KunenaLog::TYPE_MODERATION) {
            return null;
        }

        $userId       = (int) ($data['user_id'] ?? 0);
        $topicId      = (int) ($data['topic_id'] ?? 0);
        $categoryId   = (int) ($data['category_id'] ?? 0);
        $targetUserId = (int) ($data['target_user'] ?? 0);

        $key = 'COM_KUNENA_ACTIONLOG_MODERATION';

        if ($topicId > 0) {
            $key .= '_TOPIC';
        } elseif ($categoryId > 0) {
            $key .= '_CATEGORY';
        }

        if ($targetUserId > 0) {
            $key .= '_USER';
        }

        return [
            'key'     => $key,
            'message' => [
                'id'             => (string) $topicId,
                'type'           => static::operationLabel((string) ($data['operation'] ?? '')),
                'userid'         => (string) $userId,
                'accountlink'    => 'index.php?option=com_users&task=user.edit&id=' . $userId,
                'topicid'        => (string) $topicId,
                'categoryid'     => (string) $categoryId,
                'targetuserid'   => (string) $targetUserId,
                'targetlink'     => $targetUserId > 0 ? 'index.php?option=com_users&task=user.edit&id=' . $targetUserId : '',
                'username'       => static::noPlaceholders($names['user'] ?? ''),
                'title'          => static::noPlaceholders($names['topic'] ?? ''),
                'category'       => static::noPlaceholders($names['category'] ?? ''),
                'targetusername' => static::noPlaceholders($names['target_user'] ?? ''),
            ],
        ];
    }

    /**
     * The operations of KunenaLog which have a label in the action log: the LOG_ constants, without the errors and the e-mail
     * notifications.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function knownOperations(): array
    {
        $operations = [];

        foreach ((new ReflectionClass(KunenaLog::class))->getConstants() as $name => $value) {
            if (
                str_starts_with($name, 'LOG_') && !str_starts_with($name, 'LOG_ERROR_')
                && !str_starts_with($name, 'LOG_EMAIL_NOTIFICATION_')
            ) {
                $operations[] = (string) $value;
            }
        }

        return $operations;
    }

    /**
     * The label of an operation: a language key for an operation of Kunena (Joomla translates it when it shows the log), a
     * readable text of its name for any other operation (of a plugin).
     *
     * @param   string  $operation  The operation.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function operationLabel(string $operation): string
    {
        if (\in_array($operation, static::knownOperations(), true)) {
            return 'COM_KUNENA_ACTIONLOG_OPERATION_' . preg_replace('/^LOG_/', '', $operation);
        }

        return ucfirst(strtolower(str_replace('_', ' ', preg_replace('/^LOG_/', '', $operation))));
    }

    /**
     * A value written by a user, with its braces made round, so that Joomla can never read it as a placeholder.
     *
     * @param   mixed  $value  The value.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function noPlaceholders(mixed $value): string
    {
        return str_replace(['{', '}'], ['(', ')'], (string) $value);
    }
}
