<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Install;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Install\KunenaSchemaUpdate;

/**
 * Git Install, for the developers of Kunena: the database of a site which runs Kunena from a copy of the code, updated with
 * the statements of the update files which it does not have yet. Only in an internal version.
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The state of the database (KunenaSchemaUpdate::statusOnSite()).
     *
     * @since   Kunena 8.0
     */
    public array $status = [];

    /**
     * May the user update the database (core.admin)?
     *
     * @since   Kunena 8.0
     */
    public bool $canAdmin = false;

    /**
     * @param   string|null  $tpl  The template.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function display($tpl = null): void
    {
        $app = Factory::getApplication();

        // A tool for the developers: never on a site which runs a release.
        if (!KunenaForum::isInternal()) {
            $app->enqueueMessage(Text::_('COM_KUNENA_GIT_INSTALL_NOT_INTERNAL'), 'warning');
            $app->redirect(Route::_('index.php?option=com_kunena&view=tools', false));

            return;
        }

        $this->status   = KunenaSchemaUpdate::statusOnSite();
        $this->canAdmin = $this->getCurrentUser()->authorise('core.admin', 'com_kunena');

        $this->getDocument()->setTitle(Text::_('COM_KUNENA_GIT_INSTALL'));
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_GIT_INSTALL'), 'code-branch');
        ToolbarHelper::link(Route::_('index.php?option=com_kunena&view=tools'), 'JTOOLBAR_CLOSE', 'cancel');

        parent::display($tpl);
    }
}
