<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Category View
 * @since Kunena
 */
class KunenaViewCategory extends KunenaView
{
	/**
	 * @param   null $tpl tpl
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayDefault($tpl = null)
	{
		if (!$this->config->enablerss)
		{
			throw new Exception(Text::_('COM_KUNENA_RSS_DISABLED'), 500);
		}

		KunenaHtmlParser::$relative = false;

		$this->category = $this->get('Category');

		if (!$this->category->isAuthorised('read'))
		{
			throw new Exception($this->category->getError(), 404);
		}

		$this->topics = $this->get('Topics');

		$title = Text::_('COM_KUNENA_THREADS_IN_FORUM') . ': ' . $this->category->name;
		$this->setTitle($title);

		$metaDesc = $this->document->getDescription() . '. ' . $this->escape("{$this->category->name} - {$this->config->board_title}");
		$this->document->setDescription($metaDesc);

		// Create image for feed
		$image                 = new \Joomla\CMS\Document\Feed\FeedImage;
		$image->title          = $this->document->getTitle();
		$image->url            = $this->ktemplate->getImagePath('icons/rss.png');
		$image->description    = $this->document->getDescription();
		$this->document->image = $image;

		foreach ($this->topics as $topic)
		{
			if ($this->config->rss_type=='topic')
			{
			    $description = Text::sprintf('COM_KUNENA_RSS_TOPICS_CONTAINS_MESSAGES', $topic->posts) . ' - ' . Text::sprintf('COM_KUNENA_RSS_LAST_AUTHOR', KunenaFactory::getUser($topic->last_post_userid)->getName($topic->last_post_guest_name)); 
			}
			else
			{
				$description = $topic->last_post_message;
			}

			$date        = new \Joomla\CMS\Date\Date($topic->last_post_time);
			$userid      = $topic->last_post_userid;
			$username    = KunenaFactory::getUser($userid)->getName($topic->last_post_guest_name);

			$title    = $topic->subject;
			$category = $topic->getCategory();
			$url      = $topic->getUrl($category, true, 'last');

			$this->createItem($title, $url, $description, $category->name, $date, $userid, $username);
		}
	}

	/**
	 * @param $title
	 * @param $url
	 * @param $description
	 * @param $category
	 * @param $date
	 * @param $userid
	 * @param $username
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function createItem($title, $url, $description, $category, $date, $userid, $username)
	{
		if ($this->config->rss_author_in_title)
		{
			// We want author in item titles
			$title .= ' - ' . Text::_('COM_KUNENA_RSS_BY') . ': ' . $username;
		}

		if ((int) $this->config->rss_word_count === -1)
		{
			$description = '';
		}
		else
		{
			$description = preg_replace('/\[confidential\](.*?)\[\/confidential\]/s', '', $description);
			$description = preg_replace('/\[hide\](.*?)\[\/hide\]/s', '', $description);
			$description = preg_replace('/\[spoiler\](.*?)\[\/spoiler\]/s', '', $description);
			$description = preg_replace('/\[code\](.*?)\[\/code]/s', '', $description);

			if ((bool) $this->config->rss_allow_html)
			{
				$description = KunenaHtmlParser::parseBBCode($description, null, (int) $this->config->rss_word_count);
			}
			else
			{
				$description = KunenaHtmlParser::parseText($description, (int) $this->config->rss_word_count);
			}
		}

		// Assign values to feed item
		$item              = new \Joomla\CMS\Document\Feed\FeedItem;
		$item->title       = $title;
		$item->link        = $url;
		$item->description = $description;
		$item->date        = $date->toSql();
		$item->author      = $username;

		// FIXME: inefficient to load users one by one -- also vulnerable to J! 2.5 user is NULL bug
		if ($this->config->rss_author_format != 'name')
		{
			$item->authorEmail = Factory::getUser($userid)->email;
		}

		$item->category = $category;

		// Finally add item to feed
		$this->document->addItem($item);
	}
}
