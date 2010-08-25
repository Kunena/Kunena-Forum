<?php
/**
 * @version $Id$
 * KunenaLatest Module
 * @package Kunena latest
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined ( '_JEXEC' ) or die ( '' );

class modKunenaLatestHelper {
	function getModel() {
		static $instance = null;

		if ($instance==null){
			// We dont provide a $func to latestX as we leverage the same model for
			// multiple display options on a single page.
			$instance = new CKunenaLatestX ( '', 0 );
		}

		return $instance;
	}

	function getKunenaLatestList($params) {
		KunenaFactory::getSession ( true );
		$model = self::getModel ($params);
		$model->threads_per_page = $params->get ( 'nbpost' );
		$model->latestcategory = $params->get ( 'category_id' );
		$model->latestcategory_in = $params->get ( 'sh_category_id_in' );

		$result = array ();
		$threadmode = true;

		switch ( $params->get( 'choosemodel' ) ) {
			case 'latestmessages' :
				$model->getLatestPosts();
				$threadmode = false;
				break;
			case 'noreplies' :
				$model->getNoReplies();
				break;
			case 'subscriptions' :
				$model->getSubscriptions();
				break;
			case 'favorites' :
				$model->getFavorites();
				break;
			case 'owntopics' :
				$model->getOwnTopics();
				break;
			case 'deletedposts' :
				$model->getDeletedPosts();
				$threadmode = false;
				break;
			case 'saidthankyouposts' :
				$model->getSaidThankYouPosts();
				$threadmode = false;
				break;
			case 'gotthankyouposts' :
				$model->getGotThankYouPosts();
				$threadmode = false;
				break;
			case 'userposts' :
				$model->getUserPosts();
				$threadmode = false;
				break;
			case 'latesttopics' :
			default :
				$model->getLatest ();
		}

		if ($threadmode == true) {
			// for thread mode we merge the thread data with the latestreply data
			// we want to keep the subject from the thread, but userinfo and lastest post info
			// from the lastest posts array - by doing so we can leverage a single template for
			// thread and message mode
			$result = $model->threads;

			foreach ($result as $message) {
				$message->id = $model->lastreply[$message->thread]->id;
				$message->message = $model->lastreply[$message->thread]->message;
				$message->userid = $model->lastreply[$message->thread]->userid;
				$message->name = $model->lastreply[$message->thread]->name;
				$message->lasttime = $model->lastreply[$message->thread]->lasttime;
			}

		} else {
			$result = $model->customreplies;
		}

		if (empty ( $result )){
			echo JText::_ ( 'MOD_KUNENALATEST_NO_MESSAGE' );
		}

		return $result;
	}

	function userAvatar($userid, $params) {
		$kunena_user = KunenaFactory::getUser ( ( int ) $userid );
		$username = $kunena_user->getName(); // Takes care of realname vs username setting
		$avatarlink = $kunena_user->getAvatarLink ( '', $params->get ( 'avatarwidth' ), $params->get ( 'avatarheight' ) );
		return CKunenaLink::GetProfileLink ( $userid, $avatarlink, $username );
	}
}
