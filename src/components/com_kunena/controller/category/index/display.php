<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Category
 *
 * @copyright       Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerCategoryIndexDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Category/Index';

	/**
	 * @var KunenaUser
	 * @since Kunena
	 */
	public $me;

	/**
	 * @var array
	 * @since Kunena
	 */
	public $sections = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	public $categories = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	public $pending = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	public $more = array();

	/**
	 * Prepare category index display.
	 *
	 * @return void
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$this->me = KunenaUserHelper::getMyself();

		// Get sections to display.
		$catid       = $this->input->getInt('catid', 0);
		$view        = $this->input->getInt('view');
		$Itemid      = $this->input->getInt('Itemid');
		$defaultmenu = $this->input->getInt('defaultmenu');
		$layout      = $this->input->getInt('layout');

		if (!$Itemid)
		{
			$controller = JControllerLegacy::getInstance("kunena");

			if (KunenaConfig::getInstance()->index_id)
			{
				$itemidfix = KunenaConfig::getInstance()->index_id;
			}
			else
			{
				$menu = $this->app->getMenu();

				if ($view == 'home')
				{
					$getid = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=home&defaultmenu={$defaultmenu}"));
				}
				else
				{
					$getid = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=category&layout=list"));
				}

				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			if ($view == 'home')
			{
				if ($defaultmenu)
				{
					$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=home&defaultmenu={$defaultmenu}&Itemid={$itemidfix}", false));
				}
				else
				{
					$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=category&layout=list&Itemid={$itemidfix}", false));
				}
			}
			else
			{
				$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=category&layout=list&Itemid={$itemidfix}", false));
			}

			$controller->redirect();
		}

		$allowed = md5(serialize(KunenaAccess::getInstance()->getAllowedCategories()));

		/*
		$cache   = \Joomla\CMS\Factory::getCache('com_kunena', 'output');

		if ($cache->start("{$this->ktemplate->name}.common.jump.{$allowed}", 'com_kunena.template'))
		{
		return;
		}*/

		$options            = array();
		$options []         = JHtml::_('select.option', '0', JText::_('COM_KUNENA_FORUM_TOP'));
		$cat_params         = array('sections' => 1, 'catid' => 0);
		$this->categorylist = JHtml::_('kunenaforum.categorylist', 'catid', 0, $options, $cat_params, 'class="inputbox fbs" size="1" onchange = "this.form.submit()"', 'value', 'text');

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
			$this->more[$category->id]       = 0;

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
		$categories     = KunenaForumCategoryHelper::getChildren($sectionIds);

		if (empty($categories))
		{
			return;
		}

		$categoryIds = array();
		$topicIds    = array();
		$userIds     = array();
		$postIds     = array();

		foreach ($categories as $key => $category)
		{
			$this->more[$category->id] = 0;

			// Display only categories which are supposed to show up.
			if ($catid || $category->params->get('display.index.parent', 3) > 1)
			{
				if ($catid
					|| ($category->getParent()->params->get('display.index.children', 3) > 2 && $category->params->get('display.index.children', 3) > 2)
				)
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

			if (!empty($rssURL))
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
			$postIds[$topic->id]               = $topic->last_post_id;
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
			$access   = KunenaAccess::getInstance();
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
				$db      = \Joomla\CMS\Factory::getDbo();
				$db->setQuery(
					"SELECT catid, COUNT(*) AS count
					FROM #__kunena_messages
					WHERE catid IN ({$catlist}) AND hold=1
					GROUP BY catid"
				);

				try
				{
					$pending = $db->loadAssocList();
				}
				catch (JDatabaseExceptionExecuting $e)
				{
					KunenaError::displayDatabaseError($e);
				}

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

		$doc = \Joomla\CMS\Factory::getDocument();

		foreach ($doc->_links as $key => $value)
		{
			if (is_array($value))
			{
				if (array_key_exists('relation', $value))
				{
					if ($value['relation'] == 'canonical')
					{
						$canonicalUrl = KunenaRoute::_();
						$canonicalUrl = str_replace('?limitstart=0', '', $canonicalUrl);
						$doc->_links[$canonicalUrl] = $value;
						unset($doc->_links[$key]);
						break;
					}
				}
			}
		}

		KunenaHtmlParser::prepareContent($content, 'index_top');
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$app       = \Joomla\CMS\Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();
		$doc       = \Joomla\CMS\Factory::getDocument();

		$config = \Joomla\CMS\Factory::getConfig();
		$robots = $config->get('robots');

		if (JFile::exists(JPATH_SITE . '/' . KunenaConfig::getInstance()->emailheader))
		{
			$image = \Joomla\CMS\Uri\Uri::base() . KunenaConfig::getInstance()->emailheader;
			$doc->setMetaData('og:image', $image, 'property');
		}

		if ($robots == '')
		{
			$doc->setMetaData('robots', 'index, follow');
		}
		elseif ($robots == 'noindex, follow')
		{
			$doc->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$doc->setMetaData('robots', 'index, nofollow');
		}
		else
		{
			$doc->setMetaData('robots', 'nofollow, noindex');
		}

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

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
				$keywords = JText::_('COM_KUNENA_VIEW_CATEGORIES_DEFAULT');
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = JText::_('COM_KUNENA_VIEW_CATEGORIES_DEFAULT') . ' - ' . $this->config->board_title;
				$this->setDescription($description);
			}

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$doc->setMetaData('robots', $robots);
			}
		}
	}
}
