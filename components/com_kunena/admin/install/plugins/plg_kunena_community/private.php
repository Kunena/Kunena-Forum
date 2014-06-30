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

class KunenaPrivateCommunity extends KunenaPrivate {

    protected $loaded = false;
    protected $params = null;

    public function __construct($params) {
        $this->params = $params;
        CFactory::load('libraries', 'messaging');
    }

    protected function getOnClick($userid) {
        if (!$this->loaded) {
            // PM popup requires JomSocial css to be loaded from selected template
            $cconfig = CFactory::getConfig();
            $document = JFactory::getDocument();
            $document->addStyleSheet('components/com_community/assets/window.css');
            $document->addStyleSheet('components/com_community/templates/' . $cconfig->get('template') . '/css/style.css');
            $this->loaded = true;
        }
        return ' onclick="' . CMessaging::getPopup($userid) . '"';
    }

    protected function getURL($userid) {
        return "javascript:void(0)";
    }

    public function getInboxLink($text) {
        if (!$text)
            $text = JText::_('COM_KUNENA_PMS_INBOX');
        return '<a href="' . CRoute::_('index.php?option=com_community&view=inbox') . '" rel="follow">' . $text . '</a>';
    }

}
