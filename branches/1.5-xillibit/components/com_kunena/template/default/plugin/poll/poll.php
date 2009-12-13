<?php
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$do = JRequest::getVar("do", "");
$id = intval(JRequest::getVar("id", ""));

if($do == "vote"){     
// BEGIN: BOX POLL
include_once (KUNENA_PATH_LIB .DS. 'kunena.poll.js.php');
//Load the query for get the informations for the poll
$kunena_db->setQuery("SELECT title,text,options FROM #__fb_polls AS a LEFT JOIN #__fb_polls_datas AS b ON a.topicid=b.pollid WHERE a.topicid={$id}");
$this_poll_data = $kunena_db->loadObjectlist();
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
                        <span class = "fb_title fbl"><?php echo _KUNENA_POLL_NAME; ?> <?php echo stripslashes($this_poll_data[0]->title); ?></span>
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
                          for($i=0; $i < $this_poll_data[0]->options;$i++){               
       echo "<li><input type=\"radio\" name=\"radio\" id=\"radio_name".$i."\" value=\"".$this_poll_data[$i]->text."\" />".stripslashes($this_poll_data[$i]->text)."</li>";
      }
      echo "</ul></fieldset>";    
        $button_vote = "<input type=\"button\" value=\""._KUNENA_POLL_BUTTON_VOTE."\" onClick=\"javascript:ajax(".$this_poll_data[0]->options.",".$id.");\" />";      
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