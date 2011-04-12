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

kimport ( 'kunena.view' );
kimport ( 'kunena.html.parser' );
kimport ( 'kunena.html.pagination' );

/**
 * Topics View
 */
class KunenaViewTopics extends KunenaView {
	function displayDefault($tpl = null) {
		$this->layout = 'default';
		$this->params = $this->state->get('params');
		$this->Itemid = $this->get('Itemid');
		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );
		$this->assignRef ( 'topicActions', $this->get ( 'TopicActions' ) );
		$this->assignRef ( 'actionMove', $this->get ( 'ActionMove' ) );
		$this->assignRef ( 'message_ordering', $this->get ( 'MessageOrdering' ) );
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$this->URL = KunenaRoute::_();
		$this->rssURL = $this->config->enablerss ? KunenaRoute::_('&format=feed') : '';

		switch ($this->state->get ( 'list.mode' )) {
			case 'topics' :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_TOPICS');
				break;
			case 'sticky' :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_STICKY');
				break;
			case 'locked' :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_LOCKED');
				break;
			case 'noreplies' :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_NOREPLIES');
				break;
			case 'unapproved' :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_UNAPPROVED');
				break;
			case 'deleted' :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DELETED');
				break;
			case 'replies' :
			default :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DEFAULT');
		}
		$this->title = $this->headerText;

		//meta description and keywords
		$limit = $this->state->get('list.limit');
		$page = intval($this->state->get('list.start')/$limit)+1;
		$total = intval($this->total/$limit)+1;
		$pagesTxt = "{$page}/{$total}";
		$app = JFactory::getApplication();
		$metaKeys = $this->headerText . $this->escape ( ", {$this->config->board_title}, " ) . $app->getCfg ( 'sitename' );
		$metaDesc = $this->headerText . $this->escape ( " ({$pagesTxt}) - {$this->config->board_title}" );
		$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;

