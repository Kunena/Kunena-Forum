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

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class plgSystemKunenaInstallerScript extends InstallerScript
{
    /**
     * The extension name. This should be set in the installer script.
     *
     * @var    string
     * @since  5.4.0
     */
    protected $extension = 'plg_system_kunena';

    /**
     * Minimum PHP version required to install the extension
     *
     * @var    string
     * @since  5.4.0
     */
    protected $minimumPhp = '8.1';

    /**
     * Minimum Joomla! version required to install the extension
     *
     * @var    string
     * @since  6.0.0
     */
    protected $minimumJoomla = '5.3.2';

    /**
     * List of required PHP extensions.
     *
     * @var array
     * @since Kunena
     */
    protected $extensions = ['dom', 'gd', 'json', 'pcre', 'SimpleXML'];

    /**
     * method to run after an install/update/uninstall method
     *
     * @param   string            $type    'install', 'update' or 'discover_install'
     * @param   ComponentAdapter  $parent  Installer object
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function postflight($type, $parent)
    {
        $this->enablePlugin('plg_system_kunena');
    }

    /**
     * Function called before extension installation/update/removal procedure commences
     *
     * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     * @since   Kunena 6.5
     */
    public function preflight($type, $parent): bool
    {
        if (!parent::preflight($type, $parent)) {
            return false;
        }

        // Delete kunena.php
        $this->deleteFiles[] = JPATH_SITE . '/plugins/system/kunena/kunena.php';
        $this->removeFiles();

        return true;
    }

    /**
     * @param $pluginName
     *
     * @return boolean|false
     * @since  version
     */
    public function enablePlugin(string $pluginName): bool
    {
        // Create a new db object.
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
