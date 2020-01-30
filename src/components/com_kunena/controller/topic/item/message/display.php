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

namespace Kunena\Forum\Site\Controller\Topic\Item\Message;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Forum\Category\Category;
use Kunena\Forum\Libraries\Forum\Topic\Topic;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\Template;
use Kunena\Forum\Libraries\User\Helper;
use Kunena\Forum\Libraries\User\KunenaUser;
use function defined;

/**
 * Class ComponentTopicControllerItemMessageDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentTopicControllerItemMessageDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/Item/Message';

	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $message;

	/**
	 * @var     Topic
	 * @since   Kunena 6.0
	 */
	public $topic;

	/**
	 * @var     Category
	 * @since   Kunena 6.0
	 */
	public $category;

	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $profile;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $reportMessageLink;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $ipLink;

	/**
	 * Prepare displaying message.
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

		$mesid = $this->input->getInt('mesid', 0);

		$this->me       = Helper::getMyself();
		$this->location = $this->input->getInt('location', 0);
		$this->detail   = $this->input->get('detail', false);
		$this->message  = \Kunena\Forum\Libraries\Forum\Message\Helper::get($mesid);
		$this->message->tryAuthorise();

		$this->topic     = $this->message->getTopic();
		$this->category  = $this->topic->getCategory();
		$this->profile   = $this->message->getAuthor();
		$this->ktemplate = KunenaFactory::getTemplate();

		if ($this->topic->unread)
		{
			$this->setMetaData('robots', 'noindex, follow');
		}

		$this->captchaEnabled = false;

		if ($this->message->isAuthorised('reply') && $this->me->canDoCaptcha() && $this->config->quickreply)
		{
			$this->captchaDisplay = Template::getInstance()->recaptcha();
			$this->captchaEnabled = true;
		}
		else
		{
			$this->captchaEnabled = false;
		}

		// Thank you info and buttons.
		$this->thankyou        = [];
		$this->total_thankyou  = 0;
		$this->more_thankyou   = 0;
		$this->thankyou_delete = [];

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

				$userids_thankyous = [];

				foreach ($thankyous as $userid => $time)
				{
					$userids_thankyous[] = $userid;
				}

				$loaded_users = Helper::loadUsers($userids_thankyous);

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
				$this->reportMessageLink = HTMLHelper::_('link',
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
				$this->ipLink = '<a href="https://www.geoiptool.de/en/?ip=' . $this->message->ip
					. '" target="_blank" rel="nofollow noopener noreferrer"> IP: ' . $this->message->ip . '</a>';
			}
			else
			{
				$this->ipLink = '&nbsp;';
			}
		}
	}
}
