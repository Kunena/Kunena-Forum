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
use Joomla\CMS\Session\Session;

/**
 * Class ComponentKunenaControllerTopicItemMessageDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicItemMessageDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Topic/Item/Message';

	/**
	 * @var
	 * @since Kunena
	 */
	public $me;

	/**
	 * @var
	 * @since Kunena
	 */
	public $message;

	/**
	 * @var
	 * @since Kunena
	 */
	public $topic;

	/**
	 * @var
	 * @since Kunena
	 */
	public $category;

	/**
	 * @var
	 * @since Kunena
	 */
	public $profile;

	/**
	 * @var
	 * @since Kunena
	 */
	public $reportMessageLink;

	/**
	 * @var
	 * @since Kunena
	 */
	public $ipLink;

	/**
	 * Prepare displaying message.
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

		$mesid = $this->input->getInt('mesid', 0);

		$this->me       = KunenaUserHelper::getMyself();
		$this->location = $this->input->getInt('location', 0);
		$this->detail   = $this->input->get('detail', false);
		$this->message  = KunenaForumMessageHelper::get($mesid);
		$this->message->tryAuthorise();

		$this->topic     = $this->message->getTopic();
		$this->category  = $this->topic->getCategory();
		$this->profile   = $this->message->getAuthor();
		$this->ktemplate = KunenaFactory::getTemplate();
		$this->candisplaymail = $this->me->canDisplayEmail($this->profile);

		if ($this->topic->unread)
		{
			$this->setMetaData('robots', 'noindex, follow');
		}

		$this->captchaEnabled = false;

		if ($this->message->isAuthorised('reply') && $this->me->canDoCaptcha() && KunenaConfig::getInstance()->quickreply)
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
					$result               = Factory::getApplication()->triggerEvent('onInit', array("dynamic_recaptcha_{$this->message->id}"));
					$this->captchaEnabled = $result[0];
				}
			}
		}

		// Thank you info and buttons.
		$this->thankyou        = array();
		$this->total_thankyou  = 0;
		$this->more_thankyou   = 0;
		$this->thankyou_delete = array();

		if (isset($this->message->thankyou))
		{
			if ($this->config->showthankyou && $this->profile->exists())
			{
				$task = "index.php?option=com_kunena&view=topic&task=%s&catid={$this->category->id}"
					. "&id={$this->topic->id}&mesid={$this->message->id}&"
					. Session::getFormToken() . '=1';

				if (count($this->message->thankyou) > $this->config->thankyou_max)
				{
					$this->more_thankyou = count($this->message->thankyou) - $this->config->thankyou_max;
				}

				$this->total_thankyou = count($this->message->thankyou);
				$thankyous            = array_slice($this->message->thankyou, 0, $this->config->thankyou_max, true);

				$userids_thankyous = array();

				foreach ($thankyous as $userid => $time)
				{
					$userids_thankyous[] = $userid;
				}

				$loaded_users = KunenaUserHelper::loadUsers($userids_thankyous);

				foreach ($loaded_users as $userid => $user)
				{
					if ($this->message->isAuthorised('unthankyou') && $this->me->isModerator($this->message->getCategory()))
					{
						$this->thankyou_delete[$userid] = KunenaRoute::_(sprintf($task, "unthankyou&userid={$userid}"));
					}

					$this->thankyou[$userid] = $loaded_users[$userid]->getLink();
				}
			}
		}

		if ($this->config->reportmsg && $this->me->exists())
		{
			if ($this->config->user_report && $this->me->userid == $this->message->userid && !$this->me->isModerator())
			{
				$this->reportMessageLink = HTMLHelper::_('kunenaforum.link',
					'index.php?option=com_kunena&view=topic&layout=report&catid='
					. intval($this->category->id) . '&id=' . intval($this->message->thread)
					. '&mesid=' . intval($this->message->id),
					Text::_('COM_KUNENA_REPORT'),
					Text::_('COM_KUNENA_REPORT')
				);
			}
		}

		// Show admins the IP address of the user.
		if ($this->category->isAuthorised('admin')
			|| ($this->category->isAuthorised('moderate') && !$this->config->hide_ip)
		)
		{
			if (!empty($this->message->ip))
			{
				$this->ipLink = '<a href="https://dnslytics.com/ip/' . $this->message->ip
					. '" target="_blank" rel="nofollow noopener noreferrer"> IP: ' . $this->message->ip . '</a>';
			}
			else
			{
				$this->ipLink = '&nbsp;';
			}
		}
	}
}
