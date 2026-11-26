<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Form\History;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Event\KunenaPrepareEvent;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaPrivateMessageFinder;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The last versions of the messages of a topic, under the form of a reply (a sub-request of the layout topic/edit: a page part of its
 * own, as the other parts of an HMVC page).
 *
 * @since   Kunena 4.0
 */
class TopicFormHistoryDisplay extends KunenaControllerDisplay
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $name = 'Topic/Edit/History';

    public mixed $topic = null;

    public mixed $history = null;

    public mixed $replycount = null;

    public mixed $historycount = null;

    public mixed $attachments = null;

    public mixed $inline_attachments = null;

    /**
     * Prepare reply history display.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $id = $this->input->getInt('id');
        $me = KunenaUserHelper::getMyself();

        $this->topic   = KunenaTopicHelper::get($id);
        $category      = $this->topic->getCategory();
        $this->history = KunenaMessageHelper::getMessagesByTopic(
            $this->topic,
            0,
            (int) $this->config->historyLimit,
            'DESC'
        );

        $this->replycount   = $this->topic->getReplies();
        $this->historycount = \count($this->history);
        KunenaAttachmentHelper::getByMessage($this->history);
        $userlist = [];
        $messages = [];

        foreach ($this->history as $message) {
            $messages[$message->id]           = $message;
            $userlist[(int) $message->userid] = (int) $message->userid;
        }

        if ($me->exists()) {
            $pmFinder = new KunenaPrivateMessageFinder();
            $pmFinder->filterByMessageIds(array_keys($messages))->order('id');

            if (!$me->isModerator($category)) {
                $pmFinder->filterByUser($me);
            }

            $pms = $pmFinder->find();

            foreach ($pms as $pm) {
                $registry = new Registry($pm->params);
                $posts    = $registry->get('receivers.posts');

                // A private message can also be about posts which are not in the history: those are left out (writing to
                // a missing post was a fatal error).
                foreach ((array) $posts as $post) {
                    if (!isset($messages[$post])) {
                        continue;
                    }

                    if (!isset($messages[$post]->pm)) {
                        $messages[$post]->pm = [];
                    }

                    $messages[$post]->pm[$pm->id] = $pm;
                }
            }
        }

        $this->history = $messages;

        KunenaUserHelper::loadUsers($userlist);

        // Run events
        $params = new Registry();
        $params->set('ksource', 'kunena');
        $params->set('kunena_view', 'topic');
        $params->set('kunena_layout', 'history');

        PluginHelper::importPlugin('kunena');

        foreach ($this->history as &$result) {
            $prepareEvent = new KunenaPrepareEvent('onKunenaPrepare', [
                'context' => 'kunena.messages',
                'subject' => $result,
                'params'  => $params,
                'page'    => 0,
            ]);
            $this->app->getDispatcher()->dispatch('onKunenaPrepare', $prepareEvent);
        }

        // The files of all the messages at once: getAttachments() of each message reads them from the cache. Which files a message
        // shows in its text is known when it is parsed (the BBCode keeps them on the layout), so the layout lists the others.
        $this->attachments        = KunenaAttachmentHelper::getByMessage($this->history);
        $this->inline_attachments = [];

        $this->headerText = Text::_('COM_KUNENA_POST_EDIT') . ' ' . $this->topic->subject;
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    protected function prepareDocument()
    {
    }
}
