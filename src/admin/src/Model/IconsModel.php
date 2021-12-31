<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Models
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Icons Model for Kunena
 *
 * @since 5.1
 */
class IconsModel extends AdminModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 * @see     JController
	 */
	public function __construct($config = [])
	{
		parent::__construct();
	}

	/**
	 * @inheritDoc
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load data
	 *
	 * @return void
	 *
	 * @since  Kunena 6.0
	 */
	public function getForm($data = [], $loadData = true)
	{
		// TODO: Implement getForm() method.
	}
}
