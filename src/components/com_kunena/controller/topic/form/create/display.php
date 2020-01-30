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

namespace Kunena\Forum\Site\Controller\Topic\Form\Create;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Attachment\AttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\Authorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\KunenaPrivate\Message;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\Template;
use function defined;

/**
 * Class ComponentTopicControllerFormCreateDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentTopicControllerFormCreateDisplay extends KunenaControllerDisplay
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
	 * Prepare topic creation form.
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		$catid = $this->input->getInt('catid', 0);
		$saved = $this->app->getUserState('com_kunena.postfields');

		$Itemid = Factory::getApplication()->input->getCmd('Itemid');
		$format = Factory::getApplication()->input->getCmd('format');

		if (!$Itemid && $format != 'feed' && $this->config->sef_redirect)
		{
			if ($this->config->search_id)
			{
				$itemidfix = $this->config->search_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(\Kunena\Forum\Libraries\Route\KunenaRoute::getItemID("index.php?option=com_kunena&view=topic&layout=create"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");

			if ($catid)
			{
				$controller->setRedirect(\Kunena\Forum\Libraries\Route\KunenaRoute::_("index.php?option=com_kunena&view=topic&layout=create&catid={$catid}&Itemid={$itemidfix}", false));
			}
			else
			{
				$controller->setRedirect(\Kunena\Forum\Libraries\Route\KunenaRoute::_("index.php?option=com_kunena&view=topic&layout=create&Itemid={$itemidfix}", false));
			}

			$controller->redirect();
		}

		$this->me       = \Kunena\Forum\Libraries\User\KunenaUserHelper::getMyself();
		$this->template = KunenaFactory::getTemplate();

		$categories        = \Kunena\Forum\Libraries\Forum\Category\CategoryHelper::getCategories();
		$arrayanynomousbox = [];
		$arraypollcatid    = [];

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
			throw new Authorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		// FIXME: We need to proxy this...
		Template::getInstance()->addScriptOptions('com_kunena.arrayanynomousbox', json_encode($arrayanynomousbox));
		Template::getInstance()->addScriptOptions('com_kunena.pollcategoriesid', json_encode($arraypollcatid));

		$this->category = \Kunena\Forum\Libraries\Forum\Category\CategoryHelper::get($catid);
		list($this->topic, $this->message) = $this->category->newTopic($saved);

		$this->template->setCategoryIconset($this->topic->getCategory()->iconset);

		// Get topic icons if they are enabled.
		if ($this->config->topicicons)
		{
			$this->topicIcons = $this->template->getTopicIcons(false, $saved ? $saved['icon_id'] : 0);
		}

		if ($this->topic->isAuthorised('create') && $this->me->canDoCaptcha())
		{
			$this->captchaDisplay = Template::getInstance()->recaptcha();
			$this->captchaEnabled = true;
		}
		else
		{
			$this->captchaEnabled = false;
		}

		if (!$this->topic->category_id)
		{
			throw new Authorise(Text::sprintf('COM_KUNENA_POST_NEW_TOPIC_NO_PERMISSIONS',
				$this->topic->getError()), $this->me->exists() ? 403 : 401);
		}

		$options  = [];
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

		$cat_params = [
			'ordering'    => 'ordering',
			'toplevel'    => 0,
			'sections'    => 0,
			'direction'   => 1,
			'hide_lonely' => 1,
			'action'      => 'topic.create',
		];

		$this->selectcatlist = HTMLHelper::_(
			'kunenaforum.categorylist', 'catid', $catid, $options, $cat_params,
			'class="form-control inputbox required"', 'value', 'text', $selected, 'postcatid');

		$this->action = 'post';

		$this->allowedExtensions = AttachmentHelper::getExtensions($this->category);

		if ($arraypollcatid)
		{
			$this->poll = $this->topic->getPoll();
		}

		$this->privateMessage       = new Message;
		$this->privateMessage->body = $saved ? $saved['private'] : $this->privateMessage->body;

		$this->post_anonymous       = $saved ? $saved['anonymous'] : !empty($this->category->post_anonymous);
		$this->subscriptionschecked = $saved ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->app->setUserState('com_kunena.postfields', null);

		$this->canSubscribe = $this->canSubscribe();

		$this->headerText = Text::_('COM_KUNENA_NEW_TOPIC');

		return true;
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
