<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicItemMessageDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicItemMessageDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Item/Message';

	public $me;

	public $message;

	public $topic;

	public $category;

	public $profile;

	public $reportMessageLink;

	public $ipLink;

	/**
	 * Prepare displaying message.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$mesid = $this->input->getInt('mesid', 0);

		$this->me = KunenaUserHelper::getMyself();
		$this->location = $this->input->getInt('location', 0);
		$this->detail = $this->input->get('detail', false);
		$this->message = KunenaForumMessageHelper::get($mesid);
		$this->message->tryAuthorise();

		$this->topic = $this->message->getTopic();
		$this->category = $this->topic->getCategory();
		$this->profile = $this->message->getAuthor();
		$this->ktemplate = KunenaFactory::getTemplate();

		$this->captchaEnabled = false;
		if ( $this->message->isAuthorised('reply') && $this->me->canDoCaptcha() )
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
					$result = $dispatcher->trigger('onInit', "dynamic_recaptcha_{$this->message->id}");

					$this->captchaEnabled = $result[0];
				}
			}
		}

		// Thank you info and buttons.
		$this->thankyou = array();
		$this->total_thankyou = 0;
		$this->more_thankyou = 0;
		$this->thankyou_delete = array();

		if (isset($this->message->thankyou))
		{
			if ($this->config->showthankyou && $this->profile->exists())
			{
				$task = "index.php?option=com_kunena&view=topic&task=%s&catid={$this->category->id}"
					. "&id={$this->topic->id}&mesid={$this->message->id}&"
					. JSession::getFormToken() . '=1';

				// Ror normal users, show only limited number of thankyou (config->thankyou_max).
				if (!$this->me->isAdmin() && !$this->me->isModerator())
				{
					if (count($this->message->thankyou) > $this->config->thankyou_max)
					{
						$this->more_thankyou = count($this->message->thankyou) - $this->config->thankyou_max;
					}

					$this->total_thankyou = count($this->message->thankyou);
					$thankyous = array_slice($this->message->thankyou, 0, $this->config->thankyou_max, true);
				}
				else
				{
					$thankyous = $this->message->thankyou;
				}

				$userids_thankyous = array();

				foreach ($thankyous as $userid => $time)
				{
					$userids_thankyous[] = $userid;
				}

				$loaded_users = KunenaUserHelper::loadUsers($userids_thankyous);

				foreach ($loaded_users as $userid => $user)
				{
					if ($this->message->authorise('unthankyou') && $this->me->isModerator($this->message->getCategory()))
					{
						$this->thankyou_delete[$userid]  = KunenaRoute::_(sprintf($task, "unthankyou&userid={$userid}"));
					}

					$this->thankyou[$userid] = $loaded_users[$userid]->getLink();
				}
			}
		}

		if ($this->config->reportmsg && $this->me->exists()) {
			if ($this->config->user_report && $this->me->userid == $this->message->userid && !$this->me->isModerator()) {
				$this->reportMessageLink = JHTML::_('kunenaforum.link',
					'index.php?option=com_kunena&view=topic&layout=report&catid='
					. intval($this->category->id) . '&id=' . intval($this->message->thread)
					. '&mesid=' . intval($this->message->id),
					JText::_('COM_KUNENA_REPORT'),
					JText::_('COM_KUNENA_REPORT')
				);
			}
		}

		// Show admins the IP address of the user.
		if ($this->category->isAuthorised('admin')
			|| ($this->category->isAuthorised('moderate') && !$this->config->hide_ip))
		{
			if (!empty($this->message->ip))
			{
				$this->ipLink = '<a href="http://whois.domaintools.com/' . $this->message->ip
					. '" target="_blank"> IP: ' . $this->message->ip . '</a>';
			}
			else
			{
				$this->ipLink = '&nbsp;';
			}
		}
	}
}
