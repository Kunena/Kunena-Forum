<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Category View
 */
class KunenaViewCategory extends KunenaView
{
	public $pagination = null;

	function displayDefault($tpl = null)
	{
		$this->Itemid   = $this->get('Itemid');
		$this->category = $this->get('Category');

		if (!$this->category->authorise('read'))
		{
			$this->setError($this->category->getError());
		}

		$this->topics       = $this->get('Topics');
		$this->total        = $this->get('Total');
		$this->topicActions = $this->get('TopicActions');
		$this->actionMove   = $this->get('ActionMove');
		$this->moderators   = $this->get('Moderators');

		$this->message_ordering = $this->me->getMessageOrdering();
		$this->categories       = $this->get('Categories');
		$this->pending          = $this->get('UnapprovedCount');
		$this->sections         = isset($this->categories[0]) ? $this->categories[0] : array();

		$errors = $this->getErrors();

		if ($errors)
		{
			$this->displayNoAccess($errors);

			return;
		}

		$this->parentCategory = $this->category->getParent();

		$this->_prepareDocument('default');

		$this->render('Category/Item', $tpl);
	}

	function displayList($tpl = null)
	{
		$this->Itemid   = $this->get('Itemid');
		$this->category = $this->get('Category');

		if ($this->category->id && !$this->category->authorise('read'))
		{
			$this->setError($this->category->getError());
		}

		$this->message_ordering = $this->me->getMessageOrdering();
		$this->categories       = $this->get('Categories');
		$this->pending          = $this->get('UnapprovedCount');
		$this->moderators       = $this->get('Moderators');
		$this->sections         = isset($this->categories[0]) ? $this->categories[0] : array();

		if ($this->category->isSection())
		{
		// TODO: turn this on:
			/*			if ($this->me->isAdmin($this->category)) {
							$url = "index.php?option=com_kunena&view=category&layout=manage&catid={$this->category->id}";
							$this->category_manage = $this->getButton($url, 'manage', 'category', 'moderation');
						}*/
		}

		if ($this->me->exists())
		{
			$this->markAllReadURL = KunenaRoute::_();
		}

		$errors = $this->getErrors();

		if ($errors)
		{
			$this->displayNoAccess($errors);

			return;
		}

		$this->_prepareDocument('list');

		$this->render('Category/Index', $tpl);
	}

	function displayUser($tpl = null)
	{
		$this->Itemid          = $this->get('Itemid');
		$this->categories      = $this->get('LastestCategories');
		$this->categoryActions = $this->get('CategoryActions');

		$errors = $this->getErrors();

		if ($errors)
		{
			$this->displayNoAccess($errors);
		}

		$this->_prepareDocument('user');

		$this->render('Category/User', $tpl);
	}

	function displayManage($tpl)
	{
		$admin = KunenaForumCategoryHelper::getCategories(false, false, 'admin');

		if (empty($admin))
		{
			$this->setError(JText::_('COM_KUNENA_NO_ACCESS'));
			$this->displayNoAccess($this->getErrors());

			return;
		}

		KunenaFactory::loadLanguage('com_kunena', 'admin');

		$this->categories = $this->get('AdminCategories');
		$this->navigation = $this->get('AdminNavigation');
		$header           = JText::_('COM_KUNENA_ADMIN');
		$this->header     = $header;
		$this->setTitle($header);

		$this->render('Category/Manage', $tpl);
	}

	function displayCreate($tpl = null)
	{
		$this->displayEdit($tpl);
	}

	function displayEdit($tpl = null)
	{
		$this->category = $this->get('AdminCategory');

		if ($this->category === false)
		{
			$this->setError(JText::_('COM_KUNENA_NO_ACCESS'));
			$this->displayNoAccess($this->getErrors());

			return;
		}

		KunenaFactory::loadLanguage('com_kunena', 'admin');

		$this->options    = $this->get('AdminOptions');
		$this->moderators = $this->get('AdminModerators');
		$header           = $this->category->exists() ? JText::sprintf('COM_KUNENA_CATEGORY_EDIT', $this->escape($this->category->name)) : JText::_('COM_KUNENA_CATEGORY_NEW');
		$this->header     = $header;
		$this->setTitle($header);

		$this->render('Category/Edit', $tpl);
	}

