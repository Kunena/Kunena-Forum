<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * Kunena Backend Logs Controller
 *
 * @since   Kunena 5.0
 */
class LogsController extends FormController
{
    /**
     * @var     null|string
     * @since   Kunena 5.0
     */
    protected $baseurl = null;

    /**
     * Construct
     *
     * @param   array  $config  config
     *
     * @throws  Exception
     * @since   Kunena 5.0
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=logs';
    }

    /**
     * Redirect user to the right layout in order to define some settings
     *
     * @return  void
     *
     * @since   Kunena 5.0
     */
    public function cleanEntries(): void
    {
        $this->setRedirect(Route::_("index.php?option=com_kunena&view=log&layout=clean", false));
    }

    /**
     * Method to just redirect to main manager in case of use of cancel button
     *
     * @param   null  $key     key
     * @param   null  $urlVar  url var
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 5.0
     */
    public function cancel($key = null, $urlVar = null)
    {
        $this->app->redirect(KunenaRoute::_($this->baseurl, false));
    }
}
