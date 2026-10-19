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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

/**
 * Kunena Backend Badges Controller
 *
 * @since   Kunena 7.2
 */
class BadgesController extends FormController
{
    /**
     * @var     string
     * @since   Kunena 7.2
     */
    protected $baseurl = 'index.php?option=com_kunena&view=badges';

    /**
     * Save the badges and stay on the page (apply) or go to the dashboard (save).
     *
     * @param   string|null  $key     The name of the primary key of the URL variable.
     * @param   string|null  $urlVar  The name of the URL variable if different from the primary key.
     *
     * @return  boolean
     *
     * @throws  Exception
     * @since   Kunena 7.2
     */
    public function save($key = null, $urlVar = null)
    {
        $this->checkToken();

        if (!$this->app->getIdentity()->authorise('core.admin', 'com_kunena') && !$this->app->getIdentity()->authorise('core.options', 'com_kunena')) {
            $this->setRedirect(Route::_('index.php?option=com_kunena&view=cpanel', false), Text::_('JERROR_ALERTNOAUTHOR'), 'error');

            return false;
        }

        $data  = $this->input->post->get('jform', [], 'array');
        $model = $this->getModel('Badges', 'Administrator');
        $form  = $model->getForm($data, false);
        $valid = $form ? $model->validate($form, $data) : false;

        if ($valid === false || !$model->save($valid)) {
            $this->app->enqueueMessage($model->getError() ?: Text::_('JERROR_SAVE_FAILED'), 'error');
            $this->setRedirect(Route::_($this->baseurl, false));

            return false;
        }

        $this->app->enqueueMessage(Text::_('COM_KUNENA_A_BADGES_SAVED'), 'success');

        $this->setRedirect(Route::_($this->task === 'apply' ? $this->baseurl : 'index.php?option=com_kunena&view=cpanel', false));

        return true;
    }

    /**
     * Go back to the dashboard.
     *
     * @param   string|null  $key  The name of the primary key of the URL variable.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public function cancel($key = null)
    {
        $this->setRedirect(Route::_('index.php?option=com_kunena&view=cpanel', false));

        return true;
    }
}
