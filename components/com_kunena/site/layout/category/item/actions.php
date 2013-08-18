<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Layout.Category.Item.Actions
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLayoutCategoryItemActions extends KunenaLayout
{
	function render($layout = null) {
		$token = '&' . JSession::getFormToken() . '=1';
		$this->categoryButtons = array();

		// Is user allowed to post new topic?
		if ($this->category->getNewTopicCategory()->exists()) {
			$url = "index.php?option=com_kunena&view=topic&layout=create&catid={$this->category->id}";
			$this->categoryButtons['create'] = $this->getButton($url, 'create', 'topic', 'communication');
		}

		// Is user allowed to mark forums as read?
		if ($this->me->exists() && $this->total) {
			$url = "index.php?option=com_kunena&view=category&task=markread&catid={$this->category->id}{$token}";
			$this->categoryButtons['markread'] = $this->getButton($url, 'markread', 'category', 'user');
		}

		// Is user allowed to subscribe category?
		if ($this->category->authorise ( 'subscribe', null, true )) {
			$subscribed = $this->category->getSubscribed($this->me->userid);

			if (!$subscribed) {
				$url = "index.php?option=com_kunena&view=category&task=subscribe&catid={$this->category->id}{$token}";
				$this->categoryButtons['subscribe'] = $this->getButton($url, 'subscribe', 'category', 'user');
			} else {
				$url = "index.php?option=com_kunena&view=category&task=unsubscribe&catid={$this->category->id}{$token}";
				$this->categoryButtons['unsubscribe'] = $this->getButton($url, 'unsubscribe', 'category', 'user');
			}
		}
		parent::render($layout);
	}
}
