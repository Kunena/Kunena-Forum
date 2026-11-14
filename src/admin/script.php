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

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Schema\ChangeSet;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Install\KunenaModelInstall;

/**
 * Kunena package installer script.
 *
 * @since Kunena 6.0
 */
class Com_KunenaInstallerScript extends InstallerScript
{
    /**
     * The version which is assumed when the installed version can not be read: newer than every released version, older than
     * the version in development (8.0.0-DEV), so that only the updates of the version in development run.
     *
     * @var    string
     * @since  Kunena 8.0
     */
    private const DEVELOPMENT_VERSION = '7.99.0';

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

        $this->installedVersion = $this->readInstalledVersion();

        if ($type === 'update') {
            if (version_compare($this->installedVersion, '7.0.0', '<')) {
                $this->deleteFolders[] = '/administrator/components/com_kunena/tmpl/config';
                $this->deleteFolders[] = '/administrator/components/com_kunena/src/View/Config';
                $this->deleteFiles[]   = '/administrator/components/com_kunena/src/Controller/ConfigController.php';
                $this->deleteFiles[]   = '/administrator/components/com_kunena/src/Model/ConfigModel.php';
                $this->deleteFiles[]   = '/administrator/components/com_kunena/src/Controller/InstallController.php';
                $this->deleteFiles[]   = '/administrator/components/com_kunena/tmpl/tools/uninstall.php';

                $this->removeFiles();
            }
        }

