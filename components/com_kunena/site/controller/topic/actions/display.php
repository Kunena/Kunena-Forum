<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Misc
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class ComponentKunenaControllerTopicActionsDisplay extends KunenaControllerDisplay
{
	public $layout;
	/**
	 * @var KunenaForumTopic
	 */
	public $topic;
	public $topicButtons;

	protected function display() {
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Topic/Actions')->setProperties($this->getProperties());

		return $content;
	}

	protected function before() {
		$id = $this->input->getInt('id');

		$this->layout = $this->input->getCmd('layout');
		$this->topic = KunenaForumTopic::getInstance($id);

		$catid = $this->topic->category_id;
		$token = JSession::getFormToken();

		$task = "index.php?option=com_kunena&view=topic&task=%s&catid={$catid}&id={$id}&{$token}=1";
		$layout = "index.php?option=com_kunena&view=topic&layout=%s&catid={$catid}&id={$id}";

		$this->template = KunenaFactory::getTemplate();
		$this->topicButtons = new JObject();

		// Reply topic
		if ($this->topic->authorise('reply')) {
			// this user is allowed to reply to this topic
			$this->topicButtons->set('reply', $this->getButton(sprintf($layout, 'reply'), 'reply', 'topic', 'communication'));
		}

		$usertopic = $this->topic->getUserTopic();

		// Subscribe topic
		if ($usertopic->subscribed) {
			// this user is allowed to unsubscribe
			$this->topicButtons->set('subscribe', $this->getButton(sprintf($task, 'unsubscribe'), 'unsubscribe', 'topic', 'user'));
		} elseif ($this->topic->authorise('subscribe')) {
			// this user is allowed to subscribe
			$this->topicButtons->set('subscribe', $this->getButton(sprintf($task, 'subscribe'), 'subscribe', 'topic', 'user'));
		}

		// Favorite topic
		if ($usertopic->favorite) {
			// this user is allowed to unfavorite
			$this->topicButtons->set('favorite', $this->getButton(sprintf($task, 'unfavorite'), 'unfavorite', 'topic', 'user'));
		} elseif ($this->topic->authorise('favorite')) {
			// this user is allowed to add a favorite
			$this->topicButtons->set('favorite', $this->getButton(sprintf($task, 'favorite'), 'favorite', 'topic', 'user'));
		}

		// Moderator specific buttons
		if ($this->topic->getCategory()->authorise('moderate')) {
			$sticky = $this->topic->ordering ? 'unsticky' : 'sticky';
			$lock = $this->topic->locked ? 'unlock' : 'lock';

			$this->topicButtons->set('sticky', $this->getButton(sprintf($task, $sticky), $sticky, 'topic', 'moderation'));
			$this->topicButtons->set('lock', $this->getButton(sprintf($task, $lock), $lock, 'topic', 'moderation'));
			$this->topicButtons->set('moderate', $this->getButton(sprintf($layout, 'moderate'), 'moderate', 'topic', 'moderation'));
			if ($this->topic->hold == 1) {
				$this->topicButtons->set('approve', $this->getButton(sprintf($task, 'approve'), 'moderate', 'topic',
					'moderation'));
			}
			if ($this->topic->hold == 1 || $this->topic->hold == 0) {
				$this->topicButtons->set('delete', $this->getButton(sprintf($task, 'delete'), 'delete', 'topic', 'moderation'));
			} elseif ($this->topic->hold == 2 || $this->topic->hold == 3) {
				$this->topicButtons->set('undelete', $this->getButton ( sprintf($task, 'undelete'), 'undelete', 'topic', 'moderation'));
			}
		}

		if (KunenaFactory::getConfig()->enable_threaded_layouts) {

			$url = "index.php?option=com_kunena&view=user&task=change&topic_layout=%s&{$token}=1";
			if ($this->layout != 'default') {
				$this->topicButtons->set('flat', $this->getButton(sprintf($url, 'flat'), 'flat', 'layout', 'user'));
			}
			if ($this->layout != 'threaded') {
				$this->topicButtons->set('threaded', $this->getButton(sprintf($url, 'threaded'), 'threaded', 'layout', 'user'));
			}
			if ($this->layout != 'indented') {
				$this->topicButtons->set('indented', $this->getButton(sprintf($url, 'indented'), 'indented', 'layout', 'user'));
			}
		}

		JPluginHelper::importPlugin('kunena');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onKunenaGetButtons', array('topic.action', $this->topicButtons, $this));

		return true;
	}

	public function getButton($link, $name, $scope, $type, $id = null) {
		return $this->template->getButton(KunenaRoute::_($link), $name, $scope, $type, $id);
	}
}
