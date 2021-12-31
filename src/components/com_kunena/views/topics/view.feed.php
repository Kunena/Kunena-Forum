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
 * Topics View
 * @since Kunena
 */
class KunenaViewTopics extends KunenaView
{
	/**
	 * @param   null $tpl tpl
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayDefault($tpl = null)
	{
		if (!$this->config->enablerss)
		{
			throw new Exception(Text::_('COM_KUNENA_RSS_DISABLED'), 401);
		}

		KunenaHtmlParser::$relative = false;
		$cache                      = Factory::getCache('com_kunena_rss', 'output');

		if (!$this->config->get('cache'))
		{
			$cache->setCaching(0);
		}
		else
		{
			if ($this->config->rss_cache >= 1)
			{
				$cache->setCaching(1);
				$cache->setLifeTime($this->config->rss_cache);
			}
			else
			{
				$cache->setCaching(0);
			}
		}

		$this->layout = 'default';
		$this->params = $this->state->get('params');
		$this->topics = $this->get('Topics');
		$this->total  = $this->get('Total');

		// TODO: if start != 0, add information from it into description
		$this->document->setGenerator($this->config->board_title);

		switch ($this->state->get('list.mode'))
		{
			case 'topics' :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_TOPICS');
				break;
			case 'sticky' :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_STICKY');
				break;
			case 'locked' :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_LOCKED');
				break;
			case 'noreplies' :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_NOREPLIES');
				break;
			case 'unapproved' :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_UNAPPROVED');
				break;
			case 'deleted' :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DELETED');
				break;
			case 'replies' :
			default :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DEFAULT');
		}

		$this->setTitle($title);

		$this->displayTopicRows();
	}

	/**
	 * @param   null $tpl tpl
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayUser($tpl = null)
	{
		if (!$this->config->enablerss)
		{
			throw new Exception(Text::_('COM_KUNENA_RSS_DISABLED'), 401);
		}

		$this->layout = 'user';
		$this->topics = $this->get('Topics');
		$this->total  = $this->get('Total');

		// TODO: if start != 0, add information from it into description
		$title = Text::_('COM_KUNENA_ALL_DISCUSSIONS');
		$this->document->setGenerator($this->config->board_title);

		switch ($this->state->get('list.mode'))
		{
			case 'posted' :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_POSTED');
				break;
			case 'started' :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_STARTED');
				break;
			case 'favorites' :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_FAVORITES');
				break;
			case 'subscriptions' :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_SUBSCRIPTIONS');
				break;
			default :
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_DEFAULT');
		}

		$this->setTitle($title);

		$this->displayTopicRows();
	}

	/**
	 * @param   null $tpl tpl
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayPosts($tpl = null)
	{
		if (!$this->config->enablerss)
		{
			throw new Exception(Text::_('COM_KUNENA_RSS_DISABLED'), 401);
		}

		$this->layout   = 'posts';
		$this->messages = $this->get('Messages');
		$this->topics   = $this->get('Topics');
		$this->total    = $this->get('Total');

		// TODO: if start != 0, add information from it into description
		$title = Text::_('COM_KUNENA_ALL_DISCUSSIONS');
		$this->document->setGenerator($this->config->board_title);

		switch ($this->state->get('list.mode'))
		{
			case 'unapproved':
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_UNAPPROVED');
				break;
			case 'deleted':
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DELETED');
				break;
			case 'mythanks':
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_MYTHANKS');
				break;
			case 'thankyou':
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_THANKYOU');
				break;
			case 'recent':
			default:
				$title = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DEFAULT');
		}

		$this->setTitle($title);

		$this->displayPostRows();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 */
	public function displayTopicRows()
	{
		$firstpost = $this->state->get('list.mode') == 'topics';

		foreach ($this->topics as $topic)
		{
			if ($firstpost)
			{
				$id          = $topic->first_post_id;
				$page        = 'first';
				$description = $topic->first_post_message;
				$date        = new \Joomla\CMS\Date\Date($topic->first_post_time);
				$userid      = $topic->first_post_userid;
				$username    = KunenaFactory::getUser($userid)->getName($topic->first_post_guest_name);
			}
			else
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

				$date     = new \Joomla\CMS\Date\Date($topic->last_post_time);
				$userid   = $topic->last_post_userid;
				$username = KunenaFactory::getUser($userid)->getName($topic->last_post_guest_name);
			}

			$title    = $topic->subject;
			$category = $topic->getCategory();
			$url      = $topic->getUrl($category, true, $page);

			$this->createItem($title, $url, $description, $category->name, $date, $userid, $username);
		}
	}

	/**
	 * @since Kunena
	 * @throws Exception
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

			$date     = new \Joomla\CMS\Date\Date($message->time);
			$userid   = $message->userid;
			$username = KunenaFactory::getUser($userid)->getName($message->name);

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
			$title .= ' - ' . Text::_('COM_KUNENA_BY') . ': ' . $username;
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
