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

namespace Kunena\Forum\Plugin\System\Kunena\Extension;

\defined('_JEXEC') or die();

use Joomla\CMS\Event\Extension\BeforeUpdateEvent;
use Joomla\CMS\Event\Extension\BeforeInstallEvent;
use Joomla\CMS\Event\User\AfterSaveEvent;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Event\Application\AfterInitialiseEvent;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Event\Application\AfterRenderEvent;
use Joomla\CMS\Event\Application\BeforeCompileHeadEvent;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Event\Installer\BeforePackageDownloadEvent;
use Joomla\CMS\Event\Model\PrepareFormEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Kunena\Forum\Libraries\Controller\Application\Display;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\KunenaInstaller;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaBan;
use ___NAMESPACE_HELPER___\ComponentsHelper;
use ___NAMESPACE_HELPER___\InjectorHelper;
use ___NAMESPACE_HELPER___\OchElementsHelper;
use ___NAMESPACE_HELPER___\OchHelper;
use ___NAMESPACE_HELPER___\Ochfoundation5Helper;
use ___NAMESPACE_HELPER___\TagsHelper;

/**
 * Class Kunena
 *
 * @since   Kunena 6.0
 */
class Kunena extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Load language file for front-end translations
     *
     * @var boolean
     */
    protected $autoloadLanguage = \true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  - The method name to call (priority defaults to 0)
     *  - An array composed of the method name to call and the priority
     *
     * @return  array
     * @since   Kunena 6.5
     */
    public static function getSubscribedEvents(): array
    {
        $app     = Factory::getApplication();
        $mapping = [];

        if ($app->isClient('site') || $app->isClient('administrator')) {
            $mapping['onKunenaGetConfiguration'] = 'onKunenaGetConfiguration';
            $mapping['onUserAfterSave']          = 'onUserAfterSave';
            $mapping['onExtensionBeforeInstall'] = 'onExtensionBeforeInstall';
            $mapping['onAExtensionBeforeUpdate'] = 'onExtensionBeforeUpdate';
            if ($app->isClient('site')) {
                // Only allowed in the frontend
            } elseif ($app->isClient('administrator')) {
                // Only allowed in the backend
            }
        }

        return $mapping;
    }

    /**
     * @param   array   $config   Config
     *
     * @throws  \Exception
     * @since   Kunena 6.0
     */
    public function __construct(array $config)
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

        parent::__construct($config);

        $format = $this->getApplication()->input->getCmd('format');

        require_once JPATH_LIBRARIES . '/kunena/External/autoload.php';

        if (!empty($format) && $format !== 'html') {
            if ($this->getApplication()->scope == 'com_kunena') {
                if (!PluginHelper::isEnabled('kunena', 'powered')) {
                    $styles = <<<EOF
                    .layout#kunena + div { display: block !important;}
                    #kunena + div { display: block !important;}
                    EOF;

                    KunenaTemplate::getInstance()->addStyleDeclaration($styles);
                }
            }

            if (!method_exists(Display::class, 'poweredBy')) {
                $this->getApplication()->enqueueMessage(
                    'Please Buy Official powered by remover plugin on: https://www.kunena.org/downloads',
                    'notice'
                );
            }
        }

        // ! Always load language after parent::construct else the name of plugin isn't yet set
        $this->getApplication()->getLanguage()->load('plg_system_kunena.sys');
    }

    /**
     * Routines to run onAfterInitialise
     *
     * @param   AfterInitialiseEvent $event  The event instance.
     * 
     * @return  void
     * @since   Kunena 6.0
     */
    public function onAfterInitialise(AfterInitialiseEvent $event): void
    {
        // Add ban check
        if (!$this->getApplication()->isClient('administrator') && !$this->getApplication()->isClient('api')) {
            $timestamp = time();
            $lastCheck = $this->params->get('ban_check_last', 0);

            if ($timestamp - $lastCheck >= 3600) {
                try {
                    $this->cleanExpiredBans();

                    // Update last check time
                    $this->params->set('ban_check_last', $timestamp);

                    // Save the parameters
                    $db    = $this->getDatabase();
                    $query = $db->createQuery()
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
                    $this->getApplication()->enqueueMessage($e->getMessage(), 'error');
                }
            }
        }
    }

    /**
     * Clean expired bans from the system
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    private function cleanExpiredBans(): void
    {
        $db  = $this->getDatabase();
        $now = new Date();

        // Find expired site-wide bans
        $query = $db->createQuery()
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
     * Method is called after a succesfull save of the user data
     *
     * @param   AfterSaveEvent  $event  The event
     *
     * @return  void
     * @since   2.2.0
     */
    public function onUserAfterSave(AfterSaveEvent $event): void
    {
        $user    = $event->getUser();
        $isNew   = $event->getIsNew();
        $success = $event->getSavingResult();

        // Don't continue if the user wasn't stored successfully
        if (!$success) {
            return;
        }

        if ($isNew && \intval($user['id'])) {
            $kuser = KunenaFactory::getUser(\intval($user['id']));
            $kuser->save();
        }
    }

    /**
     * Prevent downgrades to Kunena 1.7 and older releases
     *
     * @param   BeforeInstallEvent  $event  The event instance.
     *
     * @return  void
     * @since   Kunena 6.0
     * @throws  \Exception
     */
    public function onExtensionBeforeInstall(BeforeInstallEvent $event)
    {

        $method   = $event->getMethod();
        $type     = $event->getType();
        $manifest = $event->getManifest();
        $eid      = $event->getExtension();

        // We don't want to handle discover install (where there's no manifest provided)
        if (!$manifest) {
            return;
        }

        $updateEvent = new BeforeUpdateEvent('onExtensionBeforeUpdate', [
            'type'     => $type,
            'manifest' => $manifest,
        ]);

        $this->onExtensionBeforeUpdate($updateEvent);
    }

    /**
     * Prevent downgrades to Kunena 1.7 and older releases
     *
     * @param   object  $type      type
     * @param   string  $manifest  manifest
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function onExtensionBeforeUpdate(BeforeUpdateEvent $event): void
    {
        $type     = $event->getType();
        $manifest = $event->getManifest();

        if ($type != 'component') {
            return;
        }

        // Generate component name
        $name    = \strtolower(InputFilter::getInstance()->clean((string) $manifest->name, 'cmd'));
        $element = (\substr($name, 0, 4) === "com_") ? $name : "com_{$name}";

        if ($element !== 'com_kunena') {
            return;
        }

        // Kunena 2.0.0-BETA2 and later support this feature in their installer
        if (\version_compare($manifest->version, '2.0.0', '>=')) {
            return;
        }

        // Check if we can downgrade to the current version
        if (\class_exists('KunenaInstaller') && KunenaInstaller::canDowngrade($manifest->version)) {
            return;
        }

        // Old version detected: emulate failed installation
        $app = $this->getApplication();
        $app->enqueueMessage(sprintf(
            'Sorry, it is not possible to downgrade Kunena %s to version %s.',
            KunenaForum::version(),
            $manifest->version
        ), 'warning');
        $app->enqueueMessage(Text::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_CUSTOM_INSTALL_FAILURE'), 'error');
        $app->enqueueMessage(Text::sprintf('COM_INSTALLER_MSG_UPDATE_ERROR', Text::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($type))), 'error');
        $app->redirect('index.php?option=com_installer');

        return;
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
     * @throws  Exception
     * @since   Kunena 2.0
     *
     * @see     self::onKunenaPrepare()
     */
    protected function runJoomlaContentEvent(string &$text, object $params, $page = 0)
    {
        // Discuss: I cannot find where this is called from, if this is related to onKunenaPrepare, the that should be refactored into a Kunena event: KunenaPrepare
        // Or not and replace all onKunenaPrepare event triggers with the HTMLHelper set below??
        return HTMLHelper::_('content.prepare', $text, $params(), 'kunena.topic');

        // PluginHelper::importPlugin('content');

        // $row       = new stdClass();
        // $row->text = &$text;

        // Factory::getApplication()->triggerEvent('onContentPrepare', ['text', &$row, &$params, 0]);

        // $text = &$row->text;

        // return $text;
    }
}
