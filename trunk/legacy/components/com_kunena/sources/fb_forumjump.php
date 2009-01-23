<?php
/**
* @version $Id: fb_forumjump.php 462 2007-12-10 00:05:53Z fxstein $
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

// MOS Intruder Alerts
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

$catid = intval(mosGetParam($_REQUEST, "catid", 0));

$options = array ();
$options[] = mosHTML::makeOption('0', _FB_FORUM_TOP);
$lists['parent'] = JJ_categoryParentList($catid, "", $options);
?>

<form id = "jumpto" name = "jumpto" method = "get" target = "_self" action = "index.php" onsubmit = "if(document.jumpto.catid.value &lt; 2){return false;}">
    <div align = "right" style = "width: 100%;">
        <input type = "hidden" name = "Itemid" value = "<?php echo FB_FB_ITEMID;?>"/>

        <input type = "hidden" name = "option" value = "com_fireboard"/>

        <input type = "hidden" name = "func" value = "showcat"/>

        <input type = "submit" name = "Go"  class="fbjumpgo fbs" value = "<?php echo _FB_GO; ?>"/>
<?php echo $lists['parent']; ?>
    </div>
</form>