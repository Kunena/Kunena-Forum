<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\Utilities\ArrayHelper;
use KunenaRoute;
use Text;

require_once __DIR__ . '/CategoriesController.php';

/**
 * Kunena Category Controller
 *
 * @since   Kunena 6.0
 */
class CategoryController extends AdminController
{
	/**
	 * @var     string
	 * @since   Kunena 2.0.0-BETA2
	 */
	protected $baseurl = null;

	/**
	 * @var     string
	 * @since   Kunena 2.0.0-BETA2
	 */
	protected $baseurl2 = null;

	/**
	 * Constructor
	 *
	 * @param   array  $config  config
	 *
	 * @since   Kunena 2.0.0-BETA2
	 *
	 * @throws  Exception
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->baseurl  = 'administrator/index.php?option=com_kunena&view=categories';

		$this->setRedirectBack(KunenaRoute::_($this->baseurl, false));
	}
}
