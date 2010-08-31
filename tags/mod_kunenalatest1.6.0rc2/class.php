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
class modKunenaLatest {
	public function __construct($params) {
		static $cssadded = false;

		require_once (KUNENA_PATH_LIB . DS . 'kunena.link.class.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.image.class.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.timeformat.class.php');
		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		require_once (JPATH_ADMINISTRATOR . '/components/com_kunena/libraries/html/parser.php');
		$this->kunena_config = KunenaFactory::getConfig ();

		// load Kunena main language file so we can leverage langaueg strings from it
		KunenaFactory::loadLanguage();

		$this->document = JFactory::getDocument ();
		if ($cssadded == false) {
			$this->document->addStyleSheet ( JURI::root () . 'modules/mod_kunenalatest/tmpl/klatest.css' );
			$cssadded = true;
		}

		$this->latestdo = null;

		if ($params->get ( 'choosemodel' ) != 'latest') {
			$this->latestdo = $params->get ( 'choosemodel' );
		}

		$this->params = $params;
		$this->ktemplate = KunenaFactory::getTemplate();
		$this->klistpost = modKunenaLatestHelper::getKunenaLatestList ( $params );

		require (JModuleHelper::getLayoutPath ( 'mod_kunenalatest' ));
	}
}

class modKunenaLatestHelper {
	function getModel() {
		$model = new CKunenaLatestX ( '', 0 );

		return $model;
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
			$result = $model->customreply;
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
