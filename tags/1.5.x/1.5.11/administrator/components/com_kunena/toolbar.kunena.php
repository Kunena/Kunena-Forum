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
require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ($task)
{
    case "new":
    case "edit":
    case "edit2":
        CKunenaToolbar::_EDIT();

        break;

    case "cancel":
        CKunenaToolbar::DEFAULT_MENU();

        break;

    case "showconfig":
        CKunenaToolbar::_EDIT_CONFIG();

        break;

    case "showCss":
        CKunenaToolbar::CSS_MENU();

        break;

    case "profiles":
        CKunenaToolbar::_PROFILE_MENU();

        break;

    case "instructions": break;

    case "newmoderator":
        CKunenaToolbar::_NEWMOD_MENU();

        break;

    case "userprofile":
        CKunenaToolbar::_EDITUSER_MENU();

        break;

    case "pruneforum":
        CKunenaToolbar::_PRUNEFORUM_MENU();

        break;

    case "syncusers":
        CKunenaToolbar::_SYNCUSERS_MENU();

        break;

    case "showAdministration":
        CKunenaToolbar::_ADMIN();

        break;

    case "showprofiles":
        CKunenaToolbar::_PROFILE_MENU();

        break;
		
				case "showsmilies":
        CKunenaToolbar::_SHOWSMILEY_MENU();

        break;

    case "editsmiley":
        CKunenaToolbar::_EDITSMILEY_MENU();

        break;

    case "newsmiley":
        CKunenaToolbar::_NEWSMILEY_MENU();

        break;
				
				case "ranks":
        CKunenaToolbar::_SHOWRANKS_MENU();

        break;

    case "editRank":
        CKunenaToolbar::_EDITRANK_MENU();

        break;

    case "newRank":
        CKunenaToolbar::_NEWRANK_MENU();

        break;

    default:
        CKunenaToolbar::BACKONLY_MENU();

        break;
								
}
?>