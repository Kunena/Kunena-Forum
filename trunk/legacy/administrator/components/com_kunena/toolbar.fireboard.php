<?php
/**
* @version $Id: toolbar.fireboard.php 462 2007-12-10 00:05:53Z fxstein $
* Fireboard Component
* @package Fireboard
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
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// ################################################################
require_once($mainframe->getPath('toolbar_html'));

switch ($task)
{
    case "new":
    case "edit":
    case "edit2":
        TOOLBAR_simpleBoard::_EDIT();

        break;

    case "cancel":
        TOOLBAR_simpleBoard::DEFAULT_MENU();

        break;

    case "showconfig":
        TOOLBAR_simpleBoard::_EDIT_CONFIG();

        break;

    case "showCss":
        TOOLBAR_simpleBoard::CSS_MENU();

        break;

    case "profiles":
        TOOLBAR_simpleBoard::_PROFILE_MENU();

        break;

    case "instructions": break;

    case "newmoderator":
        TOOLBAR_simpleBoard::_NEWMOD_MENU();

        break;

    case "userprofile":
        TOOLBAR_simpleBoard::_EDITUSER_MENU();

        break;

    case "pruneforum":
        TOOLBAR_simpleBoard::_PRUNEFORUM_MENU();

        break;

    case "syncusers":
        TOOLBAR_simpleBoard::_SYNCUSERS_MENU();

        break;

    case "showAdministration":
        TOOLBAR_simpleBoard::_ADMIN();

        break;

    case "showprofiles":
        TOOLBAR_simpleBoard::_PROFILE_MENU();

        break;
		
				case "showsmilies":
        TOOLBAR_simpleBoard::_SHOWSMILEY_MENU();

        break;

    case "editsmiley":
        TOOLBAR_simpleBoard::_EDITSMILEY_MENU();

        break;

    case "newsmiley":
        TOOLBAR_simpleBoard::_NEWSMILEY_MENU();

        break;
				
				case "ranks":
        TOOLBAR_simpleBoard::_SHOWRANKS_MENU();

        break;

    case "editRank":
        TOOLBAR_simpleBoard::_EDITRANK_MENU();

        break;

    case "newRank":
        TOOLBAR_simpleBoard::_NEWRANK_MENU();

        break;

    default:
        TOOLBAR_simpleBoard::BACKONLY_MENU();

        break;
								
}
?>