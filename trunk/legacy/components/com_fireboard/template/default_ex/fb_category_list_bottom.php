<?php
/**
* @version $Id: fb_category_list_bottom.php 855 2008-07-16 15:35:10Z fxstein $
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

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');
global $fbConfig;
?>
<!-- Cat List Bottom -->

<table  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
  <thead>
    <tr>
      <td style="padding-left:20px;" align="left" >
                <?php
                if ($my->id != 0)
                {
                ?>

                    <form action = "<?php echo $mainframe->getCfg("live_site")."/index2.php";?>" name = "markAllForumsRead" method = "post">
                        <input type = "hidden" name = "markaction" value = "allread"/>
                        <input type = "hidden" name = "Itemid" value = "<?php echo FB_FB_ITEMID?>"/>
                        <input type = "hidden" name = "option" value = "com_fireboard"/>
                        <input type = "hidden" name = "no_html" value = "1"/>

                        <input type = "submit" class = "button<?php echo $boardclass ;?> fbs" value = "<?php echo _GEN_MARK_ALL_FORUMS_READ ;?>"/>
                    </form>

                <?php
                }
                ?>
           </td>
      <td  align="right" style="padding-right:20px;">
                <?php
                //(FB) FINISH: CAT LIST BOTTOM
                if ($fbConfig->enableforumjump)
                    require_once (JB_ABSSOURCESPATH . 'fb_forumjump.php');
                ?>
            </td>
        </tr>


</table>

<!-- /Cat List Bottom -->