	function getLastPostLink($category, $content = null, $title = null, $class = null, $length = 20)
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
			$content = KunenaHtmlParser::parseText($category->getLastTopic()->subject, $length);
		}

		if ($title === null)
		{
			$title = JText::sprintf('COM_KUNENA_TOPIC_LAST_LINK_TITLE', $this->escape($category->getLastTopic()->subject));
		}

		return JHtml::_('kunenaforum.link', $uri, $content, $title, $class, 'nofollow');
	}

	public function getCategoryIcon($category, $thumb = false)
	{
		$path = JPATH_ROOT . '/media/kunena/' . $this->config->catimagepath . '/';
		$uri  = JUri::root(true) . '/media/kunena/' . $this->config->catimagepath . '/';

		if (!$thumb)
		{
			if ($category->getNewCount())
			{
				// Check Unread Cat Images
				$file = $this->getCategoryIconFile($category->id . '_on', $path);

				if ($file)
				{
					return '<img src="' . $uri . $file . '" border="0" class="kforum-cat-image" alt=" " />';
				}

				return $this->getIcon($this->ktemplate->categoryIcons[1], JText::_('COM_KUNENA_GEN_FORUM_NEWPOST'));
			}
			else
			{
				// Check Read Cat Images
				$file = $this->getCategoryIconFile($category->id . '_off', $path);

				if ($file)
				{
					return '<img src="' . $uri . $file . '" border="0" class="kforum-cat-image" alt=" " />';
				}

				return $this->getIcon($this->ktemplate->categoryIcons[0], JText::_('COM_KUNENA_GEN_FORUM_NOTNEW'));
			}
		}
		elseif ($this->config->showchildcaticon)
		{
			if ($category->getNewCount())
			{
				// Check Unread Cat Images
				$file = $this->getCategoryIconFile($category->id . '_on_childsmall', $path);

				if ($file)
				{
					return '<img src="' . $uri . $file . '" border="0" class="kforum-cat-image" alt=" " />';
				}

				return $this->getIcon($this->ktemplate->categoryIcons[1] . '-sm', JText::_('COM_KUNENA_GEN_FORUM_NEWPOST'));
			}
			else
			{
				// Check Read Cat Images
				$file = $this->getCategoryIconFile($category->id . '_off_childsmall', $path);

				if ($file)
				{
					return '<img src="' . $uri . $file . '" border="0" class="kforum-cat-image" alt=" " />';
				}

				return $this->getIcon($this->ktemplate->categoryIcons[0] . '-sm', JText::_('COM_KUNENA_GEN_FORUM_NOTNEW'));
			}
		}

		return '';
	}

	private function getCategoryIconFile($filename, $path = '')
	{
		$types = array('.gif', '.png', '.jpg');

		foreach ($types as $ext)
		{
			if (is_file($path . $filename . $ext))
			{
				return $filename . $ext;
			}
		}

		return false;
	}

	public function displaySectionField($field)
	{
		return $this->section->displayField($field);
	}

	public function displayCategoryField($field)
	{
		return $this->category->displayField($field);
	}

	function displayInfoMessage()
	{
		$this->common->header = $this->escape($this->category->name);
		$this->common->body   = '<p>' . JText::sprintf('COM_KUNENA_VIEW_CATEGORIES_INFO_EMPTY', $this->escape($this->category->name)) . '</p>';
		$this->common->html   = true;

		echo $this->common->display('default');
	}

	function displaySection($section)
	{
		$this->parentcategory = $this->category;
		$this->section        = $section;
		$this->sectionURL     = KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$this->section->id}");
		$this->sectionRssURL  = $this->config->enablerss ? KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$this->section->id}&format=feed") : '';

		$this->sectionButtons = array();

		if ($this->me->exists())
		{
			$token                            = '&' . JSession::getFormToken() . '=1';
			$this->sectionButtons['markread'] = $this->getButton("index.php?option=com_kunena&view=category&task=markread&catid={$this->section->id}{$token}", 'markread', 'section', 'user');
		}

		echo $this->loadTemplateFile('section');
		$this->rowno    = 0;
		$this->category = $this->parentcategory;
	}

	function displayCategory($category)
	{
		KUNENA_PROFILER ? $this->profiler->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$this->rowno++;
		$this->category = $category;

		$usertype = $this->me->getType($this->category->id, true);
		$catid    = $category->id;
		$lastPost = $category->getLastTopic();

		// TODO: add context (options, template) to caching
		$this->cache = true;
		$cache       = JFactory::getCache('com_kunena', 'output');
		$cachekey    = "list.item.{$this->getTemplateMD5()}.{$usertype}.{$catid}.{$lastPost->last_post_id}";
		$cachegroup  = 'com_kunena.category';

		// FIXME: enable caching after fixing the issues
		$contents = false; //$cache->get($cachekey, $cachegroup);

		if (!$contents)
		{
			$this->categoryURL    = KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$catid}");
			$this->categoryRssURL = $this->config->enablerss ? KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$catid}&format=feed") : '';
			$this->moderators     = $this->config->listcat_show_moderators ? $category->getModerators(false) : array();
			$this->subcategories  = empty($this->categories [$catid]) ? array() : $this->categories [$catid];
			$this->lastPost       = $lastPost->exists();

			if ($this->lastPost)
			{
				$this->lastUser        = KunenaFactory::getUser((int) $lastPost->last_post_userid);
				$this->lastUserName    = $lastPost->last_post_guest_name ? $lastPost->last_post_guest_name : $this->lastUser->getName();
				$this->lastPostSubject = $lastPost->subject;
				$this->lastPostTime    = $lastPost->last_post_time;
			}

			$contents = $this->loadTemplateFile('category');

			if ($usertype == 'guest')
			{
				$contents = preg_replace_callback('|\[K=(\w+)(?:\:(\w+))?\]|', array($this, 'fillCategoryInfo'), $contents);
			}
			// FIXME: enable caching after fixing the issues
			//if ($this->cache) $cache->store($contents, $cachekey, $cachegroup);
		}
		elseif ($usertype == 'guest')
		{
			echo $contents;

			return;
		}

		$contents = preg_replace_callback('|\[K=(\w+)(?:\:(\w+))?\]|', array($this, 'fillCategoryInfo'), $contents);
		KUNENA_PROFILER ? $this->profiler->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		echo $contents;
	}

	function displayTopicActions($attributes = '', $id = null)
	{
		return JHtml::_('select.genericlist', $this->topicActions, 'task', $attributes, 'value', 'text', null, $id);
	}

	function displayCategoryActions()
	{
		if (!$this->category->isSection())
		{
			echo $this->getCategoryActions();
		}
	}

	/**
	 * @deprecated    K4.0
	 */
	function getCategoryActions()
	{
		$token                 = '&' . JSession::getFormToken() . '=1';
		$this->categoryButtons = array();

		// Is user allowed to post new topic?
		if ($this->category->isAuthorised('topic.create'))
		{
			$url                             = "index.php?option=com_kunena&view=topic&layout=create&catid={$this->category->id}";
			$this->categoryButtons['create'] = $this->getButton($url, 'create', 'topic', 'communication');
		}

		// Is user allowed to mark forums as read?
		if ($this->me->exists() && $this->total)
		{
			$url                               = "index.php?option=com_kunena&view=category&task=markread&catid={$this->category->id}{$token}";
			$this->categoryButtons['markread'] = $this->getButton($url, 'markread', 'category', 'user');
		}

		// Is user allowed to subscribe category?
		if ($this->category->authorise('subscribe', null, true))
		{
			$subscribed = $this->category->getSubscribed($this->me->userid);

			if (!$subscribed)
			{
				$url                                = "index.php?option=com_kunena&view=category&task=subscribe&catid={$this->category->id}{$token}";
				$this->categoryButtons['subscribe'] = $this->getButton($url, 'subscribe', 'category', 'user');
			}
			else
			{
				$url                                  = "index.php?option=com_kunena&view=category&task=unsubscribe&catid={$this->category->id}{$token}";
				$this->categoryButtons['unsubscribe'] = $this->getButton($url, 'unsubscribe', 'category', 'user');
			}
		}

		$contents = $this->loadTemplateFile('actions', array('categoryButtons' => $this->categoryButtons));

		return $contents;
	}

	function fillCategoryInfo($matches)
	{
		switch ($matches[1])
		{
			case 'ROW':
				return $this->rowno & 1 ? 'odd' : 'even';
			case 'CATEGORY_ICON':
				return $this->getCategoryIcon($this->category);
			case 'CATEGORY_NEW_SUFFIX':
				$new = empty($matches[2]) ? $this->category->getNewCount() : KunenaForumCategoryHelper::get($matches[2])->getNewCount();

				return $new ? '-new' : '';
			case 'CATEGORY_NEW_COUNT':
				$new = empty($matches[2]) ? $this->category->getNewCount() : KunenaForumCategoryHelper::get($matches[2])->getNewCount();

				return $new ? '<sup class="knewchar">(' . $new . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ")</sup>" : '';
			case 'DATE':
				$date = new KunenaDate($matches[2]);

				return $date->toSpan('config_post_dateformat', 'config_post_dateformat_hover');
		}
	}

	function displayCategories()
	{
		if ($this->sections)
		{
			$this->subcategories = true;
			echo $this->loadTemplateFile('subcategories');
		}
	}

	function displayRows()
	{
		$lasttopic      = null;
		$this->position = 0;

		// Run events
		$params = new JRegistry();
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'category');
		$params->set('kunena_layout', 'default');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array('kunena.topics', &$this->topics, &$params, 0));

		foreach ($this->topics as $this->topic)
		{
			$this->position++;
			$usertype = $this->me->getType($this->category->id, true);

			// TODO: add context (options, template) to caching
			$this->cache = true;
			$cache       = JFactory::getCache('com_kunena', 'output');
			$cachekey    = "{$this->getTemplateMD5()}.{$usertype}.c{$this->category->id}.t{$this->topic->id}.p{$this->topic->last_post_id}";
			$cachegroup  = 'com_kunena.topics';

			// FIXME: enable caching after fixing the issues
			$contents = false; //$cache->get($cachekey, $cachegroup);

			if (!$contents)
			{
				if ($this->category->id != $this->topic->category_id)
				{
					$this->categoryLink = $this->getCategoryLink($this->topic->getCategory()->getParent()) . ' / ' . $this->getCategoryLink($this->topic->getCategory());
				}
				else
				{
					$this->categoryLink = null;
				}
				$this->firstPostAuthor  = $this->topic->getfirstPostAuthor();
				$this->firstPostTime    = $this->topic->first_post_time;
				$this->firstUserName    = $this->topic->first_post_guest_name;
				$this->lastPostAuthor   = $this->topic->getLastPostAuthor();
				$this->lastPostTime     = $this->topic->last_post_time;
				$this->lastUserName     = $this->topic->last_post_guest_name;
				$this->keywords         = $this->topic->getKeywords(false, ', ');
				$this->module           = $this->getModulePosition('kunena_topic_' . $this->position);
				$this->message_position = $this->topic->getTotal() - ($this->topic->unread ? $this->topic->unread - 1 : 0);
				$this->pages            = ceil($this->topic->getTotal() / $this->config->messages_per_page);

				if ($this->config->avataroncat)
				{
					$this->topic->avatar = KunenaFactory::getUser($this->topic->last_post_userid)->getAvatarImage('klist-avatar', 'list');
				}

				if (is_object($lasttopic) && $lasttopic->ordering != $this->topic->ordering)
				{
					$this->spacing = 1;
				}
				else
				{
					$this->spacing = 0;
				}

				$contents = $this->loadTemplateFile('row');

				if ($usertype == 'guest')
				{
					$contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);
				}
				// FIXME: enable caching after fixing the issues
				//if ($this->cache) $cache->store($contents, $cachekey, $cachegroup);
			}

			if ($usertype != 'guest')
			{
				$contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);
			}

			echo $contents;
			$lasttopic = $this->topic;
		}
	}

	function fillTopicInfo($matches)
	{
		switch ($matches[1])
		{
			case 'ROW':
				return $matches[2] . ($this->position & 1 ? 'odd' : 'even') . ($this->topic->ordering ? " {$matches[2]}sticky" : '');
			case 'TOPIC_ICON':
				return $this->topic->getIcon();
			case 'TOPIC_NEW_COUNT':
				return $this->topic->unread ? $this->getTopicLink($this->topic, 'unread', '<sup class="kindicator-new">(' . $this->topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>') : '';
			case 'DATE':
				$date = new KunenaDate($matches[2]);

				return $date->toSpan('config_post_dateformat', 'config_post_dateformat_hover');
		}
	}

	function getTopicClass($prefix = 'k', $class = 'topic')
	{
		$class = $prefix . $class;
		$txt   = $class . (($this->position & 1) + 1);

		if ($this->topic->ordering)
		{
			$txt .= '-stickymsg';
		}

		if ($this->topic->getCategory()->class_sfx)
		{
			$txt .= ' ' . $class . (($this->position & 1) + 1);

			if ($this->topic->ordering)
			{
				$txt .= '-stickymsg';
			}
			$txt .= $this->escape($this->topic->getCategory()->class_sfx);
		}

		if ($this->topic->hold == 1)
		{
			$txt .= ' ' . $prefix . 'unapproved';
		}
		else
		{
			if ($this->topic->hold)
			{
				$txt .= ' ' . $prefix . 'deleted';
			}
		}

		return $txt;
	}

	function displayManageActions($attributes = '', $id = null)
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('COM_KUNENA_SELECT_BATCH_OPTION'));
		$options[] = JHtml::_('select.option', 'publish', JText::_('COM_KUNENA_PUBLISH'));
		$options[] = JHtml::_('select.option', 'unpublish', JText::_('COM_KUNENA_UNPUBLISH'));
		$options[] = JHtml::_('select.option', 'lock', JText::_('COM_KUNENA_LOCK'));
		$options[] = JHtml::_('select.option', 'unlock', JText::_('COM_KUNENA_UNLOCK'));
		$options[] = JHtml::_('select.option', 'review', JText::_('COM_KUNENA_ENABLE_REVIEW'));
		$options[] = JHtml::_('select.option', 'unreview', JText::_('COM_KUNENA_DISABLE_REVIEW'));
		$options[] = JHtml::_('select.option', 'allow_anomymous', JText::_('COM_KUNENA_ALLOW_ANONYMOUS'));
		$options[] = JHtml::_('select.option', 'deny_anonymous', JText::_('COM_KUNENA_DISALLOW_ANONYMOUS'));
		$options[] = JHtml::_('select.option', 'allow_polls', JText::_('COM_KUNENA_ALLOW_POLLS'));
		$options[] = JHtml::_('select.option', 'deny_polls', JText::_('COM_KUNENA_DISALLOW_POLLS'));
		$options[] = JHtml::_('select.option', 'delete', JText::_('COM_KUNENA_DELETE'));

		return JHtml::_('select.genericlist', $options, 'batch', $attributes, 'value', 'text', null, $id);
	}

	function getPagination($maxpages)
	{
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination->getPagesLinks();
	}

	public function getMarkReadButtonURL($category_id, $numTopics)
	{
		// Is user allowed to mark forums as read?
		if ($this->me->exists() && $numTopics)
		{
			$token = '&' . JSession::getFormToken() . '=1';

			$url = KunenaRoute::_("index.php?option=com_kunena&view=category&task=markread&catid={$category_id}{$token}");

			return $url;
		}
	}

	public function getCategoryRSSURL($catid, $xhtml = true)
	{
		if ($this->config->enablerss)
		{
			$params = '&catid=' . (int) $catid;

			return KunenaRoute::_("index.php?option=com_kunena&view=topics&format=feed&layout=default&mode=topics{$params}", $xhtml);
		}

		return;
	}

	protected function _prepareDocument($type)
	{
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item
		$params = $menu_item->params; // get the params
		$params_title = $params->get('page_title');
		$params_keywords = $params->get('menu-meta_keywords');
		$params_description = $params->get('menu-description');

		if ($type == 'default')
		{
			$page  = intval($this->state->get('list.start') / $this->state->get('list.limit')) + 1;
			$pages = intval(($this->total - 1) / $this->state->get('list.limit')) + 1;

			$this->headerText = $this->title = JText::_('COM_KUNENA_THREADS_IN_FORUM') . ': ' . $this->category->name;

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$title = JText::sprintf('COM_KUNENA_VIEW_CATEGORY_DEFAULT', $this->category->name) . " ({$page}/{$pages})";
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->escape(JText::_('COM_KUNENA_CATEGORIES') . ", {$this->parentCategory->name}, {$this->category->name}, {$this->config->board_title}");
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = $this->escape("{$this->parentCategory->name} - {$this->category->name} ({$page}/{$pages}) - {$this->config->board_title}");
				$this->setDescription($description);
			}
		}
		elseif ($type == 'list')
		{
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
				$description = (JText::_('COM_KUNENA_CATEGORIES') . ' - ' . $this->config->board_title);
				$this->setDescription($description);
			}
		}
		elseif ($type == 'user')
		{

			$this->header = $this->title = JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$title = JText::_('COM_KUNENA_VIEW_CATEGORIES_USER');
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
				$description = (JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS') . ' - ' . $this->config->board_title);
				$this->setDescription($description);
			}
		}
	}
}
