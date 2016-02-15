<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Category
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerCategoryIndexDisplay extends KunenaControllerDisplay
{
	protected $name = 'Category/Index';

	/**
	 * @var KunenaUser
	 */
	public $me;

	public $sections = array();

	public $categories = array();

	public $pending = array();

	public $more = array();

	/**
	 * Prepare category index display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$this->me = KunenaUserHelper::getMyself();

		// Get sections to display.
		$catid = $this->input->getInt('catid', 0);

		if ($catid)
		{
			$sections = KunenaForumCategoryHelper::getCategories($catid);
		}
		else
		{
			$sections = KunenaForumCategoryHelper::getChildren();
		}

		$sectionIds = array();

		$this->more[$catid] = 0;
		foreach ($sections as $key => $category)
		{
			$this->categories[$category->id] = array();
			$this->more[$category->id] = 0;

			// Display only categories which are supposed to show up.
			if ($catid || $category->params->get('display.index.parent', 3) > 0)
			{
				if ($catid || $category->params->get('display.index.children', 3) > 1)
				{
					$sectionIds[] = $category->id;
				}
				else
				{
					$this->more[$category->id]++;
				}
			}
			else
			{
				$this->more[$category->parent_id]++;
				unset($sections[$key]);
				continue;
			}
		}

		// Get categories and subcategories.
		if (empty($sections))
		{
			return;
		}

		$this->sections = $sections;
		$categories = KunenaForumCategoryHelper::getChildren($sectionIds);

		if (empty($categories))
		{
			return;
		}

		$categoryIds = array();
		$topicIds = array();
		$userIds = array();
		$postIds = array();

		foreach ($categories as $key => $category)
		{
			$this->more[$category->id] = 0;

			// Display only categories which are supposed to show up.
			if ($catid || $category->params->get('display.index.parent', 3) > 1)
			{
				if ($catid
					|| ($category->getParent()->params->get('display.index.children', 3) > 2
						&& $category->params->get('display.index.children', 3) > 2))
				{
					$categoryIds[] = $category->id;
				}
				else
				{
					$this->more[$category->id]++;
				}
			}
			else
			{
				$this->more[$category->parent_id]++;
				unset($categories[$key]);
				continue;
			}

			// Get list of topics.
			$last = $category->getLastCategory();

			if ($last->last_topic_id)
			{
				$topicIds[$last->last_topic_id] = $last->last_topic_id;
			}

			$this->categories[$category->parent_id][] = $category;

			$rssURL = $category->getRSSUrl();
			if ( !empty($rssURL) )
			{
				$category->rssURL = $category->getRSSUrl();
			}
		}

		$subcategories = KunenaForumCategoryHelper::getChildren($categoryIds);

		foreach ($subcategories as $category)
		{
			// Display only categories which are supposed to show up.
			if ($catid || $category->params->get('display.index.parent', 3) > 2)
			{
				$this->categories[$category->parent_id][] = $category;
			}
			else
			{
				$this->more[$category->parent_id]++;
			}
		}

		// Pre-fetch topics (also display unauthorized topics as they are in allowed categories).
		$topics = KunenaForumTopicHelper::getTopics($topicIds, 'none');

		// Pre-fetch users (and get last post ids for moderators).
		foreach ($topics as $topic)
		{
			$userIds[$topic->last_post_userid] = $topic->last_post_userid;
			$postIds[$topic->id] = $topic->last_post_id;
		}

		KunenaUserHelper::loadUsers($userIds);
		KunenaForumMessageHelper::getMessages($postIds);

		// Pre-fetch user related stuff.
		$this->pending = array();

		if ($this->me->exists() && !$this->me->isBanned())
		{
			// Load new topic counts.
			KunenaForumCategoryHelper::getNewTopics(array_keys($categories + $subcategories));

			// Get categories which are moderated by current user.
			$access = KunenaAccess::getInstance();
			$moderate = $access->getAdminStatus($this->me) + $access->getModeratorStatus($this->me);

			if (!empty($moderate[0]))
			{
				// Global moderators.
				$moderate = $categories;
			}
			else
			{
				// Category moderators.
				$moderate = array_intersect_key($categories, $moderate);
			}

			if (!empty($moderate))
			{
				// Get pending messages.
				$catlist = implode(',', array_keys($moderate));
				$db = JFactory::getDbo();
				$db->setQuery(
					"SELECT catid, COUNT(*) AS count
					FROM #__kunena_messages
					WHERE catid IN ({$catlist}) AND hold=1
					GROUP BY catid"
				);
				$pending = $db->loadAssocList();
				KunenaError::checkDatabaseError();

				foreach ($pending as $item)
				{
					if ($item['count'])
					{
						$this->pending[$item['catid']] = $item['count'];
					}
				}

				if ($this->me->ordering != 0)
				{
					$topic_ordering = $this->me->ordering == 1 ? true : false;
				}
				else
				{
					$topic_ordering = $this->config->default_sort == 'asc' ? false : true;
				}

				// Fix last post position when user can see unapproved or deleted posts.
				if (!$topic_ordering)
				{
					KunenaForumMessageHelper::loadLocation($postIds);
				}
			}
		}
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive(); // get the active item

		if ($menu_item)
		{
			$params             = $menu_item->params; // get the params
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$title = JText::_('COM_KUNENA_VIEW_CATEGORIES_DEFAULT');
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = JText::_('COM_KUNENA_CATEGORIES');
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = JText::_('COM_KUNENA_CATEGORIES') . ' - ' . $this->config->board_title;
				$this->setDescription($description);
			}
		}
	}
}
