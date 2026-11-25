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
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Log\KunenaLog;

/**
 * Who changed which setting of Kunena, and when (tool "Settings history"). Each save of the configuration (and each import
 * of a snapshot) writes the settings it changed, with the old and the new value, to the log of Kunena. A secret (a key,
 * a password) is written as changed, without its values.
 *
 * @since   Kunena 8.0
 */
class KunenaSettingsHistory
{
    /**
     * The value written for a secret.
     *
     * @since   Kunena 8.0
     */
    public const HIDDEN = '***';

    /**
     * The changes shown.
     *
     * @since   Kunena 8.0
     */
    public const LIMIT = 100;

    /**
     * The settings which differ: name => [old, new]. Only the scalar settings (the lists in the params, such as the badges, are
     * not settings of the forum).
     *
     * @param   array  $before  The params before the save.
     * @param   array  $after   The params after the save.
     *
     * @return  array<string, array{0: string, 1: string}>
     *
     * @since   Kunena 8.0
     */
    public static function diff(array $before, array $after): array
    {
        $changes = [];

        foreach (array_unique(array_merge(array_keys($before), array_keys($after))) as $name) {
            $name = (string) $name;
            $old  = $before[$name] ?? '';
            $new  = $after[$name] ?? '';

            if (!\is_scalar($old) && $old !== null || !\is_scalar($new) && $new !== null) {
                continue;
            }

            $old = KunenaSettingsSnapshot::normal($old);
            $new = KunenaSettingsSnapshot::normal($new);

            if ($old === $new) {
                continue;
            }

            $changes[$name] = KunenaSettingsSnapshot::isPrivate($name) ? [self::HIDDEN, self::HIDDEN] : [$old, $new];
        }

        ksort($changes);

        return $changes;
    }

    /**
     * The params of Kunena as they are stored now (not the cached configuration).
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function paramsOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_kunena'));
        $params = json_decode((string) $db->setQuery($query)->loadResult(), true);

        return \is_array($params) ? $params : [];
    }

    /**
     * Writes the changes of a save to the log (nothing when nothing changed).
     *
     * @param   array   $before  The params before the save (paramsOnSite()).
     * @param   string  $source  "config" (the configuration) or "import" (a snapshot).
     *
     * @return  int  The settings changed.
     *
     * @since   Kunena 8.0
     */
    public static function recordOnSite(array $before, string $source = 'config'): int
    {
        $changes = self::diff($before, self::paramsOnSite());

        if (!$changes) {
            return 0;
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $type  = KunenaLog::TYPE_ADMINISTRATION;
        $user  = (int) Factory::getApplication()->getIdentity()->id;
        $time  = time();
        $op    = KunenaLog::LOG_CONFIG_EDIT;
        $data  = (string) json_encode(['source' => $source, 'changes' => $changes]);
        $empty = '';
        $query = $db->createQuery()
            ->insert($db->quoteName('#__kunena_logs'))
            ->columns($db->quoteName(['type', 'user_id', 'category_id', 'topic_id', 'target_user', 'ip', 'time', 'operation', 'data']))
            ->values(':type, :user, 0, 0, 0, :ip, :time, :op, :data')
            ->bind(':type', $type, ParameterType::INTEGER)
            ->bind(':user', $user, ParameterType::INTEGER)
            ->bind(':ip', $empty)
            ->bind(':time', $time, ParameterType::INTEGER)
            ->bind(':op', $op)
            ->bind(':data', $data);
        $db->setQuery($query)->execute();

        return \count($changes);
    }

    /**
     * The changes from the rows of the log, the newest first.
     *
     * @param   array  $rows  id, user_id, username, time, data.
     *
     * @return  array<int, array{id: int, user: int, username: string, time: int, source: string, changes: array}>
     *
     * @since   Kunena 8.0
     */
    public static function entries(array $rows): array
    {
        $entries = [];

        foreach ($rows as $row) {
            $data = json_decode((string) $row['data'], true);

            if (!\is_array($data) || !\is_array($data['changes'] ?? null)) {
                continue;
            }

            $changes = [];

            foreach ($data['changes'] as $name => $values) {
                if (\is_array($values) && \count($values) === 2) {
                    $changes[(string) $name] = [(string) $values[0], (string) $values[1]];
                }
            }

            $entries[] = [
                'id'       => (int) $row['id'],
                'user'     => (int) $row['user_id'],
                'username' => (string) ($row['username'] ?? ''),
                'time'     => (int) $row['time'],
                'source'   => ($data['source'] ?? '') === 'import' ? 'import' : 'config',
                'changes'  => $changes,
            ];
        }

        return $entries;
    }

    /**
     * The changes of this site, the newest first, of a setting when one is given.
     *
     * @param   string  $setting  Only the changes of this setting ('' all).
     *
     * @return  array{entries: array, setting: string}
     *
     * @since   Kunena 8.0
     */
    public static function historyOnSite(string $setting = ''): array
    {
        $setting = preg_match('/^\w+$/', $setting) ? $setting : '';
        $op      = KunenaLog::LOG_CONFIG_EDIT;
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $query   = $db->createQuery()
            ->select($db->quoteName(['l.id', 'l.user_id', 'l.time', 'l.data', 'u.username']))
            ->from($db->quoteName('#__kunena_logs', 'l'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('l.user_id'))
            ->where($db->quoteName('l.operation') . ' = :op')
            ->bind(':op', $op)
            ->order($db->quoteName('l.id') . ' DESC');

        if ($setting !== '') {
            $like = '%' . $db->escape(json_encode($setting), true) . ':%';
            $query->where($db->quoteName('l.data') . ' LIKE :like')->bind(':like', $like);
        }

        $entries = self::entries((array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList());

        if ($setting !== '') {
            foreach ($entries as &$entry) {
                $entry['changes'] = array_intersect_key($entry['changes'], [$setting => true]);
            }

            unset($entry);
            $entries = array_values(array_filter($entries, static fn ($entry) => $entry['changes']));
        }

        return ['entries' => $entries, 'setting' => $setting];
    }
}
