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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Administrator\Model\TrashsModel;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;

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
        'sending.notificationskunena' => [
            'langConstPrefix' => 'PLG_TASK_SENDNOTIFICATIONSKUNENA_SENDING',
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
     * Method to retrieve the list of mails to send from the table and send them.
     *
     * @param   ExecuteTaskEvent  $event  The `onExecuteTask` event.
     *
     * @return integer  The routine exit code.
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
        
        $config = KunenaFactory::getConfig();
        
        if (!$config->email) {
            KunenaError::warning(Text::_('COM_KUNENA_EMAIL_DISABLED'));
            
            return false;
        } elseif (!MailHelper::isEmailAddress($config->email)) {
            KunenaError::warning(Text::_('COM_KUNENA_EMAIL_INVALID'));
            
            return false;
        }
        
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $batchSize =  $config->emailBatchSize;
        $processedCount = 0;
        
        // Process the notifications by batch
        do {
            $query = $db->createQuery()
                ->select('*')
                ->from($db->quoteName('#__kunena_notifications_mailsqueue'))
                ->where($db->quoteName('send') . ' = ' . $db->quote('0'))
                ->order($db->quoteName('id') . ' ASC')
                ->setLimit($batchSize);
            
            $db->setQuery($query);
            
            try {
                $mailsToSend = $db->loadObjectList();
            } catch (ExecutionFailureException $e) {
                $mailsToSend = [];
            }
            
            // If no more notifications to process, we exit the loop
            if (empty($mailsToSend)) {
                break;
            }
            
            // Table to store the IDs of the notifications which are processed
            $processedIds = [];
            
            foreach($mailsToSend as $mail) {
                $message = KunenaMessageHelper::get($mail->messageId);
                $emailToList = json_decode($mail->emailListJson);
                $sentusers = json_decode($mail->sentusers);
                
                $message->sendNotificationNow($emailToList, $mail->subject, $mail->url, $mail->once, $sentusers);
                
                // Add the ID in the list of processed
                $processedIds[] = $mail->id;
            }
            
            // Mark the notifications as send
            if (!empty($processedIds)) {
                $updateQuery = $db->createQuery()
                    ->update($db->quoteName('#__kunena_notifications_mailsqueue'))
                    ->set($db->quoteName('send') . ' = ' . $db->quote('1'))
                    ->where($db->quoteName('id') . ' IN (' . implode(',', $processedIds) . ')');
                
                $db->setQuery($updateQuery);
                
                try {
                    $db->execute();
                    $processedCount += count($processedIds);
                } catch (ExecutionFailureException $e) {
                    // If an error is throwed, teh error is logged and we continue
                    $this->logTask(Text::sprintf('PLG_TASK_SENDNOTIFICATIONSKUNENA_ERROR_UPDATING', $e->getMessage()));
                }
            }
            
        } while (!empty($mailsToSend));
        
        // Log the number of notifications processed
        if ($processedCount > 0) {
            $this->logTask(Text::sprintf('PLG_TASK_SENDNOTIFICATIONSKUNENA_PROCESSED', $processedCount));
        }
        
        return Status::OK;
    }
}
