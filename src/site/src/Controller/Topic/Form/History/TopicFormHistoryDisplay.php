<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Form\History;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaFinder;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class ComponentTopicControllerFormHistoryDisplay
 *
 * TODO: merge to another controller...
 *
 * @since   Kunena 4.0
 */
class TopicFormHistoryDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/Edit/History';

	/**
	 * Prepare reply history display.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id');
		$me = KunenaUserHelper::getMyself();

		$this->topic   = KunenaTopicHelper::get($id);
		$category      = $this->topic->getCategory();
		$this->history = KunenaMessageHelper::getMessagesByTopic(
			$this->topic,
			0,
			(int) $this->config->historyLimit,
			'DESC'
		);

		$this->replycount   = $this->topic->getReplies();
		$this->historycount = \count($this->history);
		KunenaAttachmentHelper::getByMessage($this->history);
		$userlist = [];

		foreach ($this->history as $message)
		{
			$messages[$message->id]           = $message;
			$userlist[(int) $message->userid] = (int) $message->userid;
		}

		if ($me->exists())
		{
			$pmFinder = new KunenaFinder;
			$pmFinder->filterByMessageIds(array_keys($messages))->order('id');

			if (!$me->isModerator($category))
			{
				$pmFinder->filterByUser($me);
			}

			$pms = $pmFinder->find();

			foreach ($pms as $pm)
			{
				$registry = new Registry($pm->params);
				$posts    = $registry->get('receivers.posts');

				foreach ($posts as $post)
				{
					if (!isset($messages[$post]->pm))
					{
						$messages[$post]->pm = [];
					}

					$messages[$post]->pm[$pm->id] = $pm;
				}
			}
		}

		$this->history = $messages;

		KunenaUserHelper::loadUsers($userlist);

		// Run events
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'history');

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.messages', &$this->history, &$params, 0]);

		// FIXME: need to improve BBCode class on this...
		$this->attachments        = KunenaAttachmentHelper::getByMessage($this->history);
		$this->inline_attachments = [];

		$this->headerText = Text::_('COM_KUNENA_POST_EDIT') . ' ' . $this->topic->subject;
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function prepareDocument()
	{

	}
}
