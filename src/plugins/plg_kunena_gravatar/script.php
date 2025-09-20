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
class plgKunenaGravatarInstallerScript extends InstallerScript
{
    /**
     * The extension name. This should be set in the installer script.
     *
     * @var    string
     * @since  5.4.0
     */
    protected $extension = 'plg_kunena_gravatar';

    /**
     * Minimum PHP version required to install the extension
     *
     * @var    string
     * @since  5.4.0
     */
    protected $minimumPhp = '8.1';

    /**
     * Minimum Joomla! version required to install the extension
     *
     * @var    string
     * @since  6.0.0
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
        $this->deleteFiles[] = JPATH_SITE . '/plugins/kunena/gravatar/gravatar.php';
        $this->deleteFiles[] = JPATH_SITE . '/plugins/kunena/gravatar/KunenaAvatarGravatar.php';
        $this->removeFiles();

        return true;
    }
}
