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
 * Class ComponentKunenaControllerTopicItemActionsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicItemActionsDisplay extends KunenaControllerDisplay
{
	protected $name = 'Topic/Item/Actions';

	/**
	 * @var KunenaForumTopic
	 */
	public $topic;

	public $topicButtons;

	/**
	 * Prepare topic actions display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id');

		$this->topic = KunenaForumTopic::getInstance($id);

		$catid = $this->topic->category_id;
		$token = JSession::getFormToken();

		$task = "index.php?option=com_kunena&view=topic&task=%s&catid={$catid}&id={$id}&{$token}=1";
		$layout = "index.php?option=com_kunena&view=topic&layout=%s&catid={$catid}&id={$id}";

		$userTopic = $this->topic->getUserTopic();
		$this->template = KunenaFactory::getTemplate();
		$this->topicButtons = new JObject;

		if ($this->topic->isAuthorised('reply'))
		{
			// Add Reply topic button.
			$this->topicButtons->set('reply',
				$this->getButton(sprintf($layout, 'reply'), 'reply', 'topic', 'communication')
			);
		}

		if ($userTopic->subscribed)
		{
			// User can always remove existing subscription.
			$this->topicButtons->set('subscribe',
				$this->getButton(sprintf($task, 'unsubscribe'), 'unsubscribe', 'topic', 'user')
			);
		}
		elseif ($this->topic->isAuthorised('subscribe'))
		{
			// Add subscribe topic button.
			$this->topicButtons->set('subscribe',
				$this->getButton(sprintf($task, 'subscribe'), 'subscribe', 'topic', 'user')
			);
		}

		if ($userTopic->favorite)
		{
			// User can always remove existing favorite.
			$this->topicButtons->set('favorite',
				$this->getButton(sprintf($task, 'unfavorite'), 'unfavorite', 'topic', 'user')
			);
		}
		elseif ($this->topic->isAuthorised('favorite'))
		{
			// Add favorite topic button.
			$this->topicButtons->set('favorite',
				$this->getButton(sprintf($task, 'favorite'), 'favorite', 'topic', 'user')
			);
		}

		if ($this->topic->getCategory()->isAuthorised('moderate'))
		{
			// Add moderator specific buttons.
			$sticky = $this->topic->ordering ? 'unsticky' : 'sticky';
			$lock = $this->topic->locked ? 'unlock' : 'lock';

			$this->topicButtons->set('sticky',
				$this->getButton(sprintf($task, $sticky), $sticky, 'topic', 'moderation')
			);
			$this->topicButtons->set('lock',
				$this->getButton(sprintf($task, $lock), $lock, 'topic', 'moderation')
			);
			$this->topicButtons->set('moderate',
				$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'topic', 'moderation')
			);

			if ($this->topic->hold == 1)
			{
				$this->topicButtons->set('approve',
					$this->getButton(sprintf($task, 'approve'), 'moderate', 'topic', 'moderation')
				);
			}

			if ($this->topic->hold == 1 || $this->topic->hold == 0)
			{
				$this->topicButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'topic', 'moderation')
				);
			}
			elseif ($this->topic->hold == 2 || $this->topic->hold == 3)
			{
				$this->topicButtons->set('undelete',
					$this->getButton(sprintf($task, 'undelete'), 'undelete', 'topic', 'moderation')
				);
			}
		}

		// Add buttons for changing between different layout modes.
		if (KunenaFactory::getConfig()->enable_threaded_layouts)
		{
			$url = "index.php?option=com_kunena&view=user&task=change&topic_layout=%s&{$token}=1";

			if ($this->layout != 'default')
			{
				$this->topicButtons->set('flat',
					$this->getButton(sprintf($url, 'flat'), 'flat', 'layout', 'user')
				);
			}

			if ($this->layout != 'threaded')
			{
				$this->topicButtons->set('threaded',
					$this->getButton(sprintf($url, 'threaded'), 'threaded', 'layout', 'user')
				);
			}

			if ($this->layout != 'indented')
			{
				$this->topicButtons->set('indented',
					$this->getButton(sprintf($url, 'indented'), 'indented', 'layout', 'user')
				);
			}
		}

		JPluginHelper::importPlugin('kunena');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onKunenaGetButtons', array('topic.action', $this->topicButtons, $this));
	}

	/**
	 * Get button.
	 *
	 * @param   string  $url      Target link (do not route it).
	 * @param   string  $name     Name of the button.
	 * @param   string  $scope    Scope of the button.
	 * @param   string  $type     Type of the button.
	 * @param   bool    $primary  True if primary button.
	 * @param   bool    $normal   Define if the button will have the class btn or btn-small
	 *
	 * @return  string
	 */
	public function getButton($url, $name, $scope, $type, $primary = false, $normal = true)
	{
		return KunenaLayout::factory('Widget/Button')
			->setProperties(array('url' => KunenaRoute::_($url), 'name' => $name, 'scope' => $scope, 'type' => $type, 'primary' => $primary, 'normal' => $normal, 'icon' => ''));
	}
}
