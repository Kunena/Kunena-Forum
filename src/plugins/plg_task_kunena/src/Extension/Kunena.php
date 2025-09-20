<?php

/**
 * Kunena Plugin
 *
 * @package        Kunena.Plugins
 * @subpackage     Task
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Task\Kunena\Extension;

// No direct access
\defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\User\KunenaBan;

/**
 * A task plugin.
 * {@see ExecuteTaskEvent}.
 *
 * @since 7.0.0
 */
final class Kunena extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;
    use TaskPluginTrait;

    /**
     * @var string[]
     */
    private const TASKS_MAP = [
        'remove.expiredbans' => [
            'langConstPrefix' => 'PLG_TASK_KUNENA_REMOVEEXPIREDBANS',
            'method'          => 'removeExpiredBans',
        ],
    ];

    /**
     * @var boolean
     */
    protected $autoloadLanguage = true;

    /**
     * @inheritDoc
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onTaskOptionsList'    => 'advertiseRoutines',
            'onExecuteTask'        => 'standardRoutineHandler',
        ];
    }

    /**
     * Method to remove expired bans.
     *
     * @param   ExecuteTaskEvent  $event  The `onExecuteTask` event.
     *
     * @since  7.0.0
     * @throws \Exception
     */
    private function removeExpiredBans(ExecuteTaskEvent $event): int
    {
        if (!\array_key_exists($event->getRoutineId(), self::TASKS_MAP)) {
            return Status::NO_TASK;
        }

        try {
            /** @var DatabaseDriver */
            $db = $this->getDatabase();
            $now = new Date();

            // Find expired site-wide bans
            $query = $db->createQuery()
                ->select('b.*')
                ->from($db->quoteName('#__kunena_users_banned', 'b'))
                ->where($db->quoteName('b.expiration') . ' <= ' . $db->quote($now->toSql()))
                ->where($db->quoteName('b.blocked') . ' = 1')
                ->where($db->quoteName('b.expiration') . ' != ' . $db->quote('9999-12-31 23:59:59'));

            $db->setQuery($query);
            $expiredBans      = $db->loadObjectList();
            $countExpiredbans = \count($expiredBans ?? []);

            foreach ($expiredBans as $ban) {
                // Unblock user in Joomla
                $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($ban->userid);
                if ($user && $user->block) {
                    $user->block = 0;
                    $user->save();
                }

                // Update Kunena user profile
                $profile = KunenaFactory::getUser($ban->userid);
                $profile->banned = \null;
                $profile->save(\true);

                // Update ban record
                $banInstance = KunenaBan::getInstance($ban->id);
                if ($banInstance->exists()) {
                    $banInstance->addComment(Text::_('PLG_TASK_KUNENA_REMOVEEXPIREDBANSCOMMENT'));
                    $banInstance->modified_time = $now->toSql();
                    $banInstance->save(\true);
                }
            }

            $this->logTask(Text::sprintf('PLG_TASK_KUNENA_REMOVEEXPIREDBANSSUCCESS', $countExpiredbans));
        } catch (\Exception $e) {
            $this->logTask(Text::sprintf('PLG_TASK_KUNENA_REMOVEEXPIREDBANSERROR', $e->getMessage()));

            return Status::KNOCKOUT;
        }

        return Status::OK;
    }
}
