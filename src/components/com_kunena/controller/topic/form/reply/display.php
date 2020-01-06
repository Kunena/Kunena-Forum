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
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerTopicFormReplyDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerTopicFormReplyDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/Edit';

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $captchaHtml = null;

	/**
	 * Prepare topic reply form.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		$id    = $this->input->getInt('id');
		$mesid = $this->input->getInt('mesid');
		$quote = $this->input->getBool('quote', false);

		$saved = $this->app->getUserState('com_kunena.postfields');

		$this->me       = KunenaUserHelper::getMyself();
		$this->template = KunenaFactory::getTemplate();

		if (!$mesid)
		{
			$this->topic = KunenaForumTopicHelper::get($id);
			$parent      = KunenaForumMessageHelper::get($this->topic->first_post_id);
		}
		else
		{
			$parent      = KunenaForumMessageHelper::get($mesid);
			$this->topic = $parent->getTopic();
		}

		if ($this->config->read_only)
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		$doc = Factory::getApplication()->getDocument();

		foreach ($doc->_links as $key => $value)
		{
			if (is_array($value))
			{
				if (array_key_exists('relation', $value))
				{
					if ($value['relation'] == 'canonical')
					{
						$canonicalUrl               = $this->topic->getUrl();
						$doc->_links[$canonicalUrl] = $value;
						unset($doc->_links[$key]);
						break;
					}
				}
			}
		}

		$uri = trim(strtok($this->topic->getUrl(), '?'));
		$doc->addHeadLink($uri, 'canonical');

		$this->category = $this->topic->getCategory();

		if ($parent->isAuthorised('reply') && $this->me->canDoCaptcha())
		{
			$this->captchaDisplay = KunenaTemplate::getInstance()->recaptcha();
			$this->captchaEnabled = true;
		}
		else
		{
			$this->captchaEnabled = false;
		}

		$parent->tryAuthorise('reply');

		$arraypollcatid = [];
		KunenaTemplate::getInstance()->addScriptOptions('com_kunena.pollcategoriesid', json_encode($arraypollcatid));

		// Run event.
		$params = new Joomla\Registry\Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'reply');

		Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.topic', &$this->topic, &$params, 0]);

		$this->headerText = Text::_('COM_KUNENA_BUTTON_MESSAGE_REPLY') . ': ' . $this->topic->subject;

		// Can user edit topic icons?
		if ($this->config->topicicons && $this->topic->isAuthorised('edit'))
		{
			$this->topicIcons = $this->template->getTopicIcons(false, $saved ? $saved['icon_id'] : $this->topic->icon_id);
		}

		list($this->topic, $this->message) = $parent->newReply($saved ? $saved : ['quote' => $quote]);
		$this->action = 'post';

		$this->privateMessage       = new KunenaPrivateMessage;
		$this->privateMessage->body = $saved ? $saved['private'] : $this->privateMessage->body;

		$this->allowedExtensions = KunenaAttachmentHelper::getExtensions($this->category);

		$this->post_anonymous       = $saved ? $saved['anonymous'] : !empty($this->category->post_anonymous);
		$this->subscriptionschecked = $saved ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->app->setUserState('com_kunena.postfields', null);

		$this->canSubscribe = $this->canSubscribe();
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
		$menu_item = $this->app->getMenu()->getActive();

		$this->setMetaData('robots', 'nofollow, noindex');

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
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
				$this->setMetaData('robots', $robots);
			}
		}
	}

	/**
	 * Can user subscribe to the topic?
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	protected function canSubscribe()
	{
		if (!$this->me->userid || !$this->config->allowsubscriptions
			|| $this->config->topic_subscriptions == 'disabled'
		)
		{
			return false;
		}

		return !$this->topic->getUserTopic()->subscribed;
	}
}
