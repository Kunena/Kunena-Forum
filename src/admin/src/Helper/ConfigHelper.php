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
     * Tab of config.xml => [label, icon (Font Awesome), description]. In the order of config.xml; the label and the
     * description are the ones of the tab in config.xml.
     *
     * @since   Kunena 8.0
     */
    public const SECTIONS = [
        'general'     => ['COM_KUNENA_CONFIG_TAB_GENERAL', 'sliders', 'COM_KUNENA_CONFIG_TAB_GENERAL_DESC'],
        'frontend'    => ['COM_KUNENA_CONFIG_TAB_FRONTEND', 'table-columns', 'COM_KUNENA_CONFIG_TAB_FRONTEND_DESC'],
        'emails'      => ['COM_KUNENA_CONFIG_TAB_EMAILS', 'envelope', 'COM_KUNENA_CONFIG_TAB_EMAILS_DESC'],
        'rss'         => ['COM_KUNENA_CONFIG_TAB_RSS', 'rss', 'COM_KUNENA_CONFIG_TAB_RSS_DESC'],
        'statistics'  => ['COM_KUNENA_CONFIG_TAB_STATISTICS', 'chart-column', 'COM_KUNENA_CONFIG_TAB_STATISTICS_DESC'],
        'posting'     => ['COM_KUNENA_CONFIG_TAB_POSTING', 'pen-to-square', 'COM_KUNENA_CONFIG_TAB_POSTING_DESC'],
        'bbcode'      => ['COM_KUNENA_CONFIG_TAB_BBCODE', 'code', 'COM_KUNENA_CONFIG_TAB_BBCODE_DESC'],
        'media'       => ['COM_KUNENA_CONFIG_TAB_MEDIA', 'photo-film', 'COM_KUNENA_CONFIG_TAB_MEDIA_DESC'],
        'uploads'     => ['COM_KUNENA_CONFIG_TAB_UPLOADS', 'paperclip', 'COM_KUNENA_CONFIG_TAB_UPLOADS_DESC'],
        'users'       => ['COM_KUNENA_CONFIG_TAB_USERS', 'user-gear', 'COM_KUNENA_CONFIG_TAB_USERS_DESC'],
        'avatars'     => ['COM_KUNENA_CONFIG_TAB_AVATARS', 'circle-user', 'COM_KUNENA_CONFIG_TAB_AVATARS_DESC'],
        'community'   => ['COM_KUNENA_CONFIG_TAB_COMMUNITY', 'people-group', 'COM_KUNENA_CONFIG_TAB_COMMUNITY_DESC'],
        'moderation'  => ['COM_KUNENA_CONFIG_TAB_MODERATION', 'gavel', 'COM_KUNENA_CONFIG_TAB_MODERATION_DESC'],
        'security'    => ['COM_KUNENA_CONFIG_TAB_SECURITY', 'shield-halved', 'COM_KUNENA_CONFIG_TAB_SECURITY_DESC'],
        'menu'        => ['COM_KUNENA_CONFIG_TAB_MENU', 'bars', 'COM_KUNENA_CONFIG_TAB_MENU_DESC'],
        'permissions' => ['JCONFIG_PERMISSIONS_LABEL', 'key', 'COM_KUNENA_CPANEL_SETTINGS_PERMISSIONS_DESC'],
    ];

    /**
     * The headings of the list of the sections: heading => the sections below it, in the order of SECTIONS.
     *
     * @since   Kunena 8.0
     */
    public const GROUPS = [
        'COM_KUNENA_CONFIG_NAV_FORUM'    => ['general', 'frontend', 'emails', 'rss', 'statistics'],
        'COM_KUNENA_CONFIG_NAV_POSTS'    => ['posting', 'bbcode', 'media', 'uploads'],
        'COM_KUNENA_CONFIG_NAV_MEMBERS'  => ['users', 'avatars', 'community'],
        'COM_KUNENA_CONFIG_NAV_SAFETY'   => ['moderation', 'security'],
        'COM_KUNENA_CONFIG_NAV_ADVANCED' => ['menu', 'permissions'],
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
