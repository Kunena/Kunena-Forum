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

class KunenaAvatarCommunity extends KunenaAvatar {

    protected $params = null;

    public function __construct($params) {
        $this->params = $params;
    }

    /**
     *
     * @param array $userlist
     */
    public function load($userlist) {
        KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
        if (class_exists('CFactory') && method_exists('CFactory', 'loadUsers'))
            CFactory::loadUsers($userlist);
        KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
    }

    /**
     *
     * @return string
     */
    public function getEditURL() {
        return CRoute::_('index.php?option=com_community&view=profile&task=uploadAvatar');
    }

    /**
     *
     * @param type $user
     * @param type $sizex
     * @param type $sizey
     * @return string
     */
    protected function _getURL($user, $sizex, $sizey) {
        $user = KunenaFactory::getUser($user);
        // Get CUser object
        $user = CFactory::getUser($user->userid);
        if ($sizex <= 90)
            $avatar = $user->getThumbAvatar();
        else
            $avatar = $user->getAvatar();
        return $avatar;
    }

}
