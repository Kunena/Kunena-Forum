<?php

/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Community
 *
 * @copyright (C) 2013 iJoomla, Inc. - All rights reserved. Forked from Kunena Team
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * @link http://www.kunena.org
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
defined('_JEXEC') or die('Restricted access');

if (!class_exists('plgKunenaCommunity')) {

    class plgKunenaCommunity extends JPlugin {

        /**
         *
         * @param type $subject
         * @param type $config
         * @return type
         */
        public function __construct(&$subject, $config) {
            // Do not load if Kunena version is not supported or Kunena is offline
            if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('3.0') && KunenaForum::installed()))
                return;

            // Do not load if JomSocial is not installed
            $path = JPATH_ROOT . '/components/com_community/libraries/core.php';
            if (!JFile::exists($path))
                return;
            include_once ($path);

            parent::__construct($subject, $config);

            $this->loadLanguage('plg_kunena_community.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_community.sys', KPATH_ADMIN);
        }

        /*
         * @todo Remove it
         * Get Kunena access control object.
         *
         * @return KunenaAccess
         */

        public function onKunenaGetAccessControl() {
//            if (!$this->params->get('access', 1))
//                return null;
//
//            require_once __DIR__ . "/access.php";
//            return new KunenaAccessCommunity($this->params);
        }

        /**
         * Get Kunena login integration object.
         * @return null|\KunenaLoginCommunity
         */
        public function onKunenaGetLogin() {
            if ($this->params->get('login', 1) == 1) {
                require_once __DIR__ . "/login.php";
                return new KunenaLoginCommunity($this->params);
            }
            return null;
        }

        /**
         * Get Kunena avatar integration object.
         * @return \KunenaAvatarCommunity|null
         */
        public function onKunenaGetAvatar() {
            if ($this->params->get('avatar', 1)) {
                require_once __DIR__ . "/avatar.php";
                return new KunenaAvatarCommunity($this->params);
            }
            return null;
        }

        /**
         * Get Kunena profile integration object.
         * @return null|\KunenaProfileCommunity
         */
        public function onKunenaGetProfile() {
            if ($this->params->get('profile', 1)) {
                require_once __DIR__ . "/profile.php";
                return new KunenaProfileCommunity($this->params);
            }
            return null;
        }

        /**
         * Get Kunena private message integration object.
         * @return \KunenaPrivateCommunity|null
         */
        public function onKunenaGetPrivate() {
            if ($this->params->get('private', 1)) {
                require_once __DIR__ . "/private.php";
                return new KunenaPrivateCommunity($this->params);
            }
            return null;
        }

        /**
         * Get Kunena activity stream integration object.
         * @return \KunenaActivityCommunity|null
         */
        public function onKunenaGetActivity() {
            if ($this->params->get('activity', 1)) {
                require_once __DIR__ . "/activity.php";
                return new KunenaActivityCommunity($this->params);
            }
            return null;
        }

    }

}

