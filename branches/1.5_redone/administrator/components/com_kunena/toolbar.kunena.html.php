<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
// ################################################################
// MOS Intruder Alerts
defined( '_JEXEC' ) or die('Restricted access');

// ################################################################
class CKunenaToolbar
{
    function _ADMIN()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::publish();
        JToolBarHelper::spacer();
        JToolBarHelper::unpublish();
        JToolBarHelper::spacer();
        JToolBarHelper::addNew();
        JToolBarHelper::spacer();
        JToolBarHelper::editList();
        JToolBarHelper::spacer();
        JToolBarHelper::deleteList();
        JToolBarHelper::spacer();
        JToolBarHelper::endTable();
    }

    function _EDIT()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::unpublish('removemoderator');
        JToolBarHelper::spacer();
        JToolBarHelper::endTable();
    }

    function _NEWMOD_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::publish('addmoderator');
        JToolBarHelper::spacer();
        JToolBarHelper::unpublish('removemoderator');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::endTable();
    }

    function _EDIT_CONFIG()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::save('saveconfig');
        JToolBarHelper::spacer();
        JToolBarHelper::back();
        JToolBarHelper::spacer();
        JToolBarHelper::endTable();
    }

    function _EDITUSER_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::save('saveuserprofile');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('showprofiles', 'Back');
        JToolBarHelper::spacer();
        JToolBarHelper::endTable();
    }

    function _PROFILE_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('userprofile', 'edit.png', 'edit_f2.png', 'Edit');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::back();
        JToolBarHelper::spacer();
        JToolBarHelper::endTable();
    }

    function CSS_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::save('saveeditcss');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::endTable();
    }

    function _PRUNEFORUM_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('doprune', 'delete.png', 'delete_f2.png', 'Prune', false);
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::endTable();
    }

    function _SYNCUSERS_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('dousersync', 'delete.png', 'delete_f2.png', 'Sync', false);
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::endTable();
    }

    function BACKONLY_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::back();
        JToolBarHelper::endTable();
    }

    function DEFAULT_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::deleteList();
        JToolBarHelper::spacer();
        JToolBarHelper::endTable();
    }

	function _SHOWSMILEY_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::addNew('newsmiley', 'New Smilie');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('editsmiley', 'edit.png', 'edit_f2.png', 'Edit');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('deletesmiley', 'delete.png', 'delete_f2.png', 'Delete');
        JToolBarHelper::spacer();
        JToolBarHelper::back();
        JToolBarHelper::endTable();
    }

    function _EDITSMILEY_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::save('savesmiley');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('showsmilies');
        JToolBarHelper::endTable();
    }

    function _NEWSMILEY_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::save('savesmiley');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('showsmilies');
        JToolBarHelper::endTable();
    }

	function _SHOWRANKS_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::addNew('newRank', 'New Rank');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('editRank', 'edit.png', 'edit_f2.png', 'Edit');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('deleteRank', 'delete.png', 'delete_f2.png', 'Delete');
        JToolBarHelper::spacer();
        JToolBarHelper::back();
        JToolBarHelper::endTable();
    }

	function _EDITRANK_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::save('saveRank');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('ranks');
        JToolBarHelper::endTable();
    }

    function _NEWRANK_MENU()
    {
        JToolBarHelper::startTable();
        JToolBarHelper::spacer();
        JToolBarHelper::save('saveRank');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('ranks');
        JToolBarHelper::endTable();
    }

}
?>