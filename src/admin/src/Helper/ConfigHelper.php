<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Helpers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Helper;

\defined('_JEXEC') or die();

/**
 * The sections of the configuration: the tabs of config.xml, with what the dashboard and the configuration view show of them.
 *
 * @since   Kunena 8.0
 */
class ConfigHelper
{
    /**
     * Tab of config.xml => [label, icon (Font Awesome), description]. In the order of the configuration view.
     *
     * @since   Kunena 8.0
     */
    public const SECTIONS = [
        'general'     => ['COM_KUNENA_CPANEL_SETTINGS_GENERAL', 'sliders', 'COM_KUNENA_CPANEL_SETTINGS_GENERAL_DESC'],
        'frontend'    => ['COM_KUNENA_A_FRONTEND', 'display', 'COM_KUNENA_CPANEL_SETTINGS_FRONTEND_DESC'],
        'users'       => ['COM_KUNENA_A_USERS', 'user-gear', 'COM_KUNENA_CPANEL_SETTINGS_USERS_DESC'],
        'emails'      => ['COM_KUNENA_SUBSCRIPTIONS', 'bell', 'COM_KUNENA_CPANEL_SETTINGS_EMAILS_DESC'],
        'security'    => ['COM_KUNENA_A_SECURITY', 'shield-halved', 'COM_KUNENA_CPANEL_SETTINGS_SECURITY_DESC'],
        'avatars'     => ['COM_KUNENA_A_AVATARS', 'circle-user', 'COM_KUNENA_CPANEL_SETTINGS_AVATARS_DESC'],
        'uploads'     => ['COM_KUNENA_A_UPLOADS', 'cloud-arrow-up', 'COM_KUNENA_CPANEL_SETTINGS_UPLOADS_DESC'],
        'ranking'     => ['COM_KUNENA_A_RANKING', 'medal', 'COM_KUNENA_CPANEL_SETTINGS_RANKING_DESC'],
        'bbcode'      => ['COM_KUNENA_A_BBCODE', 'pen-to-square', 'COM_KUNENA_CPANEL_SETTINGS_BBCODE_DESC'],
        'rss'         => ['COM_KUNENA_ADMIN_RSS', 'rss', 'COM_KUNENA_CPANEL_SETTINGS_RSS_DESC'],
        'extra'       => ['COM_KUNENA_ADMIN_CONFIG_EXTRA', 'square-poll-vertical', 'COM_KUNENA_CPANEL_SETTINGS_EXTRA_DESC'],
        'menu'        => ['COM_KUNENA_ADMIN_MENU_SETTINGS', 'bars', 'COM_KUNENA_CPANEL_SETTINGS_MENU_DESC'],
        'permissions' => ['JCONFIG_PERMISSIONS_LABEL', 'key', 'COM_KUNENA_CPANEL_SETTINGS_PERMISSIONS_DESC'],
    ];

    /**
     * The link to the configuration view, opened on a section.
     *
     * @param   string  $section  A key of SECTIONS, or empty for the first one.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function link(string $section = ''): string
    {
        return 'index.php?option=com_kunena&view=config' . (isset(self::SECTIONS[$section]) ? '#' . $section : '');
    }
}
