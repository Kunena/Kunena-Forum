<?php
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$do = JRequest::getVar("do", "");
$id = intval(JRequest::getVar("id", ""));

if($do == "vote"){     
// BEGIN: BOX POLL
include_once (KUNENA_PATH_LIB .DS. 'kunena.poll.js.php');
//Load the query for get the informations for the poll
$kunena_db->setQuery("SELECT b.id,title,text FROM #__fb_polls AS a JOIN #__fb_polls_options AS b ON a.threadid=b.pollid WHERE a.threadid={$id}");
$kunena_db->query();
$this_poll_datas = $kunena_db->loadObjectList();
?>
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
                        <span class = "fb_title fbl"><?php echo _KUNENA_POLL_NAME; ?> <?php echo $this_poll_datas[0]->title; ?></span>
                    </div>

                    <img id = "BoxSwitch_announcements__announcements_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>
        <tbody id = "announcements_tbody">
                <tr class = "<?php echo $boardclass ;?>sectiontableentry2">
                    <td class = "td-1 fbm" align="left">
                        <div class = "anndesc">
                       
<?php 
 echo "<fieldset><legend style=\"font-size: 14px;\">"._KUNENA_POLL_OPTIONS."</legend><ul>"; 
                          for($i=0; $i < sizeof($this_poll_datas);$i++){               
       echo "<li><input type=\"radio\" name=\"radio\" id=\"radio_name".$i."\" value=\"".$this_poll_datas[$i]->id."\" />".$this_poll_datas[$i]->text."</li>";
      }
      echo "</ul></fieldset>";    
        $button_vote = "<input type=\"button\" value=\""._KUNENA_POLL_BUTTON_VOTE."\" onClick=\"javascript:ajax(".sizeof($this_poll_datas).",".$id.");\" />";      
        echo "<div class=\"poll_center\" id=\"poll_buttons\">".$button_vote;
      
      ?>        
        &nbsp;<?php echo CKunenaLink::GetThreadPageLink($fbConfig, "view", $catid, $id, "1", $limit, _KUNENA_POLL_NAME_URL_RESULT, $anker='', $rel='follow', $class=''); ?></div>                               
                    </td>
                </tr>
        </tbody>
    </table>
    </div>
</div>
</div>
</div>
</div>
<?php 
 }
?>