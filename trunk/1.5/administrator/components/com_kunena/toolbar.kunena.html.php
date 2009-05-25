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

        JToolBarHelper::spacer();
        JToolBarHelper::publish();
        JToolBarHelper::spacer();
        JToolBarHelper::unpublish();
        JToolBarHelper::spacer();
        JToolBarHelper::addNew('new');
        JToolBarHelper::spacer();
        JToolBarHelper::editList();
        JToolBarHelper::spacer();
        JToolBarHelper::deleteList();
        JToolBarHelper::spacer();

    }

    function _EDIT()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::unpublish('removemoderator');
        JToolBarHelper::spacer();

    }

    function _NEWMOD_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::publish('addmoderator');
        JToolBarHelper::spacer();
        JToolBarHelper::unpublish('removemoderator');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();

    }

    function _EDIT_CONFIG()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::save('saveconfig');
        JToolBarHelper::spacer();
        JToolBarHelper::back();
        JToolBarHelper::spacer();

    }

    function _EDITUSER_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::save('saveuserprofile');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('showprofiles', _KUNENA_BACK);
        JToolBarHelper::spacer();

    }

    function _PROFILE_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::custom('userprofile', 'edit.png', 'edit_f2.png', _KUNENA_EDIT);
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();
        JToolBarHelper::back();
        JToolBarHelper::spacer();

    }

    function CSS_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::save('saveeditcss');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();

    }

    function _PRUNEFORUM_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('doprune', 'delete.png', 'delete_f2.png', _KUNENA_PRUNE, false);
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();

    }

    function _SYNCUSERS_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::custom('douserssync', 'delete.png', 'delete_f2.png', _KUNENA_SYNC, false);
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();
        JToolBarHelper::spacer();

    }

    function BACKONLY_MENU()
    {

        JToolBarHelper::back();

    }

    function DEFAULT_MENU()
    {

        JToolBarHelper::deleteList();
        JToolBarHelper::spacer();

    }

	function _SHOWSMILEY_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::addNew('newsmiley', _KUNENA_NEW_SMILIE);
        JToolBarHelper::spacer();
        JToolBarHelper::custom('editsmiley', 'edit.png', 'edit_f2.png', _KUNENA_EDIT);
        JToolBarHelper::spacer();
        JToolBarHelper::custom('deletesmiley', 'delete.png', 'delete_f2.png', _GEN_DELETE);
        JToolBarHelper::spacer();
        JToolBarHelper::back();

    }

    function _EDITSMILEY_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::save('savesmiley');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('showsmilies');

    }

    function _NEWSMILEY_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::save('savesmiley');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('showsmilies');

    }

	function _SHOWRANKS_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::addNew('newRank', _KUNENA_NEW_RANK);
        JToolBarHelper::spacer();
        JToolBarHelper::custom('editRank', 'edit.png', 'edit_f2.png', _KUNENA_EDIT);
        JToolBarHelper::spacer();
        JToolBarHelper::custom('deleteRank', 'delete.png', 'delete_f2.png', _GEN_DELETE);
        JToolBarHelper::spacer();
        JToolBarHelper::back();

    }

	function _EDITRANK_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::save('saveRank');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('ranks');

    }

    function _NEWRANK_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::save('saveRank');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('ranks');

    }

}
?>