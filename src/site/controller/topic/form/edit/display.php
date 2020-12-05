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

namespace Kunena\Forum\Site\Controller\Topic\Form\Edit;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaFinder;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use function defined;

/**
 * Class ComponentTopicControllerFormEditDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentTopicControllerFormEditDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/Edit';
	private $me;
	private $message;
	private $headerText;
	/**
	 * @var mixed|string
	 * @since version
	 */
	private $modified_reason;
	/**
	 * @var false
	 * @since version
	 */
	private $canSubscribe;
	/**
	 * @var false
	 * @since version
	 */
	private $subscriptionschecked;
	/**
	 * @var bool|mixed
	 * @since version
	 */
	private $post_anonymous;
	/**
	 * @var \Kunena\Forum\Libraries\KunenaPrivate\KunenaPrivateMessage|mixed
	 * @since version
	 */
	private $privateMessage;
	/**
	 * @var array|bool
	 * @since version
	 */
	private $allowedExtensions;
	/**
	 * @var \Kunena\Forum\Libraries\Forum\Topic\Poll\KunenaPoll
	 * @since version
	 */
	private $poll;
	/**
	 * @var \Kunena\Forum\Libraries\Attachment\KunenaAttachment[]
	 * @since version
	 */
	private $attachments;
	/**
	 * @var string
	 * @since version
	 */
	private $action;
	/**
	 * @var array|\SimpleXMLElement
	 * @since version
	 */
	private $topicIcons;
	/**
	 * @var \Kunena\Forum\Libraries\Forum\Category\KunenaCategory
	 * @since version
	 */
	private $category;
	/**
	 * @var \Kunena\Forum\Libraries\Forum\Topic\KunenaTopic|null
	 * @since version
	 */
	private $topic;
	/**
	 * @var KunenaTemplate
	 * @since version
	 */
	private $template;
	/**
	 * @var int
	 * @since version
	 */
	private $catid;

	/**
	 * Prepare topic edit form.
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

		$this->catid = $this->input->getInt('catid');
		$mesid       = $this->input->getInt('mesid');
		$saved       = $this->app->getUserState('com_kunena.postfields');

		$this->me       = KunenaUserHelper::getMyself();
		$this->template = KunenaFactory::getTemplate();
		$this->message  = KunenaMessageHelper::get($mesid);
		$this->message->tryAuthorise('edit');

		$this->topic    = $this->message->getTopic();
		$this->category = $this->topic->getCategory();

		$this->template->setCategoryIconset($this->topic->getCategory()->iconset);

		if ($this->config->topicicons && $this->topic->isAuthorised('edit'))
		{
			$this->topicIcons = $this->template->getTopicIcons(false, $saved ? $saved['icon_id'] : $this->topic->icon_id);
		}

		if ($this->config->read_only)
		{
			throw new KunenaAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		$categories        = KunenaCategoryHelper::getCategories();
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

		KunenaTemplate::getInstance()->addScriptOptions('com_kunena.arrayanynomousbox', json_encode($arrayanynomousbox));
		KunenaTemplate::getInstance()->addScriptOptions('com_kunena.pollcategoriesid', json_encode($arraypollcatid));

		$doc = Factory::getApplication()->getDocument();
		$doc->setMetaData('robots', 'nofollow, noindex');

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

		// Run onKunenaPrepare event.
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'reply');

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.topic', &$this->topic, &$params, 0]);

		$this->action = 'edit';

		// Get attachments.
		$this->attachments = $this->message->getAttachments();

		// Get poll.
		if ($this->message->parent == 0
			&& $this->topic->isAuthorised(!$this->topic->poll_id ? 'poll.create' : 'poll.edit')
		)
		{
			$this->poll = $this->topic->getPoll();
		}

		$this->allowedExtensions = KunenaAttachmentHelper::getExtensions($this->category);

		if ($saved)
		{
			// Update message contents.
			$this->message->edit($saved);
		}

		$finder = new KunenaFinder;
		$finder
			->filterByMessage($this->message)
			->where('parent_id', '=', 0)
			->where('author_id', '=', $this->message->userid)
			->order('id')
			->limit(1);
		$this->privateMessage       = $finder->firstOrNew();
		$this->privateMessage->body = $saved ? $saved['private'] : $this->privateMessage->body;

		$this->post_anonymous       = isset($saved['anonymous']) ? $saved['anonymous'] : !empty($this->category->post_anonymous);
		$this->subscriptionschecked = false;
		$this->canSubscribe         = false;
		$usertopic                  = $this->topic->getUserTopic();

		if ($this->config->allowsubscriptions)
		{
			$this->canSubscribe = true;
		}

		if ($this->topic->isAuthorised('subscribe') && $this->topic->exists())
		{
			if ($usertopic->subscribed == 1)
			{
				$this->subscriptionschecked = true;
			}
		}
		else
		{
			if ($this->config->subscriptionschecked)
			{
				$this->subscriptionschecked = true;
			}
		}

		$this->modified_reason = isset($saved['modified_reason']) ? $saved['modified_reason'] : '';
		$this->app->setUserState('com_kunena.postfields', null);

		$this->headerText = Text::_('COM_KUNENA_POST_EDIT') . ' ' . $this->topic->subject;
	}

	/**
	 * Prepare document.
	 *
	 * @return  void|boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareDocument(): bool
	{
		$menu_item = $this->app->getMenu()->getActive();

		$this->setMetaData('robots', 'nofollow, noindex');

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
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

		if ($this->message->userid != $this->me->userid && $this->me->isModerator())
		{
			return false;
		}

		return true;
	}
}
