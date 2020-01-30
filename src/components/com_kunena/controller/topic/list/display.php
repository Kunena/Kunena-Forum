<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena\Forum\Site\Controller\Topic\KunenaList;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Access\Access;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Forum\Message\Helper;
use Kunena\Forum\Libraries\Forum\Topic\Topic;
use Kunena\Forum\Libraries\Html\Parser;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Pagination\Pagination;
use Kunena\Forum\Libraries\User\KunenaUser;
use function defined;

/**
 * Class ComponentTopicControllerListDisplay
 *
 * @since   Kunena 4.0
 */
abstract class ComponentTopicControllerListDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/List';

	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * @var     array|Topic[]
	 * @since   Kunena 6.0
	 */
	public $topics;

	/**
	 * @var     Pagination
	 * @since   Kunena 6.0
	 */
	public $pagination;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $headerText;

	/**
	 * Prepare topics by pre-loading needed information.
	 *
	 * @param   array  $userIds  List of additional user Ids to be loaded.
	 * @param   array  $mesIds   List of additional message Ids to be loaded.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareTopics(array $userIds = [], array $mesIds = [])
	{
		// Collect user Ids for avatar prefetch when integrated.
		$lastIds = [];

		foreach ($this->topics as $topic)
		{
			$userIds[(int) $topic->first_post_userid] = (int) $topic->first_post_userid;
			$userIds[(int) $topic->last_post_userid]  = (int) $topic->last_post_userid;
			$lastIds[(int) $topic->last_post_id]      = (int) $topic->last_post_id;
		}

		// Prefetch all users/avatars to avoid user by user queries during template iterations.
		if (!empty($userIds))
		{
			\Kunena\Forum\Libraries\User\Helper::loadUsers($userIds);
		}

		$topicIds = array_keys($this->topics);
		\Kunena\Forum\Libraries\Forum\Topic\Helper::getUserTopics($topicIds);

		$mesIds += \Kunena\Forum\Libraries\Forum\Topic\Helper::fetchNewStatus($this->topics);

		// Fetch also last post positions when user can see unapproved or deleted posts.
		// TODO: Optimize? Take account of configuration option...
		if ($this->me->isAdmin() || Access::getInstance()->getModeratorStatus())
		{
			$mesIds += $lastIds;
		}

		// Load position information for all selected messages.
		if ($mesIds)
		{
			Helper::loadLocation($mesIds);
		}

		$allowed = md5(serialize(Access::getInstance()->getAllowedCategories()));
		$cache   = Factory::getCache('com_kunena', 'output');

		/*
		if ($cache->start("{$this->ktemplate->name}.common.jump.{$allowed}", 'com_kunena.template'))
		{
			return;
		}*/

		$options            = [];
		$options []         = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FORUM_TOP'));
		$cat_params         = ['sections' => 1, 'catid' => 0];
		$this->categorylist = HTMLHelper::_('kunenaforum.categorylist', 'catid', 0, $options, $cat_params, 'class="form-control fbs" size="1" onchange = "this.form.submit()"', 'value', 'text');

		// Run events.
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'list');
		PluginHelper::importPlugin('kunena');
		Parser::prepareContent($content, 'topic_list_default');
		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.topic.list', &$this->topic, &$params, 0]);
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareDocument()
	{
		$page       = $this->pagination->pagesCurrent;
		$total      = $this->pagination->pagesTotal;
		$headerText = $this->headerText . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');

		$menu_item = $this->app->getMenu()->getActive();

		$config = Factory::getConfig();
		$robots = $config->get('robots');

		if ($robots == '')
		{
			$this->setMetaData('robots', 'index, follow');
		}
		elseif ($robots == 'noindex, follow')
		{
			$this->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$this->setMetaData('robots', 'index, nofollow');
		}
		else
		{
			$this->setMetaData('robots', 'nofollow, noindex');
		}

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if (!empty($params_title))
			{
				$title = $params->get('page_title') . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
				$this->setTitle($title);
			}
			else
			{
				$this->title = $this->headerText;
				$this->setTitle($headerText);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title;
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description') . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
				$this->setDescription($description);
			}
			else
			{
				$description = Text::_('COM_KUNENA_THREADS_IN_FORUM') . ': ' . $this->config->board_title . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
				$this->setDescription($description);
			}

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$this->setMetaData('robots', $robots);
			}
		}
	}

	/**
	 * Get Topic Actions.
	 *
	 * @param   array  $topics   topics
	 * @param   array  $actions  actions
	 *
	 * @return  array|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function getTopicActions(array $topics, $actions = ['delete', 'approve', 'undelete', 'move', 'permdelete'])
	{
		if (!$actions)
		{
			return;
		}

		$options                = [];
		$options['none']        = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
		$options['unsubscribe'] = HTMLHelper::_('select.option', 'unsubscribe', Text::_('COM_KUNENA_UNSUBSCRIBE_SELECTED'));
		$options['unfavorite']  = HTMLHelper::_('select.option', 'unfavorite', Text::_('COM_KUNENA_UNFAVORITE_SELECTED'));
		$options['move']        = HTMLHelper::_('select.option', 'move', Text::_('COM_KUNENA_MOVE_SELECTED'));
		$options['approve']     = HTMLHelper::_('select.option', 'approve', Text::_('COM_KUNENA_APPROVE_SELECTED'));
		$options['delete']      = HTMLHelper::_('select.option', 'delete', Text::_('COM_KUNENA_DELETE_SELECTED'));
		$options['permdelete']  = HTMLHelper::_('select.option', 'permdel', Text::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
		$options['undelete']    = HTMLHelper::_('select.option', 'restore', Text::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));

		// Only display actions that are available to user.
		$actions = array_combine($actions, array_fill(0, count($actions), false));
		array_unshift($actions, $options['none']);

		foreach ($topics as $topic)
		{
			foreach ($actions as $action => $value)
			{
				if ($value !== false)
				{
					continue;
				}

				switch ($action)
				{
					case 'unsubscribe':
					case 'unfavorite':
						$actions[$action] = isset($options[$action]) ? $options[$action] : false;
						break;
					default:
						$actions[$action] = isset($options[$action]) && $topic->isAuthorised($action) ? $options[$action] : false;
				}
			}
		}

		$actions = array_filter($actions, function ($item) {
			return !empty($item);
		});

		if (count($actions) == 1)
		{
			return;
		}

		return $actions;
	}

	/**
	 * Get Message Actions.
	 *
	 * @param   array  $messages  messages
	 * @param   array  $actions   actions
	 *
	 * @return  array|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function getMessageActions(array $messages, $actions = ['approve', 'undelete', 'delete', 'move', 'permdelete'])
	{
		if (!$actions)
		{
			return;
		}

		$options               = [];
		$options['none']       = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
		$options['approve']    = HTMLHelper::_('select.option', 'approve_posts', Text::_('COM_KUNENA_APPROVE_SELECTED'));
		$options['delete']     = HTMLHelper::_('select.option', 'delete_posts', Text::_('COM_KUNENA_DELETE_SELECTED'));
		$options['move']       = HTMLHelper::_('select.option', 'move', Text::_('COM_KUNENA_MOVE_SELECTED'));
		$options['permdelete'] = HTMLHelper::_('select.option', 'permdel_posts', Text::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
		$options['undelete']   = HTMLHelper::_('select.option', 'restore_posts', Text::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));

		// Only display actions that are available to user.
		$actions = array_combine($actions, array_fill(0, count($actions), false));
		array_unshift($actions, $options['none']);

		foreach ($messages as $message)
		{
			foreach ($actions as $action => $value)
			{
				if ($value !== false)
				{
					continue;
				}

				$actions[$action] = isset($options[$action]) && $message->isAuthorised($action) ? $options[$action] : false;
			}
		}

		$actions = array_filter($actions, function ($item) {
			return !empty($item);
		});

		if (count($actions) == 1)
		{
			return;
		}

		return $actions;
	}
}
