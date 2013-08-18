<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Misc
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class ComponentKunenaControllerMessageActionsDisplay extends KunenaControllerDisplay
{
	public $layout;
	/**
	 * @var KunenaForumTopic
	 */
	public $topic;
	public $message;
	public $messageButtons;

	protected function display() {
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Message/Actions')->setProperties($this->getProperties());

		return $content;
	}

	protected function before() {
		$mesid = $this->input->getInt('mesid');
		$me = KunenaUserHelper::getMyself();

		$this->layout = $this->input->getCmd('layout');
		$this->message = KunenaForumMessage::getInstance($mesid);
		$this->topic = $this->message->getTopic();

		$id = $this->message->thread;
		$catid = $this->message->catid;
		$token = JSession::getFormToken();

		$task = "index.php?option=com_kunena&view=topic&task=%s&catid={$catid}&id={$id}&mesid={$mesid}&{$token}=1";
		$layout = "index.php?option=com_kunena&view=topic&layout=%s&catid={$catid}&id={$id}&mesid={$mesid}";

		$this->template = KunenaFactory::getTemplate();
		$this->messageButtons = new JObject();
		$this->message_closed = null;

		// Reply / Quote
		if ($this->message->authorise('reply', null, false)) {
			if ($me->exists() && !KunenaSpamRecaptcha::getInstance()->enabled()) {
				$this->messageButtons->set('quickreply', $this->getButton(sprintf($layout, 'reply'), 'quickreply', 'message', 'communication', "kreply{$mesid}"));
			}
			$this->messageButtons->set('reply', $this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication'));
			$this->messageButtons->set('quote', $this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication'));

		} elseif (!$me->isModerator($this->topic->getCategory())) {
			// User is not allowed to write a post
			$this->message_closed = $this->topic->locked ? JText::_('COM_KUNENA_POST_LOCK_SET') : ($me->exists() ? JText::_('COM_KUNENA_REPLY_USER_REPLY_DISABLED') : JText::_('COM_KUNENA_VIEW_DISABLED'));
		}

		// Thank you
		if($this->message->authorise('thankyou') && !array_key_exists($me->userid, $this->message->thankyou)) {
			$this->messageButtons->set('thankyou', $this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user'));
		}

		// Report this
		if (KunenaFactory::getConfig()->reportmsg && $me->exists()) {
			$this->messageButtons->set('report', $this->getButton(sprintf($layout, 'report'), 'report', 'message', 'user'));
		}

		// Moderation and own post actions
		$this->message->authorise('edit') ? $this->messageButtons->set('edit', $this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation')) : null;
		$this->message->authorise('move') ? $this->messageButtons->set('moderate', $this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation')) : null;
		if ($this->message->hold == 1) {
			$this->message->authorise('approve') ? $this->messageButtons->set('publish', $this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation')) : null;
			$this->message->authorise('delete') ? $this->messageButtons->set('delete', $this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation')) : null;
		} elseif ($this->message->hold == 2 || $this->message->hold == 3) {
			$this->message->authorise('undelete') ? $this->messageButtons->set('undelete', $this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation')) : null;
			$this->message->authorise('permdelete') ? $this->messageButtons->set('permdelete', $this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'permanent')) : null;
		} else {
			$this->message->authorise('delete') ? $this->messageButtons->set('delete', $this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation')) : null;
		}

		JPluginHelper::importPlugin('kunena');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onKunenaGetButtons', array('message.action', $this->messageButtons, $this));

		return true;
	}

	public function getButton($link, $name, $scope, $type, $id = null) {
		return $this->template->getButton(KunenaRoute::_($link), $name, $scope, $type, $id);
	}
}
