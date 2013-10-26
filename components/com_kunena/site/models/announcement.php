<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Models
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Announcement Model for Kunena
 *
 * @since  2.0
 */
class KunenaModelAnnouncement extends KunenaModel
{
	protected $total = false;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @see JModelLegacy::populateState()
	 * @return  void
	 */
	protected function populateState()
	{
		$id = $this->getInt('id', 0);
		$this->setState('item.id', $id);

		$value = $this->getInt('limit', 0);

		if ($value < 1 || $value > 100)
		{
			$value = 20;
		}

		$this->setState('list.limit', $value);

		$value = $this->getInt('limitstart', 0);

		if ($value < 0)
		{
			$value = 0;
		}

		$this->setState('list.start', $value);
	}

	/**
	 * Method to return a new KunenaForumAnnouncement object
	 *
	 * @return KunenaForumAnnouncement
	 */
	function getNewAnnouncement()
	{
		return new KunenaForumAnnouncement;
	}

	/**
	 * Method to return a KunenaForumAnnouncement object with Id given
	 *
	 * @return KunenaForumAnnouncement object
	 */
	function getAnnouncement()
	{
		return KunenaForumAnnouncementHelper::get($this->getState('item.id'));
	}

	/**
	 * Method to return the number total of announcements
	 *
	 * @return int
	 */
	public function getTotal()
	{
		if ($this->total === false)
		{
			return null;
		}

		return $this->total;
	}

	/**
	 * Method to return KunenaForumAnnouncement objects
	 *
	 * @return multitype:KunenaForumAnnouncement
	 */
	function getAnnouncements()
	{
		$start = $this->getState('list.start');
		$limit = $this->getState('list.limit');

		$this->total = KunenaForumAnnouncementHelper::getCount(!$this->me->isModerator());

		// If out of range, use last page
		if ($limit && $this->total < $start)
		{
			$start = intval($this->total / $limit) * $limit;
		}

		$announces = KunenaForumAnnouncementHelper::getAnnouncements($start, $limit, !$this->me->isModerator());

		if ($this->total < $start)
		{
			$this->setState('list.start', intval($this->total / $limit) * $limit);
		}

		return $announces;
	}

	/**
	 * Method to get the select list for moderators actions
	 *
	 * @return multitype:mixed
	 */
	public function getannouncementActions()
	{
		$actions = array();
		$user = KunenaUserHelper::getMyself();

		if ( $user->isModerator())
		{
			$actions[] = JHtml::_('select.option', 'none', JText::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
			$actions[] = JHtml::_('select.option', 'unpublish', JText::_('COM_KUNENA_BULK_ANNOUNCEMENT_UNPUBLISH'));
			$actions[] = JHtml::_('select.option', 'publish', JText::_('COM_KUNENA_BULK_ANNOUNCEMENT_PUBLISH'));
			$actions[] = JHtml::_('select.option', 'edit', JText::_('COM_KUNENA_EDIT'));
			$actions[] = JHtml::_('select.option', 'delete', JText::_('COM_KUNENA__BULK_ANNOUNCEMENT_DELETE'));
		}

		return $actions;
	}
}
