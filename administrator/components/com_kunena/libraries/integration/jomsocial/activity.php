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

		//activity stream  - new post
		require_once KPATH_SITE.'/lib/kunena.link.class.php';
		$JSPostLink = CKunenaLink::GetThreadPageURL ( 'view', $message->catid, $message->thread, 0 );

		$content = KunenaHtmlParser::plainBBCode($message->message, $this->_config->activity_limit);

		// Add readmore link
		$content .= '<br /><a href="'.
				CKunenaLink::GetMessageURL($message->id, $message->catid).
				'" class="small profile-newsfeed-item-action">'.JText::_('COM_KUNENA_READMORE').'</a>';

		$act = new stdClass ();
		$act->cmd = 'wall.write';
		$act->actor = $message->userid;
		$act->target = 0; // no target
		$act->title = JText::_ ( '{actor} ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG1' ) . ' <a href="' . $JSPostLink . '">' . $message->subject . '</a> ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG2' ) );
		$act->content = $content;
		$act->app = 'kunena.post';
		$act->cid = $message->thread;
		$act->access = $this->getAccess($message->getCategory());

		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add ( $act );
	}

	public function onAfterReply($message) {
		CFactory::load ( 'libraries', 'userpoints' );
		CUserPoints::assignPoint ( 'com_kunena.thread.reply' );

		//activity stream - reply post
		require_once KPATH_SITE.'/lib/kunena.link.class.php';
		$JSPostLink = CKunenaLink::GetThreadPageURL ( 'view', $message->catid, $message->thread, 0 );

		$content = KunenaHtmlParser::plainBBCode($message->message, $this->_config->activity_limit);

		// Add readmore link
		$content .= '<br /><a href="'.
				CKunenaLink::GetMessageURL($message->id, $message->catid).
				'" class="small profile-newsfeed-item-action">'.JText::_('COM_KUNENA_READMORE').'</a>';

		$act = new stdClass ();
		$act->cmd = 'wall.write';
		$act->actor = $message->userid;
		$act->target = 0; // no target
		$act->title = JText::_ ( '{single}{actor}{/single}{multiple}{actors}{/multiple} ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG1' ) . ' <a href="' . $JSPostLink . '">' . $message->subject . '</a> ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG2' ) );
		$act->content = $content;
		$act->app = 'kunena.post';
		$act->cid = $message->thread;
		$act->access = $this->getAccess($message->getCategory());

		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add ( $act );
	}

	public function onAfterThankyou($thankyoutargetid, $username , $message) {
		CFactory::load ( 'libraries', 'userpoints' );
		CUserPoints::assignPoint ( 'com_kunena.thread.thankyou', $thankyoutargetid );

		//activity stream - reply post
		require_once KPATH_SITE.'/lib/kunena.link.class.php';
		$JSPostLink = CKunenaLink::GetThreadPageURL ( 'view', $message->catid, $message->thread, 0 );

		$act = new stdClass ();
		$act->cmd = 'wall.write';
		$act->actor = JFactory::getUser()->id;
		$act->target = $thankyoutargetid;
		$act->title = JText::_ ( '{single}{actor}{/single}{multiple}{actors}{/multiple} ' . JText::_( 'COM_KUNENA_JS_ACTIVITYSTREAM_THANKYOU' ).' <a href="' . $JSPostLink . '">' . $message->subject . '</a> ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG2' ) );
		$act->content = NULL;
		$act->app = 'kunena.thankyou';
		$act->cid = $thankyoutargetid;
		$act->access = $this->getAccess($message->getCategory());

		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add ( $act );
	}

	protected function getAccess($category) {
		// Activity access level: 0 = public, 20 = registered, 30 = friend, 40 = private

		// TODO: add support for custom access types
		if ($category->accesstype != 'none') return 40;

		if (version_compare(JVERSION, '1.6','>')) {
			// Joomla 1.6+
			if ($category->pub_access == 1) {
				// Public
				return 0;
			} elseif ($category->pub_access == 2) {
				// Registered
				return 20;
			} else {
				// Other groups (=private)
				return 40;
			}
		} else {
			// Joomla 1.5
			if ($category->pub_access == 0) {
				// Public
				return 0;
			} elseif ($category->pub_access == -1 || $category->pub_access == 18) {
				// Registered
				return 20;
			} else {
				// Other groups (=private)
				return 40;
			}
		}
	}
}
