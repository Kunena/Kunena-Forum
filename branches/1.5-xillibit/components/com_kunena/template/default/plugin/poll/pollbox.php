<?php
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$app =& JFactory::getApplication();
$document =& JFactory::getDocument();
$id = intval(JRequest::getVar("id", ""));
$catsallowed = explode(',',$fbConfig->pollallowedcategories);
if (in_array($catid, $catsallowed)){

 $kunena_db = &JFactory::getDBO();
  $kunena_db->setQuery("SELECT * FROM #__fb_polls AS a JOIN #__fb_polls_options AS b ON a.threadid=b.pollid WHERE a.threadid=$id");
  $kunena_db->query() or trigger_dberror('Unable to load poll.');
  $dataspollresult = $kunena_db->loadAssocList();    
  $kunena_db->setQuery("SELECT SUM(votes) FROM #__fb_polls_options WHERE pollid=$id");
  $kunena_db->query() or trigger_dberror('Unable to count votes.');
  $nbvoters = $kunena_db->loadResult(); 
  if($fbConfig->pollallowvoteone){ 
    $kunena_db->setQuery("SELECT * FROM #__fb_polls_users WHERE userid=$kunena_my->id AND pollid=$id");
    $kunena_db->query() or trigger_dberror('Unable to load users poll.');
    $dataspollusers = $kunena_db->loadObjectList(); 
  }   
    if(!isset($dataspollusers[0]->userid) && !isset($dataspollusers[0]->pollid)) {
      $dataspollusers[0]->userid = null;
      $dataspollusers[0]->pollid = null;    
    }   
    
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
                        <span class = "fb_title fbl"><?php echo _KUNENA_POLL_NAME; ?> <?php echo $dataspollresult[0]['title']; ?></span>
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
                          <?php foreach($dataspollresult as $row){ ?>
                            <tr><td><?php echo $row['text']; ?></td><td><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default_ex/images/bar.gif"; ?>" height = "10" width = "<?php if(isset($row['votes'])) { echo ($row['votes']*25)/5; } else { echo "0"; }?>"/></td><td><?php if(isset($row['votes'])) { echo $row['votes']; } else { echo _KUNENA_POLL_NO_VOTE; } ?></td><td><?php if($row['votes'] != "0") { echo round(($row['votes']*100)/$nbvoters,1)."%"; } else { echo "0%"; } ?></td></tr>
                          <?php }?>
                            <tr><td colspan="4"><?php if(empty($nbvoters)){$nbvoters = "0";} echo _KUNENA_POLL_VOTERS_TOTAL."<b>".$nbvoters."</b>"; ?></td></tr>
                          
                          <?php if($dataspollusers[0]->userid == $kunena_my->id && $dataspollusers[0]->pollid == $id){?><tr><td colspan="4"><?php echo _KUNENA_POLL_SAVE_VOTE_ALREADY; }else { ?>
                          <?php if($kunena_my->id == "0") { ?><tr><td colspan="4"><?php echo _KUNENA_POLL_NOT_LOGGED; ?> </td></tr> <?php }else { ?> <tr><td colspan="4"><a href = "<?php echo CKunenaLink::GetPollURL($fbConfig, 'vote', $id, $catid);?>" /><?php echo _KUNENA_POLL_BUTTON_VOTE; ?></a><?php } } ?></td></tr>
                		</table>
                	 </div>	
        </tbody>
    </table>
    </div>
</div>
</div>
</div>
</div>
<?php } ?>