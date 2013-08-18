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

class ComponentKunenaControllerTopicPollDisplay extends KunenaControllerDisplay
{
	/**
	 * @var KunenaForumTopic
	 */
	public $topic;
	public $poll;

	protected function display() {
		// Display layout with given parameters.
		if ($this->voted || !$this->topic->authorise('poll.vote', null, true)) {
			$content = KunenaLayout::factory('Topic/Poll/Results')->setProperties($this->getProperties());
		} else {
			$content = KunenaLayout::factory('Topic/Poll/Vote')->setProperties($this->getProperties());
		}

		return $content;
	}

	protected function before() {
		parent::before();

		$this->topic = KunenaForumTopicHelper::get($this->input->getInt('id'));
		$this->category = $this->topic->getCategory();
		$this->config = KunenaFactory::getConfig();
		$this->me = KunenaUserHelper::getMyself();

		// need to check if poll is allowed in this category
		if (!$this->config->pollenabled || !$this->topic->poll_id || !$this->category->allow_polls) {
			return false;
		}

		$this->poll = $this->topic->getPoll();
		$this->usercount = $this->poll->getUserCount();
		$this->usersvoted = $this->poll->getUsers();
		$this->voted = $this->poll->getMyVotes();

		$this->users_voted_list = array();
		$this->users_voted_morelist = array();
		if($this->config->pollresultsuserslist && !empty($this->usersvoted)) {
			$i = 0;
			foreach($this->usersvoted as $userid=>$vote) {
				if ( $i <= '4' ) $this->users_voted_list[] = KunenaFactory::getUser(intval($userid))->getLink();
				else $this->users_voted_morelist[] = KunenaFactory::getUser(intval($userid))->getLink();
				$i++;
			}
		}

		return true;
	}
}
