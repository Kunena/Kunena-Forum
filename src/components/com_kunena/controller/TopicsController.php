<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\Database\Exception\ExecutionFailureException;
use KunenaAttachmentFinder;
use KunenaError;
use KunenaFactory;
use KunenaForum;
use KunenaForumCategoryHelper;
use KunenaForumMessageHelper;
use KunenaForumTopicHelper;
use KunenaLog;

/**
 * Kunena Topics Controller
 *
 * @since   Kunena 2.0
 */
class TopicsController extends FormController
{
	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function none()
	{
		$this->app->enqueueMessage(Text::_('COM_KUNENA_CONTROLLER_NO_TASK'));
		$this->setRedirectBack();
	}

	/**
	 * @return  boolean|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  void
	 * @throws  Exception
	 */
	public function permdel()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$message = '';
		$ids     = array_keys($this->app->input->get('topics', [], 'post', 'array'));
		$ids     = ArrayHelper::toInteger($ids);

		$topics = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
			$this->setRedirectBack();
		}
		else
		{
			$messages = KunenaForumMessageHelper::getMessagesByTopics($ids);

			foreach ($topics as $topic)
			{
				if ($topic->isAuthorised('permdelete') && $topic->delete())
				{
					// Activity integration
					$activity = KunenaFactory::getActivityIntegration();
					$activity->onAfterDeleteTopic($topic);
					$message = Text::_('COM_KUNENA_BULKMSG_DELETED');
					KunenaForumCategoryHelper::recount($topic->getCategory()->id);
				}
				else
				{
					$this->app->enqueueMessage($topic->getError(), 'notice');
				}
			}

			// Delete attachments in each message
			$finder = new KunenaAttachmentFinder;
			$finder->where('mesid', 'IN', array_keys($messages));
			$attachments = $finder->find();

			if (!empty($attachments))
			{
				foreach ($attachments as $instance)
				{
					$instance->exists(false);
					unset($instance);
				}

				$db    = Factory::getDBO();

				$query = $db->getQuery(true)->select(array('a.id'))
					->from($db->quoteName('#__kunena_attachments', 'a'))
					->leftJoin($db->quoteName('#__kunena_messages', 'm') . ' ON ' . $db->quoteName('a.mesid') . '=' . $db->quoteName('m.id'))
					->where($db->quoteName('m.id') . ' IS NULL');
				$db->setQuery($query);

				try
				{
					$list = $db->loadObjectList('id');
				}
				catch (ExecutionFailureException $e)
				{
					KunenaError::displayDatabaseError($e);

					return false;
				}

				$ids = implode(',', array_keys($list));

				$query = $db->getQuery(true)->delete($db->quoteName('#__kunena_attachments'))->where('id IN (' . $ids . ')');
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (ExecutionFailureException $e)
				{
					KunenaError::displayDatabaseError($e);

					return false;
				}
			}
		}

		if ($message)
		{
			if ($this->config->log_moderation)
			{
				foreach ($topics as $topic)
				{
					KunenaLog::log(
						KunenaLog::TYPE_MODERATION,
						KunenaLog::LOG_TOPIC_DESTROY,
						['topic_ids' => $ids],
						$topic->getCategory(),
						$topic,
						null
					);
				}
			}

			$this->app->enqueueMessage($message);
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function delete()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
		$ids = ArrayHelper::toInteger($ids);

		$message = '';
		$topics  = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
			$this->setRedirectBack();
		}
		else
		{
			foreach ($topics as $topic)
			{
				if ($topic->isAuthorised('delete') && $topic->publish(KunenaForum::TOPIC_DELETED))
				{
					$message = Text::_('COM_KUNENA_BULKMSG_DELETED');
				}
				else
				{
					$this->app->enqueueMessage($topic->getError(), 'notice');
				}
			}
		}

		if ($message)
		{
			if ($this->config->log_moderation)
			{
				foreach ($topics as $topic)
				{
					KunenaLog::log(
						KunenaLog::TYPE_MODERATION,
						KunenaLog::LOG_TOPIC_DELETE,
						['topic_ids' => $ids],
						$topic->getCategory(),
						$topic,
						null
					);
				}
			}

			$this->app->enqueueMessage($message);
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function restore()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
		$ids = ArrayHelper::toInteger($ids);

		$message = '';
		$topics  = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
			$this->setRedirectBack();
		}
		else
		{
			foreach ($topics as $topic)
			{
				if ($topic->isAuthorised('undelete') && $topic->publish(KunenaForum::PUBLISHED))
				{
					$message = Text::_('COM_KUNENA_POST_SUCCESS_UNDELETE');
				}
				else
				{
					$this->app->enqueueMessage($topic->getError(), 'notice');
				}
			}
		}

		if ($message)
		{
			if ($this->config->log_moderation)
			{
				foreach ($topics as $topic)
				{
					KunenaLog::log(
						KunenaLog::TYPE_MODERATION,
						KunenaLog::LOG_TOPIC_UNDELETE,
						['topic_ids' => $ids],
						$topic->getCategory(),
						$topic,
						null
					);
				}
			}

			$this->app->enqueueMessage($message);
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function approve()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
		$ids = ArrayHelper::toInteger($ids);

		$message = '';
		$topics  = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
			$this->setRedirectBack();
		}
		else
		{
			foreach ($topics as $topic)
			{
				if ($topic->isAuthorised('approve') && $topic->publish(KunenaForum::PUBLISHED))
				{
					$message = Text::_('COM_KUNENA_MODERATE_APPROVE_SUCCESS');
					$topic->sendNotification();
				}
				else
				{
					$this->app->enqueueMessage($topic->getError(), 'notice');
				}
			}
		}

		if ($message)
		{
			if ($this->config->log_moderation)
			{
				foreach ($topics as $topic)
				{
					KunenaLog::log(
						KunenaLog::TYPE_MODERATION,
						KunenaLog::LOG_TOPIC_APPROVE,
						['topic_ids' => $ids],
						$topic->getCategory(),
						$topic,
						null
					);
				}
			}

			$this->app->enqueueMessage($message);
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function move()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topics_ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
		$topics_ids = ArrayHelper::toInteger($topics_ids);

		$topics = KunenaForumTopicHelper::getTopics($topics_ids);

		$messages_ids = array_keys($this->app->input->get('posts', [], 'post', 'array'));
		$messages_ids = ArrayHelper::toInteger($messages_ids);

		$messages = KunenaForumMessageHelper::getMessages($messages_ids);

		if (!$topics && !$messages)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_MESSAGES_OR_TOPICS_SELECTED'), 'notice');
			$this->setRedirectBack();
		}
		else
		{
			$target = KunenaForumCategoryHelper::get($this->app->input->getInt('target', 0));

			if (empty($target->id))
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_ACTION_NO_CATEGORY_SELECTED'), 'notice');
				$this->setRedirectBack();
			}

			if (!$target->isAuthorised('read'))
			{
				$this->app->enqueueMessage($target->getError(), 'error');
			}
			else
			{
				if ($topics)
				{
					foreach ($topics as $topic)
					{
						if ($topic->isAuthorised('move') && $topic->move($target))
						{
							$message = Text::_('COM_KUNENA_ACTION_TOPIC_SUCCESS_MOVE');
						}
						else
						{
							$this->app->enqueueMessage($topic->getError(), 'notice');
						}
					}
				}
				else
				{
					foreach ($messages as $message)
					{
						$topic = $message->getTopic();

						if ($message->isAuthorised('move') && $topic->move($target, $message->id))
						{
							$message = Text::_('COM_KUNENA_ACTION_POST_SUCCESS_MOVE');
						}
						else
						{
							$this->app->enqueueMessage($message->getError(), 'notice');
						}
					}
				}
			}
		}

		if (!empty($message))
		{
			if ($this->config->log_moderation)
			{
				foreach ($topics as $topic)
				{
					KunenaLog::log(
						KunenaLog::TYPE_MODERATION,
						KunenaLog::LOG_TOPIC_MODERATE,
						[
							'move'   => ['id' => $topic->id, 'mode' => 'topic'],
							'target' => ['category_id' => $target->id],
						],
						$topic->getCategory(),
						$topic,
						null
					);
				}
			}

			$this->app->enqueueMessage($message);
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function unfavorite()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
		$ids = ArrayHelper::toInteger($ids);

		$topics = KunenaForumTopicHelper::getTopics($ids);

		if (KunenaForumTopicHelper::favorite(array_keys($topics), 0))
		{
			if ($this->config->log_moderation)
			{
				foreach ($topics as $topic)
				{
					KunenaLog::log(
						$this->me->userid == $topic->getAuthor()->userid ? KunenaLog::TYPE_ACTION : KunenaLog::TYPE_MODERATION,
						KunenaLog::LOG_TOPIC_UNFAVORITE,
						['topic_ids' => $ids],
						$topic->getCategory(),
						$topic,
						null
					);
				}
			}

			$this->app->enqueueMessage(Text::_('COM_KUNENA_USER_UNFAVORITE_YES'));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function unsubscribe()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$userid = $this->app->input->getInt('userid');

		$ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
		$ids = ArrayHelper::toInteger($ids);

		$topics = KunenaForumTopicHelper::getTopics($ids);

		if (KunenaForumTopicHelper::subscribe(array_keys($topics), 0, $userid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_USER_UNSUBSCRIBE_YES'));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC'), 'notice');
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function approve_posts()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('posts', [], 'post', 'array'));
		$ids = ArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
		}
		else
		{
			foreach ($messages as $message)
			{
				if ($message->isAuthorised('approve') && $message->publish(KunenaForum::PUBLISHED))
				{
					$message->sendNotification();
					$success++;
				}
				else
				{
					$this->app->enqueueMessage($message->getError(), 'notice');
				}
			}
		}

		if ($success)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_APPROVE_SUCCESS'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function delete_posts()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('topics', [], 'post', 'array'));
		$ids = ArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
		}
		else
		{
			foreach ($messages as $message)
			{
				if ($message->isAuthorised('delete') && $message->publish(KunenaForum::DELETED))
				{
					$success++;
				}
				else
				{
					$this->app->enqueueMessage($message->getError(), 'notice');
				}
			}
		}

		if ($success)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_DELETE'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function restore_posts()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('posts', [], 'post', 'array'));
		$ids = ArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
		}
		else
		{
			foreach ($messages as $message)
			{
				if ($message->isAuthorised('undelete') && $message->publish(KunenaForum::PUBLISHED))
				{
					$success++;
				}
				else
				{
					$this->app->enqueueMessage($message->getError(), 'notice');
				}
			}
		}

		if ($success)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_POST_SUCCESS_UNDELETE'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function permdel_posts()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('posts', [], 'post', 'array'));
		$ids = ArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
		}
		else
		{
			foreach ($messages as $message)
			{
				if ($message->isAuthorised('permdelete') && $message->delete())
				{
					$success++;
				}
				else
				{
					$this->app->enqueueMessage($message->getError(), 'notice');
				}
			}
		}

		if ($success)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_BULKMSG_DELETED'));
		}

		$this->setRedirectBack();
	}
}
