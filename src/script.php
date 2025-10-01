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

use Joomla\CMS\Factory;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Date\Date;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\File;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Install\KunenaModelInstall;
use Kunena\Forum\Libraries\Menu\KunenaMenuFix;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

return new class() implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->set(
            InstallerScriptInterface::class,
            new class(
                $container->get(AdministratorApplication::class),
                $container->get(DatabaseInterface::class)
            ) implements InstallerScriptInterface {
                private AdministratorApplication $app;
                private DatabaseInterface $db;

                /**
                 * Minimum Joomla! version required to install the extension
                 *
                 * @var    string
                 * @since  6.0.0
                 */
                protected $minimumJoomla = '5.3.3';

                /**
                 * List of supported versions. Newest version first!
                 *
                 * @var array
                 * @since Kunena 2.0
                 */
                protected $versions = [
                    'PHP'     => [
                        '8.4' => '8.4.1',
                        '8.3' => '8.3.0',
                        '8.2' => '8.2.0',
                        '8.1' => '8.1.0',
                        '0'   => '8.3.0', // Preferred version
                    ],
                    'MySQL'   => [
                        '9.4' => '9.4.0',
                        '9.3' => '9.3.0',
                        '9.2' => '9.2.0',
                        '9.1' => '9.1.0',
                        '9.0' => '9.0.0',
                        '8.4' => '8.4.0',
                        '8.3' => '8.3.0',
                        '8.2' => '8.2.0',
                        '8.1' => '8.1.0',
                        '8.0' => '8.0.13',
                        '0'   => '8.4.0', // Preferred version
                    ],
                    'mariaDB' => [
                        '12.0' => '12.0.2',
                        '11.8' => '11.8.2',
                        '11.7' => '11.7.2',
                        '11.6' => '11.6.2',
                        '11.5' => '11.5.2',
                        '11.4' => '11.4.2',
                        '11.3' => '11.3',
                        '11.2' => '11.2',
                        '11.1' => '11.1',
                        '11.0' => '11.0',
                        '10.11' => '10.11',
                        '10.10' => '10.10',
                        '10.9' => '10.9',
                        '10.8' => '10.8',
                        '10.7' => '10.7',
                        '10.6' => '10.6',
                        '10.5' => '10.5',
                        '10.4' => '10.4',
                        '0' => '10.8.6', // Preferred version
                    ],
                    'Joomla!' => [
                        '6.0' => '6.0.0-beta1',
                        '5.4' => '5.4.0-beta1',
                        '5.3' => '5.3.3',
                        '5.2' => '5.2.6',
                        '5.1' => '5.1.4',
                        '5.0' => '5.0.3',
                        '0' => '5.3.3',  // Preferred version
                    ],
                ];

                /**
                 * List of required PHP extensions.
                 *
                 * @var array
                 * @since Kunena 2.0
                 */
                protected $extensions = ['dom', 'gd', 'json', 'pcre', 'SimpleXML', 'fileinfo', 'mbstring'];

                /**
                 * Version of the currently installed Kunena component
                 *
                 * @var string
                 * @since Kunena 7.0.0
                 */
                protected string $installedVersion;

                /**
                 *
                 * @since Kunena 7.0.0
                 */
                public function __construct(AdministratorApplication $app, DatabaseInterface $db)
                {
                    $this->app = $app;
                    $this->db  = $db;
                    $this->installedVersion = $this->getInstalledVersion();
                }

                /**
                 *
                 * @since Kunena 7.0.0
                 */
                public function install(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Successful installed.');

                    return true;
                }

                /**
                 *
                 * @since Kunena 7.0.0
                 */
                public function update(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Successful updated.');

                    return true;
                }

                /**
                 *
                 * @since Kunena 7.0.0
                 */
                public function uninstall(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Successful uninstalled.');

                    return true;
                }

                /**
                 *
                 * @since Kunena 7.0.0
                 */
                public function preflight(string $type, InstallerAdapter $parent): bool
                {
                    if ($type === 'uninstall') {
                        $items = KunenaMenuFix::getAll();

                        foreach ($items as $item) {
                            KunenaMenuFix::delete($item->id);
                        }

                        $installer = new KunenaModelInstall();
                        $installer->deleteMenu();

                        return true;
                    }


                    $manifest = $parent->manifest;

                    // Prevent installation if requirements are not met.
                    if (!$this->checkRequirements($manifest->version)) {
                        return false;
                    }

                    if (file_exists(JPATH_SITE . '/components/com_kunena/template/aurelia/assets/scss/custom.scss')) {
                        File::copy(JPATH_SITE . '/components/com_kunena/template/aurelia/assets/scss/custom.scss', JPATH_SITE . '/tmp/custom.scss');
                    }

                    $this->deleteRemovedFolder($this->installedVersion);

                    return true;
                }

                /**
                 *
                 * @since Kunena 7.0.0
                 */
                public function postflight(string $type, InstallerAdapter $parent): bool
                {

                    if ($type === 'uninstall') {
                        return true;
                    }

                    if (function_exists('apcu_clear_cache')) {
                        apcu_clear_cache();
                    }


                    $db = Factory::getContainer()->get(DatabaseInterface::class);

                    $table = $db->getPrefix() . 'kunena_version';

                    $db->setQuery("SHOW TABLES LIKE {$db->quote($table)}");
                    $upgrade = 0;
                    $installed = 'NONE';

                    if ($db->loadResult() == $table) {
                        $db->setQuery("SELECT version FROM #__kunena_version ORDER BY `id` DESC", 0, 1);
                        $installed = $db->loadResult();

                        if (!empty($installed)) {
                            if (version_compare($installed, '5.2.99', '<')) {
                                $query = "ALTER TABLE `#__kunena_version` ADD `sampleData` boolean NOT NULL default '0' AFTER `versionname`;";
                                $db->setQuery($query);

                                $db->execute();
                            }

                            $upgrade = 1;
                        }
                    }

                    $file = JPATH_MANIFESTS . '/packages/pkg_kunena.xml';

                    $manifest    = simplexml_load_file($file);
                    $version     = (string) $manifest->version;
                    $build       = (string) $manifest->version;
                    $date        = (string) $manifest->creationDate;
                    $versionname = (string) $manifest->versionname;
                    $installdate = Factory::getDate('now');
                    $state       = '';
                    $sampleData  = 0;

                    if ($upgrade == 1) {
                        $sampleData = 1;
                    } else {
                        // In case of a clean install the ranks and smileys needs to be inserted in the table to avoid else emoticons/ranks not appears
                        $queries  = [];

                        $query = "INSERT INTO `#__kunena_ranks`
    		(`rankId`, `rankTitle`, `rankMin`, `rankSpecial`, `rankImage`) VALUES
    		(1, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK1')}, 0, 0, 'rank1.gif'),
    		(2, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK2')}, 20, 0, 'rank2.gif'),
    		(3, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK3')}, 40, 0, 'rank3.gif'),
    		(4, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK4')}, 80, 0, 'rank4.gif'),
    		(5, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK5')}, 160, 0, 'rank5.gif'),
    		(6, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK6')}, 320, 0, 'rank6.gif'),
    		(7, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK_ADMIN')}, 0, 1, 'rankadmin.gif'),
    		(8, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK_MODERATOR')}, 0, 1, 'rankmod.gif'),
    		(9, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK_SPAMMER')}, 0, 1, 'rankspammer.gif'),
    		(10, {$db->quote('COM_KUNENA_SAMPLEDATA_RANK_BANNED')}, 0, 1, 'rankbanned.gif');";

                        $queries[] = ['kunena_ranks', $query];

                        $query = "INSERT INTO `#__kunena_smileys`
    		(`id`,`code`,`location`,`emoticonbar`) VALUES
    		(1, 'B)', '1.png', 1),
    		(2, '8)', '2.png', 1),
    		(3, '8-)', '3.png', 1),
    		(4, ':-(', '4.png', 1),
    		(5, ':(', '5.png', 1),
    		(6, ':sad:', '6.png', 1),
    		(7, ':cry:', '7.png', 1),
    		(8, ':)', '8.png', 1),
    		(9, ':-)', '9.png',  1),
    		(10, ':cheer:', '10.png', 1),
    		(11, ';)', '11.png', 1),
    		(12, ';-)', '12.png', 1),
    		(13, ':wink:', '13.png', 1),
    		(14, ';-)', '14.png', 1),
    		(15, ':P', '15.png', 1),
    		(16, ':p', '16.png', 1),
    		(17, ':-p', '17.png', 1),
    		(18, ':-P', '18.png', 1),
    		(19, ':razz:', '19.png', 1),
    		(20, ':angry:', '20.png', 1),
    		(21, ':mad:', '21.png', 1),
    		(22, ':unsure:', '22.png', 1),
    		(23, ':o', '23.png', 1);";

                        $queries[] = ['kunena_smileys', $query];

                        foreach ($queries as $query) {
                            // Only insert sample/default data if table is empty
                            $db->setQuery("SELECT * FROM " . $db->quoteName($db->getPrefix() . $query[0]), 0, 1);
                            $filled = $db->loadObject();

                            if (!$filled) {
                                $db->setQuery($query[1]);

                                $db->execute();
                            }
                        }
                    }

                    if ($installed != 'NONE') {
                        $state = $installed;
                    }

                    $query = $db->createQuery();

                    $values = [
                        $db->quote($version),
                        $db->quote($build),
                        $db->quote($date),
                        $db->quote($versionname),
                        $db->quote($sampleData),
                        $db->quote($installdate),
                        $db->quote($state),
                    ];

                    $query->insert($db->quoteName('#__kunena_version'))
                        ->columns(
                            [
                                $db->quoteName('version'),
                                $db->quoteName('build'),
                                $db->quoteName('versiondate'),
                                $db->quoteName('versionname'),
                                $db->quoteName('sampleData'),
                                $db->quoteName('installdate'),
                                $db->quoteName('state'),
                            ]
                        )
                        ->values(implode(', ', $values));
                    $db->setQuery($query);

                    $db->execute();

                    $version = '';
                    $date    = '';
                    $file    = JPATH_MANIFESTS . '/packages/pkg_kunena.xml';

                    if (file_exists($file)) {
                        $manifest = simplexml_load_file($file);
                        $version  = (string) $manifest->version;
                        $date     = (string) $manifest->creationDate;
                    } else {
                        $db    = Factory::getContainer()->get(DatabaseInterface::class);
                        $query = $db->createQuery();
                        $query->select('version')->from('#__kunena_version')->order('id');
                        $query->setLimit(1);
                        $db->setQuery($query);

                        $version = $db->loadResult();

                        if (!empty($version->versiondate)) {
                            $date = (string) $version->versiondate;
                        } else {
                            $date = new Date('now');
                        }
                    }

                    if (file_exists(JPATH_SITE . '/tmp/custom.scss')) {
                        File::copy(JPATH_SITE . '/tmp/custom.scss', JPATH_SITE . '/components/com_kunena/template/aurelia/assets/scss/custom.scss');
                    }

                    return true;
                }

                /**
                 * Delete the folders from Kunena directories which has been removed in the install package
                 *
                 * @param   string  $version  version
                 *
                 * @since   7.0.0
                 */
                protected function deleteRemovedFolder(string $installedVersion)
                {
                    if (version_compare($installedVersion, '6.3.0', '<') && version_compare($installedVersion, '6.0.0', '>=')) {
                        // Set and delete the following folders
                        $deleteFolders   = [];
                        // Administrator folders
                        $deleteFolders[] = '/administrator/components/com_kunena/api';
                        $deleteFolders[] = '/administrator/components/com_kunena/forms';
                        $deleteFolders[] = '/administrator/components/com_kunena/install';
                        $deleteFolders[] = '/administrator/components/com_kunena/language';
                        $deleteFolders[] = '/administrator/components/com_kunena/media';
                        $deleteFolders[] = '/administrator/components/com_kunena/services';
                        $deleteFolders[] = '/administrator/components/com_kunena/sql';
                        $deleteFolders[] = '/administrator/components/com_kunena/src';
                        $deleteFolders[] = '/administrator/components/com_kunena/tmpl';
                        // Site folders
                        $deleteFolders[] = '/components/com_kunena/language';
                        $deleteFolders[] = '/components/com_kunena/src';
                        $deleteFolders[] = '/components/com_kunena/template/system';
                        $deleteFolders[] = '/components/com_kunena/tmpl';
                        // Library folders
                        $deleteFolders[] = '/libraries/kunena/Src';

                        foreach ($deleteFolders as $folder) {
                            if (is_dir(JPATH_ROOT . $folder) && !Folder::delete(JPATH_ROOT . $folder)) {
                                echo Text::sprintf('JLIB_INSTALLER_ERROR_FILE_FOLDER', $folder) . '<br>';
                            }
                        }
                    }
                }

                /**
                 * Get the version of the current installed Kunena from Joomla! extensions table
                 *
                 * @return  string Installed version number or 0 when not installed
                 * @since   7.0.0
                 */
                private function getInstalledVersion(): string
                {
                    $type    = 'package';
                    $element = 'pkg_kunena';

                    $db    = Factory::getContainer()->get(DatabaseInterface::class);
                    $query = $db->createQuery();

                    $query->select($db->quoteName('manifest_cache'))
                        ->from($db->quoteName('#__extensions'))
                        ->where($db->quoteName('type') . ' = :type')
                        ->where($db->quoteName('element') . ' = :element')
                        ->bind(':type', $type, ParameterType::STRING)
                        ->bind(':element', $element, ParameterType::STRING);

                    // if (!is_null($this->folder)) {
                    //     $query->where($db->quoteName('folder') . ' = :folder')
                    //         ->bind(':folder', $this->folder, ParameterType::STRING);
                    // }

                    $db->setQuery($query);

                    $result = $db->loadResult();

                    if ($result) {
                        $return = \json_decode($result);

                        if ($return) {
                            return $return->version;
                        } else {
                            return '0';
                        }
                    } else {
                        return '0';
                    }
                }

                /**
                 * Check all the requirements mandatory to allow to install Kunena
                 * 
                 * @param   string  $version  version
                 *
                 * @return boolean|integer
                 *
                 * @since version
                 */
                public function checkRequirements($version)
                {
                    $db   = Factory::getContainer()->get(DatabaseInterface::class);
                    $pass = $this->checkVersion('PHP', $this->getCleanPhpVersion());
                    $pass &= $this->checkVersion('Joomla!', JVERSION);
                    $pass &= $this->checkDbVersion($db->getVersion());
                    $pass &= $this->checkDbo($db->name, ['mysql', 'mysqli', 'pdomysql']);
                    $pass &= $this->checkPhpExtensions($this->extensions);
                    $pass &= $this->checkKunena($version);

                    return $pass;
                }

                /**
                 * @param   string  $name     name
                 * @param   string  $version  version
                 *
                 * @return boolean
                 *
                 * @throws Exception
                 * @since version
                 */
                protected function checkVersion($name, $version)
                {
                    $app = Factory::getApplication();

                    $major = $minor = 0;

                    foreach ($this->versions[$name] as $major => $minor) {
                        if (!$major || version_compare($version, $major, '<')) {
                            continue;
                        }

                        if (version_compare($version, $minor, '>=')) {
                            return true;
                        }

                        break;
                    }

                    if (!$major) {
                        $minor = reset($this->versions[$name]);
                    }

                    $recommended = end($this->versions[$name]);
                    $app->enqueueMessage(
                        sprintf(
                            "%s %s is not supported. Minimum required version is %s %s, but it is highly recommended to use %s %s or later.",
                            $name,
                            $version,
                            $name,
                            $minor,
                            $name,
                            $recommended
                        ),
                        'notice'
                    );

                    return false;
                }

                /**
                 * Check MariaDB and MySQL versions
                 *
                 * @since Kunena 6.2
                 */
                protected function checkDbVersion($version)
                {
                    if (preg_match('/(?:.*-)?(\d+\.\d+\.\d+)-MariaDB.*/', $version, $matches)) {
                        return $this->checkVersion('mariaDB', $matches[1]);
                    }

                    return $this->checkVersion('MySQL', $version);
                }

                /**
                 *  On some hosting the PHP version given with the version of the packet in the distribution
                 *
                 * @return string
                 *
                 * @since Kunena
                 */
                protected function getCleanPhpVersion()
                {
                    $version = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;

                    return $version;
                }

                /**
                 * @param   string  $name   name
                 * @param   array   $types  types
                 *
                 * @return boolean
                 *
                 * @throws Exception
                 * @since version 2.0
                 */
                protected function checkDbo($name, $types)
                {
                    $app = Factory::getApplication();

                    if (in_array($name, $types)) {
                        return true;
                    }

                    $app->enqueueMessage(sprintf("Database driver '%s' is not supported. Please use MySQL instead.", $name), 'notice');

                    return false;
                }

                /**
                 * @param   array  $extensions  extensions
                 *
                 * @return integer
                 *
                 * @throws Exception
                 * @since version 2.0
                 */
                protected function checkPhpExtensions($extensions)
                {
                    $app = Factory::getApplication();

                    $pass = 1;

                    foreach ($extensions as $name) {
                        if (!extension_loaded($name)) {
                            $pass = 0;
                            $app->enqueueMessage(sprintf("Required PHP extension '%s' is missing. Please install it into your system.", $name), 'notice');
                        }
                    }

                    return $pass;
                }

                /**
                 * @param   string  $version  version
                 *
                 * @return boolean
                 *
                 * @throws Exception
                 * @since version 2.0
                 */
                protected function checkKunena($version)
                {
                    $app = Factory::getApplication();
                    $db  = Factory::getContainer()->get(DatabaseInterface::class);

                    // Do not install over Git repository (K1.6+).
                    if (class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && method_exists('KunenaForum', 'isDev') && KunenaForum::isDev()) {
                        $app->enqueueMessage('Oops! You should not install Kunena over your Git repository!', 'notice');

                        return false;
                    }

                    // Get installed Kunena version.
                    $table = $db->getPrefix() . 'kunena_version';

                    $db->setQuery("SHOW TABLES LIKE {$db->quote($table)}");

                    if ($db->loadResult() != $table) {
                        return true;
                    }

                    $installed = $db->setQuery(
                        $db->createQuery()
                            ->select('version')
                            ->from('#__kunena_version')->order('id DESC')
                            ->setLimit(1)
                    )->loadResult();

                    if (!$installed) {
                        return true;
                    }

                    // Don't allow to upgrade before the version 5.1.0
                    if (version_compare($installed, '5.1.0', '<')) {
                        $app->enqueueMessage('You should not upgrade Kunena from the version ' . $installed . ', you can do the upgrade only since 5.1.0', 'notice');

                        return false;
                    }

                    // Don't allow to upgrade before the version 6.4.5
                    if (version_compare($installed, '6.4.5', '<')) {
                        $app->enqueueMessage('You cannot not upgrade Kunena from version ' . $installed . ', you can only upgrade from version 6.4.5 or higher', 'notice');

                        return false;
                    }

                    return true;
                }
            }
        );
    }
};
