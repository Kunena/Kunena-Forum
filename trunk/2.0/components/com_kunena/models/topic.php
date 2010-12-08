<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.model' );
kimport('kunena.forum.category.helper');
kimport('kunena.forum.topic.helper');
kimport('kunena.user.helper');

/**
 * Topic Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelTopic extends KunenaModel {
	protected $topics = false;
	protected $items = false;

	protected function populateState() {
		$app = JFactory::getApplication ();
		$me = KunenaUserHelper::get();
		$config = KunenaFactory::getConfig ();
		$layout = JRequest::getCmd ( 'layout', 'default' );

		$catid = JRequest::getInt ( 'catid', 0 );
		$this->setState ( 'item.catid', $catid );

		$value = JRequest::getInt ( 'id', 0 );
		$this->setState ( 'item.id', $value );

		$access = KunenaFactory::getAccessControl();
		$value = $access->getAllowedHold($me, $catid);
		$this->setState ( 'hold', $value );

		$value = $app->getUserStateFromRequest ( "com_kunena.topic.{$layout}.list.limit", 'limit', 0, 'int' );
		if ($value < 1) $value = $config->messages_per_page;
		$this->setState ( 'list.limit', $value );

		$value = $app->getUserStateFromRequest ( "com_kunena.topic.{$layout}.list.ordering", 'filter_order', 'time', 'cmd' );
		//$this->setState ( 'list.ordering', $value );

		$value = $app->getUserStateFromRequest ( "com_kunena.topic.{$layout}.list.start", 'limitstart', 0, 'int' );
		//$value = JRequest::getInt ( 'limitstart', 0 );
		$this->setState ( 'list.start', $value );

		$value = $app->getUserStateFromRequest ( "com_kunena.topic.{$layout}.list.direction", 'filter_order_Dir', 'desc', 'word' );
		if ($me->ordering != '0') {
			$value = $me->ordering == '1' ? 'desc' : 'asc';
		} else {
			$value = $config->default_sort == 'asc' ? 'asc' : 'desc';
		}
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );
	}

	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->getState ( 'item.catid'));
	}

	public function getTopic() {
		return KunenaForumTopicHelper::get($this->getState ( 'item.id'));
	}

	public function getMessages() {
		return KunenaForumMessageHelper::getMessagesByTopic($this->getState ( 'item.id'),
			$this->getState ( 'list.start'), $this->getState ( 'list.limit'), $this->getState ( 'list.direction'), $this->getState ( 'hold'));
	}
}