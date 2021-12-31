<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Model;

use Joomla\CMS\MVC\Model\ListModel;

\defined('_JEXEC') or die();

/**
 * Common Model for Kunena
 *
 * @since   Kunena 2.0
 */
class CommonModel extends ListModel
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   null  $ordering
	 * @param   null  $direction
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function populateState($ordering = null, $direction = null): void
	{
		$params = $this->getParameters();
		$this->setState('params', $params);
	}
}
