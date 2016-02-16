<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicFormReplyDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicFormReplyDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Edit';

	public $captchaHtml = null;

	/**
	 * Prepare topic reply form.
	 *
	 * @return void
	 *
	 * @throws RuntimeException
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$catid = $this->input->getInt('catid');
		$id = $this->input->getInt('id');
		$mesid = $this->input->getInt('mesid');
		$quote = $this->input->getBool('quote', false);

		$saved = $this->app->getUserState('com_kunena.postfields');

		$this->me = KunenaUserHelper::getMyself();
		$this->template = KunenaFactory::getTemplate();

		if (!$mesid)
		{
			$this->topic = KunenaForumTopicHelper::get($id);
			$parent = KunenaForumMessageHelper::get($this->topic->first_post_id);
		}
		else
		{
			$parent = KunenaForumMessageHelper::get($mesid);
			$this->topic = $parent->getTopic();
		}

		$this->category = $this->topic->getCategory();

		if ( $parent->isAuthorised('reply') && $this->me->canDoCaptcha() )
		{
			if (JPluginHelper::isEnabled('captcha'))
			{
				$plugin = JPluginHelper::getPlugin('captcha');
				$params = new JRegistry($plugin[0]->params);
				$captcha_pubkey = $params->get('public_key');
				$catcha_privkey = $params->get('private_key');

				if (!empty($captcha_pubkey) && !empty($catcha_privkey))
				{
					JPluginHelper::importPlugin('captcha');
					$dispatcher = JDispatcher::getInstance();
					$result = $dispatcher->trigger('onInit', 'dynamic_recaptcha_1');
					$output = $dispatcher->trigger('onDisplay', array(null, 'dynamic_recaptcha_1', 'class="controls g-recaptcha" data-sitekey="' . $captcha_pubkey . '" data-theme="light"'));
					$this->captchaDisplay = $output[0];
					$this->captchaEnabled = $result[0];
				}
			}
			else
			{
				$this->captchaEnabled = false;
			}
		}

		$parent->tryAuthorise('reply');

		// Run event.
		$params = new JRegistry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'reply');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array ('kunena.topic', &$this->topic, &$params, 0));

		// Can user edit topic icons?
		if ($this->config->topicicons && $this->topic->isAuthorised('edit'))
		{
			$this->topicIcons = $this->template->getTopicIcons(false, $saved ? $saved['icon_id'] : $this->topic->icon_id);
		}

		list ($this->topic, $this->message) = $parent->newReply($saved ? $saved : $quote);
		$this->action = 'post';

		$this->allowedExtensions = KunenaAttachmentHelper::getExtensions($this->category);

		$this->post_anonymous = $saved ? $saved['anonymous'] : ! empty ( $this->category->post_anonymous );
		$this->subscriptionschecked = $saved ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->app->setUserState('com_kunena.postfields', null);

		$this->canSubscribe = $this->canSubscribe();
		$this->headerText = JText::_('COM_KUNENA_BUTTON_MESSAGE_REPLY') . ' ' . $this->topic->subject;
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item

		if ($menu_item)
		{
			$params             = $menu_item->params; // get the params
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

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
		}
	}

	/**
	 * Can user subscribe to the topic?
	 *
	 * @return bool
	 */
	protected function canSubscribe()
	{
		if (!$this->me->userid || !$this->config->allowsubscriptions
			|| $this->config->topic_subscriptions == 'disabled')
		{
			return false;
		}

		return !$this->topic->getUserTopic()->subscribed;
	}
}
