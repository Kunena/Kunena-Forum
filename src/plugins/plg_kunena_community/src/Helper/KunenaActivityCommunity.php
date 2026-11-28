<?php

/**
 * Kunena Plugin
 *
 * @package          Kunena.Plugins
 * @subpackage       Community
 *
 * @copyright   Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @copyright   (C)  2013 - 2014 iJoomla, Inc. All rights reserved.
 * @license          https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link             https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Community\Helper;

\defined('_JEXEC') or die();

use CActivityStream;
use CFactory;
use CNotificationLibrary;
use CParameter;
use CUserPoints;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Integration\KunenaActivity;
use stdClass;

/**
 * Class KunenaActivityCommunity
 *
 * @since   Kunena 6.0
 */
class KunenaActivityCommunity extends KunenaActivity
{
    /**
     * @param   Registry  $params  params
     *
     * @since   Kunena 6.0
     */
    public function __construct(Registry $params)
    {
        $this->params = $params;
    }

    /**
     * @param   KunenaMessage  $message  message
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function onAfterPost($message): void
    {
        // The points go to the author of the post (as the points of a thank you go to its target): without a user, JomSocial gave
        // them to the current user, the moderator who approved or restored the post, or the visitor who published a scheduled one.
        if ((int) $message->userid > 0 && StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
            CFactory::load('libraries', 'userpoints');
            CUserPoints::assignPoint('com_kunena.thread.new', (int) $message->userid);
        }

        $act          = new stdClass();
        $act->cmd     = 'wall.write';
        $act->actor   = $message->userid;
        $act->target  = 0;
        $act->title   = Text::_(
            '{actor} ' . Text::sprintf(
                'PLG_KUNENA_COMMUNITY_ACTIVITY_POST_TITLE',
                ' <a href="' . $message->getTopic()->getUrl() . '">' . $message->displayField('subject') . '</a>'
            )
        );
        $act->content = $this->buildContent($message);
        $act->app     = 'kunena.thread.post';
        $act->cid     = $message->thread;
        $act->access  = $this->getAccess($message->getCategory());

        // Comments and like support
        $act->comment_id   = $message->thread;
        $act->comment_type = 'kunena.thread.post';
        $act->like_id      = $message->thread;
        $act->like_type    = 'kunena.thread.post';

        // Do not add private activities
        if ($act->access > 20) {
            return;
        }

        CFactory::load('libraries', 'activities');
        $table = CActivityStream::add($act);

        if (\is_object($table)) {
            $table->like_id = $table->id;
            $table->store();
        }
    }

    /**
     * @param $message
     *
     * @return string
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    private function buildContent($message): string
    {
        $parent               = new stdClass();
        $parent->forceSecure  = true;
        $parent->forceMinimal = true;

        $content = KunenaParser::parseBBCode($message->message, $parent, $this->params->get('activity_stream_limit', 0));

        // Add readmore permalink
        $content .= '<br/><br /><a rel="nofollow" href="' . $message->getPermaUrl() . '" class="small profile-newsfeed-item-action">' . Text::_('COM_KUNENA_READMORE') . '</a>';

        return $content;
    }

    /**
     * @param $category
     *
     * @return  integer
     *
     * @since   Kunena 6.0
     */
    protected function getAccess($category): int
    {
        $levelGroups = [];

        if ($category->accesstype === 'joomla.level') {
            static $levels = [];

            $level = (int) $category->access;

            if (!isset($levels[$level])) {
                $db    = Factory::getContainer()->get('DatabaseDriver');
                $rules = $db->setQuery(
                    $db->createQuery()
                        ->select($db->quoteName('rules'))
                        ->from($db->quoteName('#__viewlevels'))
                        ->where($db->quoteName('id') . ' = ' . $level)
                )->loadResult();

                $levels[$level] = array_map('intval', (array) json_decode((string) $rules, true));
            }

            $levelGroups = $levels[$level];
        }

        return self::activityAccess(
            (string) $category->accesstype,
            (int) $category->pubAccess,
            (bool) $category->pubRecurse,
            (int) $category->adminAccess,
            (bool) $category->adminRecurse,
            $levelGroups
        );
    }

