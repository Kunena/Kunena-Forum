<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Kunena Topics Controller
 *
 * @since  2.0
 */
class KunenaControllerTopics extends KunenaController
{
	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 * @return void
	 */
	public function none()
	{
		$this->app->enqueueMessage(Text::_('COM_KUNENA_CONTROLLER_NO_TASK'));
		$this->setRedirectBack();
	}

	/**
	 * @return boolean|void
	 * @throws Exception
	 * @throws null
	 * @throws void
	 * @since Kunena
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
		$ids     = array_keys($this->app->input->get('topics', array(), 'post', 'array'));
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
				$query = "DELETE a.* FROM #__kunena_attachments AS a LEFT JOIN #__kunena_messages AS m ON a.mesid=m.id WHERE m.id IS NULL";
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (JDatabaseExceptionExecuting $e)
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
						array('topic_ids' => $ids),
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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function delete()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('topics', array(), 'post', 'array'));
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
						array('topic_ids' => $ids),
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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function restore()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('topics', array(), 'post', 'array'));
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
						array('topic_ids' => $ids),
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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function approve()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('topics', array(), 'post', 'array'));
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
						array('topic_ids' => $ids),
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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function move()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topics_ids = array_keys($this->app->input->get('topics', array(), 'post', 'array'));
		$topics_ids = ArrayHelper::toInteger($topics_ids);

		$topics = KunenaForumTopicHelper::getTopics($topics_ids);

		$messages_ids = array_keys($this->app->input->get('posts', array(), 'post', 'array'));
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
						array(
							'move'   => array('id' => $topic->id, 'mode' => 'topic'),
							'target' => array('category_id' => $target->id),
						),
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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function unfavorite()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('topics', array(), 'post', 'array'));
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
						array('topic_ids' => $ids),
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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
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

		$ids = array_keys($this->app->input->get('topics', array(), 'post', 'array'));
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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function approve_posts()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('posts', array(), 'post', 'array'));
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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function delete_posts()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('posts', array(), 'post', 'array'));
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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function restore_posts()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('posts', array(), 'post', 'array'));
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
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function permdel_posts()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys($this->app->input->get('posts', array(), 'post', 'array'));
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
