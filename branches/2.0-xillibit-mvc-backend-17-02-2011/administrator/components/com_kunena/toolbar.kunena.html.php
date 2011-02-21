<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
defined( '_JEXEC' ) or die();

class CKunenaToolbar
{
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

    function _EDITUSER_MENU()
    {
        JToolBarHelper::spacer();
        JToolBarHelper::save('saveuserprofile');
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('showprofiles', 'COM_KUNENA_CANCEL');
        JToolBarHelper::spacer();

    }

    function _PROFILE_MENU()
    {
        JToolBarHelper::spacer();
        JToolBarHelper::custom('userprofile', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('logout', 'cancel.png', 'cancel_f2.png', 'COM_KUNENA_LOGOUT');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('moveusermessages', 'move.png', 'move_f2.png', 'COM_KUNENA_MOVE_USERMESSAGES');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('trashusermessages', 'trash.png', 'icon-32-move.png', 'COM_KUNENA_TRASH_USERMESSAGES');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('deleteuser','delete.png','delete_f2.png', 'COM_KUNENA_USER_DELETE');
        JToolBarHelper::spacer();
        JToolBarHelper::back();
        JToolBarHelper::spacer();
    }

    function _MOVEUSERMESSAGES_MENU()
    {
		JToolBarHelper::custom('moveusermessagesnow', 'save.png', 'save_f2.png', 'COM_KUNENA_MOVE_USERMESSAGES');
    	JToolBarHelper::spacer();
    	JToolBarHelper::cancel('profiles');
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

    function BACKONLY_MENU()
    {

    	JToolBarHelper::back();

    }

    function DEFAULT_MENU()
    {

        JToolBarHelper::deleteList();
        JToolBarHelper::spacer();

    }
}
?>