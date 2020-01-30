<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Model;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Kunena\Forum\Libraries\Forum\Topic\Rate\Helper;
use Kunena\Forum\Libraries\Forum\Topic\Rate\Rate;

/**
 * Rate Model for Kunena
 *
 * @since   Kunena 2.0
 */
class RateModel extends ListModel
{
	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
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
	 * @return  Rate
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getNewRate()
	{
		return new Rate;
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getRate()
	{
		return Helper::get($this->getState('item.topicid'));
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getRateActions()
	{
		$actions   = [];
		$actions[] = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
		$actions[] = HTMLHelper::_('select.option', 'unpublish', Text::_('COM_KUNENA_BULK_RATE_UNPUBLISH'));
		$actions[] = HTMLHelper::_('select.option', 'publish', Text::_('COM_KUNENA_BULK_RATE_PUBLISH'));
		$actions[] = HTMLHelper::_('select.option', 'delete', Text::_('COM_KUNENA_BULK_RATE_DELETE'));

		return $actions;
	}
}
