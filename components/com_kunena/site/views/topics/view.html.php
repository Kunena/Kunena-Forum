<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Topics View
 */
class KunenaViewTopics extends KunenaView
{
	function displayDefault($tpl = null)
	{
		$this->layout           = 'default';
		$this->params           = $this->state->get('params');
		$this->Itemid           = $this->get('Itemid');
		$this->topics           = $this->get('Topics');
		$this->total            = $this->get('Total');
		$this->topicActions     = $this->get('TopicActions');
		$this->actionMove       = $this->get('ActionMove');
		$this->message_ordering = $this->me->getMessageOrdering();

		$this->URL = KunenaRoute::_();

		if ($this->embedded)
		{
			$this->moreUri = 'index.php?option=com_kunena&view=topics&layout=default&mode=' . $this->state->get('list.mode');
			$userid        = $this->state->get('user');

			if ($userid)
			{
				$this->moreUri .= "&userid={$userid}";
			}
		}

		$this->rssURL = $this->config->enablerss ? KunenaRoute::_('&format=feed') : '';

		$this->_prepareDocument('default');

		$this->render('Topic/List', $tpl);
	}

	function displayUser($tpl = null)
	{
		$this->layout           = 'user';
		$this->params           = $this->state->get('params');
		$this->topics           = $this->get('Topics');
		$this->total            = $this->get('Total');
		$this->topicActions     = $this->get('TopicActions');
		$this->actionMove       = $this->get('ActionMove');
		$this->message_ordering = $this->me->getMessageOrdering();

		$this->URL = KunenaRoute::_();

		if ($this->embedded)
		{
			$this->moreUri = 'index.php?option=com_kunena&view=topics&layout=user&mode=' . $this->state->get('list.mode');
			$userid        = $this->state->get('user');

			if ($userid)
			{
				$this->moreUri .= "&userid={$userid}";
			}
		}

		$this->_prepareDocument('user');

		$this->render('Topic/List', $tpl);
	}

	function displayPosts($tpl = null)
	{
		$this->layout           = 'posts';
		$this->params           = $this->state->get('params');
		$this->messages         = $this->get('Messages');
		$this->topics           = $this->get('Topics');
		$this->total            = $this->get('Total');
		$this->postActions      = $this->get('PostActions');
		$this->actionMove       = false;
		$this->message_ordering = $this->me->getMessageOrdering();

		$this->URL = KunenaRoute::_();

		if ($this->embedded)
		{
			$this->moreUri = 'index.php?option=com_kunena&view=topics&layout=posts&mode=' . $this->state->get('list.mode');
			$userid        = $this->state->get('user');

			if ($userid)
			{
				$this->moreUri .= "&userid={$userid}";
			}
		}

		$this->_prepareDocument('posts');

		$this->render('Message/List', $tpl);
	}

	function displayRows()
	{
		if ($this->layout == 'posts')
		{
			$this->displayPostRows();
		}
		else
		{
			$this->displayTopicRows();
		}
	}

