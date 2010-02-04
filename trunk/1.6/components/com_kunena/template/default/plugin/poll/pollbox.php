<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();


$app =& JFactory::getApplication();

$id     = intval(JRequest::getVar("id", ""));
$catid	= JRequest::getInt('catid', 0);
$kunena_my = &JFactory::getUser ();
$kunena_config = & CKunenaConfig::getInstance ();
$kunena_db = &JFactory::getDBO();
$kunena_db->setQuery ( "SELECT allow_polls FROM #__fb_categories WHERE id='{$catid}'" );
$poll_allowed = $kunena_db->loadResult ();
check_dberror ( 'Unable to load review flag from categories.' );

if ($poll_allowed)
{
  CKunenaPolls::call_javascript_vote();
  $dataspollresult = CKunenaPolls::get_poll_data($id);
  //To show the number total of votes for the poll
  $nbvoters = CKunenaPolls::get_number_total_voters($id);
  //To show the usernames of the users which have voted for this poll
  $pollusersvoted = CKunenaPolls::get_users_voted($id);
  //To know if an user has already voted for this poll
  $dataspollusers = CKunenaPolls::get_data_poll_users($kunena_my->id,$id);
  if (!isset($dataspollusers[0]->userid) && !isset($dataspollusers[0]->pollid))
  {
      $dataspollusers[0]->userid = null;
      $dataspollusers[0]->pollid = null;
  }
  ?>
  <div>
            <?php
            if (file_exists(KUNENA_ABSTMPLTPATH . '/pathway.php'))
            {
                require_once (KUNENA_ABSTMPLTPATH . '/pathway.php');
            }
            else
            {
                require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'pathway.php');
            }
            ?>
  </div>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
    <table class = "kblocktable" id = "kpoll" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "ktitle_cover km">
                        <span class = "ktitle kl"><?php echo JText::_(COM_KUNENA_POLL_NAME); ?> <?php echo CKunenaTools::parseText ($dataspollresult[0]->title); ?></span>
                    </div>

                    <img id = "BoxSwitch_polls__polls_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>
        <tbody id = "polls_tbody">
                <tr class = "ksectiontableentry2">
                    <td class = "td-1 km" align="left">
                        <div class = "polldesc">
