<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration.JomSocial
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaActivityJomSocial extends KunenaActivity {
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ( 'jomsocial' );
		if (! $this->integration || ! $this->integration->isLoaded ())
			return;
		$this->priority = 40;
		$this->_config = KunenaFactory::getConfig ();
	}

	public function onAfterPost($message) {
		CFactory::load ( 'libraries', 'userpoints' );
		CUserPoints::assignPoint ( 'com_kunena.thread.new' );

		$content = KunenaHtmlParser::plainBBCode($message->message, $this->_config->activity_limit);

		// Add readmore permalink
		$content .= '<br /><a rel="nofollow" href="'.
				KunenaRoute::_($message->getPermaUrl()).
				'" class="small profile-newsfeed-item-action">'.JText::_('COM_KUNENA_READMORE').'</a>';

		$act = new stdClass ();
		$act->cmd = 'wall.write';
		$act->actor = $message->userid;
		$act->target = 0; // no target
		$act->title = JText::_ ( '{actor} ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG1' ) . ' <a href="' . $message->getTopic()->getUrl() . '">' . $message->subject . '</a> ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG2' ) );
		$act->content = $content;
		$act->app = 'kunena.post';
		$act->cid = $message->thread;
		$act->access = $this->getAccess($message->getCategory());

		// Do not add private activities
		if ($act->access > 20) return;
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add ( $act );
	}

	public function onAfterReply($message) {
		CFactory::load ( 'libraries', 'userpoints' );
		CUserPoints::assignPoint ( 'com_kunena.thread.reply' );

		$content = KunenaHtmlParser::plainBBCode($message->message, $this->_config->activity_limit);

		// Add readmore permalink
		$content .= '<br /><a rel="nofollow" href="'.
				KunenaRoute::_($message->getPermaUrl()).
				'" class="small profile-newsfeed-item-action">'.JText::_('COM_KUNENA_READMORE').'</a>';

		$act = new stdClass ();
		$act->cmd = 'wall.write';
		$act->actor = $message->userid;
		$act->target = 0; // no target
		$act->title = JText::_ ( '{single}{actor}{/single}{multiple}{actors}{/multiple} ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG1' ) . ' <a href="' . $message->getTopic()->getUrl() . '">' . $message->subject . '</a> ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG2' ) );
		$act->content = $content;
		$act->app = 'kunena.post';
		$act->cid = $message->thread;
		$act->access = $this->getAccess($message->getCategory());

		// Do not add private activities
		if ($act->access > 20) return;
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add ( $act );
	}

	public function onAfterThankyou($thankyoutargetid, $username , $message) {
		CFactory::load ( 'libraries', 'userpoints' );
		CUserPoints::assignPoint ( 'com_kunena.thread.thankyou', $thankyoutargetid );

		$act = new stdClass ();
		$act->cmd = 'wall.write';
		$act->actor = JFactory::getUser()->id;
		$act->target = $thankyoutargetid;
		$act->title = JText::_ ( '{single}{actor}{/single}{multiple}{actors}{/multiple} ' . JText::_( 'COM_KUNENA_JS_ACTIVITYSTREAM_THANKYOU' ).' <a href="' . $message->getTopic()->getUrl() . '">' . $message->subject . '</a> ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG2' ) );
		$act->content = NULL;
		$act->app = 'kunena.thankyou';
		$act->cid = $thankyoutargetid;
		$act->access = $this->getAccess($message->getCategory());

		// Do not add private activities
		if ($act->access > 20) return;
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add ( $act );
	}

	protected function getAccess($category) {
		// Activity access level: 0 = public, 20 = registered, 30 = friend, 40 = private
		$accesstype = $category->accesstype;
		if ($accesstype != 'none' && $accesstype != 'joomla.level') {
			// Private
			return 40;
		}
		if (version_compare(JVERSION, '1.6','>')) {
			// Joomla 1.6+
			// FIXME: Joomla 1.6 can mix up groups and access levels
			if (($accesstype == 'joomla.level' && $category->access == 1)
					|| ($accesstype == 'none' && ($category->pub_access == 1 || $category->admin_access == 1))) {
				// Public
				$access = 0;
			} elseif (($accesstype == 'joomla.level' && $category->access == 2)
					|| ($accesstype == 'none' && ($category->pub_access == 2 || $category->admin_access == 2))) {
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
			if (($accesstype == 'joomla.level' && $category->access == 0)
					|| ($accesstype == 'none' && ($category->pub_access == 0 || $category->pub_access == 29 || $category->admin_access == 29))) {
				// Public
				$access = 0;
			} elseif (($accesstype == 'joomla.level' && $category->access == 1)
					|| ($accesstype == 'none' && ($category->pub_access == -1 || $category->pub_access == 18 || $category->admin_access == 18))) {
				// Registered
				$access = 20;
			} else {
				// Other groups (=private)
				$access = 40;
			}
		}
		return $access;
	}
}
