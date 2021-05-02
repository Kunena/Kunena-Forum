<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Form\History;

defined('_JEXEC') or die();

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
use function defined;

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
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id');
		$me = KunenaUserHelper::getMyself();

		$topic    = KunenaTopicHelper::get($id);
		$category = $topic->getCategory();
		$history  = KunenaMessageHelper::getMessagesByTopic(
			$topic, 0, (int) $this->config->historyLimit, 'DESC'
		);

		$replycount   = $topic->getReplies();
		$historycount = count($history);
		KunenaAttachmentHelper::getByMessage($history);
		$userlist = [];

		foreach ($history as $message)
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

		$history = $messages;

		KunenaUserHelper::loadUsers($userlist);

		// Run events
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'history');

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.messages', &$history, &$params, 0]);

		// FIXME: need to improve BBCode class on this...
		$attachments        = KunenaAttachmentHelper::getByMessage($history);
		$inline_attachments = [];

		$headerText = Text::_('COM_KUNENA_POST_EDIT') . ' ' . $topic->subject;
	}

	/**
	 * Prepare document.
	 *
	 * @return  void|boolean
	 *
	 * @since   Kunena 6.0
	 */
	protected function prepareDocument()
	{

	}
}