<?php
if ( $dataspollusers[0]->userid == $kunena_my->id || $kunena_my->id == "0")//if the user has already voted for this poll
{ ?>
                          <table border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                          <?php foreach ($dataspollresult as $row)
                          { ?>
                            <tr><td><?php echo CKunenaTools::parseText ( $row->text ); ?></td><td><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default/backgrounds/images/bar.png"; ?>" height = "10" width = "<?php if(isset($row->votes)) { echo ($row->votes*25)/5; } else { echo "0"; }?>"/></td><td><?php if(isset($row->votes) && ($row->votes > 0)) { echo $row->votes; } else { echo JText::_(COM_KUNENA_POLL_NO_VOTE); } ?></td><td><?php if($row->votes != "0") { echo round(($row->votes*100)/$nbvoters,1)."%"; } else { echo "0%"; } ?></td></tr>
                          <?php
                          }?>
                            <tr><td colspan="4"><?php if(empty($nbvoters)){$nbvoters = "0";} echo JText::_(COM_KUNENA_POLL_VOTERS_TOTAL)."<b>".$nbvoters."</b> "; if($kunena_config->pollresultsuserslist){ if(!empty($pollusersvoted)){ echo " ( "; foreach($pollusersvoted as $row){ echo CKunenaLink::GetProfileLink($kunena_config, $row->userid, ($kunena_config->username ? $row->username : $row->name))." "; } echo " ) "; } } ?></td></tr>

                          <?php
                          if ($kunena_my->id == "0")
                          {
                          ?><tr><td colspan="4"><?php echo JText::_(COM_KUNENA_POLL_NOT_LOGGED); ?> </td></tr>
                          <?php
						  }
						  else
						  {
						    if (!$kunena_config->pollallowvoteone)
						    {?>
						    	<tr><td colspan="4"><a href = "<?php echo CKunenaLink::GetPollURL($kunena_config, 'vote', $id, $catid);?>" /><?php echo JText::_(COM_KUNENA_POLL_BUTTON_VOTE); ?></a></td></tr>
						  <?php
						    }else {
						    	?>
								<tr><td colspan="4"><a href = <?php echo CKunenaLink::GetPollURL($kunena_config, 'changevote', $id, $catid); ?> /><?php echo JText::_(COM_KUNENA_POLL_BUTTON_CHANGEVOTE); ?></a></td></tr>
								<?php
						    }
						  } ?>
                		  </table>
<?php
}
elseif ((strftime("%Y-%m-%d %H:%M:%S",time()) <= $dataspollresult[0]->polltimetolive) || $dataspollresult[0]->polltimetolive == "0000-00-00 00:00:00")
{
	?>
	<div id="poll_text_help"></div>
	<fieldset><legend id="poll_xd" style="font-size: 14px;"><?php echo JText::_(COM_KUNENA_POLL_OPTIONS); ?></legend>
	<ul>
	<?php
    for ($i=0; $i < sizeof($dataspollresult);$i++)
    {
       echo "<li><input type=\"radio\" name=\"radio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" />".CKunenaTools::parseText ($dataspollresult[$i]->text )."</li>";
    }
    	?>
    </ul>
    </fieldset>
		<div class="poll_center" id="poll_buttons">
       <input id="k_poll_button_vote" type="button" value="<?php echo JText::_(COM_KUNENA_POLL_BUTTON_VOTE); ?>" />
       <input type="hidden" id="k_poll_nb_options" name="pollid" value="<?php echo sizeof($dataspollresult); ?>">
       <input type="hidden" id="k_poll_id" name="nb_options" value="<?php echo $id; ?>">
       <input type="hidden" id="k_poll_do" name="nb_options" value="pollvote">
       <?php
       if($dataspollusers[0]->userid == $kunena_my->id) {
       ?>
       <a href = <?php echo CKunenaLink::GetPollURL($kunena_config, 'changevote', $id, $catid); ?> /><?php echo JText::_(COM_KUNENA_POLL_BUTTON_CHANGEVOTE); ?></a>
       </div>
       <?php
       }
}
else
{
?>
<table border = "0" cellspacing = "0" cellpadding = "0" width="100%">
	 <?php
	 foreach ($dataspollresult as $row)
	 {
	 ?>
     	<tr><td><?php echo CKunenaTools::parseText ($row->text); ?></td><td><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default/images/backgrounds/bar.png"; ?>" height = "10" width = "<?php if(isset($row->votes)) { echo ($row->votes*25)/5; } else { echo "0"; }?>"/></td><td><?php if(isset($row->votes) && ($row->votes > 0)) { echo $row->votes; } else { echo JText::_(COM_KUNENA_POLL_NO_VOTE); } ?></td><td><?php if($row->votes != "0") { echo round(($row->votes*100)/$nbvoters,1)."%"; } else { echo "0%"; } ?></td></tr>
     <?php
	 }?>
     <tr><td colspan="4"><?php if(empty($nbvoters)){$nbvoters = "0";} echo JText::_(COM_KUNENA_POLL_VOTERS_TOTAL)."<b>".$nbvoters."</b> "; if($kunena_config->pollresultsuserslist){ if(!empty($pollusersvoted)){ echo " ( "; foreach($pollusersvoted as $row){ echo CKunenaLink::GetProfileLink($kunena_config, $row->userid, ($kunena_config->username ? $row->username : $row->name))." "; } echo " ) "; } } ?></td></tr>
     <?php
     if ($kunena_my->id == "0")
     { ?>
     	<tr><td colspan="4"><?php echo JText::_(COM_KUNENA_POLL_NOT_LOGGED); ?> </td></tr>
     <?php
	 }
	 else
	 {
						    if (!$kunena_config->pollallowvoteone)
						    {?>
						    	<tr><td colspan="4"><a href = "<?php echo CKunenaLink::GetPollURL($kunena_config, 'vote', $id, $catid);?>" /><?php echo JText::_(COM_KUNENA_POLL_BUTTON_VOTE); ?></a></td></tr>
						  <?php
						    }else {
						    	?>
								<tr><td colspan="4"><a href = <?php echo CKunenaLink::GetPollURL($kunena_config, 'changevote', $id, $catid); ?> /><?php echo JText::_(COM_KUNENA_POLL_BUTTON_CHANGEVOTE); ?></a></td></tr>
								<?php
						    }
	 } ?>
                		      </table>
<?php
}
?>
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
<?php
}
?>