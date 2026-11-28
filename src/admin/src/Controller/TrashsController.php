<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\Input\Input;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Administrator\Model\TrashsModel;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Constructor.
 *
 * @param   array                $config   An optional associative array of configuration settings.
 * Recognized key values include 'name', 'default_task', 'model_path', and
 * 'view_path' (this list is not meant to be comprehensive).
 * @param   MVCFactoryInterface  $factory  The factory.
 * @param   CMSApplication       $app      The Application for the dispatcher
 * @param   Input                $input    Input
 *
 * @since   Kunena 6.3.0-BETA3
 */
class TrashsController extends AdminController
{
    /**
     * @var     null|string
     * @since   Kunena 6.0
     */
    protected mixed $baseurl = null;

    /**
     * Construct
     *
     * @param   array                     $config   config
     * @param   MVCFactoryInterface|null  $factory  The factory.
     * @param   CMSApplication|null       $app      The CMSApplication for the dispatcher
     * @param   Input|null                $input    Input
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function __construct($config = [], ?MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        parent::__construct($config, $factory, $app, $input);

        $layout        = $this->input->getCmd('layout', 'messages');
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=trashs&layout=' . $layout;
    }

    /**
     * Purge
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function purge(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cid = $this->input->get('cid', [], 'post', 'array');
        $cid = ArrayHelper::toInteger($cid);

        $type = $this->input->getCmd('type', 'topics', 'post');

        if (empty($cid)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_MESSAGES_SELECTED'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        /** @var TrashsModel $model */
        $model = $this->getModel();

        if ($type == 'topics') {
            if ($model->purgeTopics($cid)) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TRASH_DELETE_TOPICS_DONE'), 'success');
            }
        } elseif ($type == 'messages') {
            if ($model->purgeMessages($cid, age: 0)) {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TRASH_DELETE_MESSAGES_DONE'), 'success');
            }
        }

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Restore
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function restore(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cid = $this->input->get('cid', [], 'array');
        $cid = ArrayHelper::toInteger($cid, []);

        $type = $this->input->getCmd('type', 'topics');

        if (empty($cid)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_MESSAGES_SELECTED'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $nbItems = 0;

        // The topics, the categories and the authors of what is restored: only they are counted again below.
        $topicIds    = [];
        $categoryIds = [];
        $userIds     = [];

        if ($type == 'messages') {
            $messages = KunenaMessageHelper::getMessages($cid, 'none');

            foreach ($messages as $target) {
                // A reply of a topic which is in the trash comes back with its topic: restored alone, it published the topic with
                // this reply as its only post (the others stayed deleted in a published topic, which was then no more in the
                // trash). The first message restores its topic (as it always did).
                $topic = $target->getTopic();

                if ((int) $topic->hold >= KunenaForum::DELETED && (int) $topic->first_post_id !== (int) $target->id) {
                    $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_TRASH_RESTORE_TOPIC_FIRST', (int) $target->id), 'notice');

                    continue;
                }

                try {
                    $target->publish();
                } catch (Exception $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');

                    continue;
                }

                $topicIds[(int) $target->thread]   = (int) $target->thread;
                $categoryIds[(int) $target->catid] = (int) $target->catid;
                $userIds[(int) $target->userid]    = (int) $target->userid;

                $nbItems++;
            }
        } elseif ($type == 'topics') {
            $topics = KunenaTopicHelper::getTopics($cid, 'none');

            foreach ($topics as $target) {
                if ($target->getState() == KunenaForum::UNAPPROVED) {
                    $status = KunenaForum::UNAPPROVED;
                } else {
                    $status = KunenaForum::PUBLISHED;
                }

                try {
                    $target->publish($status);
                } catch (Exception $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');

                    continue;
                }

                $topicIds[(int) $target->id]             = (int) $target->id;
                $categoryIds[(int) $target->category_id] = (int) $target->category_id;

                $nbItems++;
            }
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_MESSAGES_SELECTED'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        if ($nbItems > 0) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_TRASH_ITEMS_RESTORE_DONE', $nbItems), 'success');
        }

        // The counts of what was restored, not of the whole forum: every restore (also of one message) counted all the users, the
        // topics and the categories again, which takes long on a big forum.
        if ($topicIds) {
            // The authors of the messages of the restored topics.
            $db      = Factory::getContainer()->get('DatabaseDriver');
            $authors = array_map('intval', (array) $db->setQuery(
                $db->createQuery()
                    ->select('DISTINCT ' . $db->quoteName('userid'))
                    ->from($db->quoteName('#__kunena_messages'))
                    ->whereIn($db->quoteName('thread'), array_values($topicIds))
            )->loadColumn());

            KunenaUserHelper::recount(array_values(array_unique(array_merge(array_values($userIds), $authors))));
            KunenaTopicHelper::recount(array_values($topicIds));
            KunenaCategoryHelper::recountBatch(array_values($categoryIds));
        }

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }
}
