<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Category View
 */
class KunenaViewCategory extends KunenaView {
	function displayDefault($tpl = null) {
		if (!$this->config->enablerss) {
			JError::raiseError ( 404, JText::_ ( 'COM_KUNENA_RSS_DISABLED' ) );
		}

		KunenaHtmlParser::$relative = false;

		$this->category = $this->get ( 'Category' );
		if (! $this->category->authorise('read')) {
			JError::raiseError ( 404, $this->category->getError() );
		}

		$this->topics = $this->get ( 'Topics' );

		$title = JText::_('COM_KUNENA_THREADS_IN_FORUM').': '. $this->category->name;
		$this->setTitle ( $title );

		$metaDesc = $this->document->getDescription() . '. ' . $this->escape ( "{$this->category->name} - {$this->config->board_title}" );
		$this->document->setDescription ( $metaDesc );

		// Create image for feed
		$image = new JFeedImage();
		$image->title = $this->document->getTitle();
		$image->url = $this->ktemplate->getImagePath('icons/rss.png');
		$image->description = $this->document->getDescription();
		$this->document->image = $image;

		foreach ( $this->topics as $topic ) {
			$description = $topic->last_post_message;
			$date = new JDate($topic->last_post_time);
			$userid = $topic->last_post_userid;
			$username = KunenaFactory::getUser($userid)->getName($topic->last_post_guest_name);

			$title = $topic->subject;
			$category = $topic->getCategory();
			$url = $topic->getUrl($category, true, 'last');

			$this->createItem($title, $url, $description, $category->name, $date, $userid, $username);
		}
	}

	function createItem($title, $url, $description, $category, $date, $userid, $username) {
		if ($this->config->rss_author_in_title) {
			// We want author in item titles
			$title .= ' - '. JText::_('COM_KUNENA_BY') .': '. $username;
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
		$item->date			= $date->toSql();
		$item->author		= $username;
		// FIXME: inefficient to load users one by one -- also vulnerable to J! 2.5 user is NULL bug
		if ($this->config->rss_author_format != 'name') $item->authorEmail = JFactory::getUser($userid)->email;
		$item->category		= $category;

		// Finally add item to feed
		$this->document->addItem($item);
	}
}
