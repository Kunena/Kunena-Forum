<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerTopicFormHistoryDisplay
 *
 * TODO: merge to another controller...
 */
class ComponentKunenaControllerTopicFormHistoryDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Edit/History';

	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id');

		$this->topic = KunenaForumTopicHelper::get($id);

		$this->history = KunenaForumMessageHelper::getMessagesByTopic($this->topic, 0, (int) $this->config->historylimit, $ordering='DESC');
		$this->replycount = $this->topic->getReplies();
		$this->historycount = count($this->history);
		KunenaForumMessageAttachmentHelper::getByMessage($this->history);
		$userlist = array();
		foreach ($this->history as $message) {
			$userlist[(int) $message->userid] = (int) $message->userid;
		}
		KunenaUserHelper::loadUsers($userlist);

		// Run events
		$params = new JRegistry();
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'history');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array ('kunena.messages', &$this->history, &$params, 0));

		// FIXME: need to improve BBCode class on this...
		$this->attachments = KunenaForumMessageAttachmentHelper::getByMessage($this->history);
		$this->inline_attachments = array();

		$this->headerText = JText::_('COM_KUNENA_POST_EDIT' ) . ' ' . $this->topic->subject;
	}

	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}
}
