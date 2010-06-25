<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
//
// Dont allow direct linking
defined ( '_JEXEC' ) or die ( '' );

class KunenaActivityJomSocial extends KunenaActivity {
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ( 'jomsocial' );
		if (! $this->integration || ! $this->integration->isLoaded ())
			return;
		$this->priority = 40;
		$this->_config = KunenaFactory::getConfig();
	}

	public function onAfterPost($message) {
		CFactory::load ( 'libraries', 'userpoints' );
		CUserPoints::assignPoint ( 'com_kunena.thread.new' );

		// Check for permisions of the current category - activity only if public or registered
		if (!empty($catinfo) && ($catinfo->pub_access == 0 || $catinfo->pub_access == - 1)) {
			if ($this->_config->js_actstr_integration) {
				//activity stream  - new post
				$JSPostLink = CKunenaLink::GetThreadPageURL ( 'view', $message->get('catid'), $message->get('thread'), 1 );

				$kunena_emoticons = smile::getEmoticons ( 1 );
				$content = $message->get('message');
				$content = smile::smileReplace ( $content, 0, $this->_config->disemoticons, $kunena_emoticons );
				$content = nl2br ( $content );

				$act = new stdClass ( );
				$act->cmd = 'wall.write';
				$act->actor = $message->get('userid');
				$act->target = 0; // no target
				$act->title = JText::_ ( '{actor} ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG1' ) . ' <a href="' . $JSPostLink . '">' . $message->get('subject') . '</a> ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_CREATE_MSG2' ) );
				$act->content = $content;
				$act->app = 'wall';
				$act->cid = 0;

				// jomsocial 0 = public, 20 = registered members
				if ($catinfo->pub_access == 0) {
					$act->access = 0;
				} else {
					$act->access = 20;
				}

				CFactory::load ( 'libraries', 'activities' );
				CActivityStream::add ( $act );
			}
		}
	}

	public function onAfterReply($message) {
		CFactory::load ( 'libraries', 'userpoints' );
		CUserPoints::assignPoint ( 'com_kunena.thread.reply' );

		// Check for permisions of the current category - activity only if public or registered
		if (!empty($catinfo) && ($catinfo->pub_access == 0 || $catinfo->pub_access == - 1 && $this->_config->js_actstr_integration)) {
			if ($this->_config->js_actstr_integration) {
				//activity stream - reply post
				$JSPostLink = CKunenaLink::GetThreadPageURL ( 'view', $message->get('catid'), $message->get('thread'), 1 );

				$kunena_emoticons = smile::getEmoticons ( 1 );
				$content = $message->get('message');
				$content = smile::smileReplace ( $content, 0, $this->_config->disemoticons, $kunena_emoticons );
				$content = nl2br ( $content );

				$act = new stdClass ( );
				$act->cmd = 'wall.write';
				$act->actor = $message->get('userid');
				$act->target = 0; // no target
				$act->title = JText::_ ( '{single}{actor}{/single}{multiple}{actors}{/multiple} ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG1' ) . ' <a href="' . $JSPostLink . '">' . $message->get('subject') . '</a> ' . JText::_ ( 'COM_KUNENA_JS_ACTIVITYSTREAM_REPLY_MSG2' ) );
				$act->content = $content;
				$act->app = 'wall';
				$act->cid = 0;

				// jomsocial 0 = public, 20 = registered members
				if ($catinfo->pub_access == 0) {
					$act->access = 0;
				} else {
					$act->access = 20;
				}

				CFactory::load ( 'libraries', 'activities' );
				CActivityStream::add ( $act );
			}
		}
	}
}
