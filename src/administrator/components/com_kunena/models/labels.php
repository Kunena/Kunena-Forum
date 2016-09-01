<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Models
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * Labels Model for Kunena
 *
 * @since 5.0
 */
class KunenaAdminModelLabels extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param    array $config An optional associative array of configuration settings.
	 *
	 * @see        JController
	 */
	public function __construct($config = array())
	{

		$this->me = KunenaUserHelper::getMyself();

		parent::__construct();
	}
}
