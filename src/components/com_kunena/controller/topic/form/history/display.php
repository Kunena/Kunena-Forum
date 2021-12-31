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
 * Class ComponentKunenaControllerTopicFormHistoryDisplay
 *
 * TODO: merge to another controller...
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicFormHistoryDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Topic/Edit/History';

	/**
	 * Prepare reply history display.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id');

		$this->topic   = KunenaForumTopicHelper::get($id);
		$this->history = KunenaForumMessageHelper::getMessagesByTopic(
			$this->topic, 0, (int) $this->config->historylimit, 'DESC'
		);

		$this->replycount   = $this->topic->getReplies();
		$this->historycount = count($this->history);
		KunenaAttachmentHelper::getByMessage($this->history);
		$userlist = array();

		foreach ($this->history as $message)
		{
			$userlist[(int) $message->userid] = (int) $message->userid;
		}

		KunenaUserHelper::loadUsers($userlist);

		// Run events
		$params = new \Joomla\Registry\Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'history');

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', array('kunena.messages', &$this->history, &$params, 0));

		// FIXME: need to improve BBCode class on this...
		$this->attachments        = KunenaAttachmentHelper::getByMessage($this->history);
		$this->inline_attachments = array();

		$this->headerText = Text::_('COM_KUNENA_POST_EDIT') . ' ' . $this->topic->subject;
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @since Kunena
	 */
	protected function prepareDocument()
	{

	}
}
