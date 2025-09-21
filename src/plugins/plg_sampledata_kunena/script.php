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
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class plgSampledataKunenaInstallerScript extends InstallerScript
{
    /**
     * The extension name. This should be set in the installer script.
     *
     * @var    string
     * @since  5.4.0
     */
    protected $extension = 'plg_sampleData_kunena';

    /**
     * @var  Joomla\CMS\Application\CMSApplication  Holds the application object
     *
     * @since   Kunena 6.0
     */
    protected $app;

    /**
     * Database object
     *
     * @var    DatabaseDriver
     *
     * @since   4.0.0
     */
    protected $db;

    /**
     * @var  string  During an update, it will be populated with the old release version
     *
     * @since   Kunena 6.0
     */
    private $oldRelease;

    /**
     * method to run after an install/update/uninstall method
     *
     * @param   string            $type    'install', 'update' or 'discover_install'
     * @param   ComponentAdapter  $parent  Installer object
     *
     * @return void
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function postflight($type, $parent)
    {
        $type = strtolower($type);

        if ($type == 'install' || $type == 'discover_install') {
            $this->enablePlugin('plg_sampleData_kunena');
        }
    }

    /**
     * Function called before extension installation/update/removal procedure commences
     *
     * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     * @since   Kunena 7.0.0
     */
    public function preflight($type, $parent): bool
    {
        if (!parent::preflight($type, $parent)) {
            return false;
        }

        // Delete kunena.php
        $this->deleteFiles[] = '/plugins/sampledata/kunena/kunena.php';
        $this->removeFiles();

        return true;
    }


    /**
     * @param $pluginName
     *
     * @return boolean|false
     * @since version
     */
    public function enablePlugin($pluginName)
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
    }
}
