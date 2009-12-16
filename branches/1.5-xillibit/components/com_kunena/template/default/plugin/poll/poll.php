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
 } elseif($do == "results"){
          $kunena_db->setQuery("SELECT * FROM #__fb_polls_options WHERE pollid=$id AND id=$value_choosed");
          $kunena_db->query() or trigger_dberror('Unable to load poll.');
          $polloption = $kunena_db->loadObject();
          if (!$polloption) break; // OPTION DOES NOT EXIST
                 
          $kunena_db->setQuery("SELECT votes FROM #__fb_polls_users WHERE pollid=$id AND userid=$kunena_my->id");
          $kunena_db->query() or trigger_dberror('Unable to load datas from poll users.');
          $votes = $kunena_db->loadResult();
          if(empty($votes)) {
            $kunena_db->setQuery("INSERT INTO #__fb_polls_users (pollid,userid,votes) VALUES('$id','{$kunena_my->id}',1)");
            $kunena_db->query();
            if($votes == null){ //need this if because when the $votes is null the thing SET votes=votes+1 doesn't work
              $kunena_db->setQuery("UPDATE #__fb_polls_options SET votes=1 WHERE id=$value_choosed");
              $kunena_db->query();
            }else { 
              $kunena_db->setQuery("UPDATE #__fb_polls_options SET votes=votes+1 WHERE id=$value_choosed");
              $kunena_db->query();
            }
            echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"1\";</script>";
          }
          else if($votes = $fbConfig->pollallowvotes) {
            $kunena_db->setQuery("UPDATE #__fb_polls_users SET votes=votes+1 WHERE pollid=$id AND userid={$kunena_my->id}");
            $kunena_db->query();
            $kunena_db->setQuery("UPDATE #__fb_polls_options SET votes=votes+1 WHERE id=$value_choosed");
            $kunena_db->query();
            echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"1\";</script>";
         }
                
         if ($kunena_db->getErrorNum()) { //inform the user that an error has occured
          echo "<script language = \"JavaScript\" type = \"text/javascript\">var infoserver=\"0\";</script>";                   
         }
 }
?>