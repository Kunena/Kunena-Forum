<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Route
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once KPATH_SITE . '/router.php';

/**
 * Class KunenaRouteLegacy
 */
abstract class KunenaRouteLegacy {
	// List of legacy views from previous releases
	static $functions = array (
		'entrypage'=>1,
		'listcat'=>1,
		'showcat'=>1,
		'latest'=>1,
		'mylatest'=>1,
		'noreplies'=>1,
		'subscriptions'=>1,
		'favorites'=>1,
		'userposts'=>1,
		'unapproved'=>1,
		'deleted'=>1,
		'view'=>1,
		'profile'=>1,
		'myprofile'=>1,
		'userprofile'=>1,
		'fbprofile'=>1,
		'moderateuser'=>1,
		'userlist'=>1,
		'rss'=>1,
		'post'=>1,
		'report'=>1,
		'template'=>1,
		'announcement'=>1,
		'article'=>1,
		'who'=>1,
		'poll'=>1,
		'polls'=>1,
		'stats'=>1,
		'help'=>1,
		'review'=>1,
		'rules'=>1,
//		'search'=>1,
		'advsearch'=>1,
		'markallcatsread'=>1,
		'markthisread'=>1,
		'subscribecat'=>1,
		'unsubscribecat'=>1,
		'karma'=>1,
		'bulkactions'=>1,
		'templatechooser'=>1,
		'json'=>1,
		'pdf'=>1,
		'thankyou'=>1,
		'fb_pdf'=>1,
	);

	public static function isLegacy($view) {
		if (!$view || $view=='legacy') return true;
		return isset(self::$functions[$view]);
	}

	public static function convert($uri, $showstart = 1) {
		// Make sure that input is JUri to legacy Kunena func=xxx
		if (!($uri instanceof JUri)) {
			return;
		}
		if ($uri->getVar('option') != 'com_kunena') {
			return;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if ($uri->getVar('func')) {
			$uri->setVar('view', $uri->getVar('func'));
			$uri->delVar('func');
		}
		if (!isset(self::$functions[$uri->getVar('view')])) {
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return;
		}

		$legacy = clone $uri;

		// Turn &do=xxx into &layout=xxx
		if ($uri->getVar('do')) {
			$uri->setVar('layout', $uri->getVar('do'));
			$uri->delVar('do');
		}

		$app = JFactory::getApplication();
		$config = KunenaFactory::getConfig ();
		$changed = false;
		switch ($uri->getVar('view')) {
			case 'entrypage' :
				$changed = true;
				$uri->setVar('view', 'home');
				break;
			case 'listcat' :
				$changed = true;
				$uri->setVar('view', 'category');
				$uri->setVar('layout', 'list');
				break;
			case 'showcat' :
				$changed = true;
				$uri->setVar('view', 'category');
				$page = (int) $uri->getVar ( 'page', $showstart );
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
				$page = (int) $uri->getVar ( 'page', $showstart );
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
			case 'moderateuser' :
				if ($uri->getVar('view') == 'moderateuser') $uri->setVar('layout', 'moderate');
				// Continue to user profile
			case 'myprofile' :
			case 'userprofile' :
			case 'fbprofile' :
			case 'profile' :
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
					case 'moderate' :
						$uri->setVar('layout', 'moderate');
						break;
					default :
						$uri->delVar('layout');
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
				$uri->setVar('view', 'user');
				$uri->setVar('layout', 'list');
				break;
			case 'rss' :
				$changed = true;
				$uri->setVar('view', 'topics');
				$mode = $config->rss_type;
				switch ($mode) {
					case 'topic' :
						$uri->setVar('layout', 'default');
						$uri->setVar('mode', 'topics');
						break;
					case 'recent' :
						$uri->setVar('layout', 'default');
						$uri->setVar('mode', 'replies');
						break;
					case 'post' :
					default :
						$uri->setVar('layout', 'posts');
						$uri->setVar('mode', 'latest');
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
			case 'template' :
				$changed = true;
				$uri->setVar('view', 'misc');
				$uri->setVar('task', 'template');
				break;
			case 'rules' :
			case 'help' :
				$changed = true;
				$uri->setVar('view', 'misc');
				$uri->delVar('layout');
				break;

		}
		if ($changed) {
			JLog::add("Legacy URI {$legacy->toString(array('path', 'query'))} was converted to {$uri->toString(array('path', 'query'))}", JLog::DEBUG, 'kunena');
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $changed;
	}

	public static function convertMenuItem($item) {
		$uri = JUri::getInstance($item->link);
		$view = $uri->getVar('func', $uri->getVar('view'));

		$params = new JRegistry($item->params);

		if (self::convert($uri, 0)) {

			switch ($view) {
				case 'latest' :
				case 'mylatest' :
				case 'noreplies' :
				case 'subscriptions' :
				case 'favorites' :
				case 'userposts' :
				case 'unapproved' :
				case 'deleted' :
					$params->set('do', null);
					$params->set('mode', $uri->getVar('mode', null));
					break;
				case 'post' :
					$params->set('do', null);
					break;
				case 'rules' :
					$params->set('body', '[article=full]'.KunenaFactory::getConfig()->get('rules_cid', 1).'[/article]');
					$params->set('body_format', 'bbcode');
					$params->set('do', null);
					break;
				case 'help' :
					$params->set('body', '[article=full]'.KunenaFactory::getConfig()->get('help_cid', 1).'[/article]');
					$params->set('body_format', 'bbcode');
					$params->set('do', null);
					break;
			}
		}
		$item->link = $uri->toString();
		$item->query = $uri->getQuery(true);
		$item->params = $params->toString();
	}
}
