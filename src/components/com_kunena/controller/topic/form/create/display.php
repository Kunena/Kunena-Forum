<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Class ComponentKunenaControllerTopicFormCreateDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicFormCreateDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Topic/Edit';

	/**
	 * @var null
	 * @since Kunena
	 */
	public $captchaHtml = null;

	/**
	 * Prepare topic creation form.
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$catid = $this->input->getInt('catid', 0);
		$saved = $this->app->getUserState('com_kunena.postfields');

		$Itemid = Factory::getApplication()->input->getCmd('Itemid');
		$format = Factory::getApplication()->input->getCmd('format');

		if (!$Itemid && $format != 'feed' && KunenaConfig::getInstance()->sef_redirect)
		{
			if (KunenaConfig::getInstance()->search_id)
			{
				$itemidfix = KunenaConfig::getInstance()->search_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=topic&layout=create"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");

			if ($catid)
			{
				$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topic&layout=create&catid={$catid}&Itemid={$itemidfix}", false));
			}
			else
			{
				$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topic&layout=create&Itemid={$itemidfix}", false));
			}

			$controller->redirect();
		}

		$this->me       = KunenaUserHelper::getMyself();
		$this->template = KunenaFactory::getTemplate();

		$categories        = KunenaForumCategoryHelper::getCategories();
		$arrayanynomousbox = array();
		$arraypollcatid    = array();

		foreach ($categories as $category)
		{
			if (!$category->isSection() && $category->allow_anonymous)
			{
				$arrayanynomousbox[$category->id] = $category->post_anonymous;
			}

			if ($this->config->pollenabled)
			{
				if (!$category->isSection() && $category->allow_polls)
				{
					$arraypollcatid[$category->id] = 1;
				}
			}
		}

		if ($this->config->read_only)
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		// FIXME: We need to proxy this...
		KunenaTemplate::getInstance()->addScriptOptions('com_kunena.arrayanynomousbox', json_encode($arrayanynomousbox));
		KunenaTemplate::getInstance()->addScriptOptions('com_kunena.pollcategoriesid', json_encode($arraypollcatid));

		$this->category = KunenaForumCategoryHelper::get($catid);
		list($this->topic, $this->message) = $this->category->newTopic($saved);

		$this->template->setCategoryIconset($this->topic->getCategory()->iconset);

		// Get topic icons if they are enabled.
		if ($this->config->topicicons)
		{
			$this->topicIcons = $this->template->getTopicIcons(false, $saved ? $saved['icon_id'] : 0);
		}

		if ($this->topic->isAuthorised('create') && $this->me->canDoCaptcha())
		{
			if (\Joomla\CMS\Plugin\PluginHelper::isEnabled('captcha'))
			{
				$plugin         = \Joomla\CMS\Plugin\PluginHelper::getPlugin('captcha');
				$params         = new \Joomla\Registry\Registry($plugin[0]->params);
				$captcha_pubkey = $params->get('public_key');
				$catcha_privkey = $params->get('private_key');

				if (!empty($captcha_pubkey) && !empty($catcha_privkey))
				{
					\Joomla\CMS\Plugin\PluginHelper::importPlugin('captcha');
					$result               = Factory::getApplication()->triggerEvent('onInit', array('dynamic_recaptcha_1'));
					$output               = Factory::getApplication()->triggerEvent('onDisplay', array(null, 'dynamic_recaptcha_1', 'class="controls g-recaptcha" data-sitekey="'
						. $captcha_pubkey . '" data-theme="light"', ));
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
			throw new KunenaExceptionAuthorise(Text::sprintf('COM_KUNENA_POST_NEW_TOPIC_NO_PERMISSIONS',
				$this->topic->getError()), $this->me->exists() ? 403 : 401);
		}

		$options  = array();
		$selected = $this->topic->category_id;

		if ($this->config->pickup_category)
		{
			$options[] = HTMLHelper::_('select.option', '', Text::_('COM_KUNENA_SELECT_CATEGORY'), 'value', 'text');
			$selected  = '';
		}

		if ($saved)
		{
			$selected = $saved['catid'];
		}

		$cat_params = array(
			'ordering'    => 'ordering',
			'toplevel'    => 0,
			'sections'    => 0,
			'direction'   => 1,
			'hide_lonely' => 1,
			'action'      => 'topic.create',
		);

		$this->selectcatlist = HTMLHelper::_(
			'kunenaforum.categorylist', 'catid', $catid, $options, $cat_params,
			'class="form-control inputbox required"', 'value', 'text', $selected, 'postcatid');

		$this->action = 'post';

		$this->allowedExtensions = KunenaAttachmentHelper::getExtensions($this->category);

		if ($arraypollcatid)
		{
			$this->poll = $this->topic->getPoll();
		}

		$this->post_anonymous       = $saved ? $saved['anonymous'] : !empty($this->category->post_anonymous);
		$this->subscriptionschecked = $saved ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->app->setUserState('com_kunena.postfields', null);

		$this->canSubscribe = $this->canSubscribe();

		$this->headerText = Text::_('COM_KUNENA_NEW_TOPIC');

		$this->editorType = $this->template->params->get('editor');

		// Just set default value in case of the template aren't saved
		if ($this->editorType === 1 || $this->editorType === 0 || strlen($this->editorType) <= 1)
		{
			$this->editorType = 'ckeditor';
		}

		$this->UserCanPostImage = true;

		if ($this->config->new_users_prevent_post_url_images && $this->me->posts < $this->config->minimal_user_posts_add_url_image)
		{
			$this->UserCanPostImage = false;
		}

		return true;
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		$this->setMetaData('robots', 'nofollow, noindex');

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
				$this->setMetaData('robots', $robots);
			}
		}
	}

	/**
	 * Can user subscribe to the topic?
	 *
	 * @return boolean
	 * @since Kunena
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
