<?php

/**
 * Kunena System Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      System
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\User\UserFactoryInterface;
use Kunena\Forum\Libraries\Controller\Application\Display;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\KunenaInstaller;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaBan;

/**
 * Class plgSystemKunena
 *
 * @since   Kunena 6.0
 */
class PlgSystemKunena extends CMSPlugin
{
    /**
     * Application object
     *
     * @var    \Joomla\CMS\Application\CMSApplication
     * @since  Kunena 6.0
     */
    protected $app;

    /**
     * @param   object  $subject  Subject
     * @param   array   $config   Config
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function __construct(object $subject, array $config)
    {
        if (php_sapi_name() === 'cli') {
            return;
        }

        // Check if Kunena API exists
        $api = JPATH_ADMINISTRATOR . '/components/com_kunena/api/api.php';

        if (!is_file($api)) {
            return;
        }

        // Check if Kunena component is installed/enabled
        if (!ComponentHelper::isEnabled('com_kunena')) {
            return;
        }

        // Load Kunena API
        require_once $api;

        // Do not load if Kunena version is not supported or Kunena is not installed
        if (!(KunenaForum::isCompatible('6.4') && KunenaForum::installed())) {
            return;
        }

        parent::__construct($subject, $config);

        // Initialize application object
        $this->app = Factory::getApplication();
        
        $format = $this->app->input->getCmd('format');

        require_once JPATH_LIBRARIES . '/kunena/External/autoload.php';

        if (!empty($format) && $format != 'html') {
            if ($this->app->scope == 'com_kunena') {
                if (!PluginHelper::isEnabled('kunena', 'powered')) {
                    $styles = <<<EOF
                    .layout#kunena + div { display: block !important;}
                    #kunena + div { display: block !important;}
                    EOF;

                    KunenaTemplate::getInstance()->addStyleDeclaration($styles);
                }
            }

            if (!method_exists(Display::class, 'poweredBy')) {
                $this->app->enqueueMessage(
                    'Please Buy Official powered by remover plugin on: https://www.kunena.org/downloads',
                    'notice'
                );
            }
        }

        // ! Always load language after parent::construct else the name of plugin isn't yet set
        $this->loadLanguage('plg_system_kunena.sys');
    }

    /**
     * After initialise.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function onAfterInitialise()
    {
        // Add ban check
        TODO : in Joomla! 6.0 with plugin compat6 disabled $this->app is null
        /*if (!$this->app->isClient('administrator') && !$this->app->isClient('api')) {
            $timestamp = time();
            $lastCheck = $this->params->get('ban_check_last', 0);
            
            if ($timestamp - $lastCheck >= 3600) {
                try {
                    $this->cleanExpiredBans();
                    
                    // Update last check time
                    $this->params->set('ban_check_last', $timestamp);
                    
                    // Save the parameters
                    $db = Factory::getContainer()->get('DatabaseDriver');
                    $query = $db->getQuery(true)
                        ->update($db->quoteName('#__extensions'))
                        ->set($db->quoteName('params') . ' = ' . $db->quote($this->params->toString()))
                        ->where([
                            $db->quoteName('type') . ' = ' . $db->quote('plugin'),
                            $db->quoteName('folder') . ' = ' . $db->quote('system'),
                            $db->quoteName('element') . ' = ' . $db->quote('kunena')
                        ]);
                    
                    $db->setQuery($query);
                    $db->execute();
                } catch (\Exception $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');
                }
            }
        }*/
    }
    /**
     * Clean expired bans from the system
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    protected function cleanExpiredBans(): void
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $now = new Date();
        
        // Find expired site-wide bans
        $query = $db->getQuery(true)
            ->select('b.*')
            ->from($db->quoteName('#__kunena_users_banned', 'b'))
            ->where($db->quoteName('b.expiration') . ' <= ' . $db->quote($now->toSql()))
            ->where($db->quoteName('b.blocked') . ' = 1')
            ->where($db->quoteName('b.expiration') . ' != ' . $db->quote('9999-12-31 23:59:59'));
            
        $db->setQuery($query);
        $expiredBans = $db->loadObjectList();
        
        foreach ($expiredBans as $ban) {
            // Unblock user in Joomla
            $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($ban->userid);
            if ($user && $user->block) {
                $user->block = 0;
                $user->save();
            }
            
            // Update Kunena user profile
            $profile = KunenaFactory::getUser($ban->userid);
            $profile->banned = null;
            $profile->save(true);
            
            // Update ban record
            $banInstance = KunenaBan::getInstance($ban->id);
            if ($banInstance->exists()) {
                $banInstance->addComment('Automatically unbanned by system');
                $banInstance->modified_time = $now->toSql();
                $banInstance->save(true);
            }
        }
    }

    /**
     * @param   string  $context  Context
     * @param   array   $params   Params
     *
     * @return  void
     *
     * @internal
     *
     * @since   Kunena 6.0
     */
    public function onKunenaGetConfiguration(string $context, array &$params): void
    {
        if ($context == 'kunena.configuration') {
            $params["plg_{$this->_type}_{$this->_name}"] = $this->params;
        }
    }

    /**
     * @param   mixed        $user     User
     * @param   boolean      $isnew    Is new
     * @param   boolean      $success  Success
     * @param   string|null  $msg      Message
     *
     * @return  void
     *
     * @since   Kunena 6.0
     * @throws \Exception
     */
    public function onUserAfterSave($user, bool $isnew, bool $success, ?string $msg): void
    {
        // Don't continue if the user wasn't stored successfully
        if (!$success) {
            return;
        }

        if ($isnew && intval($user['id'])) {
            $kuser = KunenaFactory::getUser(intval($user['id']));
            $kuser->save();
        }
    }

    /**
     * Prevent downgrades to Kunena 1.7 and older releases
     *
     * @param   string  $method    method
     * @param   string  $type      type
     * @param   string  $manifest  manifest when use discover install it's null
     * @param   int     $eid       id
     *
     * @return bool
     * @since   Kunena 6.0
     * @throws \Exception
     */
    public function onExtensionBeforeInstall(string $method, string $type, $manifest, int $eid): bool
    {
        // We don't want to handle discover install (where there's no manifest provided)
        if (!$manifest) {
            return false;
        }

        return $this->onExtensionBeforeUpdate($type, $manifest);
    }

    /**
     * Prevent downgrades to Kunena 1.7 and older releases
     *
     * @param   object  $type      type
     * @param   string  $manifest  manifest
     *
     * @return  boolean
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public function onExtensionBeforeUpdate($type, object $manifest): bool
    {
        if ($type != 'component') {
            return true;
        }

        // Generate component name
        $name    = strtolower(InputFilter::getInstance()->clean((string) $manifest->name, 'cmd'));
        $element = (substr($name, 0, 4) == "com_") ? $name : "com_{$name}";

        if ($element != 'com_kunena') {
            return true;
        }

        // Kunena 2.0.0-BETA2 and later support this feature in their installer
        if (version_compare($manifest->version, '2.0.0', '>=')) {
            return true;
        }

        // Check if we can downgrade to the current version
        if (class_exists('KunenaInstaller') && KunenaInstaller::canDowngrade($manifest->version)) {
            return true;
        }

        // Old version detected: emulate failed installation
        $app = Factory::getApplication();
        $app->enqueueMessage(sprintf(
            'Sorry, it is not possible to downgrade Kunena %s to version %s.',
            KunenaForum::version(),
            $manifest->version
        ), 'warning');
        $app->enqueueMessage(Text::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_CUSTOM_INSTALL_FAILURE'), 'error');
        $app->enqueueMessage(Text::sprintf('COM_INSTALLER_MSG_UPDATE_ERROR', Text::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($type))), 'error');
        $app->redirect('index.php?option=com_installer');

        return true;
    }

    /**
     * Runs all Joomla content plugins on a single \Kunena\Forum\Libraries\Forum\Message\Message
     *
     * @access  protected
     *
     * @param   string  $text    String to run events on
     * @param   object  $params  Joomla\Registry\Registry object holding eventual parameters
     * @param   int     $page    An integer holding page number
     *
     * @return  string
     *
     * @throws Exception
     * @since   Kunena 2.0
     *
     * @see     self::onKunenaPrepare()
     */
    protected function runJoomlaContentEvent(string &$text, object $params, $page = 0)
    {
        PluginHelper::importPlugin('content');

        $row       = new stdClass();
        $row->text = &$text;

        Factory::getApplication()->triggerEvent('onContentPrepare', ['text', &$row, &$params, 0]);

        $text = &$row->text;

        return $text;
    }
}
