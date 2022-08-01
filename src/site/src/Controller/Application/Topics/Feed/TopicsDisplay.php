<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Application\Topics\Feed;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Site\Model\TopicsModel;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Document\Feed\FeedImage;
use Joomla\CMS\Document\Feed\FeedItem;

/**
 * CategoryDisplay
 *
 * @since   Kunena 6.0
 */
class TopicsDisplay extends KunenaControllerDisplay
{
	/**
	 * Return true if layout exists.
	 *
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function exists()
	{
		return KunenaFactory::getTemplate()->isHmvc();
	}

	/**
	 * Prepare the content of the feed.
	 *
	 * @return  void
	 *
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	protected function before()
	{
		$this->config = KunenaFactory::getConfig();
		$this->document = Factory::getApplication()->getDocument();
		$this->topicsModel = new TopicsModel;
		$mode = $this->getInput()->getWord('mode', 'topics');

		if (!$this->config->enableRss)
		{
			throw new Exception(Text::_('COM_KUNENA_RSS_DISABLED'), 500);
		}

		KunenaParser::$relative = false;
		$cache            = Factory::getCache('com_kunena_rss', 'output');

		if (!$this->config->get('cache'))
		{
			$cache->setCaching(0);
		}
		else
		{
			if ($this->config->rssCache >= 1)
			{
				$cache->setCaching(1);
				$cache->setLifeTime($this->config->rssCache);
			}
			else
			{
				$cache->setCaching(0);
			}
		}

		// TODO: if start != 0, add information from it into description
		$this->document->setGenerator($this->config->boardTitle);

		$this->setTitle(Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DEFAULT'));

		if ($mode == 'topics')
		{
			$this->topics = $this->topicsModel->getTopics();
			$this->displayTopicRows();
		}
		elseif ($mode == 'posts')
		{
			$this->displayPosts();
		}
		else
		{
			$this->topics = $this->topicsModel->getTopics();
			$this->displayRecentReplies();
		}
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 *
	 * @since   Kunena 6.0
	 */
	protected function prepareDocument()
	{
		$title = Text::_('COM_KUNENA_THREADS_IN_FORUM') . ': ' . $this->category->name;
		$this->setTitle($title);

		$metaDesc = $this->document->getDescription() . '. ' . KunenaParser::parseText($this->category->name . ' - ' . $this->config->boardTitle);
		$this->setDescription($metaDesc);

		// Create image for feed
		$image                 = new FeedImage;
		$image->title          = $this->document->getTitle();
		$image->url            = KunenaFactory::getTemplate()->getImagePath('icons/rss.png');
		$image->description    = $this->document->getDescription();
		$this->document->image = $image;
	}

	/**
	 * Prepare to display the recent replies
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayRecentReplies()
	{
		foreach ($this->topics as $topic)
		{
			$id   = $topic->last_post_id;
			$page = 'last';

			if (!$this->me->userid && $this->config->teaser && $id != $topic->first_post_id)
			{
				$description = Text::_('COM_KUNENA_TEASER_TEXT');
			}
			else
			{
				$description = $topic->last_post_message;
			}

			$date     = new Date($topic->last_post_time);
			$userid   = $topic->last_post_userid;
			$username = KunenaFactory::getUser($userid)->getName($topic->last_post_guest_name);

			$title    = $topic->subject;
			$category = $topic->getCategory();
			$url      = $topic->getUrl($category, true, $page);

			$this->createItem($title, $url, $description, $category->name, $date, $userid, $username);
		}
	}

	/**
	 * Prepare to display the topic rows
	 * 
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayTopicRows()
	{
		foreach ($this->topics as $topic)
		{
			$page        = 'first';
			$description = $topic->first_post_message;
			$date        = new Date($topic->first_post_time);
			$userid      = $topic->first_post_userid;
			$username    = KunenaFactory::getUser($userid)->getName($topic->first_post_guest_name);

			$title    = $topic->subject;
			$category = $topic->getCategory();
			$url      = $topic->getUrl($category, true, $page);

			$this->createItem($title, $url, $description, $category->name, $date, $userid, $username);
		}
	}

	/**
	 * Prepare to display the posts
	 * 
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayPosts($tpl = null)
	{
		$this->layout   = 'posts';
		$this->messages = $this->topicsModel->getMessages();

		// TODO: if start != 0, add information from it into description
		$this->document->setGenerator($this->config->boardTitle);

		$this->setTitle(Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DEFAULT'));

		$this->displayPostRows();
	}

	/**
	 * Prepare to display the posts rows
	 * 
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayPostRows()
	{
		foreach ($this->messages as $message)
		{
			if (!isset($this->topics[$message->thread]))
			{
				// TODO: INTERNAL ERROR
				return;
			}

			$topic    = $this->topics[$message->thread];
			$title    = $message->subject;
			$category = $topic->getCategory();
			$url      = $message->getUrl($category);

			if (!$this->me->userid && $this->config->teaser && $message->id != $topic->first_post_id)
			{
				$description = Text::_('COM_KUNENA_TEASER_TEXT');
			}
			else
			{
				$description = $message->message;
			}

			$date     = new Date($message->time);
			$userid   = $message->userid;
			$username = KunenaFactory::getUser($userid)->getName($message->name);

			$this->createItem($title, $url, $description, $category->name, $date, $userid, $username);
		}
	}

	/**
	 * Create the item for the RSS feed
	 * 
	 * @param   string   $title        title
	 * @param   string   $url          url
	 * @param   string   $description  description
	 * @param   string   $category     category
	 * @param   integer  $date         date
	 * @param   int      $userid       userid
	 * @param   string   $username     username
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function createItem($title, $url, $description, $category, $date, $userid, $username)
	{
		if ($this->config->rssAuthorInTitle)
		{
			// We want author in item titles
			$title .= ' - ' . Text::_('COM_KUNENA_BY') . ': ' . $username;
		}

		if ((int) $this->config->rssWordCount === -1)
		{
			$description = '';
		}
		else
		{
			$description = preg_replace('/\[confidential\](.*?)\[\/confidential\]/s', '', $description);
			$description = preg_replace('/\[hide\](.*?)\[\/hide\]/s', '', $description);
			$description = preg_replace('/\[spoiler\](.*?)\[\/spoiler\]/s', '', $description);
			$description = preg_replace('/\[code\](.*?)\[\/code]/s', '', $description);

			if ((bool) $this->config->rssAllowHtml)
			{
				$description = KunenaParser::parseBBCode($description, null, (int) $this->config->rssWordCount);
			}
			else
			{
				$description = KunenaParser::parseText($description, (int) $this->config->rssWordCount);
			}
		}

		// Assign values to feed item
		$item              = new FeedItem;
		$item->title       = $title;
		$item->link        = $url;
		$item->description = $description;
		$item->date        = $date->toSql();
		$item->author      = $username;

		// FIXME: inefficient to load users one by one -- also vulnerable to J! 2.5 user is NULL bug
		if ($this->config->rssAuthorFormat != 'name')
		{
			$item->authorEmail = Factory::getUser($userid)->email;
		}

		$item->category = $category;

		// Finally add item to feed
		$this->document->addItem($item);
	}
}
