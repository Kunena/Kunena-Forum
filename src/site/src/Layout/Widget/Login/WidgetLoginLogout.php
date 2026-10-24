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

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since  Kunena 6.1
 */
class WidgetLoginLogout extends KunenaLayout
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
}