        return true;
    }

    /**
     * The version of Kunena which is installed, to know which updates of the database to run.
     *
     * The version in the manifest cache of Joomla is not always a version: a site which runs Kunena from a git checkout has the
     * placeholder of the source (@kunenaversion@) there once the Database page of Joomla has written the version of the manifest
     * to it, and it is empty when the row is damaged. Both are older than every version for version_compare(), so the whole
     * history of the upgrade ran again on a current database (the old steps use the columns which are renamed since, and the
     * configuration was converted again). Then the version which Kunena recorded itself is used, and else it is assumed that
     * only the updates of the version in development are needed.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private function readInstalledVersion(): string
    {
        $manifest = $this->getItemArray('manifest_cache', '#__extensions', 'name', $this->extension);
        $version  = (string) ($manifest['version'] ?? '');

        if (preg_match('/^\d+\.\d+/', $version)) {
            return $version;
        }

        // A development site (a git checkout) has the placeholder of the source: it says nothing about the history of its
        // database, so it is ignored. Written in two parts so that the build does not replace it.
        if ($version === '@' . 'kunenaversion' . '@') {
            return self::DEVELOPMENT_VERSION;
        }

        try {
            /** @var DatabaseDriver  $db */
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery()
                ->select($db->quoteName('version'))
                ->from($db->quoteName('#__kunena_version'))
                ->order($db->quoteName('id') . ' DESC')
                ->setLimit(1);
            $recorded = (string) $db->setQuery($query)->loadResult();
        } catch (Throwable $e) {
            $recorded = '';
        }

        if (preg_match('/^\d+\.\d+/', $recorded)) {
            return $recorded;
        }

        return self::DEVELOPMENT_VERSION;
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
        if ($type === 'update') {
            if (version_compare($this->installedVersion, '7.0.0', '<')) {

                try {
                    // Get the configuration settings
                    /** @var DatabaseDriver  $db */
                    $db    = Factory::getContainer()->get('DatabaseDriver');

                    $query = $db->createQuery()
                        ->select($db->quoteName('params'))
                        ->from($db->quoteName('#__kunena_configuration'))
                        ->where($db->quoteName('id') . ' = 1');
                    $db->setQuery($query);

                    $config = $db->loadResult() ?? '{}';

                    // A missing or damaged value must not stop the update (json_decode() gives null then).
                    $processConfig    = json_decode($config, true) ?: [];

                    // Not all old config settings where converted to camel case, lets do that here
                    $keyConversions = [
                        'email_sender_name'                 => 'emailSenderName',
                        'display_filename_attachment'       => 'displayFilenameAttachment',
                        'new_users_prevent_post_url_images' => 'newUsersPreventPostUrlImages',
                        'minimal_user_posts_add_url_image'  => 'minimalUserPostsAddUrlImage',
                    ];

                    foreach ($keyConversions as $oldKey => $newKey) {
                        if (\array_key_exists($oldKey, $processConfig)) {
                            $processConfig[$newKey] = $processConfig[$oldKey];
                            unset($processConfig[$oldKey]);
                        }
                    }

                    // Convert Config parameters that are now arrays to avoid loosing the setting value on import
                    $arrayConversions = ['latestCategory', 'rssExcludedCategories', 'rssIncludedCategories'];

                    foreach ($processConfig as $param => $value) {
                        if (\in_array($param, $arrayConversions) && \is_string($value)) {
                            $processConfig[$param] = explode(',', $value);
                        }
                    }

                    $componentId = $processConfig ? $this->getComponentId() : 0;

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
                } catch (Throwable $e) {
                    // Do nothing
                }
            }

            if (version_compare($this->installedVersion, '7.0.0.DEV', '>=')) {
                // remove cached Kunena Config settings
                KunenaConfig::getInstance()->reset();
            }

            try {
                $this->upgradeDatabase();
            } catch (Exception $e) {
                Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }
        }

        if (\in_array($type, ['install', 'discover_install', 'update'], true)) {
            $this->addActionLogExtension();
            $this->setSchemaVersion();
        }
    }

    /**
     * Method to run when the component is uninstalled: removes Kunena from the extensions of the User Actions Log of Joomla.
     * The entries which were already written to the action log stay.
     *
     * @param   ComponentAdapter  $parent  Installer object
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public function uninstall($parent): bool
    {
        try {
            /** @var DatabaseDriver  $db */
            $db        = Factory::getContainer()->get('DatabaseDriver');
            $extension = $this->extension;

            $query = $db->createQuery()
                ->delete($db->quoteName('#__action_logs_extensions'))
                ->where($db->quoteName('extension') . ' = :extension')
                ->bind(':extension', $extension);
            $db->setQuery($query)->execute();
        } catch (Throwable $e) {
            // Do nothing: the uninstall goes on.
        }

        return true;
    }

    /**
     * Add Kunena to the extensions of the User Actions Log of Joomla (the choices of its option "Events To Log"), once.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    private function addActionLogExtension(): void
    {
        try {
            /** @var DatabaseDriver  $db */
            $db        = Factory::getContainer()->get('DatabaseDriver');
            $extension = $this->extension;

            $query = $db->createQuery()
                ->select('COUNT(*)')
                ->from($db->quoteName('#__action_logs_extensions'))
                ->where($db->quoteName('extension') . ' = :extension')
                ->bind(':extension', $extension);

            if ((int) $db->setQuery($query)->loadResult() > 0) {
                return;
            }

            $query = $db->createQuery()
                ->insert($db->quoteName('#__action_logs_extensions'))
                ->columns($db->quoteName('extension'))
                ->values(':extension')
                ->bind(':extension', $extension);
            $db->setQuery($query)->execute();
        } catch (Throwable $e) {
            // Do nothing: the install goes on without the action log.
        }
    }

    /**
     * Records the newest file of sql/updates/mysql as the schema version of Kunena in #__schemas. With that row Kunena is
     * listed on the Database page of the Joomla installer (System, Maintenance, Database): Joomla checks the tables with
     * those files there, and its Fix button repairs what it finds.
     *
     * The row is written here and not through an update element of the manifest: Kunena runs its own SQL and PHP updates
     * (upgradeDatabase()) and Joomla must not run the same files a second time. Joomla removes the row on uninstall.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    private function setSchemaVersion(): void
    {
        try {
            $componentId = $this->getComponentId();

            if (!$componentId) {
                return;
            }

            /** @var DatabaseDriver  $db */
            $db     = Factory::getContainer()->get('DatabaseDriver');
            $schema = (new ChangeSet($db, JPATH_ADMINISTRATOR . '/components/' . $this->extension . '/sql/updates/'))->getSchema();

            if (empty($schema)) {
                return;
            }

            $query = $db->createQuery()
                ->delete($db->quoteName('#__schemas'))
                ->where($db->quoteName('extension_id') . ' = :id')
                ->bind(':id', $componentId, ParameterType::INTEGER);
            $db->setQuery($query)->execute();

            $query = $db->createQuery()
                ->insert($db->quoteName('#__schemas'))
                ->columns($db->quoteName(['extension_id', 'version_id']))
                ->values(':id, :schema')
                ->bind(':id', $componentId, ParameterType::INTEGER)
                ->bind(':schema', $schema);
            $db->setQuery($query)->execute();
        } catch (Throwable $e) {
            // Do nothing: the install goes on, only the check on the Database page of Joomla is missing.
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
        $db         = Factory::getContainer()->get('DatabaseDriver');
        $bindValues = ['component', $this->extension];
        $query      = $db->createQuery();
        $query->select('extension_id')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quotename('type') . ' = :type')
            ->where($db->quoteName('element') . ' = :element')
            ->bind([':type', ':element'], $bindValues, [ParameterType::STRING, ParameterType::STRING]);
        $db->setQuery($query);
        $componentId = (int) ($db->loadResult() ?? 0);

        return $componentId;
    }

    /**
     * Is this the block of a version which is still in development, for a site which has a pre-release of that version?
     *
     * The block of a version in development (8.0.0-DEV) gets new statements while that version is developed. For
     * version_compare() 8.0.0-DEV is older than 8.0.0-ALPHA1-DEV, so a site which has installed an earlier pre-release would
     * never run the statements which were added to the block since. The block is run again as long as the installed version is
     * older than the version itself (8.0.0): its statements can be run twice, and the installed version of a released
     * version never runs it again.
     *
     * @param   string  $blockVersion  The version of the block in the upgrade file.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    private function isOpenDevelopmentBlock(string $blockVersion): bool
    {
        if (!preg_match('/^(\d+\.\d+\.\d+)-DEV$/i', $blockVersion, $match)) {
            return false;
        }

        return version_compare($this->installedVersion, $match[1], '<');
    }

    /**
     * Method to upgrade the database at the end of installation.
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    protected function upgradeDatabase()
    {
        $app = Factory::getApplication();
        $app->getLanguage()->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena');

        $xml = simplexml_load_file(__DIR__ . '/install/kunena.install.upgrade.xml');

        if ($xml === false) {
            $app->enqueueMessage(Text::_('COM_KUNENA_INSTALL_DB_UPGRADE_FAILED_XML'), 'error');

            return false;
        }

        // The column "state" in kunena_version indicate from which version to update

        $modelInstall = new KunenaModelInstall();
        $status       = [];

        foreach ($xml->upgrade[0] as $version) {
            // If we have already upgraded to this version, continue to the next one
            $vernum = (string) $version['version'];

            if (!empty($status[$vernum])) {
                continue;
            }

            // Update state
            $status[$vernum] = 1;

            if ($version['version'] == '@' . 'kunenaversion' . '@') {
                $git    = 1;
                $vernum = KunenaForum::version();
            }

            if (isset($git) || version_compare(strtolower($version['version']), strtolower($this->installedVersion), '>') || $this->isOpenDevelopmentBlock((string) $version['version'])) {
                foreach ($version as $action) {
                    $modelInstall->processUpgradeXMLNode($action);
                }

                // Once per version, not once per action of the version.
                $app->enqueueMessage(Text::sprintf('COM_KUNENA_INSTALL_VERSION_UPGRADED', $vernum), 'success');
            }
        }

        return true;
    }
}
