<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicFormHistoryDisplay
 *
 * TODO: merge to another controller...
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicFormHistoryDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Edit/History';

	/**
	 * Prepare reply history display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id');

		$this->topic = KunenaForumTopicHelper::get($id);
		$this->history = KunenaForumMessageHelper::getMessagesByTopic(
			$this->topic, 0, (int) $this->config->historylimit, 'DESC'
		);

		$this->replycount = $this->topic->getReplies();
		$this->historycount = count($this->history);
		KunenaAttachmentHelper::getByMessage($this->history);
		$userlist = array();

		foreach ($this->history as $message)
		{
			$userlist[(int) $message->userid] = (int) $message->userid;
		}

		KunenaUserHelper::loadUsers($userlist);

		// Run events
		$params = new JRegistry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'history');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array ('kunena.messages', &$this->history, &$params, 0));

		// FIXME: need to improve BBCode class on this...
		$this->attachments = KunenaAttachmentHelper::getByMessage($this->history);
		$this->inline_attachments = array();

		$this->headerText = JText::_('COM_KUNENA_POST_EDIT') . ' ' . $this->topic->subject;
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item

		if ($menu_item)
		{
			$params             = $menu_item->params; // get the params
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

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
		}
	}
}
