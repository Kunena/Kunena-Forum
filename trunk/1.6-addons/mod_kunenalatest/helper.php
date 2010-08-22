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
		if (! class_exists ( 'CKunenaLatestX' )) {
			// Build the path to the model based upon a supplied base path
			$path = JPATH_SITE . DS . 'components' . DS . 'com_kunena' . DS . 'funcs' . DS . 'latestx.php';
			$false = false;

			// If the model file exists include it and try to instantiate the object
			if (file_exists ( $path )) {
				require_once ($path);
				if (! class_exists ( 'CKunenaLatestX' )) {
					JError::raiseWarning ( 0, JText::_ ('MOD_KUNENALATEST_CKUNENALATEST_CLASS_NOT_FOUND') );
					return $false;
				}
			} else {
				JError::raiseWarning ( 0, JText::_ ('MOD_KUNENALATEST_CKUNENALATEST_FILE_NOT_FOUND') );
				return $false;
			}
		}

		$model = new CKunenaLatestX ( '', 0 );

		return $model;
	}

	function getKunenaLatestList($params) {
		KunenaFactory::getSession ( true );
		$model = self::getModel ();
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
			case 'categorysubscriptions' :
				$model->getCategorySubscriptions();
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
		if (empty ( $model->messages ))
			echo JText::_ ( 'MOD_KUNENALATEST_NO_MESSAGE' );
		foreach ( $model->messages as $message ) {
			if ( $params->get( 'choosemodel' ) == 'latestposts' ) {
				if ($message->parent == 0) {
					$result [$message->id] = $message;
				}
			} elseif( $params->get( 'choosemodel' ) == 'latestmessages' ) {
				$result [$message->id] = $message;
			}
		}

		return $result;
	}

	function userAvatar($userid, $params) {
		$kunena_user = KunenaFactory::getUser ( ( int ) $userid );
		$avatarlink = $kunena_user->getAvatarLink ( '', $params->get ( 'avatarwidth' ), $params->get ( 'avatarheight' ) );
		return CKunenaLink::GetProfileLink ( $userid, $avatarlink, $kunena_user->name );
	}
}
