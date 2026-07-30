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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Administrator\Model\TrashsModel;
use Kunena\Forum\Libraries\Email\KunenaEmail;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
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
        'purge.trash' => [
            'langConstPrefix' => 'PLG_TASK_KUNENA_PURGETRASH',
            'form'            => 'trashbin',
            'method'          => 'purgeTrash',
        ],
        'add.mailsqueue' => [
            'langConstPrefix' => 'PLG_TASK_KUNENA_SENDNOTIFICATIONS',
            'method'          => 'sendNotifications',
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
            'onContentPrepareForm' => 'enhanceTaskItemForm',
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

    /**
     * Method to purge trashbin.
     *
     * @param   ExecuteTaskEvent  $event  The `onExecuteTask` event.
     *
     * @since  7.0.0
     * @throws \Exception
     */
    private function purgeTrash(ExecuteTaskEvent $event): int
    {
        if (
            !ComponentHelper::isEnabled('com_kunena')
            || !KunenaForum::isCompatible('7.1')
            || !KunenaForum::installed()
        ) {
            return Status::NO_RUN;
        }

        $params      = $event->getArgument('params');
        $age         = $params->age;
        $app         = Factory::getApplication();
        $trashsModel = new TrashsModel();

        $app->input->set('layout', 'messages');
        $messages  = $trashsModel->getItems();
        $cid       = [];

        if (!empty($messages)) {
            $cid    = \array_column($messages, 'id');
            $result = $trashsModel->purgeMessages($cid, $age);

            if (!$result) {
                $msgs = $app->getMessageQueue();

                foreach ($msgs as $msg) {
                    $this->logTask($msg['message']);
                }

                return Status::KNOCKOUT;
            }
        }

        return Status::OK;
    }

    /**
     * Method to send queued notification emails in batches.
     *
     * @param   ExecuteTaskEvent  $event  The `onExecuteTask` event.
     *
     * @since  7.1.0
     * @throws \Exception
     */
    private function sendNotifications(ExecuteTaskEvent $event): int
    {
        if (
            !ComponentHelper::isEnabled('com_kunena')
            || !KunenaForum::isCompatible('7.1')
            || !KunenaForum::installed()
        ) {
            return Status::NO_RUN;
        }

        try {
            $config    = KunenaFactory::getConfig();
            $batchSize = !empty($config->emailBatchSize) ? (int) $config->emailBatchSize : 50;
            $db        = $this->getDatabase();

            $query = $db->createQuery()
                ->select('*')
                ->from($db->quoteName('#__kunena_notifications_mailsqueue'))
                ->where($db->quoteName('send') . ' = 0')
                ->setLimit($batchSize);

            $db->setQuery($query);
            $queuedItems = $db->loadObjectList();

            if (empty($queuedItems)) {
                $this->logTask(Text::_('PLG_TASK_KUNENA_SENDNOTIFICATIONS_NOTHING'));

                return Status::OK;
            }

            $sentCount = 0;

            foreach ($queuedItems as $item) {
                $receivers = json_decode($item->emailListJson, true);
                $once      = (bool) $item->once;
                $url       = $item->url;
                $subject   = $item->subject;

                $message = KunenaMessageHelper::get((int) $item->messageId);

                $mailnamesender = !empty($config->emailSenderName)
                    ? MailHelper::cleanAddress($config->emailSenderName)
                    : MailHelper::cleanAddress($config->boardTitle);

                $mailsubject = MailHelper::cleanSubject($subject . ' (' . $item->categoryName . ')');

                $mail = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
                $mail->setSubject($mailsubject);
                $mail->setSender([$config->email, $mailnamesender]);

                // Send to subscribers.
                if (!empty($receivers[1])) {
                    $message->attachEmailBody($mail, 1, $subject, $url, $once);
                    KunenaEmail::send($mail, $receivers[1]);
                }

                // Send to moderators.
                if (!empty($receivers[0])) {
                    $message->attachEmailBody($mail, 0, $subject, $url, $once);
                    KunenaEmail::send($mail, $receivers[0]);
                }

                $updateQuery = $db->createQuery()
                    ->update($db->quoteName('#__kunena_notifications_mailsqueue'))
                    ->set($db->quoteName('send') . ' = 1')
                    ->where($db->quoteName('id') . ' = ' . (int) $item->id);

                $db->setQuery($updateQuery);
                $db->execute();

                $sentCount++;
            }

            $this->logTask(Text::sprintf('PLG_TASK_KUNENA_SENDNOTIFICATIONSSUCCESS', $sentCount));
        } catch (\Exception $e) {
            $this->logTask(Text::sprintf('PLG_TASK_KUNENA_SENDNOTIFICATIONSERROR', $e->getMessage()));

            return Status::KNOCKOUT;
        }

        return Status::OK;
    }
}
