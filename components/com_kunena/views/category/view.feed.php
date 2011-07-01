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
kimport ( 'kunena.template' );
require_once KPATH_SITE.'/lib/kunena.link.class.php';

/**
 * Category View
 */
class KunenaViewCategory extends KunenaView {
	function displayDefault($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		if (!$this->config->enablerss) {
			JError::raiseError ( 404, JText::_ ( 'COM_KUNENA_RSS_DISABLED' ) );
		}

		$this->assignRef ( 'category', $this->get ( 'Category' ) );
		if (! $this->category->authorise('read')) {
			JError::raiseError ( 404, $this->category->getError() );
		}

		$this->template = KunenaTemplate::getInstance();
		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );

		$title = JText::_('COM_KUNENA_THREADS_IN_FORUM').': '. $this->category->name;
		$this->setTitle ( $title );

		$metaDesc = $this->document->get ( 'description' ) . '. ' . $this->escape ( "{$this->category->name} - {$this->config->board_title}" );
		$this->document->setDescription ( $metaDesc );

		// Create image for feed
		$image = new JFeedImage();
		$image->title = $this->document->getTitle();
		$image->url = $this->template->getImagePath('icons/rss.png');
		$image->description = $this->document->getDescription();
		$this->document->image = $image;

		foreach ( $this->topics as $topic ) {
			$id = $topic->last_post_id;
			$page = ceil ( $topic->posts / $this->config->messages_per_page );
			$description = $topic->last_post_message;
			$date = new JDate($topic->last_post_time);
			$userid = $topic->last_post_userid;
			$username = KunenaFactory::getUser($userid)->getName($topic->last_post_guest_name);

			$title = $topic->subject;
			$url = CKunenaLink::GetThreadPageURL('view', $topic->category_id, $topic->id, $page, $this->config->messages_per_page, $id, true );
			$category = $topic->getCategory()->name;

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