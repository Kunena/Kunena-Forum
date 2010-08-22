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
	function getModel($params) {
		static $instance = null;

		if ($instance==null){
			$instance = new CKunenaLatestX ( $params->get( 'choosemodel' ), 0 );
		}

		return $instance;
	}

	function getKunenaLatestList($params) {
		KunenaFactory::getSession ( true );
		$model = self::getModel ($params);
		$model->threads_per_page = $params->get ( 'nbpost' );
		$model->latestcategory = $params->get ( 'category_id' );
		$model->latestcategory_in = $params->get ( 'sh_category_id_in' );

		switch ( $params->get( 'choosemodel' ) ) {
			case 'latestmessages' :
				$model->getLatestPosts();
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
				break;
			case 'saidthankyouposts' :
				$model->getSaidThankYouPosts();
				break;
			case 'gotthankyouposts' :
				$model->getGotThankYouPosts();
				break;
			case 'userposts' :
				$model->getUserPosts();
				break;
			case 'latestposts' :
			default :
				$model->getLatest ();
		}

		$result = array ();
		if (empty ( $model->messages )){
			echo JText::_ ( 'MOD_KUNENALATEST_NO_MESSAGE' );
		}
		else {
			$result = $model->messages;
		}

//		foreach ( $model->messages as $message ) {
//			if ( $params->get( 'choosemodel' ) == 'latestposts' ) {
//				if ($message->parent == 0) {
//					$result [$message->id] = $message;
//				}
//			} elseif( $params->get( 'choosemodel' ) == 'latestmessages' ) {
//				$result [$message->id] = $message;
//			}
//		}

		return $result;
	}

	function userAvatar($userid, $params) {
		$kunena_user = KunenaFactory::getUser ( ( int ) $userid );
		$avatarlink = $kunena_user->getAvatarLink ( '', $params->get ( 'avatarwidth' ), $params->get ( 'avatarheight' ) );
		return CKunenaLink::GetProfileLink ( $userid, $avatarlink, $kunena_user->name );
	}
}
