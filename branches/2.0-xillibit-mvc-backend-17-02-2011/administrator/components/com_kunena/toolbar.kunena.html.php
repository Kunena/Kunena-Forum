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

	function _SHOWSMILEY_MENU()
    {

        JToolBarHelper::spacer();
        JToolBarHelper::addNew('newsmiley', 'COM_KUNENA_NEW_SMILIE');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('editsmiley', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('deletesmiley', 'delete.png', 'delete_f2.png', 'COM_KUNENA_GEN_DELETE');
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
        JToolBarHelper::addNew('newRank', 'COM_KUNENA_NEW_RANK');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('editRank', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('deleteRank', 'delete.png', 'delete_f2.png', 'COM_KUNENA_GEN_DELETE');
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

	function _SHOWTEMPLATES_MENU()
    {
		JToolBarHelper::spacer();
        JToolBarHelper::custom('publishTemplate', 'default.png', 'default_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT');
        JToolBarHelper::spacer();
        JToolBarHelper::addNew('addKTemplate', 'COM_KUNENA_A_TEMPLATE_MANAGER_ADD');
		JToolBarHelper::spacer();
        JToolBarHelper::custom('editKTemplate', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('uninstallKTemplate', 'delete.png','delete_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_UNINSTALL');
        JToolBarHelper::spacer();
        JToolBarHelper::back();
    }

	function _EDITKTEMPLATE_MENU()
    {
        JToolBarHelper::spacer();
		JToolBarHelper::apply('applyTemplate');
		JToolBarHelper::spacer();
		JToolBarHelper::save('saveTemplate');
        JToolBarHelper::spacer();
        JToolBarHelper::custom('chooseCSSTemplate', 'css.png','css_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS', false, false );
        JToolBarHelper::spacer();
        JToolBarHelper::cancel('showTemplates');
        JToolBarHelper::spacer();
    }

	function _CHOOSECSS_MENU()
    {
        JToolBarHelper::spacer();
        JToolBarHelper::custom('editTemplateCSS', 'css.png', 'css_f2.png', 'COM_KUNENA_A_TEMPLATE_MANAGER_EDITCSS');
        JToolBarHelper::spacer();
		JToolBarHelper::spacer();
        JToolBarHelper::cancel('showTemplates');
		JToolBarHelper::spacer();
    }

	function _EDITCSS_MENU()
    {
        JToolBarHelper::spacer();
        JToolBarHelper::save('saveTemplateCSS');
        JToolBarHelper::spacer();
		JToolBarHelper::spacer();
        JToolBarHelper::cancel('chooseCSSTemplate');
		JToolBarHelper::spacer();
    }
}
?>