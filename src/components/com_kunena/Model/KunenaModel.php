<?php
/**
 * @package     Kunena.Site
 * @subpackage  com_kunena
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Site\Model;

defined('_JEXEC') or die;

use Exception;
use JDatabaseQuery;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * This models supports retrieving lists.
 *
 * @since   Kunena 6.0
 */
class KunenaModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @see     \JController
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   Kunena 6.0
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
}
