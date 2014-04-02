<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Community
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.utilities.string');

class KunenaActivityCommunity extends KunenaActivity {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	public function onAfterPost($message) {
		if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
			CFactory::load ( 'libraries', 'userpoints' );
			CUserPoints::assignPoint ( 'com_kunena.thread.new' );
		}

		$parent = new stdClass();
		$parent->forceSecure = true;
		$parent->forceMinimal = true;

		$content = KunenaHtmlParser::parseBBCode($message->message, $parent, $this->params->get('activity_stream_limit', 0));

		// Add readmore permalink
		$content .= '<br /><a rel="nofollow" href="'.$message->getPermaUrl().'" class="small profile-newsfeed-item-action">'.JText::_('COM_KUNENA_READMORE').'</a>';

		$act = new stdClass ();
		$act->cmd = 'wall.write';
		$act->actor = $message->userid;
		$act->target = 0; // no target
		$act->title = JText::_ ( '{actor} ' . JText::sprintf ( 'PLG_KUNENA_COMMUNITY_ACTIVITY_POST_TITLE', ' <a href="' . $message->getTopic()->getUrl() . '">' . $message->subject . '</a>') );
		$act->content = $content;
		$act->app = 'kunena.post';
		$act->cid = $message->thread;
		$act->access = $this->getAccess($message->getCategory());

		// Comments and like support
		$act->comment_id = $message->thread;
		$act->comment_type = 'kunena.post';
		$act->like_id = $message->thread;
		$act->like_type = 'kunena.post';

		// Do not add private activities
		if ($act->access > 20) return;
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add ( $act );
	}

	public function onAfterReply($message) {
		if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
			CFactory::load ( 'libraries', 'userpoints' );
			CUserPoints::assignPoint ( 'com_kunena.thread.reply' );
		}

		$parent = new stdClass();
		$parent->forceSecure = true;
		$parent->forceMinimal = true;

		$content = KunenaHtmlParser::parseBBCode($message->message, $parent, $this->params->get('activity_stream_limit', 0));

		// Add readmore permalink
		$content .= '<br /><a rel="nofollow" href="'.$message->getPermaUrl().'" class="small profile-newsfeed-item-action">'.JText::_('COM_KUNENA_READMORE').'</a>';

		$act = new stdClass ();
		$act->cmd = 'wall.write';
		$act->actor = $message->userid;
		$act->target = 0; // no target
		$act->title = JText::_ ( '{single}{actor}{/single}{multiple}{actors}{/multiple} ' . JText::sprintf ( 'PLG_KUNENA_COMMUNITY_ACTIVITY_REPLY_TITLE', '<a href="' . $message->getTopic()->getUrl() . '">' . $message->subject . '</a>' ) );
		$act->content = $content;
		$act->app = 'kunena.post';
		$act->cid = $message->thread;
		$act->access = $this->getAccess($message->getCategory());

		// Comments and like support
		$act->comment_id = $message->thread;
		$act->comment_type = 'kunena.post';
		$act->like_id = $message->thread;
		$act->like_type = 'kunena.post';

		// Do not add private activities
		if ($act->access > 20) return;
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add ( $act );
	}

	public function onAfterThankyou($actor, $target, $message) {
		CFactory::load ( 'libraries', 'userpoints' );
		CUserPoints::assignPoint ( 'com_kunena.thread.thankyou', $target );
		$targetUser = KunenaFactory::getUser($target);
		$target_link = $targetUser->getLink($targetUser->getName(), $targetUser->getName());

		$act = new stdClass ();
		$act->cmd = 'wall.write';
		$act->actor = JFactory::getUser()->id;
		$act->target = $target;
		$act->title = JText::_ ( '{single}{actor}{/single}{multiple}{actors}{/multiple} ' . JText::sprintf( 'PLG_KUNENA_COMMUNITY_ACTIVITY_THANKYOU_TITLE', $target_link, ' <a href="' . $message->getPermaUrl() . '">' . $message->subject . '</a>' ) );
		$act->content = NULL;
		$act->app = 'kunena.thankyou';
		$act->cid = $target;
		$act->access = $this->getAccess($message->getCategory());

		// Comments and like support
		$act->comment_id = $target;
		$act->comment_type = 'kunena.thankyou';
		$act->like_id = $target;
		$act->like_type = 'kunena.thankyou';

		// Do not add private activities
		if ($act->access > 20) return;
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add ( $act );
	}

	public function onAfterDeleteTopic($target) {
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::remove ('kunena.post', $target->id );
	}

	protected function getAccess($category) {
		// Activity access level: 0 = public, 20 = registered, 30 = friend, 40 = private
		$accesstype = $category->accesstype;
		if ($accesstype != 'joomla.group' && $accesstype != 'joomla.level') {
			// Private
			return 40;
		}
		// FIXME: Joomla 2.5 can mix up groups and access levels
		if (($accesstype == 'joomla.level' && $category->access == 1)
				|| ($accesstype == 'joomla.group' && ($category->pub_access == 1 || $category->admin_access == 1))) {
			// Public
			$access = 0;
		} elseif (($accesstype == 'joomla.level' && $category->access == 2)
				|| ($accesstype == 'joomla.group' && ($category->pub_access == 2 || $category->admin_access == 2))) {
			// Registered
			$access = 20;
		} else {
			// Other groups (=private)
			$access = 40;
		}
		return $access;
	}
}
