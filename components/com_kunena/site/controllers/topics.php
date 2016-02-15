<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Controllers
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Kunena Topics Controller
 *
 * @since        2.0
 */
class KunenaControllerTopics extends KunenaController
{

	function none()
	{
		$this->app->enqueueMessage(JText::_('COM_KUNENA_CONTROLLER_NO_TASK'));
		$this->setRedirectBack();
	}

	function permdel()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$message = '';
		$ids     = array_keys(JRequest::getVar('topics', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

		$topics = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$message = JText::_('COM_KUNENA_NO_TOPICS_SELECTED');
		}
		else
		{
			foreach ($topics as $topic)
			{
				if ($topic->authorise('permdelete') && $topic->delete())
				{
					// Activity integration
					$activity = KunenaFactory::getActivityIntegration();
					$activity->onAfterDeleteTopic($topic);
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
			$this->app->enqueueMessage($message);
		}

		$this->setRedirectBack();
	}

	function delete()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JRequest::getVar('topics', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

		$message = '';
		$topics  = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$message = JText::_('COM_KUNENA_NO_TOPICS_SELECTED');
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
			$this->app->enqueueMessage($message);
		}

		$this->setRedirectBack();
	}

	function restore()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JRequest::getVar('topics', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

		$message = '';
		$topics  = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$message = JText::_('COM_KUNENA_NO_TOPICS_SELECTED');
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
			$this->app->enqueueMessage($message);
		}

		$this->setRedirectBack();
	}

	public function approve()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JRequest::getVar('topics', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

		$message = '';
		$topics  = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$message = JText::_('COM_KUNENA_NO_TOPICS_SELECTED');
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
			$this->app->enqueueMessage($message);
		}

		$this->setRedirectBack();
	}

	function move()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JRequest::getVar('topics', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

		$topics = KunenaForumTopicHelper::getTopics($ids);

		if (!$topics)
		{
			$message = JText::_('COM_KUNENA_NO_TOPICS_SELECTED');
		}
		else
		{
			$target = KunenaForumCategoryHelper::get(JRequest::getInt('target', 0));

			if (!$target->authorise('read'))
			{
				$this->app->enqueueMessage($target->getError(), 'error');
			}
			else
			{
				foreach ($topics as $topic)
				{
					if ($topic->authorise('move') && $topic->move($target))
					{
						$message = JText::_('COM_KUNENA_POST_SUCCESS_MOVE');
					}
					else
					{
						$this->app->enqueueMessage($topic->getError(), 'notice');
					}
				}
			}
		}

		if (!empty($message))
		{
			$this->app->enqueueMessage($message);
		}

		$this->setRedirectBack();
	}

	function unfavorite()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JRequest::getVar('topics', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

		$topics = KunenaForumTopicHelper::getTopics($ids);

		if (KunenaForumTopicHelper::favorite(array_keys($topics), 0))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_USER_UNFAVORITE_YES'));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_POST_NO_UNFAVORITED_TOPIC'));
		}

		$this->setRedirectBack();
	}

	function unsubscribe()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JRequest::getVar('topics', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

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

	public function approve_posts()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JRequest::getVar('posts', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_MESSAGES_SELECTED'));
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

	public function delete_posts()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JRequest::getVar('posts', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_MESSAGES_SELECTED'));
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

	function restore_posts()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JRequest::getVar('posts', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_MESSAGES_SELECTED'));
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

	function permdel_posts()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$ids = array_keys(JRequest::getVar('posts', array(), 'post', 'array')); // Array of integer keys
		JArrayHelper::toInteger($ids);

		$success  = 0;
		$messages = KunenaForumMessageHelper::getMessages($ids);

		if (!$messages)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_NO_MESSAGES_SELECTED'));
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
