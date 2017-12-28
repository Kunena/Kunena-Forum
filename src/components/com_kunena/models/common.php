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
defined('_JEXEC') or die();

/**
 * Common Model for Kunena
 *
 * @since  2.0
 */
class KunenaModelCommon extends KunenaModel
{
	/**
	 *
	 */
	protected function populateState()
	{
		$params = $this->getParameters();
		$this->setState('params', $params);
	}
}
