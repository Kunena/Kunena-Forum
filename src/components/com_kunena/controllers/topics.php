<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Controllers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Topics Controller
 *
 * @since  2.0
 */
class KunenaControllerTopics extends KunenaController
{

	/**
	 *
	 */
	public function none()
	{
		$this->app->enqueueMessage(JText::_('COM_KUNENA_CONTROLLER_NO_TASK'));
		$this->setRedirectBack();
	}

	/**
	 * @throws Exception
	 */
	public function permdel()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$message = '';
		$ids     = array_keys(JFactory::getApplication()->input->get('topics', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$topics = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
			$this->setRedirectBack();
		}
		else
		{
			$messages = KunenaForumMessageHelper::getMessagesByTopics($ids);

			foreach ($topics as $topic)
			{
				if ($topic->authorise('permdelete') && $topic->delete())
				{
					// Activity integration
					$activity = KunenaFactory::getActivityIntegration();
					$activity->onAfterDeleteTopic($topic);
					$message = JText::_('COM_KUNENA_BULKMSG_DELETED');
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

				$db = JFactory::getDBO();
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
	 */
	public function delete()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JFactory::getApplication()->input->get('topics', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$message = '';
		$topics  = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
			$this->setRedirectBack();
		}
		else
		{
			foreach ($topics as $topic)
			{
				if ($topic->authorise('delete') && $topic->publish(KunenaForum::TOPIC_DELETED))
				{
					$message = JText::_('COM_KUNENA_BULKMSG_DELETED');
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
	 */
	public function restore()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JFactory::getApplication()->input->get('topics', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$message = '';
		$topics  = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
			$this->setRedirectBack();
		}
		else
		{
			foreach ($topics as $topic)
			{
				if ($topic->authorise('undelete') && $topic->publish(KunenaForum::PUBLISHED))
				{
					$message = JText::_('COM_KUNENA_POST_SUCCESS_UNDELETE');
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
	 */
	public function approve()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JFactory::getApplication()->input->get('topics', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$message = '';
		$topics  = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_TOPICS_SELECTED'), 'notice');
			$this->setRedirectBack();
		}
		else
		{
			foreach ($topics as $topic)
			{
				if ($topic->authorise('approve') && $topic->publish(KunenaForum::PUBLISHED))
				{
					$message = JText::_('COM_KUNENA_MODERATE_APPROVE_SUCCESS');
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
	 */
	public function move()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$topics_ids = array_keys($this->app->input->get('topics', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($topics_ids);

		$topics = KunenaForumTopicHelper::getTopics($topics_ids);

		$messages_ids = array_keys($this->app->input->get('posts', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($messages_ids);

		$messages = KunenaForumMessageHelper::getMessages($messages_ids);

		if (!$topics && !$messages)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_MESSAGES_OR_TOPICS_SELECTED'), 'notice');
			$this->setRedirectBack();
		}
		else
		{
			$target = KunenaForumCategoryHelper::get($this->app->input->getInt('target', 0));

			if (!$target->authorise('read'))
			{
				$this->app->enqueueMessage($target->getError(), 'error');
			}
			else
			{
				if ($topics)
				{
					foreach ($topics as $topic)
					{
						if ($topic->authorise('move') && $topic->move($target))
						{
							$message = JText::_('COM_KUNENA_ACTION_TOPIC_SUCCESS_MOVE');
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

						if ($message->authorise('move') && $topic->move($target, $message->id))
  						{
  							$message = JText::_('COM_KUNENA_ACTION_POST_SUCCESS_MOVE');
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
							'move' => array('id' => $topic->id, 'mode' => 'topic'),
							'target' => array('category_id' => $target->id)
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
	 */
	public function unfavorite()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JFactory::getApplication()->input->get('topics', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$topics = KunenaForumTopicHelper::getTopics($ids);

		if (KunenaForumTopicHelper::favorite(array_keys($topics), 0))
		{
			if ($this->config->log_moderation)
			{
				foreach($topics as $topic)
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

			$this->app->enqueueMessage(JText::_('COM_KUNENA_USER_UNFAVORITE_YES'));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @throws Exception
	 */
	public function unsubscribe()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JFactory::getApplication()->input->get('topics', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$topics = KunenaForumTopicHelper::getTopics($ids);

		if (KunenaForumTopicHelper::subscribe(array_keys($topics), 0))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_USER_UNSUBSCRIBE_YES'));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_NO_UNSUBSCRIBED_TOPIC'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @throws Exception
	 */
	public function approve_posts()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JFactory::getApplication()->input->get('posts', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
		}
		else
		{
			foreach ($messages as $message)
			{
				if ($message->authorise('approve') && $message->publish(KunenaForum::PUBLISHED))
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
			$this->app->enqueueMessage(JText::_('COM_KUNENA_MODERATE_APPROVE_SUCCESS'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @throws Exception
	 */
	public function delete_posts()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JFactory::getApplication()->input->get('posts', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
		}
		else
		{
			foreach ($messages as $message)
			{
				if ($message->authorise('delete') && $message->publish(KunenaForum::DELETED))
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
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_SUCCESS_DELETE'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @throws Exception
	 */
	public function restore_posts()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JFactory::getApplication()->input->get('posts', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
		}
		else
		{
			foreach ($messages as $message)
			{
				if ($message->authorise('undelete') && $message->publish(KunenaForum::PUBLISHED))
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
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_SUCCESS_UNDELETE'));
		}

		$this->setRedirectBack();
	}

	/**
	 * @throws Exception
	 */
	public function permdel_posts()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JFactory::getApplication()->input->get('posts', array(), 'post', 'array'));
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_MESSAGES_SELECTED'), 'notice');
		}
		else
		{
			foreach ($messages as $message)
			{
				if ($message->authorise('permdelete') && $message->delete())
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
			$this->app->enqueueMessage(JText::_('COM_KUNENA_BULKMSG_DELETED'));
		}

		$this->setRedirectBack();
	}
}
