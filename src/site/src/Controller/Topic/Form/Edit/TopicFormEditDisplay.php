<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
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
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
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
class TopicFormEditDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/Edit';
	private $me;
	private $message;

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

		$this->me      = KunenaUserHelper::getMyself();
		$template      = KunenaFactory::getTemplate();
		$this->message = KunenaMessageHelper::get($mesid);
		$this->message->tryAuthorise('edit');

		$topic     = $this->message->getTopic();
		$category1 = $topic->getCategory();

		$template->setCategoryIconset($topic->getCategory()->iconset);

		if ($this->config->topicIcons && $topic->isAuthorised('edit'))
		{
			$this->topicIcons = $template->getTopicIcons(false, $saved ? $saved['icon_id'] : $topic->icon_id);
		}

		if ($this->config->readOnly)
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		$categories        = KunenaCategoryHelper::getCategories();
		$arrayanynomousbox = [];
		$arraypollcatid    = [];

		foreach ($categories as $category)
		{
			if (!$category->isSection() && $category->allowAnonymous)
			{
				$arrayanynomousbox[$category->id] = $category->postAnonymous;
			}

			if ($this->config->pollEnabled)
			{
				if (!$category->isSection() && $category->allowPolls)
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
						$canonicalUrl               = $topic->getUrl();
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

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.topic', &$topic, &$params, 0]);

		$this->action = 'edit';

		// Get attachments.
		$this->attachments = $this->message->getAttachments();

		// Get poll.
		if ($this->message->parent == 0
			&& $topic->isAuthorised(!$topic->poll_id ? 'poll.create' : 'poll.edit')
		)
		{
			$this->poll = $topic->getPoll();
		}

		$this->allowedExtensions = KunenaAttachmentHelper::getExtensions($category1);

		if ($saved)
		{
			// Update message contents.
			$this->message->edit($saved);
		}

		$finder = new KunenaFinder;
		$finder
			->filterByMessage($this->message)
			->where('parentid', '=', 0)
			->where('author_id', '=', $this->message->userid)
			->order('id')
			->limit(1);
		$privateMessage       = $finder->firstOrNew();
		$privateMessage->body = $saved ? $saved['private'] : $privateMessage->body;

		$this->postAnonymous        = isset($saved['anonymous']) ? $saved['anonymous'] : !empty($category1->postAnonymous);
		$this->subscriptionsChecked = false;
		$this->canSubscribe         = false;
		$usertopic                  = $topic->getUserTopic();

		if ($this->config->allowSubscriptions)
		{
			$this->canSubscribe = true;
		}

		if ($topic->isAuthorised('subscribe') && $topic->exists())
		{
			if ($usertopic->subscribed == 1)
			{
				$this->subscriptionsChecked = true;
			}
		}
		else
		{
			if ($this->config->subscriptionsChecked)
			{
				$this->subscriptionsChecked = true;
			}
		}

		$this->modified_reason = isset($saved['modified_reason']) ? $saved['modified_reason'] : '';
		$this->app->setUserState('com_kunena.postfields', null);

		$this->headerText = Text::_('COM_KUNENA_POST_EDIT') . ' ' . $topic->subject;
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
	protected function prepareDocument()
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
		if (!$this->me->userid || !$this->config->allowSubscriptions
			|| $this->config->topicSubscriptions == 'disabled'
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
