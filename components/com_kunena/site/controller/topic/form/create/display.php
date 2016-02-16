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
 * Class ComponentKunenaControllerTopicFormCreateDisplay
 *
 * @since  K4.0
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

		$catid = $this->input->getInt('catid', 0);
		$saved = $this->app->getUserState('com_kunena.postfields');

		$this->me = KunenaUserHelper::getMyself();
		$this->template = KunenaFactory::getTemplate();

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

		$this->template->setCategoryIconset($this->topic->getCategory()->iconset);

		// Get topic icons if they are enabled.
		if ($this->config->topicicons)
		{
			$this->topicIcons = $this->template->getTopicIcons(false, $saved ? $saved['icon_id'] : 0, $this->topic->getCategory()->iconset);
		}

		if ( $this->topic->isAuthorised('create') && $this->me->canDoCaptcha())
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
		}
		else
		{
			$this->captchaEnabled = false;
		}

		if (!$this->topic->category_id)
		{
			throw new KunenaExceptionAuthorise(JText::sprintf('COM_KUNENA_POST_NEW_TOPIC_NO_PERMISSIONS', $this->topic->getError()), $this->me->exists() ? 403 : 401);
		}

		$options = array();
		$selected = $this->topic->category_id;

		if ($this->config->pickup_category)
		{
			$options[] = JHtml::_('select.option', '', JText::_('COM_KUNENA_SELECT_CATEGORY'), 'value', 'text');
			$selected = '';
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
			'class="form-control inputbox required"', 'value', 'text', $selected, 'postcatid');

		$this->action = 'post';

		$this->allowedExtensions = KunenaAttachmentHelper::getExtensions($this->category);

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
		if (! $this->me->userid || !$this->config->allowsubscriptions
			|| $this->config->topic_subscriptions == 'disabled')
		{
			return false;
		}

		return !$this->topic->getUserTopic()->subscribed;
	}
}
