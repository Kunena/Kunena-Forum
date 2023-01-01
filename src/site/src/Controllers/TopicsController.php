<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controllers;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Attachment\KunenaFinder;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Log\KunenaLog;

/**
 * Kunena Topics Controller
 *
 * @since   Kunena 2.0
 */
class TopicsController extends KunenaController
{
    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function none()
    {
        $this->app->enqueueMessage(Text::_('COM_KUNENA_CONTROLLER_NO_TASK'), 'error');
        $this->setRedirectBack();
    }

    /**
     * @return  boolean|void
     *
     * @throws  null
     * @throws  void
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function permdel()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $message = '';
        $ids     = array_keys($this->app->input->get('topics', [], 'post', 'array'));
        $ids     = ArrayHelper::toInteger($ids);

        $topics = KunenaTopicHelper::getTopics($ids);

        if (!$topics) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
            $this->setRedirectBack();
        } else {
            $messages = KunenaMessageHelper::getMessagesByTopics($ids);

            foreach ($topics as $topic) {
                if ($topic->isAuthorised('permdelete') && $topic->delete()) {
                    // Activity integration
                    $activity = KunenaFactory::getActivityIntegration();
                    $activity->onAfterDeleteTopic($topic);
                    $message = Text::_('COM_KUNENA_BULKMSG_DELETED');
                    KunenaCategoryHelper::recount($topic->getCategory()->id);
                } else {
                    $this->app->enqueueMessage($topic->getError(), 'error');
                }
            }

            // Delete attachments in each message
            $finder = new KunenaFinder();
            $finder->where('mesid', 'IN', array_keys($messages));
            $attachments = $finder->find();

            if ($finder->count() > 0) {
                foreach ($attachments as $instance) {
                    $instance->exists(false);
                    unset($instance);
                }

                $query = $this->db->getQuery(true)->select(['a.id'])
                    ->from($this->db->quoteName('#__kunena_attachments', 'a'))
                    ->leftJoin($this->db->quoteName('#__kunena_messages', 'm') . ' ON ' . $this->db->quoteName('a.mesid') . '=' . $this->db->quoteName('m.id'))
                    ->where($this->db->quoteName('m.id') . ' IS NULL');
                $this->db->setQuery($query);

                try {
                    $list = $this->db->loadObjectList('id');
                } catch (ExecutionFailureException $e) {
                    KunenaError::displayDatabaseError($e);

                    return false;
                }

                $ids = implode(',', array_keys($list));

                $query = $this->db->getQuery(true)->delete($this->db->quoteName('#__kunena_attachments'))->where('id IN (' . $ids . ')');
                $this->db->setQuery($query);

                try {
                    $this->db->execute();
                } catch (ExecutionFailureException $e) {
                    KunenaError::displayDatabaseError($e);

                    return false;
                }
            }
        }

        if ($message) {
            if ($this->config->logModeration) {
                foreach ($topics as $topic) {
                    KunenaLog::log(
                        KunenaLog::TYPE_MODERATION,
                        KunenaLog::LOG_TOPIC_DESTROY,
                        ['topic_ids' => $ids],
                        $topic->getCategory(),
                        $topic,
                        null
                    );
                }
            }

            $this->app->enqueueMessage($message, 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function delete()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
        $ids = ArrayHelper::toInteger($ids);

        $message = '';
        $topics  = KunenaTopicHelper::getTopics($ids);

        if (!$topics) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
            $this->setRedirectBack();
        } else {
            foreach ($topics as $topic) {
                if ($topic->isAuthorised('delete') && $topic->publish(KunenaForum::TOPIC_DELETED)) {
                    $message = Text::_('COM_KUNENA_BULKMSG_DELETED');
                } else {
                    $this->app->enqueueMessage($topic->getError(), 'error');
                }
            }
        }

        if ($message) {
            if ($this->config->logModeration) {
                foreach ($topics as $topic) {
                    KunenaLog::log(
                        KunenaLog::TYPE_MODERATION,
                        KunenaLog::LOG_TOPIC_DELETE,
                        ['topic_ids' => $ids],
                        $topic->getCategory(),
                        $topic,
                        null
                    );
                }
            }

            $this->app->enqueueMessage($message, 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function restore()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
        $ids = ArrayHelper::toInteger($ids);

        $message = '';
        $topics  = KunenaTopicHelper::getTopics($ids);

        if (!$topics) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
            $this->setRedirectBack();
        } else {
            foreach ($topics as $topic) {
                if ($topic->isAuthorised('undelete') && $topic->publish(KunenaForum::PUBLISHED)) {
                    $message = Text::_('COM_KUNENA_POST_SUCCESS_UNDELETE');
                } else {
                    $this->app->enqueueMessage($topic->getError(), 'error');
                }
            }
        }

        if ($message) {
            if ($this->config->logModeration) {
                foreach ($topics as $topic) {
                    KunenaLog::log(
                        KunenaLog::TYPE_MODERATION,
                        KunenaLog::LOG_TOPIC_UNDELETE,
                        ['topic_ids' => $ids],
                        $topic->getCategory(),
                        $topic,
                        null
                    );
                }
            }

            $this->app->enqueueMessage($message, 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function approve()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
        $ids = ArrayHelper::toInteger($ids);

        $message = '';
        $topics  = KunenaTopicHelper::getTopics($ids);

        if (!$topics) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'error');
            $this->setRedirectBack();
        } else {
            foreach ($topics as $topic) {
                if ($topic->isAuthorised('approve') && $topic->publish(KunenaForum::PUBLISHED)) {
                    $message = Text::_('COM_KUNENA_MODERATE_APPROVE_SUCCESS');
                    $topic->sendNotification();
                } else {
                    $this->app->enqueueMessage($topic->getError(), 'error');
                }
            }
        }

        if ($message) {
            if ($this->config->logModeration) {
                foreach ($topics as $topic) {
                    KunenaLog::log(
                        KunenaLog::TYPE_MODERATION,
                        KunenaLog::LOG_TOPIC_APPROVE,
                        ['topic_ids' => $ids],
                        $topic->getCategory(),
                        $topic,
                        null
                    );
                }
            }

            $this->app->enqueueMessage($message, 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * Move posts or messages
     *
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function move()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $topics_ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
        $messages_ids = array_keys($this->app->input->get('posts', [], 'post', 'array'));

        if (!empty($topics_ids)) {
            $topics_ids = ArrayHelper::toInteger($topics_ids);
            $topics = KunenaTopicHelper::getTopics($topics_ids);
        } else {
            $messages_ids = ArrayHelper::toInteger($messages_ids);
            $messages = KunenaMessageHelper::getMessages($messages_ids);
        }

        if (!$topics_ids && !$messages_ids) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_MESSAGES_OR_TOPICS_SELECTED'), 'notice');
            $this->setRedirectBack();
        } else {
            $target = KunenaCategoryHelper::get($this->app->input->getInt('target', 0));

            if (empty($target->id)) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_ACTION_NO_CATEGORY_SELECTED'), 'notice');
                $this->setRedirectBack();
            }

            if (!$target->isAuthorised('read')) {
                $this->app->enqueueMessage($target->getError(), 'error');
            } else {
                if ($topics) {
                    foreach ($topics as $topic) {
                        if ($topic->isAuthorised('move') && $topic->move($target)) {
                            $message = Text::_('COM_KUNENA_ACTION_TOPIC_SUCCESS_MOVE');
                        } else {
                            $this->app->enqueueMessage($topic->getError(), 'error');
                        }
                    }
                } else {
                    foreach ($messages as $message) {
                        $topic = $message->getTopic();

                        if ($message->isAuthorised('move') && $topic->move($target, $message->id)) {
                            $message = Text::_('COM_KUNENA_ACTION_POST_SUCCESS_MOVE');
                        } else {
                            $this->app->enqueueMessage($message->getError(), 'error');
                        }
                    }
                }
            }
        }

        if (!empty($message)) {
            if ($this->config->logModeration) {
                foreach ($topics as $topic) {
                    KunenaLog::log(
                        KunenaLog::TYPE_MODERATION,
                        KunenaLog::LOG_TOPIC_MODERATE,
                        [
                            'move'   => ['id' => $topic->id, 'mode' => 'topic'],
                            'target' => ['category_id' => $target->id],
                        ],
                        $topic->getCategory(),
                        $topic,
                        null
                    );
                }
            }

            $this->app->enqueueMessage($message, 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function unfavorite()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
        $ids = ArrayHelper::toInteger($ids);

        $topics = KunenaTopicHelper::getTopics($ids);

        if (KunenaTopicHelper::favorite(array_keys($topics), 0)) {
            if ($this->config->logModeration) {
                foreach ($topics as $topic) {
                    KunenaLog::log(
                        $this->me->userid == $topic->getAuthor()->userid ? KunenaLog::TYPE_ACTION : KunenaLog::TYPE_MODERATION,
                        KunenaLog::LOG_TOPIC_UNFAVORITE,
                        ['topic_ids' => $ids],
                        $topic->getCategory(),
                        $topic,
                        null
                    );
                }
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_USER_UNFAVORITE_YES'), 'success');
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC'), 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function unsubscribe()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $userid = $this->app->input->getInt('userid');

        $ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
        $ids = ArrayHelper::toInteger($ids);

        $topics = KunenaTopicHelper::getTopics($ids);

        if (KunenaTopicHelper::subscribe(array_keys($topics), 0, $userid)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_USER_UNSUBSCRIBE_YES'), 'success');
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC'), 'notice');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function approve_posts()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
        $ids = ArrayHelper::toInteger($ids);

        $success  = 0;
        $messages = KunenaMessageHelper::getMessages($ids);

        if (!$messages) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
        } else {
            foreach ($messages as $message) {
                if ($message->isAuthorised('approve') && $message->publish(KunenaForum::PUBLISHED)) {
                    $message->sendNotification();
                    $success++;
                } else {
                    $this->app->enqueueMessage($message->getError(), 'error');
                }
            }
        }

        if ($success) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_APPROVE_SUCCESS'), 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function delete_posts()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
        $ids = ArrayHelper::toInteger($ids);

        $success  = 0;
        $messages = KunenaMessageHelper::getMessages($ids);

        if (!$messages) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
        } else {
            foreach ($messages as $message) {
                if ($message->isAuthorised('delete') && $message->publish(KunenaForum::DELETED)) {
                    $success++;
                } else {
                    $this->app->enqueueMessage($message->getError(), 'error');
                }
            }
        }

        if ($success) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_DELETE'), 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function restore_posts()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $ids = array_keys($this->app->input->get('posts', [], 'post', 'array'));
        $ids = ArrayHelper::toInteger($ids);

        $success  = 0;
        $messages = KunenaMessageHelper::getMessages($ids);

        if (!$messages) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
        } else {
            foreach ($messages as $message) {
                if ($message->isAuthorised('undelete') && $message->publish(KunenaForum::PUBLISHED)) {
                    $success++;
                } else {
                    $this->app->enqueueMessage($message->getError(), 'error');
                }
            }
        }

        if ($success) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_UNDELETE'), 'success');
        }

        $this->setRedirectBack();
    }

    /**
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function permdel_posts()
    {
        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirectBack();

            return;
        }

        $ids = array_keys($this->app->input->get('posts', [], 'post', 'array'));
        $ids = ArrayHelper::toInteger($ids);

        $success  = 0;
        $messages = KunenaMessageHelper::getMessages($ids);

        if (!$messages) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
        } else {
            foreach ($messages as $message) {
                if ($message->isAuthorised('permdelete') && $message->delete()) {
                    $success++;
                } else {
                    $this->app->enqueueMessage($message->getError(), 'error');
                }
            }
        }

        if ($success) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_BULKMSG_DELETED'), 'success');
        }

        $this->setRedirectBack();
    }
}
