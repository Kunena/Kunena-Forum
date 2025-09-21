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

use Joomla\CMS\Installer\InstallerScript;

/**
 * Kunena package installer script.
 *
 * @since Kunena
 */
class plgKunenaEasysocialInstallerScript extends InstallerScript
{
    /**
     * The extension name. This should be set in the installer script.
     *
     * @var    string
     * @since  5.4.0
     */
    protected $extension = 'plg_kunena_easysocial';

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
        $this->deleteFiles[] = '/plugins/kunena/easysocial/easysocial.php';
        $this->deleteFiles[] = '/plugins/kunena/easysocial/KunenaActivityEasysocial.php';
        $this->deleteFiles[] = '/plugins/kunena/easysocial/KunenaAvatarEasysocial.php';
        $this->deleteFiles[] = '/plugins/kunena/easysocial/KunenaLoginEasysocial.php';
        $this->deleteFiles[] = '/plugins/kunena/easysocial/KunenaPrivateEasysocial.php';
        $this->deleteFiles[] = '/plugins/kunena/easysocial/KunenaProfileEasysocial.php';
        $this->deleteFolders[] = '/plugins/kunena/easysocial/alerts';
        $this->deleteFolders[] = '/plugins/kunena/easysocial/badges';
        $this->deleteFolders[] = '/plugins/kunena/easysocial/points';
        $this->removeFiles();

        return true;
    }
}
