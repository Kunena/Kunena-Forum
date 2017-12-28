<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Models
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Rate Model for Kunena
 *
 * @since  2.0
 */
class KunenaModelRate extends KunenaModel
{

	protected function populateState()
	{
		$id = $this->getInt('topicid', 0);
		$this->setState('item.topicid', $id);

		$value = $this->getInt('limit', 0);

		if ($value < 1)
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
	 * @return KunenaForumRate
	 */
	function getNewRate()
	{
		return new KunenaForumRate;
	}

	/**
	 * @return mixed
	 */
	function getRate()
	{
		return KunenaForumRateHelper::get($this->getState('item.topicid'));
	}

	/**
	 * @return mixed
	 */
	function getRates()
	{
		return KunenaForumRateHelper::getRates($this->getState('list.start'), $this->getState('list.limit'), !$this->me->isModerator());
	}

	/**
	 * @return array
	 */
	public function getRateActions()
	{
		$actions   = array();
		$actions[] = JHtml::_('select.option', 'none', JText::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
		$actions[] = JHtml::_('select.option', 'unpublish', JText::_('COM_KUNENA_BULK_RATE_UNPUBLISH'));
		$actions[] = JHtml::_('select.option', 'publish', JText::_('COM_KUNENA_BULK_RATE_PUBLISH'));
		$actions[] = JHtml::_('select.option', 'delete', JText::_('COM_KUNENA_BULK_RATE_DELETE'));

		return $actions;
	}
}
