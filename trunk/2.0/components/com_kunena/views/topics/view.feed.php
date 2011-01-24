<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );
kimport ( 'kunena.template' );

/**
 * Topics View
 */
class KunenaViewTopics extends KunenaView {
	function displayDefault($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		if (!$this->config->enablerss) {
			JError::raiseError ( 404, JText::_ ( 'COM_KUNENA_RSS_DISABLED' ) );
		}
		/*
		// TODO: caching (this is from old code)
		$cache = JFactory::getCache( 'com_kunena_rss' );
		if ( $this->caching ) {
			$cache->setCaching( 1 );
			$cache->setLifeTime( $this->caching );
		}
		$data = $cache->call( array( 'CKunenaRSSData', 'fetch' ), $this->type, $this->incl_cat, $this->excl_cat, $this->limit, $this->timelimit );
		*/
		$this->layout = 'default';
		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );
		$this->me = KunenaFactory::getUser();
		$this->template = KunenaTemplate::getInstance();

		// TODO: if start != 0, add information from it into description
		$this->document->setGenerator('Kunena Forum (Joomla)');

		switch ($this->state->get ( 'list.mode' )) {
			case 'topics' :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_TOPICS');
				break;
			case 'sticky' :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_STICKY');
				break;
			case 'locked' :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_LOCKED');
				break;
			case 'noreplies' :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_NOREPLIES');
				break;
			case 'unapproved' :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_UNAPPROVED');
				break;
			case 'deleted' :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DELETED');
				break;
			case 'replies' :
			default :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DEFAULT');
		}
		$this->setTitle ( $title );

		// Create image for feed
		$image = new JFeedImage();
		$image->title = $this->document->getTitle();
		$image->url = $this->template->getImagePath('icons/rss.png');
		$image->description = $this->document->getDescription();
		$this->document->image = $image;

		$this->displayTopicRows();
	}

	function displayUser($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		if (!$this->config->enablerss) {
			JError::raiseError ( 404, JText::_ ( 'COM_KUNENA_RSS_DISABLED' ) );
		}
		$this->layout = 'user';
		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );
		$this->me = KunenaFactory::getUser();
		$this->template = KunenaTemplate::getInstance();

		// TODO: if start != 0, add information from it into description
		$title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');
		$this->document->setGenerator('Kunena Forum (Joomla)');

		switch ($this->state->get ( 'list.mode' )) {
			case 'posted' :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_POSTED');
				break;
			case 'started' :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_STARTED');
				break;
			case 'favorites' :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_FAVORITES');
				break;
			case 'subscriptions' :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_SUBSCRIPTIONS');
				break;
			default :
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_DEFAULT');
		}
		$this->setTitle ( $title );

		// Create image for feed
		$image = new JFeedImage();
		$image->title = $this->document->getTitle();
		$image->url = $this->template->getImagePath('icons/rss.png');
		$image->description = $this->document->getDescription();
		$this->document->image = $image;

		$this->displayTopicRows();
	}

	function displayPosts($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		if (!$this->config->enablerss) {
			JError::raiseError ( 404, JText::_ ( 'COM_KUNENA_RSS_DISABLED' ) );
		}
		$this->layout = 'posts';
		$this->assignRef ( 'messages', $this->get ( 'Messages' ) );
		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );
		$this->me = KunenaFactory::getUser();
		$this->template = KunenaTemplate::getInstance();

		// TODO: if start != 0, add information from it into description
		$title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');
		$this->document->setGenerator('Kunena Forum (Joomla)');

		switch ($this->state->get ( 'list.mode' )) {
			case 'unapproved':
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_UNAPPROVED');
				break;
			case 'deleted':
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DELETED');
				break;
			case 'mythanks':
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_MYTHANKS');
				break;
			case 'thankyou':
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_THANKYOU');
				break;
			case 'recent':
			default:
				$title =  JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DEFAULT');
		}
		$this->setTitle ( $title );

		// Create image for feed
		$image = new JFeedImage();
		$image->title = $this->document->getTitle();
		$image->url = $this->template->getImagePath('icons/rss.png');
		$image->description = $this->document->getDescription();
		$this->document->image = $image;

		$this->displayPostRows();
	}

	function displayTopicRows() {
		require_once KPATH_SITE.'/lib/kunena.link.class.php';
		$firstpost = $this->state->get ( 'list.mode' ) == 'topics';
		foreach ( $this->topics as $topic ) {
			if ($firstpost) {
				$id = $topic->first_post_id;
				$page = 0;
				$description = $topic->first_post_message;
				$date = new JDate($topic->first_post_time);
				$userid = $topic->first_post_userid;
				$username = KunenaFactory::getUser($userid)->getName($topic->first_post_guest_name);
			} else {
				$id = $topic->last_post_id;
				$page = ceil ( $topic->posts / $this->config->messages_per_page );
				$description = $topic->last_post_message;
				$date = new JDate($topic->last_post_time);
				$userid = $topic->last_post_userid;
				$username = KunenaFactory::getUser($userid)->getName($topic->last_post_guest_name);
			}
			$title = $topic->subject;
			$url = CKunenaLink::GetThreadPageURL('view', $topic->category_id, $topic->id, $page, $this->config->messages_per_page, $id, true );
			$category = $topic->getCategory()->name;

			$this->createItem($title, $url, $description, $category, $date, $userid, $username);
		}
	}

	function displayPostRows() {
		require_once KPATH_SITE.'/lib/kunena.link.class.php';
		foreach ( $this->messages as $message ) {
			if (!isset($this->topics[$message->thread])) {
				// TODO: INTERNAL ERROR
				return;
			}
			$topic = $this->topics[$message->thread];
			$title = $message->subject;
			// TODO: link must point into right page
			$url = CKunenaLink::GetThreadPageURL('view', $message->catid, $message->thread, 0, $this->config->messages_per_page, 0, true );
			$description = $message->message;
			$category = $topic->getCategory()->name;
			$date = new JDate($message->time);
			$userid = $message->userid;
			$username = KunenaFactory::getUser($userid)->getName($message->name);

			$this->createItem($title, $url, $description, $category, $date, $userid, $username);
		}
	}

	function createItem($title, $url, $description, $category, $date, $userid, $username) {
		if ($this->config->rss_author_in_title) {
			// We want author in item titles
			$title .= ' - '. JText::_('COM_KUNENA_GEN_BY') .': '. $username;
		}
		$description = preg_replace ( '/\[confidential\](.*?)\[\/confidential\]/s', '', $description );
		$description = preg_replace ( '/\[hide\](.*?)\[\/hide\]/s', '', $description );
		$description = preg_replace ( '/\[spoiler\](.*?)\[\/spoiler\]/s', '', $description );
		$description = preg_replace ( '/\[code\](.*?)\[\/code]/s', '', $description );
		if ((bool) $this->config->rss_allow_html) {
			$description = KunenaHtmlParser::parseBBCode($description, null, (int)$this->config->rss_word_count);
		} else {
			$description = KunenaHtmlParser::parseText($description, (int)$this->config->rss_word_count);
		}

		// Assign values to feed item
		$item = new JFeedItem();
		$item->title		= $title;
		$item->link			= $url;
		$item->description	= $description;
		$item->date			= $date->toMySQL();
		$item->author		= $username;
		if ($this->config->rss_author_format != 'name') $item->authorEmail = JFactory::getUser($userid)->email;
		$item->category		= $category;

		// Finally add item to feed
		$this->document->addItem($item);
	}
}