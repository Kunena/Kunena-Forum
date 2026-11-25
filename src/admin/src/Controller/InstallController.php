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
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Install\KunenaSchemaUpdate;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * Git Install: the update of the database of a site which runs Kunena from a copy of the code.
 *
 * @since   Kunena 8.0
 */
class InstallController extends BaseController
{
    /**
     * Applies the statements of the update files which the database does not have yet.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function update(): void
    {
        $this->setRedirect(KunenaRoute::_('administrator/index.php?option=com_kunena&view=install', false));

        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

            return;
        }

        // It runs statements on the database: an administrator, on an internal version only.
        if (!$this->app->getIdentity()->authorise('core.admin', 'com_kunena') || !KunenaForum::isInternal()) {
            $this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');

            return;
        }

        $result = KunenaSchemaUpdate::updateOnSite();

        $this->app->enqueueMessage(Text::plural('COM_KUNENA_GIT_INSTALL_N_FIXED', $result['fixed'], $result['version']), $result['fixed'] ? 'success' : 'info');

        foreach ($result['failed'] as $query) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_GIT_INSTALL_FAILED', htmlspecialchars($query, ENT_QUOTES, 'UTF-8')), 'error');
        }
    }
}
