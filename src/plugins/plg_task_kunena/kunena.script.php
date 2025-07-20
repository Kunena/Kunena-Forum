<?php

/**
 * Kunena Plugin
 *
 * @package        Kunena.Plugins
 * @subpackage     Task
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
use Joomla\Registry\Registry;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class plgTaskKunenaInstallerScript extends InstallerScript
{
    /**
     * The extension name. This should be set in the installer script.
     *
     * @var    string
     * @since  6.5.0
     */
    protected $extension = 'plg_task_kunena';

    /**
     * Minimum PHP version required to install the extension
     *
     * @var    string
     * @since  6.5.0
     */
    protected $minimumPhp = '8.1';

    /**
     * Minimum Joomla! version required to install the extension
     *
     * @var    string
     * @since  6.5.0
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
     * @return void
     *
     * @throws Exception
     * @since   Kunena 6.5.0
     */
    public function postflight($type, $parent)
    {
        $this->enablePlugin('plg_task_kunena');
        $this->addRemoveExpiredBansTask();
    }

    /**
     * Function to enable the plugin after installation
     * 
     * @param   string  $pluginName
     *
     * @return  boolean
     * @since   6.5.0
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
            return $db->execute();
        } catch (ExecutionFailureException $e) {
            return false;
        }
    }

    /**
     * Function to enable the Remove Expired bans task
     * 
     * @return  void
     * @since   6.5.0
     */
    private function addRemoveExpiredBansTask(): void
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select('COUNT(*)')
            ->from($db->quoteName('#__scheduler_tasks'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('remove.exiredbans'));
        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result) {
            // We are already setup
            return;
        }

        // Get the plugin parameters
        $params = new Registry();

        /** @var \Joomla\Component\Scheduler\Administrator\Extension\SchedulerComponent $component */
        $component = Factory::getApplication()->bootComponent('com_scheduler');

        /** @var \Joomla\Component\Scheduler\Administrator\Model\TaskModel $model */
        $model = $component->getMVCFactory()->createModel('Task', 'Administrator', ['ignore_request' => true]);
        $task  = [
            'title'           => 'Remove Expired Bans',
            'type'            => 'remove.exiredbans',
            'execution_rules' => [
                'rule-type'      => 'interval-hours',
                'interval-hours' => 1,
                'exec-time'      => gmdate('H:i'),
                'exec-day'       => gmdate('d'),
            ],
            'state'  => 1,
            'params' => [],
        ];
        $model->save($task);
    }
}
