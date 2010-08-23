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

		switch ( $params->get( 'choosemodel' ) ) {
			case 'latestmessages' :
				$model->getLatestPosts();
				$result = $model->customreply;
				break;
			case 'noreplies' :
				$model->getNoReplies();
				$result = $model->threads;
				break;
			case 'subscriptions' :
				$model->getSubscriptions();
				$result = $model->threads;
				break;
			case 'favorites' :
				$model->getFavorites();
				$result = $model->threads;
				break;
			case 'owntopics' :
				$model->getOwnTopics();
				$result = $model->threads;
				break;
			case 'deletedposts' :
				$model->getDeletedPosts();
				$result = $model->customreply;
				break;
			case 'saidthankyouposts' :
				$model->getSaidThankYouPosts();
				$result = $model->customreply;
				break;
			case 'gotthankyouposts' :
				$model->getGotThankYouPosts();
				$result = $model->customreply;
				break;
			case 'userposts' :
				$model->getUserPosts();
				$result = $model->customreply;
				break;
			case 'latesttopics' :
			default :
				$model->getLatest ();
		}

		if (empty ( $result )){
			echo JText::_ ( 'MOD_KUNENALATEST_NO_MESSAGE' );
		}

		return $result;
	}

	function userAvatar($userid, $params) {
		$kunena_user = KunenaFactory::getUser ( ( int ) $userid );
		$username = $params->get ( 'sh_usernameorrealname') ? $kunena_user->username : $kunena_user->name;
		$avatarlink = $kunena_user->getAvatarLink ( '', $params->get ( 'avatarwidth' ), $params->get ( 'avatarheight' ) );
		return CKunenaLink::GetProfileLink ( $userid, $avatarlink, $username );
	}
}
