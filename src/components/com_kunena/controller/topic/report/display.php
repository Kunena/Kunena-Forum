<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
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
		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->params;
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
				$this->setTitle(JText::_('COM_KUNENA_REPORT_TO_MODERATOR'));
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$this->setKeywords(JText::_('COM_KUNENA_REPORT_TO_MODERATOR'));
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription(JText::_('COM_KUNENA_REPORT_TO_MODERATOR'));
			}
		}
	}
}
