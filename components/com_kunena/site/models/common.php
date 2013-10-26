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
 * Common Model for Kunena
 *
 * @since  2.0
 */
class KunenaModelCommon extends KunenaModel
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * @see JModelLegacy::populateState()
	 * @return  void
	 */
	protected function populateState()
	{
		$params = $this->getParameters();
		$this->setState('params', $params);
	}
}
