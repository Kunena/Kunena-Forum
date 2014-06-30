<?php

/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Community
 *
 * @copyright (C) 2013 iJoomla, Inc. - All rights reserved. Forked from Kunena Team
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * @link http://www.kunena.org
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
defined('_JEXEC') or die('Restricted access');

class KunenaActivityCommunity extends KunenaActivity {

    protected $params = null;

    public function __construct($params) {
        $this->params = $params;
    }

    /**
     *
     * @param type $message
     * @return type
     */
    public function onAfterPost($message) {

        if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
            CFactory::load('libraries', 'userpoints');
            CUserPoints::assignPoint('com_kunena.thread.new');
        }

        $act                = new stdClass ();
        $act->cmd           = 'wall.write';
        $act->actor         = $message->userid;
        $act->target        = 0; // no target
        $act->title         = JText::_('{actor} ' . JText::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_POST_TITLE', ' <a href="' . $message->getTopic()->getUrl() . '">' . $message->subject . '</a>'));
        $act->content       = $this->buildContent($message);
        $act->app           = 'kunena.thread.post';
        $act->cid           = $message->thread;
        $act->access        = $this->getAccess($message->getCategory());

        // Comments and like support
        $act->comment_id    = $message->thread;
        $act->comment_type  = 'kunena.thread.post';
        $act->like_id       = $message->thread;
        $act->like_type     = 'kunena.thread.post';

        // Do not add private activities
        if ($act->access > 20)
            return;

        CFactory::load('libraries', 'activities');
        CActivityStream::add($act);
    }

    /**
     *
     * @param type $message
     * @return type
     */
    public function onAfterReply($message) {

        if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
            CFactory::load('libraries', 'userpoints');
            CUserPoints::assignPoint('com_kunena.thread.reply');
        }

        $subscribers = $this->_getSubscribers($message);

