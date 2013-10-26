<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicFormCreateDisplay
 *
 * @since  3.1
 */
class ComponentKunenaControllerTopicFormCreateDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Edit';

	public $captchaHtml = null;

	/**
	 * Prepare topic creation form.
	 *
	 * @return bool
	 *
	 * @throws RuntimeException
	 */
	protected function before()
	{
		parent::before();

		$catid = $this->input->getInt('catid');
		$saved = $this->app->getUserState('com_kunena.postfields');

		$this->me = KunenaUserHelper::getMyself();
		$this->template = KunenaFactory::getTemplate();

		$captcha = KunenaSpamRecaptcha::getInstance();

		if ($captcha->enabled())
		{
			$this->captchaHtml = $captcha->getHtml();

			if (!$this->captchaHtml)
			{
				throw new RuntimeException($captcha->getError(), 500);
			}
		}

		// Get topic icons if they are enabled.
		if ($this->config->topicicons)
		{
			$this->topicIcons = $this->template->getTopicIcons(false, $saved ? $saved['icon_id'] : 0);
		}

		$categories = KunenaForumCategoryHelper::getCategories();
		$arrayanynomousbox = array();
		$arraypollcatid = array();

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
		$arraypollcatid = implode(',', $arraypollcatid);

		// FIXME: We need to proxy this...
		$this->document = JFactory::getDocument();
		$this->document->addScriptDeclaration('var arrayanynomousbox={' . $arrayanynomousbox . '}');
		$this->document->addScriptDeclaration('var pollcategoriesid = {' . $arraypollcatid . '};');

		$this->category = KunenaForumCategoryHelper::get($catid);
		list ($this->topic, $this->message) = $this->category->newTopic($saved);

		if (!$this->topic->category_id)
		{
			$msg = JText::sprintf('COM_KUNENA_POST_NEW_TOPIC_NO_PERMISSIONS', $this->topic->getError());
			$this->app->enqueueMessage($msg, 'notice');

			return false;
		}

		$options = array();
		$selected = $this->topic->category_id;

		if ($this->config->pickup_category)
		{
			$options[] = JHtml::_('select.option', '', JText::_('COM_KUNENA_SELECT_CATEGORY'), 'value', 'text');
			$selected = 0;
		}

		if ($saved)
		{
			$selected = $saved['catid'];
		}

		$cat_params = array (
			'ordering' => 'ordering',
			'toplevel' => 0,
			'sections' => 0,
			'direction' => 1,
			'hide_lonely' => 1,
			'action' => 'topic.create'
		);

		$this->selectcatlist = JHtml::_('kunenaforum.categorylist', 'catid', $catid, $options, $cat_params,
			'class="inputbox required"', 'value', 'text', $selected, 'postcatid');

		$this->action = 'post';

		$this->allowedExtensions = KunenaForumMessageAttachmentHelper::getExtensions($this->category);

		if ($arraypollcatid)
		{
			$this->poll = $this->topic->getPoll();
		}

		$this->post_anonymous = $saved ? $saved['anonymous'] : ! empty ( $this->category->post_anonymous );
		$this->subscriptionschecked = $saved ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->app->setUserState('com_kunena.postfields', null);

		$this->canSubscribe = $this->canSubscribe();

		$this->headerText = JText::_('COM_KUNENA_NEW_TOPIC');

		return true;
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}

	/**
	 * Can user subscribe to the topic?
	 *
	 * @return bool
	 */
	protected function canSubscribe()
	{
		if (! $this->me->userid || !$this->config->allowsubscriptions
			|| $this->config->topic_subscriptions == 'disabled')
		{
			return false;
		}

		return !$this->topic->getUserTopic()->subscribed;
	}
}
