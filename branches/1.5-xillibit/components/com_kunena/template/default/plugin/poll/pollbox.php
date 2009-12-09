<?php
/**
* @version $Id: 
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$app =& JFactory::getApplication();
$document =& JFactory::getDocument();
$id = intval(JRequest::getVar("id", ""));

 $kunena_db = &JFactory::getDBO();
  $kunena_db->setQuery("SELECT * FROM #__fb_polls_datas AS a JOIN #__fb_polls AS b ON b.topicid=a.pollid WHERE a.pollid=$id");
  $kunena_db->query() or trigger_dberror('Unable to load poll.');
  $dataspollresult = $kunena_db->loadObjectList();

if($kunena_my->id > "1"){

   
  ?>  
  <div>
            <?php
            if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_pathway.php')) {
                require_once (KUNENA_ABSTMPLTPATH . '/fb_pathway.php');
            }
            else {
                require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_pathway.php');
            }
            ?>
        </div>                           
  <div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
    <table class = "fb_blocktable" id = "fb_announcement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "fb_title_cover fbm">
                        <span class = "fb_title fbl"><?php echo _KUNENA_POLL_NAME; ?> <?php echo stripslashes($dataspollresult[0]->title); ?></span>
                    </div>

                    <img id = "BoxSwitch_announcements__announcements_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>
        <tbody id = "announcements_tbody">
                <tr class = "<?php echo $boardclass ;?>sectiontableentry2">
                    <td class = "td-1 fbm" align="left">
                        <div class = "anndesc">
                          <table border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                          <?php for($i=0; $i < $dataspollresult[0]->options;$i++){ ?>
                            <tr><td><?php echo $dataspollresult[$i]->text; ?></td><td><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default_ex/images/bar.gif"; ?>" height = "10" width = "<?php if(isset($dataspollresult[$i]->hits)) { echo ($dataspollresult[$i]->hits*25)/5; } else { echo "0"; }?>"/></td><td><?php if(isset($dataspollresult[$i]->hits)) { echo $dataspollresult[$i]->hits; } else { echo _KUNENA_POLL_NO_VOTE; } ?></td><td><?php if(isset($dataspollresult[$i]->hits)) { echo round(($dataspollresult[$i]->hits*100)/$dataspollresult[0]->voters,1)."%"; } else { echo "0%"; } ?></td></tr>
                          <?php }?>
                            <tr><td colspan="4"><?php if(empty($dataspollresult[0]->voters)){$dataspollresult[0]->voters = "0";} echo _KUNENA_POLL_VOTERS_TOTAL."<b>".$dataspollresult[0]->voters."</b>"; ?></tr></td>
                            <tr><td colspan="4"><a href = "<?php echo CKunenaLink::GetPollURL($fbConfig, 'vote', $id, $catid);?>"> <input type="button" name="lienresult" value="<?php echo _KUNENA_POLL_BUTTON_VOTE; ?>" /></a></td></tr>
                          </table>
                        </div>
                    </td>
                </tr>
        </tbody>
    </table>
    </div>
</div>
</div>
</div>
</div>        
<?php } else { ?>
<div>
            <?php
            if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_pathway.php')) {
                require_once (KUNENA_ABSTMPLTPATH . '/fb_pathway.php');
            }
            else {
                require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_pathway.php');
            }
            ?>
        </div>                           
  <div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
    <table class = "fb_blocktable" id = "fb_announcement" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "fb_title_cover fbm">
                        <span class = "fb_title fbl"><?php echo _KUNENA_POLL_NAME; ?> <?php echo stripslashes($dataspollresult[0]->title); ?></span>
                    </div>

                    <img id = "BoxSwitch_announcements__announcements_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>
        <tbody id = "announcements_tbody">
                <tr class = "<?php echo $boardclass ;?>sectiontableentry2">
                    <td class = "td-1 fbm" align="left">
                        <div class = "anndesc">
                          <table border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                          <?php for($i=0; $i < $dataspollresult[0]->options;$i++){ ?>
                            <tr><td><?php echo $dataspollresult[$i]->text; ?></td><td><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default_ex/images/bar.gif"; ?>" height = "10" width = "<?php if(isset($dataspollresult[$i]->hits)) { echo ($dataspollresult[$i]->hits*25)/5; } else { echo "0"; }?>"/></td><td><?php if(isset($dataspollresult[$i]->hits)) { echo $dataspollresult[$i]->hits; } else { echo _KUNENA_POLL_NO_VOTE; } ?></td><td><?php if(isset($dataspollresult[$i]->hits)) { echo round(($dataspollresult[$i]->hits*100)/$dataspollresult[0]->voters,1)."%"; } else { echo "0%"; } ?></td></tr>
                          <?php }?>
                            <tr><td colspan="4"><?php if(empty($dataspollresult[0]->voters)){$dataspollresult[0]->voters = "0";} echo _KUNENA_POLL_VOTERS_TOTAL."<b>".$dataspollresult[0]->voters."</b> "._KUNENA_POLL_NOT_LOGGED; ?></tr></td>                            
                          </table>
                        </div>
                    </td>
                </tr>
        </tbody>
    </table>
    </div>
</div>
</div>
</div>
</div> 
<?php } ?>

