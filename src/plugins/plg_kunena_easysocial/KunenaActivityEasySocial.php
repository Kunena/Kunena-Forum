<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easysocial
 *
 * @copyright      Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @copyright      Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * EasySocial is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Integration\KunenaActivity;

/**
 * @package  Easysocial
 *
 * @since    Kunena 5.0
 */
class KunenaActivityEasySocial extends KunenaActivity
{
    /**
     * @var     null
     * @since   Kunena 5.0
     */
    protected $params = null;

    /**
     * KunenaActivityEasySocial constructor.
     *
     * @param   object  $params  params
     *
     * @since   Kunena 5.0
     * @throws Exception
     */
    public function __construct(object $params)
    {
        $this->params = $params;
    }

    /**
     * @param   KunenaMessage  $message  message
     *
     * @return  void
     *
     * @since   Kunena 5.0
     */
    public function onAfterPost(KunenaMessage $message): void
    {
        if (StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
            $this->assignPoints('thread.new');
        }

        if (StringHelper::strlen($message->message) > $this->params->get('activity_badge_limit', 0)) {
            $this->assignBadge('thread.new', Text::_('PLG_KUNENA_EASYSOCIAL_BADGE_NEW_TITLE'));
        }

        $stream = FD::stream();

        $tmpl = $stream->getTemplate();

        $tmpl->setActor($message->userid, SOCIAL_TYPE_USER);
        $tmpl->setContext($message->thread, 'kunena');
        $tmpl->setVerb('create');
        $tmpl->setAccess('core.view');

        $stream->add($tmpl);
    }

    /**
     * @param   string  $command  command
     * @param   null    $target   target
     *
     * @return  mixed
     *
     * @since   Kunena 5.0
     */
    public function assignPoints(string $command, $target = null)
    {
        $user = FD::user($target);

        $points = FD::points();

        return $points->assign($command, 'com_kunena', $user->id);
    }

    /**
     * @param   string  $command  command
     * @param   string  $message  message
     * @param   null    $target   target
     *
     * @return  mixed
     *
     * @since   Kunena 5.0
     */
    public function assignBadge(string $command, string $message, $target = null)
    {
        $user  = FD::user($target);
        $badge = FD::badges();

        return $badge->log('com_kunena', $command, $user->id, $user->id);
    }

    /**
     * After a person replies a topic
     *
     * @internal  param $string
     *
     * @param   KunenaMessage  $message  message
     *
     * @return  void
     *
     * @since     1.3
     *
     * @access    public
     *
     * @throws Exception
     */
    public function onAfterReply(KunenaMessage $message): void
    {
        $length = StringHelper::strlen($message->message);

        // Assign points for replying a thread
        if ($length > $this->params->get('activity_points_limit', 0)) {
            $this->assignPoints('thread.reply');
        }

        // Assign badge for replying to a thread
        if ($length > $this->params->get('activity_badge_limit', 0)) {
            $this->assignBadge('thread.reply', Text::_('PLG_KUNENA_EASYSOCIAL_BADGE_REPLY_TITLE'));
        }

        $stream = FD::stream();
        $tmpl   = $stream->getTemplate();
        $tmpl->setActor($message->userid, SOCIAL_TYPE_USER);
        $tmpl->setContext($message->id, 'kunena');
        $tmpl->setVerb('reply');
        $tmpl->setAccess('core.view');

        // Add into stream
        $stream->add($tmpl);

        // Get a list of subscribers
        $recipients = $this->getSubscribers($message);

        if (!$recipients) {
            return;
        }

        $permalink = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $message->getPermaUrl(null);

        $options = [
            'uid'      => $message->id,
            'actor_id' => $message->userid,
            'title'    => '',
            'type'     => 'post',
            'url'      => $permalink,
            'image'    => '',
        ];

        // Add notifications in EasySocial
        FD::notify('post.reply', $recipients, [], $options);
    }

    /**
     * Get a list of subscribers for a thread
     *
     * @internal  param $string
     *
     * @param   KunenaMessage  $message  message
     *
     * @return   array|boolean
     *
     * @since     5.0
     *
     * @access    public
     *
     * @throws Exception
     */
    public function getSubscribers(KunenaMessage $message)
    {
        $config = KunenaFactory::getConfig();

        if ($message->hold > 1) {
            return false;
        }

        if ($message->hold == 1) {
            $mailsubs   = 0;
            $mailmods   = $config->mailModerators >= 0;
            $mailadmins = $config->mailAdministrators >= 0;
        } else {
            $mailsubs   = (bool) $config->allowSubscriptions;
            $mailmods   = $config->mailModerators >= 1;
            $mailadmins = $config->mailAdministrators >= 1;
        }

        if ($mailsubs) {
            if (!$message->parent) {
                // New topic: Send email only to category subscribers
                $mailsubs = $config->categorySubscriptions != 'disabled' ? KunenaAccess::CATEGORY_SUBSCRIPTION : 0;
            } elseif ($config->categorySubscriptions != 'post') {
                // Existing topic: Send email only to topic subscribers
                $mailsubs = $config->topicSubscriptions != 'disabled' ? KunenaAccess::TOPIC_SUBSCRIPTION : 0;
            } else {
                // Existing topic: Send email to both category and topic subscribers
                $mailsubs = $config->topicSubscriptions == 'disabled' ? KunenaAccess::CATEGORY_SUBSCRIPTION : KunenaAccess::CATEGORY_SUBSCRIPTION | KunenaAccess::TOPIC_SUBSCRIPTION;
            }
        }

        // Get all subscribers, moderators and admins who will get the email
        $me          = Factory::getApplication()->getIdentity();
        $acl         = KunenaAccess::getInstance();
        $subscribers = $acl->getSubscribers($message->catid, $message->thread, $mailsubs, $mailmods, $mailadmins, $me->id);

        if (!$subscribers) {
            return false;
        }

        $result = [];

        foreach ($subscribers as $subscriber) {
            if ($subscriber->id) {
                $result[] = $subscriber->id;
            }
        }

        return $result;
    }

    /**
     * @param   int            $actor    actor
     * @param   int            $target   target
     * @param   KunenaMessage  $message  message
     *
     * @return  void
     *
     * @since   Kunena 5.0
     */
    public function onAfterThankyou(int $actor, int $target, KunenaMessage $message): void
    {
        if (StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
            $this->assignPoints('thread.thanks', $target);
        }

        $this->assignBadge('thread.thanks', Text::_('PLG_KUNENA_EASYSOCIAL_BADGE_THANKED_TITLE'), $target);

        $tmpl = FD::stream()->getTemplate();

        $tmpl->setActor($actor, SOCIAL_TYPE_USER);
        $tmpl->setTarget($target);
        $tmpl->setContext($message->id, 'kunena');
        $tmpl->setVerb('thanked');
        $tmpl->setAccess('core.view');

        FD::stream()->add($tmpl);
    }

    /**
     * @param   object  $target  target
     *
     * @return  void
     *
     * @since   Kunena 5.0
     */
    public function onBeforeDeleteTopic(object $target): void
    {
        FD::stream()->delete($target->id, 'thread.new');
    }

    /**
     * @param   object  $topic  topic
     *
     * @return  void
     *
     * @since  Kunena 5.0
     */
    public function onAfterDeleteTopic(object $topic): void
    {
        FD::stream()->delete($topic->id, 'thread.new');
    }
}
