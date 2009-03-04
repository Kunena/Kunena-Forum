<?php
/**
* @version $Id: fb_forumjump.php 462 2007-12-10 00:05:53Z fxstein $
* Kunena Component
* @package Kunena
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// MOS Intruder Alerts
defined( '_JEXEC' ) or die('Restricted access');

$catid = JRequest::getInt('catid', 0);

$options = array ();
$options[] = JHTML::_('select.option', 0, _KUNENA_FORUM_TOP, 'value', 'text');
$lists['parent'] = JJ_categoryParentList($catid, "", $options);
?>

<form id = "jumpto" name = "jumpto" method = "get" target = "_self" action = "index.php">
    <div align = "right" style = "width: 100%;">
        <input type = "hidden" name = "Itemid" value = "<?php echo KUNENA_COMPONENT_ITEMID;?>"/>

        <input type = "hidden" name = "option" value = "com_kunena"/>

        <input type = "hidden" name = "func" value = "showcat"/>

        <input type = "submit" name = "Go"  class="fbjumpgo fbs" value = "<?php echo _KUNENA_GO; ?>"/>
<?php echo $lists['parent']; ?>
    </div>
</form>