        foreach ($subscribers as $user) {

            $actor = CFactory::getUser($message->userid);
            $target = CFactory::getUser($user->userid);

            $params = new CParameter('');
            $params->set('actorName', $actor->getDisplayName());
            $params->set('recipientName', $target->getDisplayName());
            $params->set('url',  JUri::getInstance()->toString(array('scheme', 'host', 'port')) . $message->getPermaUrl(null)); // {url} tag for activity. Used when hovering over avatar in notification window, as well as in email notification
            $params->set('title', $message->subject); // (title) tag in language file
            $params->set('title_url' , $message->getPermaUrl() ); // Make the title in notification - linkable
            $params->set('message', $message->message); // (message) tag in language file
            $params->set('actor', $actor->getDisplayName()); // Actor in the stream
            $params->set('actor_url', 'index.php?option=com_community&view=profile&userid=' . $actor->id); // Actor Link

            // Finally, send notifications
            CNotificationLibrary::add( 'kunena_reply', $actor->id, $target->id, JText::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_REPLY_TITLE'), JText::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_REPLY_TEXT'), '', $params );
        }

        /* Activity stream */
        $act                = new stdClass ();
        $act->cmd           = 'wall.write';
        $act->actor         = $message->userid;
        $act->target        = 0; // no target
        $act->title         = JText::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_REPLY_WALL',$params->get('actor_url'),$params->get('actor'),$params->get('title_url'),$params->get('title'));
        $act->content       = $this->buildContent($message);
        $act->app           = 'kunena.thread.reply';
        $act->cid           = $message->thread;
        $act->access        = $this->getAccess($message->getCategory());

        // Comments and like support
        $act->comment_id    = $message->thread;
        $act->comment_type  = 'kunena.thread.reply';
        $act->like_id       = $message->thread;
        $act->like_type         = 'kunena.thread.reply';

        // Do not add private activities
        if ($act->access > 20)
            return;

        CFactory::load('libraries', 'activities');
        CActivityStream::add($act);

        #echo "<pre>";var_dump($act);die();
    }

    public function onAfterThankyou($actor, $target, $message) {

        CFactory::load('libraries', 'userpoints');
        CUserPoints::assignPoint('com_kunena.thread.thankyou', $target);

        $actor = CFactory::getUser($actor);
        $target = CFactory::getUser($target);

        //Create CParameter use for params
        $params = new CParameter('');
        $params->set('actorName',       $actor->getDisplayName());
        $params->set('recipientName',   $target->getDisplayName());
        $params->set('recipientUrl',    'index.php?option=com_community&view=profile&userid=' . $target->id); // Actor Link
        $params->set('url',             JUri::getInstance()->toString(array('scheme', 'host', 'port')) . $message->getPermaUrl(null)); // {url} tag for activity. Used when hovering over avatar in notification window, as well as in email notification
        $params->set('title',           $message->subject); // (title) tag in language file
        $params->set('title_url' ,      $message->getPermaUrl() ); // Make the title in notification - linkable
        $params->set('message',         $message->message); // (message) tag in language file
        $params->set('actor',           $actor->getDisplayName()); // Actor in the stream
        $params->set('actor_url',       'index.php?option=com_community&view=profile&userid=' . $actor->id); // Actor Link

        // Finally, send notifications
        CNotificationLibrary::add('kunena_thankyou' , $actor->id , $target->id , JText::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_THANKYOU_TITLE') , JText::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_THANKYOU_TEXT') , '' , $params );

        $act = new stdClass ();
        $act->cmd           = 'wall.write';
        $act->actor         = $actor->id;
        $act->target        = $target->id;
        $act->title         = JText::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_THANKYOU_WALL', $params->get('actor_url'),$params->get('actor'),$params->get('recipientUrl'),$params->get('recipientName'),$params->get('url'),$params->get('title'));
        $act->content       = NULL;
        $act->app           = 'kunena.message.thankyou';
        $act->cid           = $target->id;
        $act->access        = $this->getAccess($message->getCategory());

        // Comments and like support
        $act->comment_id    = $target->id;
        $act->comment_type  = 'kunena.message.thankyou';
        $act->like_id       = $target->id;
        $act->like_type     = 'kunena.message.thankyou';

        // Do not add private activities
        if ($act->access > 20)
            return;
        CFactory::load('libraries', 'activities');
        CActivityStream::add($act);

        #var_dump($act);die();
    }

    public function onAfterDeleteTopic($target) {
        CFactory::load('libraries', 'activities');
        CActivityStream::remove('kunena.thread.post', $target->id);
        /**
         * @todo Need get replied id
         */
        CActivityStream::remove('kunena.thread.replied', $target->id);
    }

    protected function getAccess($category) {
        // Activity access level: 0 = public, 20 = registered, 30 = friend, 40 = private
        $accesstype = $category->accesstype;
        if ($accesstype != 'joomla.group' && $accesstype != 'joomla.level') {
            // Private
            return 40;
        }
        if (version_compare(JVERSION, '1.6', '>')) {
            // Joomla 1.6+
            // FIXME: Joomla 1.6 can mix up groups and access levels
            if (($accesstype == 'joomla.level' && $category->access == 1) || ($accesstype == 'joomla.group' && ($category->pub_access == 1 || $category->admin_access == 1))) {
                // Public
                $access = 0;
            } elseif (($accesstype == 'joomla.level' && $category->access == 2) || ($accesstype == 'joomla.group' && ($category->pub_access == 2 || $category->admin_access == 2))) {
                // Registered
                $access = 20;
            } else {
                // Other groups (=private)
                $access = 40;
            }
        } else {
            // Joomla 1.5
            // Joomla access levels: 0 = public,  1 = registered
            // Joomla user groups:  29 = public, 18 = registered
            if (($accesstype == 'joomla.level' && $category->access == 0) || ($accesstype == 'joomla.group' && ($category->pub_access == 0 || $category->pub_access == 29 || $category->admin_access == 29))) {
                // Public
                $access = 0;
            } elseif (($accesstype == 'joomla.level' && $category->access == 1) || ($accesstype == 'joomla.group' && ($category->pub_access == -1 || $category->pub_access == 18 || $category->admin_access == 18))) {
                // Registered
                $access = 20;
            } else {
                // Other groups (=private)
                $access = 40;
            }
        }
        return $access;
    }

    /**
     *
     * @param type $message
     * @return type
     */
    private function _getSubscribers($message) {
        $db		= JFactory::getDBO();
        $query	= 'SELECT DISTINCT '.$db->quoteName('userid').' FROM ' . $db->quoteName('#__kunena_messages')
            . ' WHERE '.$db->quoteName('thread').'=' . $db->Quote( $message->thread );

        $db->setQuery( $query );
        $repliers = $db->loadObjectList();

        return $repliers;
    }

    private function buildContent($message) {

        $parent = new stdClass();
        $parent->forceSecure = true;
        $parent->forceMinimal = true;

        $content = KunenaHtmlParser::parseBBCode($message->message, $parent, $this->params->get('activity_stream_limit', 0));

        // Add readmore permalink
        $content .= '<br/><br /><a rel="nofollow" href="' . $message->getPermaUrl() . '" class="small profile-newsfeed-item-action">' . JText::_('COM_KUNENA_READMORE') . '</a>';

        return $content;
    }

}
