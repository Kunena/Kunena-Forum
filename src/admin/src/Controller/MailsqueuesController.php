<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use RuntimeException;

/**
 * Kunena Mailsqueues Controller
 *
 * @since   Kunena 7.1
 */
class MailsqueuesController extends FormController
{
    /**
     * @var     null|string
     * @since   Kunena 7.1
     */
    protected $baseurl = null;

    /**
     * Constructor
     *
     * @param   array  $config  config
     *
     * @throws  Exception
     * @since   Kunena 7.1
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=mailsqueues';
    }

    /**
     * Remove selected mail queue entries
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.1
     */
    public function remove(): void
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cid = $this->input->get('cid', [], 'array');
        $cid = ArrayHelper::toInteger($cid, []);

        if (empty($cid)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_MAILSQUEUE_NO_ROWS_SELECTED'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $query = $db->createQuery()
            ->delete()->from($db->quoteName('#__kunena_plg_task_mailsqueue'))
            ->whereIn($db->quoteName('id'), $cid);

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (RuntimeException $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_A_MAILSQUEUE_DELETED'), 'success');
        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Purge all mail queue entries
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.1
     */
    public function purge(): void
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        if (!Session::checkToken('post')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $query = $db->createQuery()
            ->delete()->from($db->quoteName('#__kunena_plg_task_mailsqueue'));

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (RuntimeException $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_A_MAILSQUEUE_PURGED'), 'success');
        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }
}
