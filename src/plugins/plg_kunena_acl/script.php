<?php

/**
 * Kunena Package
 *
 * @package        Kunena.Package
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Kunena rank plugin installer script.
 *
 * @since Kunena 8.0
 */
class plgKunenaAclInstallerScript extends InstallerScript
{
    /**
     * The extension name. This should be set in the installer script.
     *
     * @var    string
     * @since  Kunena 8.0
     */
    protected $extension = 'plg_kunena_acl';

    /**
     * method to run after an install/update/uninstall method
     *
     * @param   string            $type    'install', 'update' or 'discover_install'
     * @param   ComponentAdapter  $parent  Installer object
     *
     * @return void
     *
     * @throws Exception
     * @since   Kunena 8.0
     */
    public function postflight($type, $parent)
    {
        $type = strtolower($type);

        if ($type === 'install' || $type === 'discover_install') {
            $this->enablePlugin('plg_kunena_acl');
        }
    }

    /**
     * @param   string  $pluginName  The name of the plugin as it is stored in the extensions table.
     *
     * @return  boolean
     * @since   Kunena 8.0
     */
    public function enablePlugin(string $pluginName): bool
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();

        $query
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('enabled') . ' = 1')
            ->where($db->quoteName('name') . ' = :pluginname')
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->bind(':pluginname', $pluginName);

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            return false;
        }

        return true;
    }
}
