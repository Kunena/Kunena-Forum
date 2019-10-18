<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerTopicModerateDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicModerateDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Topic/Moderate';

	/**
	 * @var KunenaForumTopic
	 * @since Kunena
	 */
	public $topic;

	/**
	 * @var KunenaForumMessage|null
	 * @since Kunena
	 */
	public $message;

	/**
	 * @var
	 * @since Kunena
	 */
	public $uri;

	/**
	 * @var
	 * @since Kunena
	 */
	public $title;

	/**
	 * @var
	 * @since Kunena
	 */
	public $topicIcons;

	/**
	 * @var
	 * @since Kunena
	 */
	public $userLink;

	/**
	 * Prepare topic moderate display.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$catid = $this->input->getInt('catid');
		$id    = $this->input->getInt('id');
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

		if ($this->config->read_only)
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		$this->category = $this->topic->getCategory();

		$this->uri   = "index.php?option=com_kunena&view=topic&layout=moderate"
			. "&catid={$this->category->id}&id={$this->topic->id}"
			. ($this->message ? "&mesid={$this->message->id}" : '');
		$this->title = !$this->message ?
			Text::_('COM_KUNENA_TITLE_MODERATE_TOPIC') :
			Text::_('COM_KUNENA_TITLE_MODERATE_MESSAGE');

		$this->template = KunenaTemplate::getInstance();
		$this->template->setCategoryIconset($this->topic->getCategory()->iconset);
		$this->topicIcons = $this->template->getTopicIcons(false);

		// Have a link to moderate user as well.
		if (isset($this->message))
		{
			$user = $this->message->getAuthor();

			if ($user->exists())
			{
				$username       = $user->getName();
				$this->userLink = $this->message->userid ? HTMLHelper::_('kunenaforum.link',
					'index.php?option=com_kunena&view=user&layout=moderate&userid=' . $this->message->userid,
					$username . ' (' . $this->message->userid . ')', $username . ' (' . $this->message->userid . ')'
				)
					: null;
			}
		}

		if ($this->message)
		{
			$this->banHistory = KunenaUserBan::getUserHistory($this->message->userid);
			$this->me = Factory::getApplication()->getIdentity();

			// Get thread and reply count from current message:
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(mm.id) AS replies')
				->from($db->quoteName('#__kunena_messages', 'm'))
				->innerJoin($db->quoteName('#__kunena_messages', 't') . ' ON m.thread=t.id')
				->leftJoin($db->quoteName('#__kunena_messages', 'mm') . ' ON mm.thread=m.thread AND mm.time > m.time')
				->where('m.id=' . $db->quote($this->message->id));
			$query->setLimit(1);
			$db->setQuery($query);

			try
			{
				$this->replies = $db->loadResult();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);

				return;
			}
		}

		$this->banInfo = KunenaUserBan::getInstanceByUserid($this->app->getIdentity()->id, true);
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$menu_item = $this->app->getMenu()->getActive();

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
}
