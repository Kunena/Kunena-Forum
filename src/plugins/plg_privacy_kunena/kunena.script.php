<?php

/**
 * [___HEADER_PHP___]
 */

// No direct access
defined('_JEXEC') or die;

use Kunena\Forum\Plugin\Privacy\Kunena\Helper\OchInstallerScriptHelper;

// Include the helper functions only once, because autoloading doesn't work on fresh install (autoload not registered)
JLoader::register('Kunena\Forum\Plugin\Privacy\Kunena\Helper\OchInstallerScriptHelper', __DIR__ . '/src/Helper/OchInstallerScriptHelper.php');

/**
 * Script file to clean up left-overs from previous versions
 * Called from upgrade routine
 *
 * @since  0.2.1
 */
class PlgPrivacyKunenaInstallerScript
{
    /**
     * @var string
     */
    protected $installedVersion;

    /**
     * @var array
     */
    protected $maintenanceVariables;

    /**
     * @var array
     */
    protected $preflightVariables;

    /**
     * Method to run before the install routine.
     *
     * @param   string                      $type    The action being performed
     * @param   JInstallerAdapterComponent  $parent  The class calling this method
     *
     * @return  void|boolean
     */
    public function preflight($type, $parent)
    {
        // Check for minimum required Joomla! version
        if (!OchInstallerScriptHelper::checkMinimumJoomlaVersion($type, $parent)) {
            // We are not on minimum Joomla! version: get out of here...
            return false;
        }

        $type = strtolower($type);

        if ($type == 'update') {
            $this->installedVersion = OchInstallerScriptHelper::getInstalledVersion('plugin', 'kunena', 'privacy');

            // Load all maintenance variables
            $this->setPreFlightMaintenanceVariables();

            // Do preflight maintenance
            OchInstallerScriptHelper::doMaintenance($this->preflightVariables, $this->installedVersion);
        }
    }

    /**
     * Code to execute on plugin update, used for cleaning left-overs from previous versions
     *
     * @param   object  $adapter  Adapter instance
     *
     * @return  void
     */
    public function update($adapter)
    {
        $newVersion = $adapter->manifest->version;

        // Load all maintenance variables
        $this->setMaintenanceVariables();

        // Rename all configured files
        OchInstallerScriptHelper::doMaintenance($this->maintenanceVariables, $this->installedVersion);
    }

    /**
     * Set the maintenance variables
     *
     * @return void
     */
    public function setMaintenanceVariables()
    {
        $this->maintenanceVariables['rename_files'] = [];

        $this->maintenanceVariables['delete_files'] = [];

        $this->maintenanceVariables['remove_directories'] = [];

        $this->maintenanceVariables['installation_messages'] = [];

        $this->maintenanceVariables['component_warnings'] = [];

        $this->maintenanceVariables['update_sites'] = [];
    }

    /**
     * Set the maintenance variables
     *
     * @return void
     */
    public function setPreFlightMaintenanceVariables()
    {
        $this->preflightVariables['remove_directories'] = [];

        $this->preflightVariables['remove_files'] = [];
    }
}
