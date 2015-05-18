<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicReportDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicReportDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Report';

	/**
	 * @var KunenaForumTopic
	 */
	public $topic;

	/**
	 * @var KunenaForumMessage|null
	 */
	public $message;

	public $uri;

	/**
	 * Prepare report message form.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id');
		$mesid = $this->input->getInt('mesid');

		$me = KunenaUserHelper::getMyself();

		if (!$this->config->reportmsg)
		{
			// Deny access if report feature has been disabled.
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 404);
		}

		if (!$me->exists())
		{
			// Deny access if user is guest.
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 401);
		}

		if (!$mesid)
		{
			$this->topic = KunenaForumTopicHelper::get($id);
			$this->topic->tryAuthorise();
		}
		else
		{
			$this->message = KunenaForumMessageHelper::get($mesid);
			$this->message->tryAuthorise();
			$this->topic = $this->message->getTopic();
		}

		$this->category = $this->topic->getCategory();

		$this->uri = "index.php?option=com_kunena&view=topic&layout=report&catid={$this->category->id}" .
			"&id={$this->topic->id}" . ($this->message ? "&mesid={$this->message->id}" : '');
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$this->setTitle(JText::_('COM_KUNENA_REPORT_TO_MODERATOR'));
	}
}
