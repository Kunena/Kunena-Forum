<?php
/**
* @version $Id: fb_rules.php 855 2008-07-16 15:35:10Z fxstein $
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
<!-- INSERT YOUR RULES IN HTML BEGINNING HERE -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id ="fb_forumrules" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th>
                <div class = "fb_title_cover">
                        <span class="fb_title" ><?php echo _COM_FORUM_RULES; ?></span>
                </div>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td class = "<?php echo $boardclass; ?>rulesdesc">
<?php
          $database->setQuery("SELECT introtext FROM #__content  WHERE id=".$fbConfig->rules_cid."");
		  $j_introtext = $database->loadResult();

           ?>
            <?php echo $j_introtext; ?>
<?php // echo _COM_FORUM_RULES_DESC; ?>
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<!-- THIS IS WHERE YOUR RULES FINISH -->
<!-- Begin: Forum Jump -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table  class = "fb_blocktable" id="fb_bottomarea"  border = "0" cellspacing = "0" cellpadding = "0">
    <thead>
        <tr>
            <th class = "th-right">
                <?php
                //(JJ) FINISH: CAT LIST BOTTOM
                if ($fbConfig->enableforumjump)
                    require_once (JB_ABSSOURCESPATH . 'fb_forumjump.php');
                ?>
            </th>
        </tr>
    </thead>
	<tbody><tr><td></td></tr></tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<!-- Finish: Forum Jump -->