<?php

/**
 * Kunena System Plugin
 *
 * @package        Kunena.Plugins
 * @subpackage     System
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\Database\ParameterType;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class Com_KunenaInstallerScript extends InstallerScript
{
    /**
     * The extension name. This should be set in the installer script.
     *
     * @var    string
     * @since  7.0.0
     */
    protected $extension = 'com_kunena';

    /**
     * The installed version when upgrading
     *
     * @var    string
     * @since  7.0.0
     */
    protected string $installedVersion = '';

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

        $manifest               = $this->getItemArray('manifest_cache', '#__extensions', 'name', $this->extension);
        $this->installedVersion = $manifest['version'] ?? '';

        if ($type === 'upgrade') {
            if (version_compare($this->installedVersion, '7.0.0', '<')) {
                $this->deleteFolders[] = '/administrator/components/com_kunena/tmpl/config';
                $this->deleteFolders[] = '/administrator/components/com_kunena/src/View/Config';
                $this->deleteFiles[]   = '/administrator/components/com_kunena/src/Controller/ConfigController.php';
                $this->deleteFiles[]   = '/administrator/components/com_kunena/src/Model/ConfigModel.php';

                $this->removeFiles();
            }
        }

        return true;
    }

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
        // Convert Config settings to com_kunena component parameters on update and when installed version is < 7.0.0
        if ($type === 'update' && version_compare($this->installedVersion, '7.0.0', '<')) {
            $comParams = $this->getItemArray('params', '#__extensions', 'name', 'com_kunena');

            // Only convert when there are not component settings ( = first upgrade to 7.0.0)
            if (empty($comParams)) {
                // Get the configuration settings
                /** @var DatabaseDriver  $db */
                $db    = Factory::getContainer()->get('DatabaseDriver');

                $query = $db->createQuery()
                    ->select($db->quoteName('params'))
                    ->from($db->quoteName('#__kunena_configuration'))
                    ->where($db->quoteName('id') . ' = 1');
                $db->setQuery($query);

                $config = $db->loadResult() ?? '{}';

                // Convert Config parameters that are now arrays to avoid loosing the setting value on import
                $processConfig    = json_decode($config, true);
                $arrayConversions = ['latestCategory', 'rssExcludedCategories', 'rssIncludedCategories'];

                foreach ($processConfig as $param => $value) {
                    if (in_array($param, $arrayConversions) && is_string($value)) {
                        $processConfig[$param] = explode(',', $value);
                    }
                }

                $componentId = $this->getComponentId();

                if ($componentId) {
                    $paramsString = json_encode($processConfig);

                    $query = $db->createQuery()
                        ->update($db->quoteName('#__extensions'))
                        ->set('params = :params')
                        ->where('extension_id = :id')
                        ->bind(':params', $paramsString)
                        ->bind(':id', $componentId, ParameterType::INTEGER);

                    // Update table
                    $converted = $db->setQuery($query)->execute();
                } else {
                    $converted = false;
                }

                if ($converted) {
                    // We have  been able to convert the configuration to component parameters
                    Factory::getApplication()->enqueueMessage('Kunena Configuration settings were automatically converted to the new Configuration settings, please validate after installation completes.');
                } else {
                    // We have not been able to convert the configuration to component parameters
                    Factory::getApplication()->enqueueMessage('We are sorry to inform you that we were not able to convert you Kunena configuration settings to the new version. You need to do this manually after installation completes.', 'error');
                }
            }
        }
    }

    /**
     * Function to get the Component ID in the #__etensions table
     * 
     * @return integer
     * @since  7.0.0
     */
    private function getComponentId(): int
    {
        /** @var DatabaseDriver  $db */
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $bindValues = ['component', $this->extension];
        $query = $db->createQuery();
        $query->select('extension_id')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quotename('type') . ' = :type')
            ->where($db->quoteName('element') . ' = :element')
            ->bind([':type', ':element'], $bindValues, [ParameterType::STRING, ParameterType::STRING]);
        $db->setQuery($query);
        $componentId = (int) ($db->loadResult() ?? 0);

        return $componentId;
    }
}
