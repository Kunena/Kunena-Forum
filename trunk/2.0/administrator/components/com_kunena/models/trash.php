<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );
kimport('kunena.model');
kimport('kunena.forum.topic.helper');
kimport('kunena.forum.message.helper');

/**
 * Trash Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminModelTrash extends KunenaModel {
	protected $__state_set = false;
	protected $_items = false;
	protected $_items_order = false;
	protected $_object = false;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication ();

		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.trash.list.limit", 'limit', $app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.trash.list.ordering', 'filter_order', 'id', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.trash.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.trash.list.direction', 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.trash.list.search', 'search', '', 'string' );
		$this->setState ( 'list.search', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.trash.list.levels", 'levellimit', 10, 'int' );
		$this->setState ( 'list.levels', $value );
	}

	/**
	 * Method to get all deleted messages.
	 *
	 * @return	Array
	 * @since	1.6
	 */
	 public function getMessagesItems() {
		$cats = KunenaForumCategoryHelper::getCategories();
		$cats_array =array();
		foreach ($cats as $cat) {
			if ( $cat->id ) $cats_array[] = $cat->id;
		}
		list($total,$messages) = KunenaForumMessageHelper::getLatestMessages($cats_array, $this->getState('list.start'), $this->getState('list.limit'), array ('hold' => '2,3'));
		$this->setState ( 'list.total', $total );
		return $messages;
	}

	/**
	 * Method to get all deleted topics.
	 *
	 * @return	Array
	 * @since	1.6
	 */
	public function getTopicsItems() {
		/*$db = JFactory::getDBO ();
		$where = '';
		if ($this->getState ( 'list.search')) {
			$where = ' AND LOWER( subject ) LIKE '.$db->Quote( '%'.$db->getEscaped( $this->getState ( 'list.search'), true ).'%', false ).' OR LOWER( username )LIKE '.$db->Quote( '%'.$db->getEscaped( $this->getState ( 'list.search'), true ).'%', false ).' OR id LIKE '.$db->Quote( '%'.$db->getEscaped( $this->getState ( 'list.search'), true ).'%', false );
		}*/

		$cats = KunenaForumCategoryHelper::getCategories();
		$cats_array =array();
		foreach ($cats as $cat) {
			if ( $cat->id ) $cats_array[] = $cat->id;
		}
		list($total,$topics) = KunenaForumTopicHelper::getLatestTopics ( $cats_array, $this->getState('list.start'), $this->getState('list.limit'), array ('hold' => '2,3') );
		$this->setState ( 'list.total', $total );

		return $topics;
	}

	/**
	 * Method to get details on selected items.
	 *
	 * @return	Array
	 * @since	1.6
	 */
	public function getPurgeItems() {
		kimport('kunena.error');

		$app = JFactory::getApplication ();

		$ids = $app->getUserState ( 'com_kunena.purge' );
		$topic = $app->getUserState('com_kunena.topic');
		$message = $app->getUserState('com_kunena.message');

		$ids = implode ( ',', $ids );

		if ( $topic ) {
			$items = KunenaForumTopicHelper::getTopics($ids);
		} elseif ( $message ) {
			$items = KunenaForumMessageHelper::getMessages($ids);
		} else {

		}

		return $items;
	}

	/**
	 * Method to hash datas.
	 *
	 * @return	hash
	 * @since	1.6
	 */
	public function getMd5() {
		$app = JFactory::getApplication ();
		$ids = $app->getUserState ( 'com_kunena.purge' );

		return md5(serialize($ids));
	}

	public function getNavigation() {
		jimport ( 'joomla.html.pagination' );
		$navigation = new JPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
