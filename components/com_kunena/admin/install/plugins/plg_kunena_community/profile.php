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

class KunenaProfileCommunity extends KunenaProfile {

    protected $params = null;

    public function __construct($params) {
        $this->params = $params;
    }

    public function getUserListURL($action = '', $xhtml = true) {
        $config = KunenaFactory::getConfig();
        $my = JFactory::getUser();
        if ($config->userlist_allowed == 1 && $my->id == 0)
            return false;
        return CRoute::_('index.php?option=com_community&view=search&task=browse', $xhtml);
    }

    public function getProfileURL($userid, $task = '', $xhtml = true) {
        if ($userid == 0)
            return false;
        // Get CUser object
        $user = CFactory::getUser($userid);
        if ($user === null)
            return false;
        return CRoute::_('index.php?option=com_community&view=profile&userid=' . $userid, $xhtml);
    }

    public function _getTopHits($limit = 0) {
        $db = JFactory::getDBO();
        $query = "SELECT userid AS id, view AS count FROM #__community_users WHERE view>0 ORDER BY view DESC";
        $db->setQuery($query, 0, $limit);
        $top = (array) $db->loadObjectList();
        KunenaError::checkDatabaseError();
        return $top;
    }

    public function showProfile($view, &$params) {

    }

}
