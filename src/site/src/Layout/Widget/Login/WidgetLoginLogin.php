<?php

/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget\Login;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since  Kunena 6.1
 */
class WidgetLoginLogin extends KunenaLayout
{
    public mixed $output = null;

    public mixed $user = null;

    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $config = null;

    public mixed $me = null;

    public mixed $my = null;

    public mixed $registrationUrl = null;

    public mixed $resetPasswordUrl = null;

    public mixed $remindUsernameUrl = null;

    public mixed $rememberMe = null;

    public mixed $lastvisitDate = null;

    public mixed $announcementsUrl = null;

    public mixed $pm_link = null;

    public mixed $inboxCount = null;

    public mixed $inboxCountValue = null;

    public mixed $profile_edit_url = null;

    public mixed $plglogin = null;

    /**
     * Load Joomla! module into Kunena login form to have the authentification by Webauthn
     *
     * @return  void
     *
     * @since   Kunena 6.2
     */
    public function loadModuleWebauthn()
    {
        $moduleId = KunenaFactory::getTemplate()->params->get('moduleIdWebauthn');

        if ($moduleId > 0) {
            $checkModule = ModuleHelper::getModuleById($moduleId);

            if ($checkModule->id == 0 && $checkModule->module != "mod_login") {
                $module = ModuleHelper::getModule('mod_login');

                if (!isset($module->id)) {
                    $moduleId = null;
                    Factory::getApplication()->enqueueMessage('<b>Error</b> display login menu failed - no login module found', 'error');
                } else {
                    $moduleId = $module->id;

                    echo HTMLHelper::_('content.prepare', "'{loadmoduleid " . $moduleId . "}'");
                }
            }

            if (!PluginHelper::isEnabled('content', 'loadmodule')) {
                $moduleId = null;
                Factory::getApplication()->enqueueMessage('<b>Error</b> display login menu failed - loadmodule content plugin is disabled', 'error');
            }
        }
    }
}
