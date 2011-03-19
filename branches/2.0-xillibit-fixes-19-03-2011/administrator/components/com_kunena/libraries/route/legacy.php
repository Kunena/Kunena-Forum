<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once KPATH_SITE . '/router.php';
kimport ( 'kunena.forum.message.helper' );

class KunenaRouteLegacy {
	function convert($uri) {
		// We need to convert URIs only in site
		if (!JFactory::getApplication()->isSite()) return;

		// Make sure that input is JURI to legacy Kunena func=xxx
		if (!($uri instanceof JURI)) return;
		if ($uri->getVar('option') != 'com_kunena') return;
		if ($uri->getVar('func')) {
			$uri->setVar('view', $uri->getVar('func'));
			$uri->delVar('func');
		}
		if (!isset(KunenaRouter::$functions[$uri->getVar('view')])) return;

		// Turn &do=xxx into &layout=xxx
		if ($uri->getVar('do')) {
			$uri->setVar('layout', $uri->getVar('do'));
			$uri->delVar('do');
		}

		$app = JFactory::getApplication();
		$config = KunenaFactory::getConfig ();
		$changed = false;
		switch ($uri->getVar('view')) {
			case 'listcat' :
				$changed = true;
				$uri->setVar('view', 'category');
				break;
			case 'showcat' :
				$changed = true;
				$uri->setVar('view', 'category');
				$page = (int) $uri->getVar ( 'page', 1 );
				if ($page > 0) {
					$uri->setVar ( 'limitstart', (int) $config->messages_per_page * ($page - 1) );
					$uri->setVar ( 'limit', (int) $config->messages_per_page );
				}
				$uri->delVar ( 'page' );
				break;
			case 'latest' :
			case 'mylatest' :
			case 'noreplies' :
			case 'subscriptions' :
			case 'favorites' :
			case 'userposts' :
			case 'unapproved' :
			case 'deleted' :
				$changed = true;
				$uri->setVar('view', 'topics');
				// Handle both &func=noreplies and &func=latest&do=noreplies
				$mode = $uri->getVar('layout') ? $uri->getVar('layout') : $uri->getVar('view');
				switch ($mode) {
					case 'latest' :
						$uri->setVar('layout', 'default');
						$uri->setVar('mode', 'replies');
						break;
					case 'unapproved' :
						$uri->setVar('layout', 'default');
						$uri->setVar('mode', 'unapproved');
						break;
					case 'deleted' :
						$uri->setVar('layout', 'default');
						$uri->setVar('mode', 'deleted');
						break;
					case 'noreplies' :
						$uri->setVar('layout', 'default');
						$uri->setVar('mode', 'noreplies');
						break;
					case 'latesttopics' :
						$uri->setVar('layout', 'default');
						$uri->setVar('mode', 'topics');
						break;
					case 'mylatest' :
						$uri->setVar('layout', 'user');
						$uri->setVar('mode', 'default');
						break;
					case 'subscriptions' :
						$uri->setVar('layout', 'user');
						$uri->setVar('mode', 'subscriptions');
						break;
					case 'favorites' :
						$uri->setVar('layout', 'user');
						$uri->setVar('mode', 'favorites');
						break;
					case 'owntopics' :
						$uri->setVar('layout', 'user');
						$uri->setVar('mode', 'started');
						break;
					case 'userposts' :
						$uri->setVar ( 'userid', '0' );
						// Continue in latestposts
					case 'latestposts' :
						$uri->setVar('layout', 'posts');
						$uri->setVar('mode', 'recent');
						break;
					case 'saidthankyouposts' :
						$uri->setVar('layout', 'posts');
						$uri->setVar('mode', 'mythanks');
						break;
					case 'gotthankyouposts' :
						$uri->setVar('layout', 'posts');
						$uri->setVar('mode', 'thankyou');
						break;
					case 'catsubscriptions' :
						$uri->setVar('view', 'category');
						$uri->setVar('layout', 'user');
						break;
					default :
						$uri->setVar('layout', 'default');
						$uri->setVar('mode', 'replies');
				}
				$page = (int) $uri->getVar ( 'page', 1 );
				if ($page > 0) {
					$uri->setVar ( 'limitstart', (int) $config->threads_per_page * ($page - 1) );
					$uri->setVar ( 'limit', (int) $config->threads_per_page );
				}
				$uri->delVar ( 'page' );
				break;
			case 'view' :
				$changed = true;
				$uri->setVar('view', 'topic');

				// Convert URI to have both id and mesid
				$id = $uri->getVar ( 'id' );
				$message = KunenaForumMessageHelper::get ( $id );
				$mesid = $uri->getVar ( 'mesid' );
				if ($message->exists ()) {
					$id = $message->thread;
					if ($id != $message->id)
						$mesid = $message->id;
				}
				if ($id) $uri->setVar ( 'id', $id );
				if ($mesid) $uri->setVar ( 'mesid', $mesid );
				break;
			case 'myprofile' :
			case 'userprofile' :
			case 'fbprofile' :
			case 'profile' :
			case 'moderateuser' :
				$changed = true;
				$uri->setVar('view', 'user');
				if ($uri->getVar ( 'task' )) {
					$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_DEPRECATED_ACTION' ), 'error' );
					$uri->delVar ( 'task' );
				}
				// Handle &do=xxx
				switch ($uri->getVar('layout')) {
					case 'edit' :
						$uri->setVar('layout', 'edit');
						break;
					default :
						$uri->setVar('layout', 'default');
						break;
				}
				break;
			case 'report' :
				$changed = true;
				$uri->setVar('view', 'topic');
				$uri->setVar('layout', 'report');

				// Convert URI to have both id and mesid
				$id = $uri->getVar ( 'id' );
				$message = KunenaForumMessageHelper::get ( $id );
				$mesid = null;
				if ($message->exists ()) {
					$id = $message->thread;
					if ($id != $message->id)
						$mesid = $message->id;
				}
				if ($id) $uri->setVar ( 'id', $id );
				if ($mesid) $uri->setVar ( 'mesid', $mesid );
				break;

			case 'userlist' :
				$changed = true;
				$uri->setVar('view', 'users');
				break;
			case 'rss' :
				$changed = true;
				$uri->setVar('view', 'topics');
				$mode = $config->rss_type;
				switch ($mode) {
					case 'topic' :
						$uri->getVar('mode', 'topics');
						break;
					case 'recent' :
						$uri->getVar('mode', 'replies');
						break;
					case 'post' :
					default :
						$uri->getVar('mode', 'posts');
						break;
				}
				switch ($config->rss_timelimit) {
					case 'week' :
						$uri->setVar ( 'sel', 168);
						break;
					case 'year' :
						$uri->setVar ( 'sel', 8760);
						break;
					case 'month' :
					default :
						$uri->setVar ( 'sel', 720);
						break;
				}
				$uri->setVar ( 'type', 'rss' );
				$uri->setVar ( 'format', 'feed' );
				break;
			case 'post' :
				$changed = true;
				$uri->setVar('view', 'topic');

				// Support old &parentid=123 and &replyto=123 variables
				$id = $uri->getVar ( 'id' );
				if (! $id) {
					$id = $uri->getVar ( 'parentid' );
					$uri->delVar ( 'parentid' );
				}
				if (! $id) {
					$id = $uri->getVar ( 'replyto' );
					$uri->delVar ( 'replyto' );
				}

				// Convert URI to have both id and mesid
				$message = KunenaForumMessageHelper::get ( $id );
				$mesid = null;
				if ($message->exists ()) {
					$id = $message->thread;
					if ($id != $message->id)
						$mesid = $message->id;
				}
				if ($id) $uri->setVar ( 'id', $id );
				if ($mesid) $uri->setVar ( 'mesid', $mesid );

				if ($uri->getVar ( 'action')) {
					$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_DEPRECATED_ACTION' ), 'error' );
					$uri->delVar ( 'action');
				} else {
					// Handle &do=xxx
					$layout = $uri->getVar ('layout');
					$uri->delVar ('layout');
					switch ($layout) {

						// Create, reply, quote and edit:
						case 'new' :
							$uri->setVar('layout', 'create');
							$uri->delVar ( 'id' );
							$uri->delVar ( 'mesid' );
							break;
						case 'quote' :
							$uri->setVar('layout', 'reply');
							$uri->setVar ('quote', 1);
							break;
						case 'reply' :
							$uri->setVar('layout', 'reply');
							break;
						case 'edit' :
							$uri->setVar('layout', 'edit');
							// Always add &mesid=x
							if (! $mesid)
								$uri->setVar ( 'mesid', $id );
							break;

						// Topic moderation:
						case 'moderatethread' :
							$uri->setVar('layout', 'moderate');
							// Always remove &mesid=x
							$uri->delVar ( 'mesid' );
							break;
						case 'deletethread' :
							$uri->setVar('task', 'delete');
							// Always remove &mesid=x
							$uri->delVar ( 'mesid' );
							break;
						case 'sticky' :
							$uri->setVar('task', 'sticky');
							// Always remove &mesid=x
							$uri->delVar ( 'mesid' );
							break;
						case 'unsticky' :
							$uri->setVar('task', 'unsticky');
							// Always remove &mesid=x
							$uri->delVar ( 'mesid' );
							break;
						case 'lock' :
							$uri->setVar('task', 'lock');
							// Always remove &mesid=x
							$uri->delVar ( 'mesid' );
							break;
						case 'unlock' :
							$uri->setVar('task', 'unlock');
							// Always remove &mesid=x
							$uri->delVar ( 'mesid' );
							break;

						// Message moderator actions:
						case 'moderate' :
							$uri->setVar('layout', 'moderate');
							// Always add &mesid=x
							if (! $mesid)
								$uri->setVar ( 'mesid', $id );
							break;
						case 'approve' :
							$uri->setVar('task', 'approve');
							// Always add &mesid=x
							if (! $mesid)
								$uri->setVar ( 'mesid', $id );
							break;
						case 'delete' :
							$uri->setVar('task', 'delete');
							// Always add &mesid=x
							if (! $mesid)
								$uri->setVar ( 'mesid', $id );
							break;
						case 'undelete' :
							$uri->setVar('task', 'undelete');
							// Always add &mesid=x
							if (! $mesid)
								$uri->setVar ( 'mesid', $id );
							break;
						case 'permdelete' :
							$uri->setVar('task', 'permdelete');
							// Always add &mesid=x
							if (! $mesid)
								$uri->setVar ( 'mesid', $id );
							break;

						// Topic user actions:
						case 'subscribe' :
							$uri->setVar('task', 'subscribe');
							// Always remove &mesid=x
							$uri->delVar ( 'mesid' );
							break;
						case 'unsubscribe' :
							$uri->setVar('task', 'unsubscribe');
							// Always remove &mesid=x
							$uri->delVar ( 'mesid' );
							break;
						case 'favorite' :
							$uri->setVar('task', 'favorite');
							// Always remove &mesid=x
							$uri->delVar ( 'mesid' );
							break;
						case 'unfavorite' :
							$uri->setVar('task', 'unfavorite');
							// Always remove &mesid=x
							$uri->delVar ( 'mesid' );
							break;

						default :
							$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_DEPRECATED_ACTION' ), 'error' );
					}
				}
				break;
			case 'stats':
				$changed = true;
				$uri->setVar('view', 'statistics');
				break;
			case 'search':
			case 'advsearch':
				$changed = true;
				$uri->setVar('view', 'search');
				break;
			case 'poll':
				$changed = true;
				$uri->setVar('view', 'topic');
				// Handle &do=xxx
				switch ($uri->getVar('layout')) {
					case 'changevote' :
						$uri->setVar('layout', 'vote');
						break;
				}
				break;
			case 'review':
				$changed = true;
				$uri->setVar('view', 'topics');
				$uri->setVar('layout', 'posts');
				$uri->setVar('mode', 'unapproved');
				$uri->setVar('userid', 0);
				$uri->delVar('action');
				break;
			case 'announcement':
				switch ($uri->getVar('layout')) {
					case 'read':
						$changed = true;
						$uri->delVar('layout');
						break;
					case 'show':
						$changed = true;
						$uri->setVar('layout', 'list');
						break;
					case 'add':
						$changed = true;
						$uri->setVar('layout', 'create');
						break;
					case 'edit':
						$changed = false;
						break;
					case 'doedit':
						$changed = true;
						$uri->delVar('layout');
						$uri->setVar('task', 'edit');
						break;
					case 'delete':
						$changed = true;
						$uri->delVar('layout');
						$uri->setVar('task', 'delete');
						break;
				}
				break;
			case 'thankyou':
				// Convert URI to have both id and mesid
				$id = $uri->getVar ( 'pid' );
				if ($id) {
					$changed = true;
					$uri->setVar('view', 'topic');
					$uri->setVar('task', 'thankyou');
					$uri->delVar('pid');

					$message = KunenaForumMessageHelper::get ( $id );
					if ($message->exists ()) {
						$id = $message->thread;
						$uri->setVar ( 'mesid', $message->id );
					}
					$uri->setVar ( 'id', $id );
				}
				break;
			case 'karma':
				$changed = true;
				$uri->setVar('view', 'user');
				switch ($uri->getVar('layout')) {
					case 'increase':
						$uri->setVar('task', 'karmaup');
						break;
					case 'decrease':
						$uri->setVar('task', 'karmadown');
						break;
				}
				$uri->delVar('layout');
				$uri->delVar('pid');
				break;

		}
		return $changed;
	}
}