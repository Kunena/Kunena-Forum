<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Models
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Announcement Model for Kunena
 *
 * @since		2.0
 */
class KunenaModelAnnouncement extends KunenaModel {
	protected $total = false;

	protected function populateState() {
		$id = $this->getInt ( 'id', 0 );
		$this->setState ( 'item.id', $id );

		$value = $this->getInt ( 'limit', 0 );
		if ($value < 1 || $value > 100) $value = 20;
		$this->setState ( 'list.limit', $value );

		$value = $this->getInt ( 'limitstart', 0 );
		if ($value < 0) $value = 0;
		$this->setState ( 'list.start', $value );
	}

	function getNewAnnouncement() {
		return new KunenaForumAnnouncement;
	}

	function getAnnouncement() {
		return KunenaForumAnnouncementHelper::get($this->getState ( 'item.id' ));
	}

	public function getTotal() {
		if ($this->total === false) return null;

		return $this->total;
	}

	function getAnnouncements() {
		$start = $this->getState ( 'list.start');
		$limit = $this->getState ( 'list.limit');

		$this->total = KunenaForumAnnouncementHelper::getCount(!$this->me->isModerator());

		// If out of range, use last page
		if ($limit && $this->total < $start)
			$start = intval($this->total / $limit) * $limit;

		$announces = KunenaForumAnnouncementHelper::getAnnouncements($start, $limit, !$this->me->isModerator());

		if ($this->total < $start)
			$this->setState('list.start', intval($this->total / $limit) * $limit);

		return $announces;
	}

	public function getannouncementActions() {
		$actions = array();
		$user = KunenaUserHelper::getMyself();
		if ( $user->isModerator()) {
			$actions[] = JHtml::_('select.option', 'none', JText::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
			$actions[] = JHtml::_('select.option', 'unpublish', JText::_('COM_KUNENA_BULK_ANNOUNCEMENT_UNPUBLISH'));
			$actions[] = JHtml::_('select.option', 'publish', JText::_('COM_KUNENA_BULK_ANNOUNCEMENT_PUBLISH'));
			$actions[] = JHtml::_('select.option', 'edit', JText::_('COM_KUNENA_EDIT'));
			$actions[] = JHtml::_('select.option', 'delete', JText::_('COM_KUNENA__BULK_ANNOUNCEMENT_DELETE'));
		}

		return $actions;
	}
}
