<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerTopicFormReplyDisplay
 */
class ComponentKunenaControllerTopicFormReplyDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Edit';

	public $captchaHtml = null;

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

		$captcha = KunenaSpamRecaptcha::getInstance();
		if ($captcha->enabled()) {
			$this->captchaHtml = $captcha->getHtml();
			if (!$this->captchaHtml) {
				throw new RuntimeException($captcha->getError(), 500);
			}
		}

		if (!$mesid) {
			$this->topic = KunenaForumTopicHelper::get($id);
			$parent = KunenaForumMessageHelper::get($this->topic->first_post_id);
		} else {
			$parent = KunenaForumMessageHelper::get($mesid);
			$this->topic = $parent->getTopic();
		}
		$this->category = $this->topic->getCategory();

		$parent->tryAuthorise('reply');

		// Run event.
		$params = new JRegistry();
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'reply');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array ('kunena.topic', &$this->topic, &$params, 0));

		// Can user edit topic icons?
		if ($this->config->topicicons && $this->topic->isAuthorised('edit')) {
			$this->topicIcons = $this->template->getTopicIcons(false, $saved ? $saved['icon_id'] : $this->topic->icon_id);
		}

		list ($this->topic, $this->message) = $parent->newReply($saved ? $saved : $quote);
		$this->action = 'post';

		$this->allowedExtensions = KunenaForumMessageAttachmentHelper::getExtensions($this->category);

		$this->post_anonymous = $saved ? $saved['anonymous'] : ! empty ( $this->category->post_anonymous );
		$this->subscriptionschecked = $saved ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->app->setUserState('com_kunena.postfields', null);

		$this->canSubscribe = $this->canSubscribe();

		$this->headerText = JText::_('COM_KUNENA_BUTTON_MESSAGE_REPLY' ) . ' ' . $this->topic->subject;
	}

	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}

	protected function canSubscribe() {
		if (! $this->me->userid || ! $this->config->allowsubscriptions || $this->config->topic_subscriptions == 'disabled')
			return false;
		return ! $this->topic->getUserTopic()->subscribed;
	}
}
