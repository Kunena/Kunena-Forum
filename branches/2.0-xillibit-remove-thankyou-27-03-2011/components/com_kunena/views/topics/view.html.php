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
		$this->assignRef ( 'topic_ordering', $this->get ( 'MessageOrdering' ) );
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

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
		$this->assignRef ( 'topic_ordering', $this->get ( 'MessageOrdering' ) );
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

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
		$this->assignRef ( 'topic_ordering', $this->get ( 'MessageOrdering' ) );
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

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

	function getCategoryLink($category, $content = null) {
		if (!$content) $content = $this->escape($category->name);
		return JHTML::_('kunenaforum.link', "index.php?option=com_kunena&view=category&catid={$category->id}", $content, $this->escape($category->name), '', 'follow');
	}
	function getTopicLink($topic, $action, $content = null, $title = null, $class = null) {
		if ($action instanceof StdClass) {
			$message = $action;
			$action = 'm'.$message->id;
		}
		$uri = JURI::getInstance("index.php?option=com_kunena&view=topic&id={$topic->id}&action={$action}");
		if ($uri->getVar('action') !== null) {
			$uri->delVar('action');
			$uri->setVar('catid', $topic->getCategory()->id);
			/*if ($this->Itemid) {
				$uri->setVar('Itemid', $this->Itemid);
			}*/
			$limit = max(1, $this->config->messages_per_page);
			$mesid = 0;
			if (is_numeric($action)) {
				if ($action) $uri->setVar('limitstart', $action * $limit);
			} elseif (isset($message)) {
				$position = $topic->getPostLocation($message->id, $this->topic_ordering);
				$uri->setFragment($message->id);
			} else {
				switch ($action) {
					case 'first':
						$mesid = $topic->first_post_id;
						$position = $topic->getPostLocation($mesid, $this->topic_ordering);
						break;
					case 'last':
						$mesid = $topic->last_post_id;
						$position = $topic->getPostLocation($mesid, $this->topic_ordering);
						break;
					case 'unread':
						$mesid = $topic->lastread ? $topic->lastread : $topic->last_post_id;
						$position = $topic->getPostLocation($mesid, $this->topic_ordering);
						break;
				}
			}
			if ($mesid) {
				if (JFactory::getApplication()->getUserState( 'com_kunena.topic_layout', 'default' ) != 'threaded') {
					$uri->setFragment($mesid);
				} else {
					$uri->setVar('mesid', $mesid);
				}
			}
			if (isset($position)) {
				$limitstart = intval($position / $limit) * $limit;
				if ($limitstart) $uri->setVar('limitstart', $limitstart);
			}
		}
		if (!$content) $content = KunenaHtmlParser::parseText($topic->subject);
		if ($title === null) $title = $this->escape($topic->subject);
		return JHTML::_('kunenaforum.link', $uri, $content, $title, $class, 'nofollow');
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
			$this->category = $this->topic->getCategory();
			$this->position++;
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
			echo $this->loadTemplate('row');
			$lasttopic = $this->topic;
		}
	}

	function displayPostRows() {
		$lasttopic = NULL;
		$this->position = 0;
		foreach ( $this->messages as $this->message ) {
			if (!isset($this->topics[$this->message->thread])) {
				// TODO: INTERNAL ERROR
				return;
			}
			$this->topic = $this->topics[$this->message->thread];
			$this->category = $this->topic->getCategory();
			$this->position++;
			$this->module = $this->getModulePosition('kunena_topic_' . $this->position);
			$this->message_position = $this->topic->posts - ($this->topic->unread ? $this->topic->unread - 1 : 0);
			$this->pages = ceil ( $this->topic->posts / $this->config->messages_per_page );
			if ($this->config->avataroncat) {
				$this->topic->avatar = KunenaFactory::getUser($this->message->userid)->getAvatarImage('klist-avatar', 'list');
			}
			$this->spacing = 0;
			echo $this->loadTemplate('row');
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