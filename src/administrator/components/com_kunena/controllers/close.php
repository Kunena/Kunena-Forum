<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Controllers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Cpanel Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerClose extends KunenaController
{
	/**
	 *
	 * @since    2.0.0-BETA2
	 * @var null|string
	 */
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array  $config  config
	 *
	 * @since    2.0.0-BETA2
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena';
	}
}
