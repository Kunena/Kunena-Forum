<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Message
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerMessageItemActionsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerMessageItemActionsDisplay extends KunenaControllerDisplay
{
	protected $name = 'Message/Item/Actions';

	/**
	 * @var KunenaForumTopic
	 */
	public $topic;

	public $message;

	public $messageButtons;

	/**
	 * Prepare message actions display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$mesid = $this->input->getInt('mesid');
		$me = KunenaUserHelper::getMyself();

		$this->message = KunenaForumMessage::getInstance($mesid);
		$this->topic = $this->message->getTopic();

		$id = $this->message->thread;
		$catid = $this->message->catid;
		$token = JSession::getFormToken();

		$task = "index.php?option=com_kunena&view=topic&task=%s&catid={$catid}&id={$id}&mesid={$mesid}&{$token}=1";
		$layout = "index.php?option=com_kunena&view=topic&layout=%s&catid={$catid}&id={$id}&mesid={$mesid}";

		$this->template = KunenaFactory::getTemplate();
		$this->messageButtons = new JObject;
		$this->message_closed = null;

		if ($this->message->isAuthorised('reply'))
		{
			if (version_compare(JVERSION, '3.4', '<') && $me->canDoCaptcha())
			{
				$this->quickreply = false;
			}
			elseif (version_compare(JVERSION, '3.4', '>') && $me->canDoCaptcha())
			{
				$this->quickreply = true;
			}
			else
			{
				$this->quickreply = true;
			}
		}
		else
		{
			$this->quickreply = false;
		}

		// Reply / Quote
		if ($this->message->isAuthorised('reply'))
		{

			$this->messageButtons->set('reply',
				$this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication', null, true)
			);

			if ($me->exists() && !KunenaSpamRecaptcha::getInstance()->enabled())
			{
				$this->messageButtons->set('quickreply',
					$this->getButton(sprintf($layout, 'reply'), 'quickreply', 'message', 'communication', "kreply{$mesid}")
				);
			}

			$this->messageButtons->set('quote',
				$this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication')
			);
		}
		elseif (!$me->isModerator($this->topic->getCategory()))
		{
			// User is not allowed to write a post.
			$this->message_closed = $this->topic->locked ? JText::_('COM_KUNENA_POST_LOCK_SET') :
				($me->exists() ? JText::_('COM_KUNENA_REPLY_USER_REPLY_DISABLED'): ' ');
		}

		$login = KunenaLogin::getInstance();
		if (!$this->message->isAuthorised('reply') && !$this->message_closed && $login->enabled() && !$this->message->hold || !$this->message->isAuthorised('reply') && !$this->topic->locked && $login->enabled() && !$me->userid && !$this->message->hold) {
			$logintext =  '<a class="btn-link" href="#klogin" rel="nofollow"> ' . JText::_('JLOGIN'). '</a>';
			if ($login->getRegistrationUrl()) {
				$register =  ' ' . JText::_('COM_KUNENA_LOGIN_OR') .' <a class="btn-link" href="' . $login->getRegistrationUrl() . '">'. JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT') . '</a>';
			}
			else {
				$register = '';
			}
			echo '<p>'. JText::sprintf('COM_KUNENA_LOGIN_PLEASE', $logintext, $register).'</p>';
		}

		// Thank you.
		if (isset($this->message->thankyou)) {
			if ($this->message->isAuthorised('thankyou') && !array_key_exists($me->userid, $this->message->thankyou))
			{
				$this->messageButtons->set('thankyou',
					$this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user', null, false)
				);
			}
		}

		// Unthank you
		if ($this->message->isAuthorised('unthankyou') && array_key_exists($me->userid, $this->message->thankyou))
		{
			$this->messageButtons->set('unthankyou',
				$this->getButton(sprintf($task, 'unthankyou&userid='.$me->userid), 'unthankyou', 'message', 'user', null, false)
			);
		}

		// Report this.
		if (KunenaFactory::getConfig()->reportmsg && $me->exists())
		{
			$this->messageButtons->set('report',
				$this->getButton(sprintf($layout, 'report'), 'report', 'message', 'user')
			);
		}

		// Moderation and own post actions.
		if ($this->message->isAuthorised('edit'))
		{
			$this->messageButtons->set('edit',
				$this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation')
			);
		}

		if ($this->message->isAuthorised('move'))
		{
			$this->messageButtons->set('moderate',
				$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation')
			);
		}

		if ($this->message->hold == 1)
		{
			if ($this->message->isAuthorised('approve'))
			{
				$this->messageButtons->set('publish',
					$this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation')
				);
			}

			if ($this->message->isAuthorised('delete'))
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation')
				);
			}
		}
		elseif ($this->message->hold == 2 || $this->message->hold == 3)
		{
			if ($this->message->isAuthorised('undelete'))
			{
				$this->messageButtons->set('undelete',
					$this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation')
				);
			}

			if ($this->message->isAuthorised('permdelete'))
			{
				$this->messageButtons->set('permdelete',
					$this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'permanent')
				);
			}
		}
		elseif ($this->message->isAuthorised('delete'))
		{
			$this->messageButtons->set('delete',
				$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation')
			);
		}

		JPluginHelper::importPlugin('kunena');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onKunenaGetButtons', array('message.action', $this->messageButtons, $this));
	}

	/**
	 * Get button.
	 *
	 * @param   string  $url     Target link (do not route it).
	 * @param   string  $name    Name of the button.
	 * @param   string  $scope   Scope of the button.
	 * @param   string  $type    Type of the button.
	 * @param   int     $id      Id of the button.
	 * @param   bool    $normal  Define if the button will have the class btn or btn-small
	 *
	 * @return  string
	 */
	public function getButton($url, $name, $scope, $type, $id = null, $normal = true)
	{
		return KunenaLayout::factory('Widget/Button')
			->setProperties(array('url' => KunenaRoute::_($url), 'name' => $name, 'scope' => $scope, 'type' => $type, 'id' => $id, 'normal' => $normal, 'icon' => ''));
	}
}
