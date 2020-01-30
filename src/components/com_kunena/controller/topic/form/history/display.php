<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Form\History;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Attachment\Helper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Joomla\Registry\Registry;
use function defined;

/**
 * Class ComponentTopicControllerFormHistoryDisplay
 *
 * TODO: merge to another controller...
 *
 * @since   Kunena 4.0
 */
class ComponentTopicControllerFormHistoryDisplay extends KunenaControllerDisplay
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
	 * @throws  \Exception
	 */
	protected function before()
	{
		parent::before();

		$id       = $this->input->getInt('id');
		$this->me = \Kunena\Forum\Libraries\User\Helper::getMyself();

		$this->topic    = \Kunena\Forum\Libraries\Forum\Topic\Helper::get($id);
		$this->category = $this->topic->getCategory();
		$this->history  = \Kunena\Forum\Libraries\Forum\Message\Helper::getMessagesByTopic(
			$this->topic, 0, (int) $this->config->historylimit, 'DESC'
		);

		$this->replycount   = $this->topic->getReplies();
		$this->historycount = count($this->history);
		Helper::getByMessage($this->history);
		$userlist = [];

		foreach ($this->history as $message)
		{
			$messages[$message->id]           = $message;
			$userlist[(int) $message->userid] = (int) $message->userid;
		}

		if ($this->me->exists())
		{
			$pmFinder = new \Kunena\Forum\Libraries\KunenaPrivate\Message\Finder;
			$pmFinder->filterByMessageIds(array_keys($messages))->order('id');

			if (!$this->me->isModerator($this->category))
			{
				$pmFinder->filterByUser($this->me);
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

		\Kunena\Forum\Libraries\User\Helper::loadUsers($userlist);

		// Run events
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'history');

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.messages', &$this->history, &$params, 0]);

		// FIXME: need to improve BBCode class on this...
		$this->attachments        = Helper::getByMessage($this->history);
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