		$this->document->setMetadata ( 'robots', 'noindex, follow' );
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );
		$this->setTitle ( "{$this->title} ({$pagesTxt})" );

		$this->display($tpl);
	}

	function displayUser($tpl = null) {
		$this->layout = 'user';
		$this->params = $this->state->get('params');
		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );
		$this->assignRef ( 'topicActions', $this->get ( 'TopicActions' ) );
		$this->assignRef ( 'actionMove', $this->get ( 'ActionMove' ) );
		$this->assignRef ( 'message_ordering', $this->get ( 'MessageOrdering' ) );
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$this->URL = KunenaRoute::_();
		switch ($this->state->get ( 'list.mode' )) {
			case 'posted' :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_POSTED');
				break;
			case 'started' :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_STARTED');
				break;
			case 'favorites' :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_FAVORITES');
				break;
			case 'subscriptions' :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_SUBSCRIPTIONS');
				break;
			default :
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_DEFAULT');
		}
		$this->title = $this->headerText;

		//meta description and keywords
		$limit = $this->state->get('list.limit');
		$page = intval($this->state->get('list.start')/$limit)+1;
		$total = intval($this->total/$limit)+1;
		$pagesTxt = "{$page}/{$total}";
		$app = JFactory::getApplication();
		$metaKeys = $this->headerText . $this->escape ( ", {$this->config->board_title}, " ) . $app->getCfg ( 'sitename' );
		$metaDesc = $this->headerText . $this->escape ( " ({$pagesTxt}) - {$this->config->board_title}" );
		$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;

		$this->document->setMetadata ( 'robots', 'noindex, follow' );
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );
		$this->setTitle ( "{$this->title} ({$pagesTxt})" );

		$this->display($tpl);
	}

	function displayPosts($tpl = null) {
		$this->layout = 'posts';
		$this->params = $this->state->get('params');
		$this->assignRef ( 'messages', $this->get ( 'Messages' ) );
		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );
		$this->assignRef ( 'postActions', $this->get ( 'PostActions' ) );
		$this->actionMove = false;
		$this->assignRef ( 'message_ordering', $this->get ( 'MessageOrdering' ) );
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$this->URL = KunenaRoute::_();
		switch ($this->state->get ( 'list.mode' )) {
			case 'unapproved':
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_UNAPPROVED');
				break;
			case 'deleted':
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DELETED');
				break;
			case 'mythanks':
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_MYTHANKS');
				break;
			case 'thankyou':
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_THANKYOU');
				break;
			case 'recent':
			default:
				$this->headerText =  JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DEFAULT');
		}
		$this->title = $this->headerText;

		//meta description and keywords
		$limit = $this->state->get('list.limit');
		$page = intval($this->state->get('list.start')/$limit)+1;
		$total = intval($this->total/$limit)+1;
		$pagesTxt = "{$page}/{$total}";
		$app = JFactory::getApplication();
		$metaKeys = $this->headerText . $this->escape ( ", {$this->config->board_title}, " ) . $app->getCfg ( 'sitename' );
		$metaDesc = $this->headerText . $this->escape ( " ({$pagesTxt}) - {$this->config->board_title}" );
		$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;

		$this->document->setMetadata ( 'robots', 'noindex, follow' );
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );
		$this->setTitle ( "{$this->title} ({$pagesTxt})" );

		$this->display($tpl);
	}

	function displayRows() {
		if ($this->layout == 'posts') {
			$this->displayPostRows();
		} else {
			$this->displayTopicRows();
		}
	}

	function displayTopicRows() {
		$lasttopic = NULL;
		$this->position = 0;

		foreach ( $this->topics as $this->topic ) {
			$this->position++;
			$this->category = $this->topic->getCategory();
			$usertype = $this->me->getType($this->category->id, true);

			// TODO: add context (options, template) to caching
			$this->cache = true;
			$cache = JFactory::getCache('com_kunena', 'output');
			$cachekey = "{$this->getTemplateMD5()}.{$usertype}.t{$this->topic->id}.p{$this->topic->last_post_id}";
			$cachegroup = 'com_kunena.topics';

			$contents = $cache->get($cachekey, $cachegroup);
			if (!$contents) {
				$this->categoryLink = $this->getCategoryLink($this->category->getParent()) . ' / ' . $this->getCategoryLink($this->category);
				$this->firstPostAuthor = $this->topic->getfirstPostAuthor();
				$this->firstPostTime = $this->topic->last_post_time;
				$this->firstUserName = $this->topic->first_post_guest_name;
				$this->lastPostAuthor = $this->topic->getLastPostAuthor();
				$this->lastPostTime = $this->topic->last_post_time;
				$this->lastUserName = $this->topic->last_post_guest_name;
				$this->keywords = $this->topic->getKeywords(false, ', ');
				$this->module = $this->getModulePosition('kunena_topic_' . $this->position);
				$this->message_position = $this->topic->posts - ($this->topic->unread ? $this->topic->unread - 1 : 0);
				$this->pages = ceil ( $this->topic->getTotal() / $this->config->messages_per_page );
				if ($this->config->avataroncat) {
					$this->topic->avatar = KunenaFactory::getUser($this->topic->last_post_userid)->getAvatarImage('klist-avatar', 'list');
				}

				if (is_object($lasttopic) && $lasttopic->ordering != $this->topic->ordering) {
					$this->spacing = 1;
				} else {
					$this->spacing = 0;
				}
				$contents = $this->loadTemplate('row');
				if ($usertype == 'guest') $contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);
				if ($this->cache) $cache->store($contents, $cachekey, $cachegroup);
			}
			if ($usertype != 'guest') {
				$contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);
			}
			echo $contents;
			$lasttopic = $this->topic;
		}
	}

	function fillTopicInfo($matches) {
		switch ($matches[1]) {
			case 'ROW':
				return $matches[2].($this->position & 1 ? 'odd' : 'even').($this->topic->ordering ? " {$matches[2]}sticky" : '');
			case 'TOPIC_ICON':
				return $this->getTopicLink ( $this->topic, 'unread', $this->topic->getIcon() );
			case 'TOPIC_NEW_COUNT':
				if (!$this->config->shownew || !$this->me->exists()) return;
				return $this->topic->unread ? $this->getTopicLink ( $this->topic, 'unread', '<sup class="kindicator-new">(' . $this->topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>' ) : '';
			case 'DATE':
				$date = new KunenaDate($matches[2]);
				return $date->toSpan('config_post_dateformat', 'config_post_dateformat_hover');
		}
	}

	function displayPostRows() {
		$lasttopic = NULL;
		$this->position = 0;

		foreach ( $this->messages as $this->message ) {
			$this->position++;
			$this->topic = $this->message->getTopic();
			$this->category = $this->topic->getCategory();
			$usertype = $this->me->getType($this->category->id, true);

			// TODO: add context (options, template) to caching
			$this->cache = true;
			$cache = JFactory::getCache('com_kunena', 'output');
			$cachekey = "{$this->getTemplateMD5()}.{$usertype}.t{$this->topic->id}.p{$this->message->id}";
			$cachegroup = 'com_kunena.posts';

			$contents = $cache->get($cachekey, $cachegroup);
			if (!$contents) {
				$this->categoryLink = $this->getCategoryLink($this->category->getParent()) . ' / ' . $this->getCategoryLink($this->category);
				$this->postAuthor = KunenaFactory::getUser($this->message->userid);
				$this->firstPostAuthor = $this->topic->getfirstPostAuthor();
				$this->firstPostTime = $this->topic->last_post_time;
				$this->firstUserName = $this->topic->first_post_guest_name;
				$this->keywords = $this->topic->getKeywords(false, ', ');
				$this->module = $this->getModulePosition('kunena_topic_' . $this->position);
				$this->message_position = $this->topic->posts - ($this->topic->unread ? $this->topic->unread - 1 : 0);
				$this->pages = ceil ( $this->topic->getTotal() / $this->config->messages_per_page );
				if ($this->config->avataroncat) {
					$this->topic->avatar = KunenaFactory::getUser($this->topic->last_post_userid)->getAvatarImage('klist-avatar', 'list');
				}
				$contents = $this->loadTemplate('row');
				if ($usertype == 'guest') $contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);
				if ($this->cache) $cache->store($contents, $cachekey, $cachegroup);
			}
			if ($usertype != 'guest') {
				$contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);
			}
			echo $contents;
			$lasttopic = $this->topic;
		}
	}

	function getTopicClass($prefix='k', $class='topic') {
		$class = $prefix . $class;
		$txt = $class . (($this->position & 1) + 1);
		if ($this->topic->ordering) {
			$txt .= '-stickymsg';
		}
		if ($this->topic->getCategory()->class_sfx) {
			$txt .= ' ' . $class . (($this->position & 1) + 1);
			if ($this->topic->ordering) {
				$txt .= '-stickymsg';
			}
			$txt .= $this->escape($this->topic->getCategory()->class_sfx);
		}
		if ($this->topic->hold == 1) $txt .= ' '.$prefix.'unapproved';
		else if ($this->topic->hold) $txt .= ' '.$prefix.'deleted';
		return $txt;
	}

	function getPagination($maxpages) {
		$pagination = new KunenaHtmlPagination ( $this->total, $this->state->get('list.start'), $this->state->get('list.limit') );
		$pagination->setDisplay($maxpages);
		return $pagination->getPagesLinks();
	}
}