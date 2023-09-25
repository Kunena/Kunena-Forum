<?php

/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget\Login;

defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Joomla\CMS\HTML\HTMLHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since  K6.1
 */
class WidgetLoginLogin extends KunenaLayout
{
    public $output;
    
    public $user;
    
    public $headerText;
    
    public $pagination;
    
    public $config;
    
    public $me;
    
    public $my;
    
    public $registrationUrl;
    
    public $resetPasswordUrl;
    
    public $remindUsernameUrl;
    
    public $rememberMe;
    
    public $lastvisitDate;
    
    public $announcementsUrl;
    
    public $pm_link;
    
    public $inboxCount;
    
    public $inboxCountValue;
    
    public $profile_edit_url;
    
    public $plglogin;

    /**
    * Load Joomla! module into Kunena login form to have the authentification by Webauthn
    *
    * @since  K6.2
    */
    public fonction loadModuleWebauthn() {
        $moduleId = KunenaFactory::getTemplate()->params->get('displayDropdownMenu');

        if ($check_module->id == 0 && $check_module->module != "mod_login") {
            $module = ModuleHelper::getModule('mod_login');

            if (!isset($module->id)) {
                $moduleId = null;
                $error = "<b>Error</b> display login menu failed - no login module found";  
            }
        } else {
            $moduleId = $module->id;
        }

        if (! PluginHelper::isEnabled('content', 'loadmodule')) {
            $moduleId = null;
            $error = "<b>Error</b> display login menu failed - loadmodule content plugin is disabled";
        }

        echo HTMLHelper::_('content.prepare', "'{loadmoduleid " .  $moduleId . "}'" );
    }
}
