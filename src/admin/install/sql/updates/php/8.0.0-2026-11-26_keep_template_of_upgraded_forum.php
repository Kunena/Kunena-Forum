<?php

/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * A forum which is upgraded keeps its template. Kunena 8.0 has the template Agora as the default (config.xml), Kunena 7 had Aurelia:
 * a forum whose settings were never saved has no template in them, and it got Agora after the upgrade. It gets Aurelia, which it had.
 * A forum which has a template in its settings keeps it. (This was in the configuration migration of the old web installer, which
 * nothing runs any more.)
 *
 * The settings are read from the database, not from the cache of the components of Joomla: the upgrade of a Kunena 6 converts the
 * configuration to the settings earlier in the same request.
 *
 * @param   mixed  $parent  The installer.
 *
 * @return  array
 *
 * @throws  Exception
 * @since   Kunena 8.0
 */
function kunena_800_2026_11_26_keep_template_of_upgraded_forum($parent)
{
    $name = Text::_('COM_KUNENA_INSTALL_800_KEEP_TEMPLATE');
    $db   = Factory::getContainer()->get('DatabaseDriver');

    // Only a forum which comes from Kunena 7 or older: the block of 8.0 runs again on each update of a pre-release of 8.0, whose
    // forum has Agora as its default. The versions which the site had are in #__kunena_version (the package writes the new one
    // after this step); a site which runs from a git checkout has the placeholder of the version there.
    $versions = (array) $db->setQuery(
        $db->createQuery()->select($db->quoteName('version'))->from($db->quoteName('#__kunena_version'))
    )->loadColumn();

    $kunena8 = array_filter(
        $versions,
        static fn ($version) => !preg_match('/^\d/', (string) $version) || version_compare(strtolower((string) $version), '8.0.0-dev', '>=')
    );

    if (!$versions || $kunena8) {
        return ['action' => '', 'name' => $name, 'success' => true];
    }

    $query = $db->createQuery()
        ->select($db->quoteName(['extension_id', 'params']))
        ->from($db->quoteName('#__extensions'))
        ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
        ->where($db->quoteName('element') . ' = ' . $db->quote('com_kunena'));
    $row = $db->setQuery($query)->loadObject();

    if (!$row) {
        return ['action' => '', 'name' => $name, 'success' => true];
    }

    $params = json_decode((string) $row->params ?: '{}', true);

    if (!\is_array($params) || \array_key_exists('template', $params)) {
        return ['action' => '', 'name' => $name, 'success' => true];
    }

    $params['template'] = 'aurelia';
    $json               = json_encode($params);
    $id                 = (int) $row->extension_id;

    $query = $db->createQuery()
        ->update($db->quoteName('#__extensions'))
        ->set($db->quoteName('params') . ' = :params')
        ->where($db->quoteName('extension_id') . ' = :id')
        ->bind(':params', $json)
        ->bind(':id', $id, ParameterType::INTEGER);
    $db->setQuery($query)->execute();

    // The configuration of this request is read again with the template.
    KunenaConfig::getInstance()->reset();

    return ['action' => '', 'name' => $name, 'success' => true];
}
