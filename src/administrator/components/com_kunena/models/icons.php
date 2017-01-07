<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Models
 *
 * @copyright     Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * Icons Model for Kunena
 *
 * @since 5.1
 */
class KunenaAdminModelIcons extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      Kunena
	 */
	public function __construct($config = array())
	{

		$this->me = KunenaUserHelper::getMyself();

		parent::__construct();
	}
}
