<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Models
 *
 * @copyright     Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * Block IPs Model for Kunena
 *
 * @since 5.1
 */
class KunenaAdminModelBlockips extends Joomla\CMS\MVC\Model\ListModel
{
	/**
	 * Constructor.
	 *
	 * @see        JController
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since      5.1
	 * @throws Exception
	 */
	public function __construct($config = array())
	{
		$this->me = KunenaUserHelper::getMyself();

		parent::__construct();
	}
}
