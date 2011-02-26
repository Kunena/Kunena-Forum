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

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

$task = JRequest::getCmd( 'task' );

switch ($task)
{
    case "cancel":
        CKunenaToolbar::DEFAULT_MENU();

        break;

    case "instructions": break;

    case "newmoderator":
        CKunenaToolbar::_NEWMOD_MENU();

        break;

    default:

        CKunenaToolbar::BACKONLY_MENU();

        break;

}

?>