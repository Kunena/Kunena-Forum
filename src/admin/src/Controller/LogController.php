<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
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
 * Kunena Backend Log Controller
 *
 * @since   Kunena 6.1
 */
class LogController extends FormController
{
    /**
     * @var     null|string
     * @since   Kunena 6.1
     */
    protected $baseurl = null;

    /**
     * Construct
     *
     * @param   array  $config  config
     *
     * @throws  Exception
     * @since   Kunena 6.1
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=logs';
    }

    /**
     * Clean
     *
     * @return bool
     *
     * @since   Kunena 6.1
     * @throws \Exception
     */
    public function clean()
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return false;
        }

        $days      = $this->input->getInt('clean_days', 0);
        $timestamp = new Date('now -' . $days . ' days');

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true)
            ->delete('#__kunena_logs')
            ->where('time < ' . $timestamp->toUnix());

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (Exception $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return false;
        }

        $numRows = $db->getAffectedRows();

        if ($numRows > 0) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_LOG_ENTRIES_DELETED', $numRows), 'success');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_LOG_ENTRIES_DELETED_NOTHING_TO_DELETE'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));
        }
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
     * @since   Kunena 6.1
     */
    public function cancel($key = null, $urlVar = null)
    {
        $this->app->redirect(KunenaRoute::_($this->baseurl, false));
    }
}
