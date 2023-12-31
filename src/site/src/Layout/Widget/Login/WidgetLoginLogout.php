<?php

/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget\Login;

defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since  K6.1
 */
class WidgetLoginLogout extends KunenaLayout
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
}