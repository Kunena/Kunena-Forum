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

/**
 * @link libraries\kunena\login.php
 */
class KunenaLoginCommunity {

    protected $params = null;

    public function __construct($params) {
        $this->params = $params;
    }

    /**
     *
     * @return string
     */
    public function getLoginURL() {
        return CRoute::_('index.php?option=com_community&view=frontpage');
    }

    /**
     *
     * @return string
     */
    public function getLogoutURL() {
        return CRoute::_('index.php?option=com_community&view=frontpage');
    }

    /**
     *
     * @return string
     */
    public function getRegistrationURL() {
        $usersConfig = JComponentHelper::getParams('com_users');
        if ($usersConfig->get('allowUserRegistration'))
            return CRoute::_('index.php?option=com_community&view=register');
    }

}
