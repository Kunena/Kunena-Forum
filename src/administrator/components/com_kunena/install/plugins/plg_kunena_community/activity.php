<?php
/**
 * Kunena Plugin
 *
 * @package          Kunena.Plugins
 * @subpackage       Community
 *
 * @copyright   (C)  2008 - 2022 Kunena Team. All rights reserved.
 * @copyright   (C)  2013 - 2014 iJoomla, Inc. All rights reserved.
 * @license          https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link             https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

jimport('joomla.utilities.string');

/**
 * Class KunenaActivityCommunity
 * @since Kunena
 */
class KunenaActivityCommunity extends KunenaActivity
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaActivityCommunity constructor.
	 *
	 * @param $params
	 *
	 * @since Kunena
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param $message
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	public function onAfterPost($message)
	{
		if (Joomla\String\StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0))
		{
			CFactory::load('libraries', 'userpoints');
			CUserPoints::assignPoint('com_kunena.thread.new');
		}

		$act          = new stdClass;
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
		if ($act->access > 20)
		{
			return;
		}

		CFactory::load('libraries', 'activities');
		$table = CActivityStream::add($act);

		if (is_object($table))
		{
			$table->like_id = $table->id;
			$table->store();
		}
	}

	/**
	 * @param $message
	 *
	 * @return mixed|string|void
	 * @throws Exception
	 * @since Kunena
	 */
	private function buildContent($message)
	{
		$parent               = new stdClass;
		$parent->forceSecure  = true;
		$parent->forceMinimal = true;

		$content = KunenaHtmlParser::parseBBCode($message->message, $parent, $this->params->get('activity_stream_limit', 0));

		// Add readmore permalink
		$content .= '<br/><br /><a rel="nofollow" href="' . $message->getPermaUrl() . '" class="small profile-newsfeed-item-action">' . Text::_('COM_KUNENA_READMORE') . '</a>';

		return $content;
	}

	/**
	 * @param $category
	 *
	 * @return integer
	 * @since Kunena
	 */
	protected function getAccess($category)
	{
		// Activity access level: 0 = public, 20 = registered, 30 = friend, 40 = private
		$accesstype = $category->accesstype;

		if ($accesstype != 'joomla.group' && $accesstype != 'joomla.level')
		{
			// Private
			return 40;
		}

		// FIXME: Joomla 2.5 can mix up groups and access levels
		if (($accesstype == 'joomla.level' && $category->access == 1)
			|| ($accesstype == 'joomla.group' && ($category->pub_access == 1 || $category->admin_access == 1))
		)
		{
			// Public
			$access = 0;
		}
		elseif (($accesstype == 'joomla.level' && $category->access == 2)
			|| ($accesstype == 'joomla.group' && ($category->pub_access == 2 || $category->admin_access == 2))
		)
		{
			// Registered
			$access = 20;
		}
		else
		{
			// Other groups (=private)
			$access = 40;
		}

		return $access;
	}

	/**
	 * @param $message
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function onAfterReply($message)
	{
		if (Joomla\String\StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0))
		{
			CFactory::load('libraries', 'userpoints');
			CUserPoints::assignPoint('com_kunena.thread.reply');
		}

		// Get users who have subscribed to the topic, excluding current user.
		$acl         = KunenaAccess::getInstance();
		$subscribers = $acl->getSubscribers(
			$message->catid, $message->thread, KunenaAccess::TOPIC_SUBSCRIPTION, false, false, array($message->userid)
		);

		foreach ($subscribers as $userid)
		{
			$actor  = CFactory::getUser($message->userid);
			$target = CFactory::getUser($userid);

			$params = new CParameter('');
			$params->set('actorName', $actor->getDisplayName());
			$params->set('recipientName', $target->getDisplayName());
			$params->set('url', Uri::getInstance()->toString(array('scheme', 'host', 'port')) . $message->getPermaUrl(null)); // {url} tag for activity. Used when hovering over avatar in notification window, as well as in email notification
			$params->set('title', $message->displayField('subject')); // (title) tag in language file
			$params->set('title_url', $message->getPermaUrl()); // Make the title in notification - linkable
			$params->set('message', $message->displayField('message')); // (message) tag in language file
			$params->set('actor', $actor->getDisplayName()); // Actor in the stream
			$params->set('actor_url', 'index.php?option=com_community&view=profile&userid=' . $actor->id); // Actor Link

			// Finally, send notifications
			CNotificationLibrary::add('kunena_reply', $actor->id, $target->id, Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_REPLY_TITLE_ACT'), Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_REPLY_TEXT'), '', $params);
		}

		// Activity stream

		$act          = new stdClass;
		$act->cmd     = 'wall.write';
		$act->actor   = $message->userid;
		$act->target  = 0; // No target
		$act->title   = Text::_('{single}{actor}{/single}{multiple}{actors}{/multiple} ' . Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_REPLY_TITLE', '<a href="' . $message->getTopic()->getUrl() . '">' . $message->subject . '</a>'));
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
		if ($act->access > 20)
		{
			return;
		}

		CFactory::load('libraries', 'activities');
		$table = CActivityStream::add($act);

		if (is_object($table))
		{
			$table->like_id = $table->id;
			$table->store();
		}
	}

	/**
	 * @param   int $actor   actor
	 * @param   int $target  target
	 * @param   int $message message
	 *
	 * @since Kunena
	 */
	public function onAfterThankyou($actor, $target, $message)
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
		$params->set('url', Uri::getInstance()->toString(array('scheme', 'host', 'port')) . $message->getPermaUrl(null) . '#' . $message->id); // {url} tag for activity. Used when hovering over avatar in notification window, as well as in email notification
		$params->set('title', $message->displayField('subject')); // (title) tag in language file
		$params->set('title_url', $message->getPermaUrl()); // Make the title in notification - linkable
		$params->set('message', $message->message); // (message) tag in language file
		$params->set('actor', $actor->getDisplayName()); // Actor in the stream
		$params->set('actor_url', 'index.php?option=com_community&view=profile&userid=' . $actor->id); // Actor Link

		// Finally, send notifications
		CNotificationLibrary::add('kunena_thankyou', $actor->id, $target->id, Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_THANKYOU_TITLE_ACT'), Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_THANKYOU_TEXT'), '', $params);

		$act          = new stdClass;
		$act->cmd     = 'wall.write';
		$act->actor   = $actor->id;
		$act->target  = $target->id;
		$act->title   = Text::sprintf('PLG_KUNENA_COMMUNITY_ACTIVITY_THANKYOU_WALL', $params->get('actor_url'), $params->get('actor'), $params->get('recipientUrl'), $params->get('recipientName'), $params->get('url'), $params->get('title'));
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
		if ($act->access > 20)
		{
			return;
		}

		CFactory::load('libraries', 'activities');
		$table = CActivityStream::add($act);

		if (is_object($table))
		{
			$table->like_id = $table->id;
			$table->store();
		}
	}

	/**
	 * @param $target
	 *
	 * @since Kunena
	 */
	public function onAfterDeleteTopic($target)
	{
		CFactory::load('libraries', 'activities');
		CActivityStream::remove('kunena.thread.post', $target->id);

		// TODO: Need get replied id
		CActivityStream::remove('kunena.thread.replied', $target->id);
	}
}
