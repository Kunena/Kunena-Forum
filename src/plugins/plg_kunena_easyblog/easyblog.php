<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyblog
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Forum\KunenaForum;

/**
 * Class plgKunenaEasyblog
 *
 * @since Kunena
 */
class plgKunenaEasyblog extends Joomla\CMS\Plugin\CMSPlugin
{
    /**
     * plgKunenaEasyblog constructor.
    *
     * @param   DispatcherInterface  &$subject  The object to observe
     * @param   array                $config    An optional associative array of configuration settings.
     *                                          Recognized key values include 'name', 'group', 'params', 'language'
     *                                         (this list is not meant to be comprehensive).
     *
     * @since Kunena
     */
    public function __construct(&$subject, $config)
    {
        // Do not load if Kunena version is not supported or Kunena is offline
        if (!(class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') && KunenaForum::isCompatible('6.2') && KunenaForum::enabled())) {
            return;
        }

        // Do not load if Easyblog is not installed
        $path = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

        if (!is_file($path)) {
            return;
        }

        include_once $path;

        parent::__construct($subject, $config);

        $this->loadLanguage('plg_kunena_easyblog.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_easyblog.sys', JPATH_ADMINISTRATOR . '/components/com_kunena');
    }

    /**
     * Get Kunena avatar integration object.
     *
     * @return KunenaAvatarEasyblog|null
     * @since Kunena
     */
    public function onKunenaGetAvatar()
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('avatar', 1)) {
            return;
        }

        require_once __DIR__ . "/KunenaAvatarEasyblog.php";

        return new KunenaAvatarEasyblog($this->params);
    }

    /**
     * Get Kunena profile integration object.
     *
     * @return KunenaProfileEasyblog|null
     * @since Kunena
     */
    public function onKunenaGetProfile()
    {
        if (!isset($this->params)) {
            return;
        }

        if (!$this->params->get('profile', 1)) {
            return;
        }

        require_once __DIR__ . "/KunenaProfileEasyblog.php";

        return new KunenaProfileEasyblog($this->params);
    }
}
