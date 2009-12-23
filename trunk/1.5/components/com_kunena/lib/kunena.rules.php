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

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

global $boardclass;

$kunena_db = &JFactory::getDBO();
$kunena_config =& CKunenaConfig::getInstance();
$document=& JFactory::getDocument();

$document->setTitle(_GEN_RULES . ' - ' . stripslashes($kunena_config->board_title));

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
          $kunena_db->setQuery("SELECT introtext, id FROM #__content WHERE id='{$kunena_config->rules_cid}'");
		  $j_introtext = $kunena_db->loadResult();

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
                if ($kunena_config->enableforumjump)
                    require_once (KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
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
