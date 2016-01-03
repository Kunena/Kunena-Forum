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
 * Class ComponentKunenaControllerTopicModerateDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicModerateDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Moderate';

	/**
	 * @var KunenaForumTopic
	 */
	public $topic;

	/**
	 * @var KunenaForumMessage|null
	 */
	public $message;

	public $uri;

	public $title;

	public $topicIcons;

	public $userLink;

	/**
	 * Prepare topic moderate display.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$catid = $this->input->getInt('catid');
		$id = $this->input->getInt('id');
		$mesid = $this->input->getInt('mesid');

		if (!$mesid)
		{
			$this->topic = KunenaForumTopicHelper::get($id);
			$this->topic->tryAuthorise('move');
		}
		else
		{
			$this->message = KunenaForumMessageHelper::get($mesid);
			$this->message->tryAuthorise('move');
			$this->topic = $this->message->getTopic();
		}

		$this->category = $this->topic->getCategory();

		$this->uri = "index.php?option=com_kunena&view=topic&layout=moderate"
			. "&catid={$this->category->id}&id={$this->topic->id}"
			. ($this->message ? "&mesid={$this->message->id}" : '');
		$this->title = !$this->message ?
			JText::_('COM_KUNENA_TITLE_MODERATE_TOPIC') :
			JText::_('COM_KUNENA_TITLE_MODERATE_MESSAGE');

		// Load topic icons if available.
		if ($this->config->topicicons)
		{
			$this->template = KunenaTemplate::getInstance();
			$this->template->setCategoryIconset();
			$this->topicIcons = $this->template->getTopicIcons(false);
		}

		// Have a link to moderate user as well.
		if (isset($this->message))
		{
			$user = $this->message->getAuthor();

			if ($user->exists())
			{
				$username = $user->getName();
				$this->userLink = $this->message->userid ? JHtml::_('kunenaforum.link',
					'index.php?option=com_kunena&view=user&layout=moderate&userid=' . $this->message->userid,
					$username . ' (' . $this->message->userid . ')', $username . ' (' . $this->message->userid . ')')
					: null;
			}
		}

		if ($this->message)
		{
			$this->banHistory = KunenaUserBan::getUserHistory($this->message->userid);

			$this->me = KunenaFactory::getUser();

			// Get thread and reply count from current message:
			$db = JFactory::getDbo();
			$query = "SELECT COUNT(mm.id) AS replies FROM #__kunena_messages AS m
				INNER JOIN #__kunena_messages AS t ON m.thread=t.id
				LEFT JOIN #__kunena_messages AS mm ON mm.thread=m.thread AND mm.time > m.time
				WHERE m.id={$db->Quote($this->message->id)}";
			$db->setQuery($query, 0, 1);
			$this->replies = $db->loadResult();

			if (KunenaError::checkDatabaseError())
			{
				return;
			}
		}

		$this->banInfo = KunenaUserBan::getInstanceByUserid(JFactory::getUser()->id, true);
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
		$params = $menu_item->params; // get the params
		$params_title = $params->get('page_title');
		$params_keywords = $params->get('menu-meta_keywords');
		$params_description = $params->get('menu-description');

		if (!empty($params_title))
		{
			$title = $params->get('page_title');
			$this->setTitle($title);
		}
		else
		{
			$this->setTitle($this->title);
		}

		if (!empty($params_keywords))
		{
			$keywords = $params->get('menu-meta_keywords');
			$this->setKeywords($keywords);
		}
		else
		{
			$this->setKeywords($this->title);
		}

		if (!empty($params_description))
		{
			$description = $params->get('menu-meta_description');
			$this->setDescription($description);
		}
		else
		{
			$this->setDescription($this->title);
		}
	}
}
