<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Category
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Category\Index;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since   Kunena 4.0
 */
class CategoryIndexDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $sections = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $categories = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $pending = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $more = [];

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Category/Index';

	/**
	 * Prepare category index display.
	 *
	 * @return  void
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function before()
	{
		parent::before();

		$this->me        = KunenaUserHelper::getMyself();
		$this->ktemplate = KunenaFactory::getTemplate();

		// Get sections to display.
		$catid        = $this->input->getInt('catid', 0);
		$view         = $this->input->getInt('view');
		$Itemid       = $this->input->getInt('Itemid');
		$defaultmenu  = $this->input->getInt('defaultmenu');
		$this->layout = $this->input->getInt('layout');

		if (!$Itemid && $this->config->sefRedirect)
		{
			$controller = BaseController::getInstance("kunena");

			if ($this->config->indexId)
			{
				$itemidfix = $this->config->indexId;
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

		$this->allowed = md5(serialize(KunenaAccess::getInstance()->getAllowedCategories()));

		/*
		$cache   = Factory::getCache('com_kunena', 'output');

		if ($cache->start("{$this->ktemplate->name}.common.jump.{$allowed}", 'com_kunena.template'))
		{
		return;
		}*/

		$options            = [];
		$options []         = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FORUM_TOP'));
		$catParams          = ['sections' => 1, 'catid' => 0];
		$this->categorylist = HTMLHelper::_('kunenaforum.categorylist', 'catid', $catid, $options, $catParams, 'class="form-select inputbox fbs" size="1" onchange = "this.form.submit()"');

		if ($catid)
		{
			$sections = KunenaCategoryHelper::getCategories($catid);
		}
		else
		{
			$sections = KunenaCategoryHelper::getChildren();
		}

		$sectionIds = [];

		$this->more[$catid] = 0;

		foreach ($sections as $key => $category)
		{
			$this->categories[$category->id] = [];
			$this->more[$category->id]       = 0;

			$registry = new Registry;

			if (!empty($registry->params))
			{
				$registry->loadString($category->params);
			}

			$params = $registry->loadString($category->params);

			// Display only categories which are supposed to show up.
			if ($catid || $params->get('display.index.parent', 3) > 0)
			{
				if ($catid || $params->get('display.index.children', 3) > 1)
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
				$this->more[$category->parentid]++;
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
		$categories     = KunenaCategoryHelper::getChildren($sectionIds);

		if (empty($categories))
		{
			return;
		}

		$categoryIds = [];
		$topicIds    = [];
		$userIds     = [];
		$postIds     = [];

		foreach ($categories as $key => $category)
		{
			$this->more[$category->id] = 0;

			$registry = new Registry;

			if (!empty($registry->params))
			{
				$registry->loadString($category->params);
			}

			$params = $registry->loadString($category->params);

			$subregistry = new Registry;

			if (!empty($subregistry->params))
			{
				$subregistry->loadString($category->getParent()->params);
			}

			if ($category->getParent())
			{
				$subparams = $subregistry->loadString($category->getParent()->params);

				// Display only categories which are supposed to show up.
				if ($catid || $params->get('display.index.parent', 3) > 1)
				{
					if ($catid
						|| ($subparams->get('display.index.children', 3) > 2 && $params->get('display.index.children', 3) > 2)
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
					$this->more[$category->parentid]++;
					unset($categories[$key]);
					continue;
				}
			}

			// Get list of topics.
			$last = $category->getLastCategory();

			if ($last->last_topic_id)
			{
				$topicIds[$last->last_topic_id] = $last->last_topic_id;
			}

			$this->categories[$category->parentid][] = $category;

			$rssURL = $category->getRSSUrl();

			if (!empty($rssURL))
			{
				$category->rssURL = $category->getRSSUrl();
			}
		}

		$subcategories = KunenaCategoryHelper::getChildren($categoryIds);

		foreach ($subcategories as $category)
		{
			$registry = new Registry;

			if (!empty($registry->params))
			{
				$registry->loadString($category->params);
			}

			$params = $registry->loadString($category->params);

			// Display only categories which are supposed to show up.
			if ($catid || $params->get('display.index.parent', 3) > 2)
			{
				$this->categories[$category->parentid][] = $category;
			}
			else
			{
				$this->more[$category->parentid]++;
			}
		}

		// Pre-fetch topics (also display unauthorized topics as they are in allowed categories).
		$topics = KunenaTopicHelper::getTopics($topicIds, 'none');

		// Pre-fetch users (and get last post ids for moderators).
		foreach ($topics as $topic)
		{
			$userIds[$topic->last_post_userid] = $topic->last_post_userid;
			$postIds[$topic->id]               = $topic->last_post_id;
		}

		KunenaUserHelper::loadUsers($userIds);
		KunenaMessageHelper::getMessages($postIds);

		// Pre-fetch user related stuff.
		$this->pending = [];

		if ($this->me->exists() && !$this->me->isBanned())
		{
			// Load new topic counts.
			KunenaCategoryHelper::getNewTopics(array_keys($categories + $subcategories));

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
				$db      = Factory::getContainer()->get('DatabaseDriver');
				$query   = $db->getQuery(true);
				$query->select('catid, COUNT(*) AS count')
					->from($db->quoteName('#__kunena_messages'))
					->where('catid IN (' . $catlist . ') AND hold=1')
					->group('catid');
				$db->setQuery($query);

				try
				{
					$pending = $db->loadAssocList();
				}
				catch (ExecutionFailureException $e)
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
					$topicOrdering = $this->me->ordering == 1;
				}
				else
				{
					$topicOrdering = $this->config->defaultSort == 'asc' ? false : true;
				}

				// Fix last post position when user can see unapproved or deleted posts.
				if (!$topicOrdering)
				{
					KunenaMessageHelper::loadLocation($postIds);
				}
			}
		}

		$doc = Factory::getApplication()->getDocument();

		foreach ($doc->_links as $key => $value)
		{
			if (\is_array($value))
			{
				if (\array_key_exists('relation', $value))
				{
					if ($value['relation'] == 'canonical')
					{
						$canonicalUrl               = KunenaRoute::_();
						$canonicalUrl               = str_replace('?limitstart=0', '', $canonicalUrl);
						$doc->_links[$canonicalUrl] = $value;
						unset($doc->_links[$key]);
						break;
					}
				}
			}
		}

		KunenaParser::prepareContent($content, 'index_top');
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	protected function prepareDocument()
	{
		$menu_item = $this->app->getMenu()->getActive();

		$config = Factory::getApplication();
		$robots = $config->get('robots');
		$image  = null;

		if (File::exists(JPATH_SITE . '/' . $this->config->emailHeader))
		{
			$image = Uri::base() . $this->config->emailHeader;
			$this->setMetaData('og:image', $image, 'property');
		}

		if ($robots == 'noindex, follow')
		{
			$this->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$this->setMetaData('robots', 'index, nofollow');
		}
		elseif ($robots == 'noindex, nofollow')
		{
			$this->setMetaData('robots', 'noindex, nofollow');
		}
		else
		{
			$this->setMetaData('robots', 'index, follow');
		}

		if ($menu_item)
		{
			$registry = new Registry;

			if (!empty($registry->params))
			{
				$registry->loadString($menu_item->getParams());
			}

			$params             = $registry->loadString($menu_item->getParams());
			$params_title       = $params->get('page_title');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$title = Text::_('COM_KUNENA_VIEW_CATEGORIES_DEFAULT');
				$this->setTitle($title);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = Text::_('COM_KUNENA_VIEW_CATEGORIES_DEFAULT') . ' - ' . $this->config->boardTitle;
				$this->setDescription($description);
			}

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$this->setMetaData('robots', $robots);
			}

			$this->setMetaData('og:type', 'article', 'property');
			$this->setMetaData('og:description', $description, 'property');
			$this->setMetaData('og:title', $title, 'property');
			$this->setMetaData('twitter:card', 'summary', 'name');
			$this->setMetaData('twitter:title', $title, 'name');
			$this->setMetaData('twitter:image', $image, 'property');
			$this->setMetaData('twitter:description', $description);
		}
	}
}
