<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Site\View\Topic;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Kunena\Forum\Libraries\Attachment\AttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\CategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\Message;
use Kunena\Forum\Libraries\Forum\Message\MessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\TopicHelper;
use Kunena\Forum\Libraries\Html\Parser;
use Kunena\Forum\Libraries\Layout\Layout;
use Kunena\Forum\Libraries\Pagination\Pagination;
use Kunena\Forum\Libraries\Request\Request;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use LogicException;

/**
 * Topic View
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $topicButtons = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $messageButtons = null;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $inline_attachments = [];

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $poll = null;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $mmm = 0;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $k = 0;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $cache = true;

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function displayDefault($tpl = null)
	{
		$this->layout = $this->state->get('layout');

		$errors = [];

		if ($this->layout == 'flat')
		{
			$this->layout = 'default';
		}

		$this->setLayout($this->layout);

		$this->category = $this->get('Category');
		$this->topic    = $this->get('Topic');

		$channels = $this->category->getChannels();

		if ($this->category->id && !$this->category->isAuthorised('read'))
		{
			// User is not allowed to see the category
			$errors[] = $this->category->getError();
		}
		elseif (!$this->topic)
		{
			// Moved topic loop detected (1 -> 2 -> 3 -> 2)
			$errors[] = Text::_('COM_KUNENA_VIEW_TOPIC_ERROR_LOOP');
		}
		elseif (!$this->topic->isAuthorised('read'))
		{
			// User is not allowed to see the topic
			$errors[] = $this->topic->getError();
		}
		elseif ($this->state->get('item.id') != $this->topic->id
			|| ($this->category->id != $this->topic->category_id && !isset($channels[$this->topic->category_id]))
			|| ($this->state->get('layout') != 'threaded' && $this->state->get('item.mesid'))
		)
		{
			// We need to redirect: message has been moved or we have permalink
			$mesid = $this->state->get('item.mesid');

			if (!$mesid)
			{
				$mesid = $this->topic->first_post_id;
			}

			$message = MessageHelper::get($mesid);

			// Redirect to correct location (no redirect in embedded mode).
			if (empty($this->embedded) && $message->exists())
			{
				while (@ob_end_clean())
				{
				}

				$this->app->redirect($message->getUrl(null, false));
			}
		}

		if (!MessageHelper::get($this->topic->first_post_id)->exists())
		{
			$this->displayError([Text::_('COM_KUNENA_NO_ACCESS')], 404);

			return;
		}

		if ($errors)
		{
			$this->displayNoAccess($errors);

			return;
		}

		$this->messages = $this->get('Messages');
		$this->total    = $this->get('Total');

		// If page does not exist, redirect to the last page (no redirect in embedded mode).
		if (empty($this->embedded) && $this->total && $this->total <= $this->state->get('list.start'))
		{
			while (@ob_end_clean())
			{
			}

			$this->app->redirect($this->topic->getUrl(null, false, (int) (($this->total - 1) / $this->state->get('list.limit'))));
		}

		// Run events
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'default');

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.topic', &$this->topic, &$params, 0]);
		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.messages', &$this->messages, &$params, 0]);

		$this->moderators = $this->get('Moderators');
		$this->usertopic  = $this->topic->getUserTopic();

		$this->pagination = $this->getPagination(5);

		// Mark topic read
		$this->topic->hit();

		$this->keywords = $this->topic->getKeywords(false, ', ');

		$this->_prepareDocument('default');

		$this->render('Topic/Item', $tpl);
		$this->topic->markRead();
	}

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayUnread($tpl = null)
	{
		// Redirect unread layout to the page that contains the first unread message
		$category = $this->get('Category');
		$topic    = $this->get('Topic');
		TopicHelper::fetchNewStatus([$topic->id => $topic]);

		$message = Message::getInstance($topic->lastread ? $topic->lastread : $topic->last_post_id);

		while (@ob_end_clean())
		{
		}

		$this->app->redirect($topic->getUrl($category, false, $message));
	}

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function displayFlat($tpl = null)
	{
		$this->state->set('layout', 'default');
		$this->me->setTopicLayout('flat');
		$this->displayDefault($tpl);
	}

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function displayThreaded($tpl = null)
	{
		$this->state->set('layout', 'threaded');
		$this->me->setTopicLayout('threaded');
		$this->displayDefault($tpl);
	}

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function displayIndented($tpl = null)
	{
		$this->state->set('layout', 'indented');
		$this->me->setTopicLayout('indented');
		$this->displayDefault($tpl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayMessageProfile()
	{
		echo $this->getMessageProfileBox();
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getMessageProfileBox()
	{
		static $profiles = [];

		$key = $this->profile->userid . '.' . $this->profile->username;

		if (!isset($profiles [$key]))
		{
			// Run events
			$params = new Registry;

			// Modify profile values by integration
			$params->set('ksource', 'kunena');
			$params->set('kunena_view', 'topic');
			$params->set('kunena_layout', $this->state->get('layout'));

			PluginHelper::importPlugin('kunena');

			Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.user', &$this->profile, &$params, 0]);

			// Karma points and buttons
			$this->userkarma_title = $this->userkarma_minus = $this->userkarma_plus = '';

			if ($this->config->showkarma && $this->profile->userid)
			{
				$this->userkarma_title = Text::_('COM_KUNENA_KARMA') . ": " . $this->profile->karma;

				if ($this->me->userid && $this->me->userid != $this->profile->userid)
				{
					$this->userkarma_minus = ' ' . HTMLHelper::_('link', 'index.php?option=com_kunena&view=user&task=karmadown&userid=' . $this->profile->userid . '&' . Session::getFormToken() . '=1', '<span class="kkarma-minus" alt="Karma-" border="0" title="' . Text::_('COM_KUNENA_KARMA_SMITE') . '"> </span>');
					$this->userkarma_plus  = ' ' . HTMLHelper::_('link', 'index.php?option=com_kunena&view=user&task=karmaup&userid=' . $this->profile->userid . '&' . Session::getFormToken() . '=1', '<span class="kkarma-plus" alt="Karma+" border="0" title="' . Text::_('COM_KUNENA_KARMA_APPLAUD') . '"> </span>');
				}
			}

			if ($this->me->exists() && $this->message->userid == $this->me->userid)
			{
				$usertype = 'me';
			}
			else
			{
				$usertype = $this->profile->getType($this->category->id, true);
			}

			// TODO: add context (options) to caching
			$cache      = Factory::getCache('com_kunena', 'output');
			$cachekey   = "profile.{$this->getTemplateMD5()}.{$this->profile->userid}.{$usertype}";
			$cachegroup = 'com_kunena.messages';

			// FIXME: enable caching after fixing the issues
			$contents = false; // $cache->get($cachekey, $cachegroup);

			if (!$contents)
			{
				$this->userkarma = "{$this->userkarma_title} {$this->userkarma_minus} {$this->userkarma_plus}";

				// Use kunena profile
				if ($this->config->showuserstats)
				{
					$this->userrankimage = $this->profile->getRank($this->topic->category_id, 'image');
					$this->userranktitle = $this->profile->getRank($this->topic->category_id, 'title');
					$this->userposts     = $this->profile->posts;
					$activityIntegration = KunenaFactory::getActivityIntegration();
					$this->userthankyou  = $this->profile->thankyou;
					$this->userpoints    = $activityIntegration->getUserPoints($this->profile->userid);
					$this->usermedals    = $activityIntegration->getUserMedals($this->profile->userid);
				}
				else
				{
					$this->userrankimage = null;
					$this->userranktitle = null;
					$this->userposts     = null;
					$this->userthankyou  = null;
					$this->userpoints    = null;
					$this->usermedals    = null;
				}

				$this->personalText = Parser::parseText($this->profile->personalText);

				$contents = trim(KunenaFactory::getProfile()->showProfile($this, $params));

				if (!$contents)
				{
					$contents = (string) $this->loadTemplateFile('profile');
				}

				$contents .= implode(' ', Factory::getApplication()->triggerEvent('onKunenaDisplay', ['topic.profile', $this, $params]));

				// FIXME: enable caching after fixing the issues (also external profile stuff affects this)
				// if ($this->cache) $cache->store($contents, $cachekey, $cachegroup);
			}

			$profiles [$key] = $contents;
		}

		return $profiles [$key];
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayMessageContents()
	{
		echo $this->loadTemplateFile('message');
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayTopicActions()
	{
		echo $this->getTopicActions();
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getTopicActions()
	{
		$catid = $this->state->get('item.catid');
		$id    = $this->state->get('item.id');

		$task   = "index.php?option=com_kunena&view=topic&task=%s&catid={$catid}&id={$id}&" . Session::getFormToken() . '=1';
		$layout = "index.php?option=com_kunena&view=topic&layout=%s&catid={$catid}&id={$id}";

		$this->topicButtons = new CMSObject;

		// Reply topic
		if ($this->topic->isAuthorised('reply'))
		{
			// This user is allowed to reply to this topic
			$this->topicButtons->set('reply', $this->getButton(sprintf($layout, 'reply'), 'reply', 'topic', 'communication'));
		}

		// Subscribe topic
		if ($this->usertopic->subscribed)
		{
			// This user is allowed to unsubscribe
			$this->topicButtons->set('subscribe', $this->getButton(sprintf($task, 'unsubscribe'), 'unsubscribe', 'topic', 'user'));
		}
		elseif ($this->topic->isAuthorised('subscribe'))
		{
			// This user is allowed to subscribe
			$this->topicButtons->set('subscribe', $this->getButton(sprintf($task, 'subscribe'), 'subscribe', 'topic', 'user'));
		}

		// Favorite topic
		if ($this->usertopic->favorite)
		{
			// This user is allowed to unfavorite
			$this->topicButtons->set('favorite', $this->getButton(sprintf($task, 'unfavorite'), 'unfavorite', 'topic', 'user'));
		}
		elseif ($this->topic->isAuthorised('favorite'))
		{
			// This user is allowed to add a favorite
			$this->topicButtons->set('favorite', $this->getButton(sprintf($task, 'favorite'), 'favorite', 'topic', 'user'));
		}

		// Moderator specific buttons
		if ($this->category->isAuthorised('moderate'))
		{
			$sticky = $this->topic->ordering ? 'unsticky' : 'sticky';
			$lock   = $this->topic->locked ? 'unlock' : 'lock';

			$this->topicButtons->set('sticky', $this->getButton(sprintf($task, $sticky), $sticky, 'topic', 'moderation'));
			$this->topicButtons->set('lock', $this->getButton(sprintf($task, $lock), $lock, 'topic', 'moderation'));
			$this->topicButtons->set('moderate', $this->getButton(sprintf($layout, 'moderate'), 'moderate', 'topic', 'moderation'));

			if ($this->topic->hold == 1 || $this->topic->hold == 0)
			{
				$this->topicButtons->set('delete', $this->getButton(sprintf($task, 'delete'), 'delete', 'topic', 'moderation'));
			}
			elseif ($this->topic->hold == 2 || $this->topic->hold == 3)
			{
				$this->topicButtons->set('undelete', $this->getButton(sprintf($task, 'undelete'), 'undelete', 'topic', 'moderation'));
			}
		}

		if ($this->config->enable_threaded_layouts)
		{
			$url = "index.php?option=com_kunena&view=user&task=change&topic_layout=%s&" . Session::getFormToken() . '=1';

			if ($this->layout != 'default')
			{
				$this->topicButtons->set('flat', $this->getButton(sprintf($url, 'flat'), 'flat', 'layout', 'user'));
			}

			if ($this->layout != 'threaded')
			{
				$this->topicButtons->set('threaded', $this->getButton(sprintf($url, 'threaded'), 'threaded', 'layout', 'user'));
			}

			if ($this->layout != 'indented')
			{
				$this->topicButtons->set('indented', $this->getButton(sprintf($url, 'indented'), 'indented', 'layout', 'user'));
			}
		}

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaGetButtons', ['topic.action', $this->topicButtons, $this]);

		return (string) $this->loadTemplateFile('actions');
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayMessageActions()
	{
		echo $this->getMessageActions();
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getMessageActions()
	{
		$catid        = $this->state->get('item.catid');
		$id           = $this->topic->id;
		$mesid        = $this->message->id;
		$targetuserid = $this->me->userid;

		$task   = "index.php?option=com_kunena&view=topic&task=%s&catid={$catid}&id={$id}&mesid={$mesid}&userid={$targetuserid}&" . Session::getFormToken() . '=1';
		$layout = "index.php?option=com_kunena&view=topic&layout=%s&catid={$catid}&id={$id}&mesid={$mesid}";

		$this->messageButtons = new CMSObject;
		$this->message_closed = null;

		// Reply / Quote
		if ($this->message->isAuthorised('reply'))
		{
			$this->quickreply ? $this->messageButtons->set('quickreply', $this->getButton(sprintf($layout, 'reply'), 'quickreply', 'message', 'communication', "kreply{$mesid}")) : null;
			$this->messageButtons->set('reply', $this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication'));
			$this->messageButtons->set('quote', $this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication'));
		}
		elseif (!$this->me->isModerator($this->topic->getCategory()))
		{
			// User is not allowed to write a post
			$this->message_closed = $this->topic->locked ? Text::_('COM_KUNENA_POST_LOCK_SET') : ($this->me->exists() ? Text::_('COM_KUNENA_REPLY_USER_REPLY_DISABLED') : Text::_('COM_KUNENA_VIEW_DISABLED'));
		}

		// Thank you
		if ($this->message->isAuthorised('thankyou') && !array_key_exists($this->me->userid, $this->message->thankyou))
		{
			$this->messageButtons->set('thankyou', $this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user'));
		}

		// Unthank you
		if ($this->message->isAuthorised('unthankyou') && array_key_exists($this->me->userid, $this->message->thankyou))
		{
			$this->messageButtons->set('unthankyou', $this->getButton(sprintf($task, 'unthankyou'), 'unthankyou', 'message', 'moderation'));
		}

		// Report this
		if ($this->config->reportmsg && $this->me->exists())
		{
			$this->messageButtons->set('report', $this->getButton(sprintf($layout, 'report'), 'report', 'message', 'user'));
		}

		// Moderation and own post actions
		$this->message->isAuthorised('edit') ? $this->messageButtons->set('edit', $this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation')) : null;
		$this->message->isAuthorised('move') ? $this->messageButtons->set('moderate', $this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation')) : null;

		if ($this->message->hold == 1)
		{
			$this->message->isAuthorised('approve') ? $this->messageButtons->set('publish', $this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation')) : null;
			$this->message->isAuthorised('delete') ? $this->messageButtons->set('delete', $this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation')) : null;
		}
		elseif ($this->message->hold == 2 || $this->message->hold == 3)
		{
			$this->message->isAuthorised('undelete') ? $this->messageButtons->set('undelete', $this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation')) : null;
			$this->message->isAuthorised('permdelete') ? $this->messageButtons->set('permdelete', $this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'permanent')) : null;
		}
		else
		{
			$this->message->isAuthorised('delete') ? $this->messageButtons->set('delete', $this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation')) : null;
		}

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaGetButtons', ['message.action', $this->messageButtons, $this]);

		return (string) $this->loadTemplateFile("message_actions");
	}

	/**
	 * @param   integer  $id        id
	 * @param   string   $message   message
	 * @param   null     $template  template
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function displayMessage($id, $message, $template = null)
	{
		$layout = $this->getLayout();

		if (!$template)
		{
			$template = $this->state->get('profile.location');
			$this->setLayout('default');
		}

		$this->mmm++;
		$this->message  = $message;
		$this->profile  = $this->message->getAuthor();
		$this->replynum = $message->replynum;
		$usertype       = $this->me->getType($this->category->id, true);

		if ($usertype == 'user' && $this->message->userid == $this->profile->userid)
		{
			$usertype = 'owner';
		}

		// Thank you info and buttons
		$this->thankyou       = [];
		$this->total_thankyou = 0;
		$this->more_thankyou  = 0;

		if (isset($message->thankyou))
		{
			if ($this->config->showthankyou && $this->profile->userid)
			{
				$task = "index.php?option=com_kunena&view=topic&task=%s&catid={$this->category->id}&id={$this->topic->id}&mesid={$this->message->id}&" . Session::getFormToken() . '=1';

				if (count($message->thankyou) > $this->config->thankyou_max)
				{
					$this->more_thankyou = count($message->thankyou) - $this->config->thankyou_max;
				}

				$this->total_thankyou = count($message->thankyou);
				$thankyous            = array_slice($message->thankyou, 0, $this->config->thankyou_max, true);

				if ($this->message->isAuthorised('unthankyou') && $this->me->isModerator($this->message->getCategory()))
				{
					$canUnthankyou = true;
				}
				else
				{
					$canUnthankyou = false;
				}

				$userids_thankyous = [];

				foreach ($thankyous as $userid => $time)
				{
					$userids_thankyous[] = $userid;
				}

				$loaded_users = KunenaUserHelper::loadUsers($userids_thankyous);

				$thankyou_delete = '';

				foreach ($loaded_users as $userid => $user)
				{
					$thankyou_delete  = $canUnthankyou === true ? ' <a title="' . Text::_('COM_KUNENA_BUTTON_THANKYOU_REMOVE_LONG') . '" href="'
						. KunenaRoute::_(sprintf($task, "unthankyou&userid={$userid}")) . '"><img src="' . $this->ktemplate->getImagePath('icons/publish_x.png') . '" title="" alt="" /></a>' : '';
					$this->thankyou[] = $loaded_users[$userid]->getLink() . $thankyou_delete;
				}
			}
		}

		// TODO: add context (options, template) to caching
		$cache      = Factory::getCache('com_kunena', 'output');
		$cachekey   = "message.{$this->getTemplateMD5()}.{$layout}.{$template}.{$usertype}.c{$this->category->id}.m{$this->message->id}.{$this->message->modified_time}";
		$cachegroup = 'com_kunena.messages';

		if ($this->config->reportmsg && $this->me->exists())
		{
			if (!$this->config->user_report && $this->me->userid == $this->message->userid && !$this->me->isModerator())
			{
				$this->reportMessageLink = null;
			}
			else
			{
				$this->reportMessageLink = HTMLHelper::_('link', 'index.php?option=com_kunena&view=topic&layout=report&catid=' . intval($this->category->id) . '&id=' . intval($this->message->thread) . '&mesid=' . intval($this->message->id), Text::_('COM_KUNENA_REPORT'), Text::_('COM_KUNENA_REPORT'));
			}
		}

		// Get number of attachments to display error messages
		$this->attachs = $this->message->getNbAttachments();

		$contents = false; // $cache->get($cachekey, $cachegroup);

		if (!$contents)
		{
			// Show admins the IP address of the user:
			if ($this->category->isAuthorised('admin') || ($this->category->isAuthorised('moderate') && !$this->config->hide_ip))
			{
				if ($this->message->ip)
				{
					if (!empty($this->message->ip))
					{
						$this->ipLink = '<a href="https://www.geoiptool.de/en/?ip=' . $this->message->ip . '" target="_blank" rel="nofollow noopener noreferrer"> IP: ' . $this->message->ip . '</a>';
					}
					else
					{
						$this->ipLink = '&nbsp;';
					}
				}
				else
				{
					$this->ipLink = null;
				}
			}

			$this->signatureHtml = Parser::parseBBCode($this->profile->signature, null, $this->config->maxsig);
			$this->attachments   = $this->message->getAttachments();

			// Link to individual message
			if ($this->config->ordering_system == 'replyid')
			{
				$this->numLink = $this->getSamePageAnkerLink($message->id, '#[K=REPLYNO]');
			}
			else
			{
				$this->numLink = $this->getSamePageAnkerLink($message->id, '#' . $message->id);
			}

			if ($this->message->hold == 0)
			{
				$this->class = 'kmsg';
			}
			elseif ($this->message->hold == 1)
			{
				$this->class = 'kmsg kunapproved';
			}
			else
			{
				if ($this->message->hold == 2 || $this->message->hold == 3)
				{
					$this->class = 'kmsg kdeleted';
				}
			}

			// New post suffix for class
			$this->msgsuffix = '';

			if ($this->message->isNew())
			{
				$this->msgsuffix = '-new';
			}

			$contents = (string) $this->loadTemplateFile($template);

			if ($usertype == 'guest')
			{
				$contents = preg_replace_callback('|\[K=(\w+)(?:\:(\w+))?\]|', [$this, 'fillMessageInfo'], $contents);
			}

			// FIXME: enable caching after fixing the issues
			// if ($this->cache) $cache->store($contents, $cachekey, $cachegroup);
		}
		elseif ($usertype == 'guest')
		{
			echo $contents;
			$this->setLayout($layout);

			return;
		}

		$contents = preg_replace_callback('|\[K=(\w+)(?:\:(\w+))?\]|', [$this, 'fillMessageInfo'], $contents);
		echo $contents;
		$this->setLayout($layout);
	}

	/**
	 * @param   array  $matches  matches
	 *
	 * @return  mixed|string|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function fillMessageInfo($matches)
	{
		switch ($matches[1])
		{
			case 'ROW':
				return $this->mmm && 1 ? 'odd' : 'even';
			case 'DATE':
				$date = new KunenaDate($matches[2]);

				return $date->toSpan('config_post_dateformat', 'config_post_dateformat_hover');
			case 'NEW':
				return $this->message->isNew() ? 'new' : 'old';
			case 'REPLYNO':
				return $this->replynum;
			case 'MESSAGE_PROFILE':
				return $this->getMessageProfileBox();
			case 'MESSAGE_ACTIONS':
				return $this->getMessageActions();
		}
	}

	/**
	 * @param   null  $template  template
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function displayMessages($template = null)
	{
		foreach ($this->messages as $id => $message)
		{
			$this->displayMessage($id, $message, $template);
		}
	}

	/**
	 * @param   integer  $maxpages  max pages
	 *
	 * @return  Pagination
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getPaginationObject($maxpages)
	{
		$pagination = new Pagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		$uri = KunenaRoute::normalize(null, true);

		if ($uri)
		{
			$uri->delVar('mesid');
			$pagination->setUri($uri);
		}

		return $pagination;
	}

	/**
	 * @param   integer  $maxpages  max pages
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getPagination($maxpages)
	{
		return $this->getPaginationObject($maxpages)->getPagesLinks();
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function hasThreadHistory()
	{
		if (!$this->config->showhistory || !$this->topic->exists())
		{
			return false;
		}

		return true;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayThreadHistory()
	{
		if (!$this->hasThreadHistory())
		{
			return;
		}

		$this->history      = MessageHelper::getMessagesByTopic($this->topic, 0, (int) $this->config->historylimit, $ordering = 'DESC');
		$this->historycount = count($this->history);
		$this->replycount   = $this->topic->getReplies();
		AttachmentHelper::getByMessage($this->history);
		$userlist = [];

		foreach ($this->history as $message)
		{
			$userlist[(int) $message->userid] = (int) $message->userid;
		}

		KunenaUserHelper::loadUsers($userlist);

		// Run events
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'history');

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.messages', &$this->history, &$params, 0]);

		echo $this->loadTemplateFile('history');
	}

	/**
	 * @param   integer  $mesid     mesid
	 * @param   integer  $replycnt  reply count
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getNumLink($mesid, $replycnt)
	{
		if ($this->config->ordering_system == 'replyid')
		{
			$this->numLink = $this->getSamePageAnkerLink($mesid, '#' . $replycnt);
		}
		else
		{
			$this->numLink = $this->getSamePageAnkerLink($mesid, '#' . $mesid);
		}

		return $this->numLink;
	}

	// Helper functions

	/**
	 * @param   string  $name  name
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function displayMessageField($name)
	{
		return $this->message->displayField($name);
	}

	/**
	 * @param   string  $name  name
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function displayTopicField($name)
	{
		return $this->topic->displayField($name);
	}

	/**
	 * @param   string  $name  name
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function displayCategoryField($name)
	{
		return $this->category->displayField($name);
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function canSubscribe()
	{
		if (!$this->me->userid || !$this->config->allowsubscriptions || $this->config->topic_subscriptions == 'disabled')
		{
			return false;
		}

		return !$this->topic->getUserTopic()->subscribed;
	}

	/**
	 * @param   integer  $anker  anker
	 * @param   string   $name   name
	 * @param   string   $rel    rel
	 * @param   string   $class  class
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getSamePageAnkerLink($anker, $name, $rel = 'nofollow', $class = '')
	{
		return '<a ' . ($class ? 'class="' . $class . '" ' : '') . 'href="#' . $anker . '"' . ($rel ? ' rel="' . $rel . '"' : '') . '>' . $name . '</a>';
	}

	/**
	 * @param   string  $title  Title name on the browser
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function setTitle($title)
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		if (!$this->state->get('embedded'))
		{
			// Check for empty title and add site name if param is set
			$title = strip_tags($title);

			if ($this->app->get('sitename_pagetitles', 0) == 1)
			{
				$title = Text::sprintf('JPAGETITLE', $this->app->get('sitename'), $this->config->board_title . ' - ' . $title);
			}
			elseif ($this->app->get('sitename_pagetitles', 0) == 2)
			{
				$title = Text::sprintf('JPAGETITLE', $title . ' - ' . $this->config->board_title, $this->app->get('sitename'));
			}
			else
			{
				$title = KunenaFactory::getConfig()->board_title . ': ' . $title;
			}

			$this->document->setTitle($title);
		}
	}

	/**
	 * @param   string  $description  description
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function setDescription($description)
	{
		if ($this->inLayout)
		{
			throw new LogicException(sprintf('HMVC template should not call %s::%s()', __CLASS__, __FUNCTION__));
		}

		if (!$this->state->get('embedded'))
		{
			// TODO: allow translations/overrides
			$lang   = Factory::getLanguage();
			$length = StringHelper::strlen($lang->getName());
			$length = 137 - $length;

			if (StringHelper::strlen($description) > $length)
			{
				$description = StringHelper::substr($description, 0, $length) . '...';
			}

			$this->document->setMetadata('description', $description);
		}
	}

	/**
	 * Display layout from current layout.
	 *
	 * By using $this->subLayout() instead of KunenaLayout::factory() you can make your template files both
	 * easier to read and gain some context awareness -- for example possibility to use setLayout().
	 *
	 * @param   string  $path  path
	 *
	 * @return  Layout
	 *
	 * @since   Kunena 4.0
	 */
	public function subLayout($path)
	{
		return self::factory($path)
			->setLayout($this->getLayout())
			->setOptions($this->getOptions());
	}

	/**
	 * Display arbitrary MVC triad from current layout.
	 *
	 * By using $this->subRequest() instead of KunenaRequest::factory() you can make your template files both
	 * easier to read and gain some context awareness.
	 *
	 * @param   string  $path     path
	 * @param   Input   $input    input
	 * @param   array   $options  options
	 *
	 * @return  KunenaControllerDisplay
	 *
	 * @since   Kunena 4.0
	 */
	public function subRequest($path, Input $input = null, $options = null)
	{
		return Request::factory($path . '/Display', $input, $options)
			->setLayout($this->getLayout());
	}

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  boolean|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function DisplayCreate($tpl = null)
	{
		$this->setLayout('edit');

		// Get saved message
		$saved = $this->app->getUserState('com_kunena.postfields');

		// Get topic icons if allowed
		if ($this->config->topicicons)
		{
			$this->topicIcons = $this->ktemplate->getTopicIcons(false, $saved ? $saved['icon_id'] : 0);
		}

		$categories        = CategoryHelper::getCategories();
		$arrayanynomousbox = [];
		$arraypollcatid    = [];

		foreach ($categories as $category)
		{
			if (!$category->isSection() && $category->allow_anonymous)
			{
				$arrayanynomousbox[] = '"' . $category->id . '":' . $category->post_anonymous;
			}

			if (!$category->isSection() && $category->allow_polls)
			{
				$arraypollcatid[] = '"' . $category->id . '":1';
			}
		}

		$arrayanynomousbox = implode(',', $arrayanynomousbox);
		$arraypollcatid    = implode(',', $arraypollcatid);
		$this->document->addScriptDeclaration('var arrayanynomousbox={' . $arrayanynomousbox . '}');
		$this->document->addScriptDeclaration('var pollcategoriesid = {' . $arraypollcatid . '};');

		$cat_params = ['ordering'    => 'ordering',
		               'toplevel'    => 0,
		               'sections'    => 0,
		               'direction'   => 1,
		               'hide_lonely' => 1,
		               'action'      => 'topic.create'];

		$this->catid    = $this->state->get('item.catid');
		$this->category = CategoryHelper::get($this->catid);
		list($this->topic, $this->message) = $this->category->newTopic($saved);

		if (!$this->topic->category_id)
		{
			$msg = Text::sprintf('COM_KUNENA_POST_NEW_TOPIC_NO_PERMISSIONS', $this->topic->getError());
			$this->app->enqueueMessage($msg, 'notice');

			return false;
		}

		$options  = [];
		$selected = $this->topic->category_id;

		if ($this->config->pickup_category)
		{
			$options[] = HTMLHelper::_('select.option', '', Text::_('COM_KUNENA_SELECT_CATEGORY'), 'value', 'text');
			$selected  = '';
		}

		if ($saved)
		{
			$selected = $saved['catid'];
		}

		$this->selectcatlist = HTMLHelper::_('kunenaforum.categorylist', 'catid', $this->catid, $options, $cat_params, 'class="inputbox required"', 'value', 'text', $selected, 'postcatid');

		$this->_prepareDocument('create');

		$this->action = 'post';

		$this->allowedExtensions = AttachmentHelper::getExtensions($this->category);

		if ($arraypollcatid)
		{
			$this->poll = $this->topic->getPoll();
		}

		$this->post_anonymous       = $saved ? $saved['anonymous'] : !empty($this->category->post_anonymous);
		$this->subscriptionschecked = $saved ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->app->setUserState('com_kunena.postfields', null);

		$this->render('Topic/Edit', $tpl);
	}

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  boolean|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function DisplayReply($tpl = null)
	{
		$this->setLayout('edit');

		$saved = $this->app->getUserState('com_kunena.postfields');

		$this->catid = $this->state->get('item.catid');
		$this->mesid = $this->state->get('item.mesid');

		if (!$this->mesid)
		{
			$this->topic = TopicHelper::get($this->state->get('item.id'));
			$parent      = MessageHelper::get($this->topic->first_post_id);
		}
		else
		{
			$parent      = MessageHelper::get($this->mesid);
			$this->topic = $parent->getTopic();
		}

		try
		{
			$parent->isAuthorised('reply');
		}
		catch (Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'notice');

			return false;
		}

		// Run events
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'reply');

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.topic', &$this->topic, &$params, 0]);

		$quote          = (bool) $this->app->input->getBool('quote', false);
		$this->category = $this->topic->getCategory();

		if ($this->config->topicicons && $this->topic->isAuthorised('edit', null))
		{
			$this->topicIcons = $this->ktemplate->getTopicIcons(false, $saved ? $saved['icon_id'] : $this->topic->icon_id);
		}

		list($this->topic, $this->message) = $parent->newReply($saved ? $saved : $quote);
		$this->_prepareDocument('reply');
		$this->action = 'post';

		$this->allowedExtensions = AttachmentHelper::getExtensions($this->category);

		$this->post_anonymous       = $saved ? $saved['anonymous'] : !empty($this->category->post_anonymous);
		$this->subscriptionschecked = $saved ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->app->setUserState('com_kunena.postfields', null);

		$this->render('Topic/Edit', $tpl);
	}

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  boolean|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function displayEdit($tpl = null)
	{
		$this->catid = $this->state->get('item.catid');
		$mesid       = $this->state->get('item.mesid');

		$saved = $this->app->getUserState('com_kunena.postfields');

		$this->message = MessageHelper::get($mesid);

		try
		{
			$this->message->isAuthorised('edit');
		}
		catch (Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'notice');

			return false;
		}

		$this->topic    = $this->message->getTopic();
		$this->category = $this->topic->getCategory();

		if ($this->config->topicicons && $this->topic->isAuthorised('edit', null))
		{
			$this->topicIcons = $this->ktemplate->getTopicIcons(false, $saved ? $saved['icon_id'] : $this->topic->icon_id);
		}

		// Run events
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'reply');

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.topic', &$this->topic, &$params, 0]);
		$this->_prepareDocument('edit');

		$this->action = 'edit';

		// Get attachments
		$this->attachments = $this->message->getAttachments();

		// Get poll
		if ($this->message->parent == 0 && ((!$this->topic->poll_id && $this->topic->isAuthorised('poll.create', null)) || ($this->topic->poll_id && $this->topic->isAuthorised('poll.edit', null))))
		{
			$this->poll = $this->topic->getPoll();
		}

		$this->allowedExtensions = AttachmentHelper::getExtensions($this->category);

		if ($saved)
		{
			// Update message contents
			$this->message->edit($saved);
		}

		$this->post_anonymous       = isset($saved['anonymous']) ? $saved['anonymous'] : !empty($this->category->post_anonymous);
		$this->subscriptionschecked = isset($saved['subscribe']) ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->modified_reason      = isset($saved['modified_reason']) ? $saved['modified_reason'] : '';
		$this->app->setUserState('com_kunena.postfields', null);

		$this->render('Topic/Edit', $tpl);
	}

	/**
	 * Redirect back to the referrer page.
	 *
	 * If there's no referrer or it's external, Kunena will return to forum home page.
	 * Also redirects back to tasks are prevented.
	 *
	 * @param   string  $anchor  anchor
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	protected function redirectBack($anchor = '')
	{
		$default  = Uri::base() . ($this->app->isClient('site') ? ltrim(KunenaRoute::_('index.php?option=com_kunena'), '/') : '');
		$referrer = $this->app->input->server->getString('HTTP_REFERER');

		$uri = Uri::getInstance($referrer ? $referrer : $default);

		if (Uri::isInternal($uri->toString()))
		{
			// Parse route.
			$vars = $this->app->getRouter()->parse($uri);
			$uri  = new Uri('index.php');
			$uri->setQuery($vars);

			// Make sure we do not return into a task.
			$uri->delVar('task');
			$uri->delVar(Session::getFormToken());
		}
		else
		{
			$uri = Uri::getInstance($default);
		}

		if ($anchor)
		{
			$uri->setFragment($anchor);
		}

		$this->app->redirect(Route::_($uri->toString()));
	}

	/**
	 * @param   string  $type  type
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function _prepareDocument($type)
	{
		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive(); // Get the active item

		if ($menu_item)
		{
			$params             = $menu_item->getParams(); // Get the params
			$params_title       = $params->get('page_title');
			$params_description = $params->get('menu-meta_description');

			if ($type == 'default')
			{
				$this->headerText = Text::_('COM_KUNENA_MENU_LATEST_DESC');
				$this->title      = Text::_('COM_KUNENA_ALL_DISCUSSIONS');

				$page  = intval($this->state->get('list.start') / $this->state->get('list.limit')) + 1;
				$pages = intval(($this->total - 1) / $this->state->get('list.limit')) + 1;

				if (!empty($params_title))
				{
					$title = $params->get('page_title');
					$this->setTitle($title);
				}
				else
				{
					$title = Text::sprintf($this->topic->subject) . " ({$page}/{$pages})";
					$this->setTitle($title);
				}

				if (!empty($params_description))
				{
					$description = $params->get('menu-meta_description');
					$this->setDescription($description);
				}
				else
				{
					// Create Meta Description form the content of the first message
					// better for search results display but NOT for search ranking!
					$description = Parser::stripBBCode($this->topic->first_post_message, 182);
					$description = preg_replace('/\s+/', ' ', $description); // Remove newlines
					$description = trim($description); // Remove trailing spaces and beginning

					if ($page)
					{
						$description .= " ({$page}/{$pages})";  // Avoid the "duplicate meta description" error in google webmaster tools
					}

					$this->setDescription($description);
				}
			}
			elseif ($type == 'create')
			{
				if (!empty($params_title))
				{
					$title = $params->get('page_title');
					$this->setTitle($title);
				}
				else
				{
					$this->title = Text::_('COM_KUNENA_POST_NEW_TOPIC');
					$this->setTitle($this->title);
				}

				if (!empty($params_description))
				{
					$description = $params->get('menu-meta_description');
					$this->setDescription($description);
				}
				else
				{
					$this->setDescription(Text::_('COM_KUNENA_POST_NEW_TOPIC'));
				}
			}
			elseif ($type == 'reply')
			{
				if (!empty($params_title))
				{
					$title = $params->get('page_title');
					$this->setTitle($title);
				}
				else
				{
					$this->title = Text::_('COM_KUNENA_POST_REPLY_TOPIC') . ' ' . $this->topic->subject;
					$this->setTitle($this->title);
				}

				if (!empty($params_description))
				{
					$description = $params->get('menu-meta_description');
					$this->setDescription($description);
				}
				else
				{
					$this->setDescription(Text::_('COM_KUNENA_POST_REPLY_TOPIC'));
				}
			}
			elseif ($type == 'edit')
			{
				if (!empty($params_title))
				{
					$title = $params->get('page_title');
					$this->setTitle($title);
				}
				else
				{
					$this->title = Text::_('COM_KUNENA_POST_EDIT') . ' ' . $this->topic->subject;
					$this->setTitle($this->title);
				}

				if (!empty($params_description))
				{
					$description = $params->get('menu-meta_description');
					$this->setDescription($description);
				}
				else
				{
					$this->setDescription(Text::_('COM_KUNENA_POST_EDIT'));
				}
			}
		}
	}

}