	function displayTopicRows()
	{
		$lasttopic      = null;
		$this->position = 0;

		// Run events
		$params = new JRegistry();
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'user');
		$params->set('kunena_layout', 'topics');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array('kunena.topics', &$this->topics, &$params, 0));

		foreach ($this->topics as $this->topic)
		{
			$this->position++;
			$this->category = $this->topic->getCategory();
			$usertype       = $this->me->getType($this->category->id, true);

			// TODO: add context (options, template) to caching
			$this->cache = true;
			$cache       = JFactory::getCache('com_kunena', 'output');
			$cachekey    = "{$this->getTemplateMD5()}.{$usertype}.t{$this->topic->id}.p{$this->topic->last_post_id}";
			$cachegroup  = 'com_kunena.topics';

			// FIXME: enable caching after fixing the issues
			$contents = false; //$cache->get($cachekey, $cachegroup);

			if (!$contents)
			{
				$this->categoryLink     = $this->getCategoryLink($this->category->getParent()) . ' / ' . $this->getCategoryLink($this->category);
				$this->firstPostAuthor  = $this->topic->getfirstPostAuthor();
				$this->firstPostTime    = $this->topic->first_post_time;
				$this->firstUserName    = $this->topic->first_post_guest_name;
				$this->lastPostAuthor   = $this->topic->getLastPostAuthor();
				$this->lastPostTime     = $this->topic->last_post_time;
				$this->lastUserName     = $this->topic->last_post_guest_name;
				$this->keywords         = $this->topic->getKeywords(false, ', ');
				$this->module           = $this->getModulePosition('kunena_topic_' . $this->position);
				$this->message_position = $this->topic->posts - ($this->topic->unread ? $this->topic->unread - 1 : 0);
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

	function displayPostRows()
	{
		$lasttopic      = null;
		$this->position = 0;

		// Run events
		$params = new JRegistry();
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'user');
		$params->set('kunena_layout', 'posts');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array('kunena.messages', &$this->messages, &$params, 0));

		foreach ($this->messages as $this->message)
		{
			$this->position++;
			$this->topic    = $this->message->getTopic();
			$this->category = $this->topic->getCategory();
			$usertype       = $this->me->getType($this->category->id, true);

			// TODO: add context (options, template) to caching
			$this->cache = true;
			$cache       = JFactory::getCache('com_kunena', 'output');
			$cachekey    = "{$this->getTemplateMD5()}.{$usertype}.t{$this->topic->id}.p{$this->message->id}";
			$cachegroup  = 'com_kunena.posts';

			// FIXME: enable caching after fixing the issues
			$contents = false; //$cache->get($cachekey, $cachegroup);

			if (!$contents)
			{
				$this->categoryLink     = $this->getCategoryLink($this->category->getParent()) . ' / ' . $this->getCategoryLink($this->category);
				$this->postAuthor       = KunenaFactory::getUser($this->message->userid);
				$this->firstPostAuthor  = $this->topic->getfirstPostAuthor();
				$this->firstPostTime    = $this->topic->first_post_time;
				$this->firstUserName    = $this->topic->first_post_guest_name;
				$this->keywords         = $this->topic->getKeywords(false, ', ');
				$this->module           = $this->getModulePosition('kunena_topic_' . $this->position);
				$this->message_position = $this->topic->posts - ($this->topic->unread ? $this->topic->unread - 1 : 0);
				$this->pages            = ceil($this->topic->getTotal() / $this->config->messages_per_page);

				if ($this->config->avataroncat)
				{
					$this->topic->avatar = KunenaFactory::getUser($this->topic->last_post_userid)->getAvatarImage('klist-avatar', 'list');
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
		if ($this->topic->moved_id > 0)
		{
			$txt .= ' ' . $prefix . 'moved';
		}

		return $txt;
	}

	function displayTimeFilter($id = 'kfilter-select-time', $attrib = 'class="kinputbox" onchange="this.form.submit()" size="1"')
	{
		// make the select list for time selection
		$timesel[] = JHtml::_('select.option', -1, JText::_('COM_KUNENA_SHOW_ALL'));
		$timesel[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_SHOW_LASTVISIT'));
		$timesel[] = JHtml::_('select.option', 4, JText::_('COM_KUNENA_SHOW_4_HOURS'));
		$timesel[] = JHtml::_('select.option', 8, JText::_('COM_KUNENA_SHOW_8_HOURS'));
		$timesel[] = JHtml::_('select.option', 12, JText::_('COM_KUNENA_SHOW_12_HOURS'));
		$timesel[] = JHtml::_('select.option', 24, JText::_('COM_KUNENA_SHOW_24_HOURS'));
		$timesel[] = JHtml::_('select.option', 48, JText::_('COM_KUNENA_SHOW_48_HOURS'));
		$timesel[] = JHtml::_('select.option', 168, JText::_('COM_KUNENA_SHOW_WEEK'));
		$timesel[] = JHtml::_('select.option', 720, JText::_('COM_KUNENA_SHOW_MONTH'));
		$timesel[] = JHtml::_('select.option', 8760, JText::_('COM_KUNENA_SHOW_YEAR'));
		echo JHtml::_('select.genericlist', $timesel, 'sel', $attrib, 'value', 'text', $this->state->get('list.time'), $id);
	}

	function getPagination($maxpages)
	{
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination->getPagesLinks();
	}

	protected function _prepareDocument($type)
	{
		$limit    = $this->state->get('list.limit');
		$page     = intval($this->state->get('list.start') / $limit) + 1;
		$total    = intval(($this->total - 1) / $limit) + 1;
		$pagesTxt = "{$page}/{$total}";

		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item

		if ($menu_item)
		{
			$params = $menu_item->params;

			$params_title = $params->get('page_title');
			$params_keywords = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if ($type == 'default')
			{

				switch ($this->state->get('list.mode'))
				{
					case 'topics' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_TOPICS');
						break;
					case 'sticky' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_STICKY');
						break;
					case 'locked' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_LOCKED');
						break;
					case 'noreplies' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_NOREPLIES');
						break;
					case 'unapproved' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_UNAPPROVED');
						break;
					case 'deleted' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DELETED');
						break;
					case 'replies' :
					default :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DEFAULT');
				}

				if (!empty($params_title))
				{
					$title = $params->get('page_title');
					$this->setTitle($title);
				}
				else
				{
					$this->title = $this->headerText;
					$title = "{$this->title} ({$pagesTxt})";
					$this->setTitle($title);
				}

				if (!empty($params_keywords))
				{
					$keywords = $params->get('menu-meta_keywords');
					$this->setKeywords($keywords);
				}
				else
				{
					$keywords = $this->headerText . $this->escape(" ({$pagesTxt}) - {$this->config->board_title}");
					$this->setKeywords($keywords);
				}

				if (!empty($params_description))
				{
					$description = $params->get('menu-meta_description');
					$this->setDescription($description);
				}
				else
				{
					$description = $this->headerText . $this->escape(" ({$pagesTxt}) - {$this->config->board_title}");
					$this->setDescription($description);
				}
			}
			elseif ($type == 'user')
			{

				switch ($this->state->get('list.mode'))
				{
					case 'posted' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_POSTED');
						break;
					case 'started' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_STARTED');
						break;
					case 'favorites' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_FAVORITES');
						break;
					case 'subscriptions' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_SUBSCRIPTIONS');
						break;
					case 'plugin' :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_PLUGIN_' . strtoupper($this->state->get('list.modetype')));
						break;
					default :
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_DEFAULT');
				}

				if (!empty($params_title))
				{
					$title = $params->get('page_title');
					$this->setTitle($title);
				}
				else
				{
					$this->title = $this->headerText;
					$title = "{$this->title} ({$pagesTxt})";
					$this->setTitle($title);
				}

				if (!empty($params_keywords))
				{
					$keywords = $params->get('menu-meta_keywords');
					$this->setKeywords($keywords);
				}
				else
				{
					$keywords = $this->headerText . $this->escape(" ({$pagesTxt}) - {$this->config->board_title}");
					$this->setKeywords($keywords);
				}

				if (!empty($params_description))
				{
					$description = $params->get('menu-meta_description');
					$this->setDescription($description);
				}
				else
				{
					$description = $this->headerText . $this->escape(" ({$pagesTxt}) - {$this->config->board_title}");
					$this->setDescription($description);
				}
			}
			elseif ($type == 'posts')
			{

				switch ($this->state->get('list.mode'))
				{
					case 'unapproved':
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_UNAPPROVED');
						break;
					case 'deleted':
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DELETED');
						break;
					case 'mythanks':
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_MYTHANKS');
						break;
					case 'thankyou':
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_THANKYOU');
						break;
					case 'recent':
					default:
						$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DEFAULT');
				}

				if (!empty($params_title))
				{
					$title = $params->get('page_title');
					$this->setTitle($title);
				}
				else
				{
					$this->title = $this->headerText;
					$title = "{$this->title} ({$pagesTxt})";
					$this->setTitle($title);
				}

				if (!empty($params_keywords))
				{
					$keywords = $params->get('menu-meta_keywords');
					$this->setKeywords($keywords);
				}
				else
				{
					$keywords = $this->headerText . $this->escape(" ({$pagesTxt}) - {$this->config->board_title}");
					$this->setKeywords($keywords);
				}

				if (!empty($params_description))
				{
					$description = $params->get('menu-meta_description');
					$this->setDescription($description);
				}
				else
				{
					$description = $this->headerText . $this->escape(" ({$pagesTxt}) - {$this->config->board_title}");
					$this->setDescription($description);
				}
			}
			else
			{
				$description = $this->headerText . $this->escape(" ({$pagesTxt}) - {$this->config->board_title}");
				$this->setDescription($description);
			}
		}
	}
}
