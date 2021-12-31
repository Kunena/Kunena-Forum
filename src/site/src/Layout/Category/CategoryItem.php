<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Category.Item
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Category;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * KunenaLayoutCategoryItem
 *
 * @since   Kunena 4.0
 */
class CategoryItem extends KunenaLayout
{
	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $total;

	/**
	 * @var     object
	 * @since   Kunena 6.0
	 */
	public $state;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $subcategories;

	/**
	 * @var     void
	 * @since   Kunena 6.0
	 */
	public $sections;

	/**
	 * @var     KunenaCategory
	 * @since   Kunena 6.0
	 */
	public $category;

	/**
	 * @var     KunenaTemplate|void
	 * @since   Kunena 6.0
	 */
	public $ktemplate;

	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * Method to display categories Index sublayout
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function displayCategories()
	{
		if ($this->sections)
		{
			$this->subcategories = true;
			echo $this->subLayout('Category/Index')->setProperties($this->getProperties())->setLayout('subcategories');
		}
	}

	/**
	 * Method to display category action sublayout
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function displayCategoryActions()
	{
		if (!$this->category->isSection())
		{
			echo $this->subLayout('Category/Item/Actions')->setProperties($this->getProperties());
		}
	}

	/**
	 * Method to return array of actions sublayout
	 *
	 * @return  array|boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getCategoryActions()
	{
		$category = $this->category;
		$token    = '&' . Session::getFormToken() . '=1';
		$actions  = [];

		// Is user allowed to post new topic?
		$url             = $category->getNewTopicUrl();
		$this->ktemplate = KunenaFactory::getTemplate();
		$config          = KunenaConfig::getInstance();

		if ($config->readOnly)
		{
			return false;
		}

		if ($category->isAuthorised('topic.create'))
		{
			$actions['create'] = $this->subLayout('Widget/Button')
				->setProperties(
					['url'  => $url, 'name' => 'create', 'scope' => 'topic', 'type' => 'communication', 'success' => true,
					 'icon' => KunenaIcons::pencil(), ]
				);
		}

		if ($category->getTopics() > 0)
		{
			// Is user allowed to mark forums as read?
			$url = $category->getMarkReadUrl();

			if ($this->me->exists())
			{
				$actions['markread'] = $this->subLayout('Widget/Button')
					->setProperties(
						['url'  => $url, 'name' => 'markread', 'scope' => 'category', 'type' => 'user',
					                 'icon' => KunenaIcons::bookmark(), ]
					);
			}
		}

		// Is user allowed to subscribe category?
		if ($category->isAuthorised('subscribe'))
		{
			$subscribed = $category->getSubscribed($this->me->userid);

			if (!$subscribed)
			{
				$url                  = "index.php?option=com_kunena&view=category&task=subscribe&catid={$category->id}{$token}";
				$actions['subscribe'] = $this->subLayout('Widget/Button')
					->setProperties(
						['url'  => $url, 'name' => 'subscribe', 'scope' => 'category', 'type' => 'user',
						 'icon' => KunenaIcons::email(), ]
					);
			}
			else
			{
				$url                    = "index.php?option=com_kunena&view=category&task=unsubscribe&catid={$category->id}{$token}";
				$actions['unsubscribe'] = $this->subLayout('Widget/Button')
					->setProperties(
						['url'  => $url, 'name' => 'unsubscribe', 'scope' => 'category', 'type' => 'user',
						 'icon' => KunenaIcons::emailOpen(), ]
					);
			}
		}

		return $actions;
	}

	/**
	 * Method to get the last post link
	 *
	 * @param   KunenaCategory  $category   The KunenaCategory object
	 * @param   string          $content    The content of last topic subject
	 * @param   string          $title      The title of the link
	 * @param   string          $class      The class attribute of the link
	 * @param   int             $length     length
	 * @param   bool            $follow     follow
	 * @param   bool            $canonical  canonical
	 *
	 * @return  string
	 *
	 * @throws Exception
	 * @throws null
	 * @see     KunenaLayout::getLastPostLink()
	 *
	 * @since   Kunena 6.0
	 */
	public function getLastPostLink(KunenaCategory $category, $content, $title, $class, $length = 30, $follow = true, $canonical = false)
	{
		$lastTopic = $category->getLastTopic();
		$channels  = $category->getChannels();

		if (!isset($channels[$lastTopic->category_id]))
		{
			$category = $lastTopic->getCategory();
		}

		$uri = $lastTopic->getUri($category, 'last');

		if (!$content)
		{
			if (KunenaConfig::getInstance()->disableRe)
			{
				$content = KunenaParser::parseText($category->getLastTopic()->subject, $length);
			}
			else
			{
				$content = $lastTopic->first_post_id != $lastTopic->last_post_id ? Text::_('COM_KUNENA_RE') . ' ' : '';
				$content .= KunenaParser::parseText($category->getLastTopic()->subject, $length);
			}
		}

		if ($title === null)
		{
			$title = Text::sprintf('COM_KUNENA_TOPIC_LAST_LINK_TITLE', $this->escape($category->getLastTopic()->subject));
		}

		return HTMLHelper::_('link', $uri, $content, $title, $class, 'nofollow');
	}

	/**
	 * Return the links of pagination item
	 *
	 * @param   int  $maxpages  The maximum number of pages
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getPagination($maxpages)
	{
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination->getPagesLinks();
	}
}
