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
class plgKunenaCommunityInstallerScript extends InstallerScript
{
    /**
     * The extension name. This should be set in the installer script.
     *
     * @var    string
     * @since  5.4.0
     */
    protected $extension = 'plg_kunena_community';

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
        $this->deleteFiles[] = '/plugins/kunena/community/community.php';
        $this->deleteFiles[] = '/plugins/kunena/community/KunenaAccessCommunity.php';
        $this->deleteFiles[] = '/plugins/kunena/community/KunenaActivityCommunity.php';
        $this->deleteFiles[] = '/plugins/kunena/community/KunenaAvatarCommunity.php';
        $this->deleteFiles[] = '/plugins/kunena/community/KunenaLoginCommunity.php';
        $this->deleteFiles[] = '/plugins/kunena/community/KunenaPrivateCommunity.php';
        $this->deleteFiles[] = '/plugins/kunena/community/KunenaProfileCommunity.php';
        $this->removeFiles();

        return true;
    }
}
