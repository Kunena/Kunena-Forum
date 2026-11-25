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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Scheduler\Administrator\Scheduler\Scheduler;
use Joomla\Database\ParameterType;
use Throwable;

/**
 * The scheduled tasks of Kunena (tool "Scheduled tasks"): which routines of the plugin plg_task_kunena have a task in the
 * scheduler of Joomla, when they ran, whether they failed; and a task run now.
 *
 * @since   Kunena 8.0
 */
class KunenaScheduledTasks
{
    /**
     * The routines of plg_task_kunena: id => prefix of its language keys (_TITLE, _DESC).
     *
     * @since   Kunena 8.0
     */
    public const ROUTINES = [
        'add.mailsqueue'            => 'PLG_TASK_SENDNOTIFICATIONSKUNENA_SENDING',
        'send.digests'              => 'PLG_TASK_KUNENA_SENDDIGESTS',
        'publish.scheduledreplies'  => 'PLG_TASK_KUNENA_PUBLISHSCHEDULED',
        'remove.expiredbans'        => 'PLG_TASK_KUNENA_REMOVEEXPIREDBANS',
        'clean.pendingattachments'  => 'PLG_TASK_KUNENA_CLEANPENDINGATTACHMENTS',
        'purge.trash'               => 'PLG_TASK_KUNENA_PURGETRASH',
        'unsubscribe.inactiveusers' => 'PLG_TASK_KUNENA_UNSUBSCRIBEINACTIVE',
    ];

    /**
     * The state of a task from its row.
     *
     * @param   array|null  $task  The row of #__scheduler_tasks (null: the routine has no task).
     * @param   int         $now   The current time.
     *
     * @return  string  missing, disabled, failed, late, never, ok.
     *
     * @since   Kunena 8.0
     */
    public static function state(?array $task, int $now): string
    {
        if ($task === null) {
            return 'missing';
        }

        if ((int) $task['state'] !== 1) {
            return 'disabled';
        }

        // 0: done, 123: will resume (a long task which goes on at the next run).
        if (isset($task['last_exit_code']) && $task['last_exit_code'] !== '' && !\in_array((int) $task['last_exit_code'], [0, 123], true)
            && !empty($task['last_execution'])) {
            return 'failed';
        }

        if (empty($task['last_execution'])) {
            return 'never';
        }

        // Due for more than a day: the scheduler does not run (no visitor, the lazy scheduler off, no cron).
        $next = !empty($task['next_execution']) ? (int) strtotime($task['next_execution'] . ' UTC') : 0;

        if ($next > 0 && $now - $next > 86400) {
            return 'late';
        }

        return 'ok';
    }

    /**
     * The routines with their task on this site.
     *
     * @return  array<string, array{prefix: string, task: array|null, state: string}>
     *
     * @since   Kunena 8.0
     */
    public static function listOnSite(): array
    {
        $tasks = [];

        try {
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery()
                ->select($db->quoteName(['id', 'title', 'type', 'state', 'last_exit_code', 'last_execution', 'next_execution', 'times_executed', 'times_failed', 'locked']))
                ->from($db->quoteName('#__scheduler_tasks'))
                ->whereIn($db->quoteName('type'), array_keys(self::ROUTINES), ParameterType::STRING)
                ->where($db->quoteName('state') . ' >= 0')
                ->order($db->quoteName('state') . ' DESC, ' . $db->quoteName('id'));

            foreach ((array) $db->setQuery($query)->loadAssocList() as $row) {
                // One task per routine: the first enabled one.
                $tasks[$row['type']] ??= $row;
            }
        } catch (Throwable $e) {
            // The scheduler of Joomla without its table.
        }

        $list = [];

        foreach (self::ROUTINES as $routine => $prefix) {
            $task           = $tasks[$routine] ?? null;
            $list[$routine] = ['prefix' => $prefix, 'task' => $task, 'state' => self::state($task, time())];
        }

        return $list;
    }

    /**
     * Is the plugin of the routines enabled, and is the lazy scheduler on (it runs the tasks on the visits)?
     *
     * @return  array{plugin: bool, lazy: bool}
     *
     * @since   Kunena 8.0
     */
    public static function environment(): array
    {
        $lazy = false;

        try {
            $lazy = PluginHelper::isEnabled('system', 'schedulerunner')
                && (bool) ComponentHelper::getParams('com_scheduler')->get('lazy_scheduler.enabled', true);
        } catch (Throwable $e) {
            // Not known: shown as off.
        }

        return ['plugin' => PluginHelper::isEnabled('task', 'kunena'), 'lazy' => $lazy];
    }

    /**
     * Runs a task of Kunena now.
     *
     * @param   int  $id  The id of the task.
     *
     * @return  array{ok: bool, code: int|null}
     *
     * @since   Kunena 8.0
     */
    public static function run(int $id): array
    {
        $routines = array_keys(self::ROUTINES);
        $task     = null;

        foreach (self::listOnSite() as $routine => $item) {
            if ($item['task'] !== null && (int) $item['task']['id'] === $id && \in_array($routine, $routines, true)) {
                $task = $item['task'];
            }
        }

        if ($task === null || !class_exists(Scheduler::class)) {
            return ['ok' => false, 'code' => null];
        }

        try {
            $result = (new Scheduler())->runTask(['id' => $id, 'allowDisabled' => true, 'allowConcurrent' => false]);
        } catch (Throwable $e) {
            return ['ok' => false, 'code' => null];
        }

        if ($result === null) {
            return ['ok' => false, 'code' => null];
        }

        return ['ok' => $result->isSuccess(), 'code' => (int) ($result->getContent()['status'] ?? -1)];
    }
}
