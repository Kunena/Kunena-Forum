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
use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerTopicFormReplyDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicFormReplyDisplay extends KunenaControllerDisplay
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
	 * Prepare topic reply form.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$catid = $this->input->getInt('catid');
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

		$doc = Factory::getDocument();

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

		$doc->addHeadLink($this->topic->getUrl(), 'canonical');

		$this->category = $this->topic->getCategory();

		if ($parent->isAuthorised('reply') && $this->me->canDoCaptcha())
		{
			if (\Joomla\CMS\Plugin\PluginHelper::isEnabled('captcha'))
			{
				$plugin = \Joomla\CMS\Plugin\PluginHelper::getPlugin('captcha');
				$params = new \Joomla\Registry\Registry($plugin[0]->params);

				$captcha_pubkey = $params->get('public_key');
				$catcha_privkey = $params->get('private_key');

				if (!empty($captcha_pubkey) && !empty($catcha_privkey))
				{
					\Joomla\CMS\Plugin\PluginHelper::importPlugin('captcha');
					$result               = Factory::getApplication()->triggerEvent('onInit', array('dynamic_recaptcha_1'));
					$output               = Factory::getApplication()->triggerEvent('onDisplay', array(null, 'dynamic_recaptcha_1', 'class="controls g-recaptcha" data-sitekey="'
							. $captcha_pubkey . '" data-theme="light"', )
					);
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
		$params = new \Joomla\Registry\Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'reply');

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', array('kunena.topic', &$this->topic, &$params, 0));

		$this->headerText   = Text::_('COM_KUNENA_BUTTON_MESSAGE_REPLY') . ': ' . $this->topic->subject;

		// Can user edit topic icons?
		if ($this->config->topicicons && $this->topic->isAuthorised('edit'))
		{
			$this->topicIcons = $this->template->getTopicIcons(false, $saved ? $saved['icon_id'] : $this->topic->icon_id);
		}

		list($this->topic, $this->message) = $parent->newReply($saved ? $saved : $quote, $parent->userid);
		$this->action = 'post';

		$this->allowedExtensions = KunenaAttachmentHelper::getExtensions($this->category);

		$this->post_anonymous       = $saved ? $saved['anonymous'] : !empty($this->category->post_anonymous);
		$this->subscriptionschecked = $saved ? $saved['subscribe'] : $this->config->subscriptionschecked == 1;
		$this->app->setUserState('com_kunena.postfields', null);

		$this->canSubscribe = $this->canSubscribe();

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