    /**
     * The access of the activity of a message for JomSocial (0 public, 20 registered, 30 friends, 40 private): the widest one which
     * does not show it to users who may not read its category.
     *
     * It was read from the ids: a view level 1 or a group 1 was public, 2 registered (a note said that Joomla 2.5 mixes up groups
     * and access levels). But a view level is what its groups say (the level 1 of a site can be another one), and a category of a user
     * group without its subgroups is not for the users of the groups under it: the activity of a category "Registered only" was
     * shown to every registered user, the authors and editors too.
     *
     * @param   string  $accesstype    The access type of the category.
     * @param   int     $pubAccess     Its group (joomla.group).
     * @param   bool    $pubRecurse    With the groups under it.
     * @param   int     $adminAccess   Its admin group.
     * @param   bool    $adminRecurse  With the groups under it.
     * @param   int[]   $levelGroups   The groups of its view level (joomla.level).
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function activityAccess(string $accesstype, int $pubAccess, bool $pubRecurse, int $adminAccess, bool $adminRecurse, array $levelGroups): int
    {
        // A view level is for the users of its groups and of the groups under them: the group Public is everybody, the group
        // Registered (2) every registered user.
        if ($accesstype === 'joomla.level') {
            if (\in_array(1, $levelGroups, true)) {
                return 0;
            }

            return \in_array(2, $levelGroups, true) ? 20 : 40;
        }

        // A user group is for everybody, or for every registered user, only with all the groups under it.
        if ($accesstype === 'joomla.group') {
            $groups = array_merge($pubRecurse ? [$pubAccess] : [], $adminRecurse ? [$adminAccess] : []);

            if (\in_array(1, $groups, true)) {
                return 0;
            }

            return \in_array(2, $groups, true) ? 20 : 40;
        }

        // The access of another extension: private.
        return 40;
    }

    /**
     * @param   KunenaMessage  $message  message
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function onAfterReply($message): void
    {
        // The points go to the author of the post (as the points of a thank you go to its target): without a user, JomSocial gave
        // them to the current user, the moderator who approved or restored the post, or the visitor who published a scheduled one.
        if ((int) $message->userid > 0 && StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
            CFactory::load('libraries', 'userpoints');
            CUserPoints::assignPoint('com_kunena.thread.reply', (int) $message->userid);
        }

        // Get users who have subscribed to the topic, excluding current user.
        $acl         = KunenaAccess::getInstance();
        $subscribers = $acl->getSubscribers(
            $message->catid,
            $message->thread,
            KunenaAccess::TOPIC_SUBSCRIPTION,
            false,
            false,
            [$message->userid]
        );

        foreach ($subscribers as $sub) {
            $actor  = CFactory::getUser($message->userid);
            $target = CFactory::getUser($sub->id);

            $params = new CParameter('');
            $params->set('actorName', $actor->getDisplayName());
            $params->set('recipientName', $target->getDisplayName());
            $params->set('url', Uri::getInstance()->toString(['scheme', 'host', 'port']) . $message->getPermaUrl(null) . '#' . $message->id); // {url} tag for activity. Used when hovering over avatar in notification window, as well as in email notification
            $params->set('title', $message->displayField('subject')); // (title) tag in language file
            $params->set('title_url', $message->getPermaUrl()); // Make the title in notification - linkable
            // (message) tag in language file. Parsed as for an e-mail (a context): the [confidential], [hide] and [private] parts
            // are left out. Parsed for the page, they were shown as the poster (or the moderator who approved) sees them, and the
            // notification and its e-mail gave them to every subscriber.
            $params->set('message', $message->displayField('message', true, 'email'));
            $params->set('actor', $actor->getDisplayName()); // Actor in the stream
            $params->set('actor_url', 'index.php?option=com_community&view=profile&userid=' . $actor->id); // Actor Link

            // Finally, send notifications
            CNotificationLibrary::add('kunena_reply', $actor->id, $target->id, Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_REPLY_TITLE_ACT'), Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_REPLY_TEXT'), '', $params);
        }

        // Activity stream

        $act          = new stdClass();
        $act->cmd     = 'wall.write';
        $act->actor   = $message->userid;
        $act->target  = 0; // No target
        // The subject escaped, as in the activity of a new topic: it is stored as it was typed, and the title of the activity is
        // HTML (a subject with a tag ran its script for every reader of the stream).
        $act->title   = Text::_('{single}{actor}{/single}{multiple}{actors}{/multiple} ' . Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_REPLY_TITLE', '<a href="' . $message->getTopic()->getUrl() . '">' . $message->displayField('subject') . '</a>'));
        $act->content = $this->buildContent($message);
        $act->app     = 'kunena.thread.reply';
        $act->cid     = $message->thread;
        $act->access  = $this->getAccess($message->getCategory());

        // Comments and like support
        $act->comment_id   = $message->thread;
        $act->comment_type = 'kunena.thread.reply';
        $act->like_id      = $message->thread;
        $act->like_type    = 'kunena.thread.reply';

        // Do not add private activities
        if ($act->access > 20) {
            return;
        }

        CFactory::load('libraries', 'activities');
        $table = CActivityStream::add($act);

        if (\is_object($table)) {
            $table->like_id = $table->id;
            $table->store();
        }
    }

    /**
     * @param   int            $actor    actor
     * @param   int            $target   target
     * @param   KunenaMessage  $message  message
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function onAfterThankyou(int $actor, int $target, KunenaMessage $message): void
    {
        CFactory::load('libraries', 'userpoints');
        CUserPoints::assignPoint('com_kunena.thread.thankyou', $target);

        $actor  = CFactory::getUser($actor);
        $target = CFactory::getUser($target);

        // Create CParameter use for params
        $params = new CParameter('');
        $params->set('actorName', $actor->getDisplayName());
        $params->set('recipientName', $target->getDisplayName());
        $params->set('recipientUrl', 'index.php?option=com_community&view=profile&userid=' . $target->id); // Actor Link
        $params->set('url', Uri::getInstance()->toString(['scheme', 'host', 'port']) . $message->getPermaUrl(null)); // {url} tag for activity. Used when hovering over avatar in notification window, as well as in email notification
        $params->set('title', $message->displayField('subject')); // (title) tag in language file
        $params->set('title_url', $message->getPermaUrl()); // Make the title in notification - linkable
        $params->set('message', $message->message); // (message) tag in language file
        $params->set('actor', $actor->getDisplayName()); // Actor in the stream
        $params->set('actor_url', 'index.php?option=com_community&view=profile&userid=' . $actor->id); // Actor Link

        // Finally, send notifications
        CNotificationLibrary::add(
            'kunena_thankyou',
            $actor->id,
            $target->id,
            Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_THANKYOU_TITLE_ACT'),
            Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_THANKYOU_TEXT'),
            '',
            $params
        );

        $act          = new stdClass();
        $act->cmd     = 'wall.write';
        $act->actor   = $actor->id;
        $act->target  = $target->id;
        $act->title   = Text::sprintf(
            'PLG_KUNENA_COMMUNITY_ACTIVITY_THANKYOU_WALL',
            $params->get('actor_url'),
            $params->get('actor'),
            $params->get('recipientUrl'),
            $params->get('recipientName'),
            $params->get('url'),
            $params->get('title')
        );
        $act->content = null;
        $act->app     = 'kunena.message.thankyou';
        $act->cid     = $target->id;
        $act->access  = $this->getAccess($message->getCategory());

        // Comments and like supports
        $act->comment_id   = $target->id;
        $act->comment_type = 'kunena.message.thankyou';
        $act->like_id      = $target->id;
        $act->like_type    = 'kunena.message.thankyou';

        // Do not add private activities
        if ($act->access > 20) {
            return;
        }

        CFactory::load('libraries', 'activities');
        $table = CActivityStream::add($act);

        if (\is_object($table)) {
            $table->like_id = $table->id;
            $table->store();
        }
    }

    /**
     * @param   KunenaTopic  $target  The topic which was deleted.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function onAfterDeleteTopic($target): void
    {
        CFactory::load('libraries', 'activities');
        CActivityStream::remove('kunena.thread.post', $target->id);

        // The activities of the replies are kept by the id of their topic too (onAfterReply(): app kunena.thread.reply, cid the
        // topic). They were removed as kunena.thread.replied, a name which no activity has: they stayed in the stream.
        CActivityStream::remove('kunena.thread.reply', $target->id);
    }
}
