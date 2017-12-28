<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicFormEditDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicFormEditDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Edit';

	/**
	 * Prepare topic edit form.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$this->catid = $this->input->getInt('catid');
		$mesid = $this->input->getInt('mesid');
		$saved = $this->app->getUserState('com_kunena.postfields');

		$this->me = KunenaUserHelper::getMyself();
		$this->template = KunenaFactory::getTemplate();
		$this->message = KunenaForumMessageHelper::get($mesid);
		$this->message->tryAuthorise('edit');

		$this->topic = $this->message->getTopic();
		$this->category = $this->topic->getCategory();

		$this->template->setCategoryIconset($this->topic->getCategory()->iconset);

		if ($this->config->topicicons && $this->topic->isAuthorised('edit'))
		{
			$this->topicIcons = $this->template->getTopicIcons(false, $saved ? $saved['icon_id'] : $this->topic->icon_id);
		}

		if ($this->config->read_only)
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		$doc = JFactory::getDocument();
		$doc->setMetaData('robots', 'nofollow, noindex');

		foreach ($doc->_links as $key => $value)
		{
			if (is_array($value))
			{
				if (array_key_exists('relation', $value))
				{
					if ($value['relation'] == 'canonical')
					{
						$canonicalUrl = $this->topic->getUrl();
						$doc->_links[$canonicalUrl] = $value;
						unset($doc->_links[$key]);
						break;
					}
				}
			}
		}

		// Run onKunenaPrepare event.
		$params = new JRegistry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'reply');

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array ('kunena.topic', &$this->topic, &$params, 0));

		$this->action = 'edit';

		// Get attachments.
		$this->attachments = $this->message->getAttachments();

		// Get poll.
		if ($this->message->parent == 0
			&& $this->topic->isAuthorised(!$this->topic->poll_id ? 'poll.create' : 'poll.edit'))
		{
			$this->poll = $this->topic->getPoll();
		}

		$this->allowedExtensions = KunenaAttachmentHelper::getExtensions($this->category);

		if ($saved)
		{
			// Update message contents.
			$this->message->edit($saved);
		}

		$this->post_anonymous = isset($saved['anonymous']) ? $saved['anonymous'] : !empty($this->category->post_anonymous);
		$this->subscriptionschecked = isset($saved['subscribe']) ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->modified_reason = isset($saved['modified_reason']) ? $saved['modified_reason'] : '';
		$this->app->setUserState('com_kunena.postfields', null);

		$this->canSubscribe = $this->canSubscribe();

		$this->headerText = JText::_('COM_KUNENA_POST_EDIT') . ' ' . $this->topic->subject;
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive();

		$doc = JFactory::getDocument();
		$doc->setMetaData('robots', 'nofollow, noindex');

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
				$this->setTitle($this->headerText);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$this->setKeywords($this->headerText);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription($this->headerText);
			}

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$doc->setMetaData('robots', $robots);
			}
		}
	}

	/**
	 * Can user subscribe to the topic?
	 *
	 * @return boolean
	 */
	protected function canSubscribe()
	{
		if (!$this->me->userid || !$this->config->allowsubscriptions
			|| $this->config->topic_subscriptions == 'disabled')
		{
			return false;
		}

		if($this->message->userid!=$this->me->userid && $this->me->isModerator())
		{
			return false;
		}

		return !$this->topic->getUserTopic()->subscribed;
	}
}
