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

namespace Kunena\Forum\Site\Controller\Application\Category\Feed;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaException;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Document\Feed\FeedImage;
use Joomla\CMS\Document\Feed\FeedItem;
use Kunena\Forum\Site\Model\CategoryModel;

/**
 * CategoryDisplay
 *
 * @since   Kunena 6.0
 */
class CategoryDisplay extends KunenaControllerDisplay
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
		$catid = $this->input->getInt('catid', 0);
		$model = new CategoryModel;
		$this->document = Factory::getApplication()->getDocument();

		if (!$this->config->enableRss)
		{
			throw new Exception(Text::_('COM_KUNENA_RSS_DISABLED'), 500);
		}

		KunenaParser::$relative = false;

		$this->category = KunenaCategoryHelper::get($catid);

		if (!$this->category->isAuthorised('read'))
		{
			throw new KunenaException($this->category->getError(), 404);
		}

		$this->topics = $model->getTopics();

		foreach ($this->topics as $topic)
		{
			if ($this->config->rssType == 'topic')
			{
				$description = Text::sprintf('COM_KUNENA_RSS_TOPICS_CONTAINS_MESSAGES', $topic->posts) . ' - ' . Text::sprintf('COM_KUNENA_RSS_LAST_AUTHOR', KunenaFactory::getUser($topic->last_post_userid)->getName($topic->last_post_guest_name));
			}
			else
			{
				$description = $topic->last_post_message;
			}

			$date        = new Date($topic->last_post_time);
			$userid      = $topic->last_post_userid;

			$username    = KunenaFactory::getUser($userid)->getName($topic->last_post_guest_name);

			$title    = $topic->subject;
			$category = $topic->getCategory();
			$url      = $topic->getUrl($category, true, 'last');

			$this->createItem($title, $url, $description, $this->category->name, $date, $userid, $username);
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
	 * Create the item for the feed
	 * 
	 * @param   string   $title        title
	 * @param   string   $url          url
	 * @param   string   $description  description
	 * @param   string   $category     category
	 * @param   integer  $date         date
	 * @param   integer  $userid       userid
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
			$title .= ' - ' . Text::_('COM_KUNENA_RSS_BY') . ': ' . $username;
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

		// Finally, add item to feed
		$this->document->addItem($item);
	}
}
