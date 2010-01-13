<?php
/**
* @version $Id: pollbox.php 1426 2010-01-02 09:13:33Z xillibit $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();


$app =& JFactory::getApplication();

$id     = intval(JRequest::getVar("id", ""));
$catid	= JRequest::getInt('catid', 0);

$kunena_config = & CKunenaConfig::getInstance ();

$catsallowed = explode(',',$kunena_config->pollallowedcategories);
if (in_array($catid, $catsallowed))
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
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
    <table class = "fb_blocktable" id = "fb_poll" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th align="left">
                    <div class = "fb_title_cover fbm">
                        <span class = "fb_title fbl"><?php echo _KUNENA_POLL_NAME; ?> <?php echo $dataspollresult[0]->title; ?></span>
                    </div>

                    <img id = "BoxSwitch_polls__polls_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                </th>
            </tr>
        </thead>
        <tbody id = "polls_tbody">
                <tr class = "<?php echo KUNENA_BOARD_CLASS;?>sectiontableentry2">
                    <td class = "td-1 fbm" align="left">
                        <div class = "polldesc">
<?php
if ( $dataspollusers[0]->userid == $kunena_my->id || $kunena_my->id == "0")//if the user has already voted for this poll
{ ?>
                          <table border = "0" cellspacing = "0" cellpadding = "0" width="100%">
                          <?php foreach ($dataspollresult as $row)
                          { ?>
                            <tr><td><?php echo $row->text; ?></td><td><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default_ex/images/bar.gif"; ?>" height = "10" width = "<?php if(isset($row->votes)) { echo ($row->votes*25)/5; } else { echo "0"; }?>"/></td><td><?php if(isset($row->votes) && ($row->votes > 0)) { echo $row->votes; } else { echo _KUNENA_POLL_NO_VOTE; } ?></td><td><?php if($row->votes != "0") { echo round(($row->votes*100)/$nbvoters,1)."%"; } else { echo "0%"; } ?></td></tr>
                          <?php
                          }?>
                            <tr><td colspan="4"><?php if(empty($nbvoters)){$nbvoters = "0";} echo _KUNENA_POLL_VOTERS_TOTAL."<b>".$nbvoters."</b> "; if($kunena_config->pollresultsuserslist){ if(!empty($pollusersvoted)){ echo " ( "; foreach($pollusersvoted as $row){ echo CKunenaLink::GetProfileLink($kunena_config, $row->userid, ($kunena_config->username ? $row->username : $row->name))." "; } echo " ) "; } } ?></td></tr>

                          <?php
                          if ($kunena_my->id == "0")
                          {
                          ?><tr><td colspan="4"><?php echo _KUNENA_POLL_NOT_LOGGED; ?> </td></tr>
                          <?php
						  }
						  else
						  {
						    if (!$kunena_config->pollallowvoteone)
						    {?>
						    	<tr><td colspan="4"><a href = "<?php echo CKunenaLink::GetPollURL($kunena_config, 'vote', $id, $catid);?>" /><?php echo _KUNENA_POLL_BUTTON_VOTE; ?></a></td></tr>
						  <?php
						    }else {
						    	?>
								<tr><td colspan="4"><a href = <?php echo CKunenaLink::GetPollURL($kunena_config, 'changevote', $id, $catid); ?> /><?php echo _KUNENA_POLL_BUTTON_CHANGEVOTE; ?></a></td></tr>
								<?php
						    }
						  } ?>
                		  </table>
<?php
}
elseif ((strftime("%Y-%m-%d %H:%M:%S",time()) <= $dataspollresult[0]->polltimetolive) || $dataspollresult[0]->polltimetolive == "0000-00-00 00:00:00")
{
	?>
	<div style="font-weight:bold;" id="poll_text_help"></div>
	<?php
	echo "<fieldset><legend style=\"font-size: 14px;\">"._KUNENA_POLL_OPTIONS."</legend><ul>";
    for ($i=0; $i < sizeof($dataspollresult);$i++)
    {
       echo "<li><input type=\"radio\" name=\"radio\" id=\"radio_name".$i."\" value=\"".$dataspollresult[$i]->id."\" />".$dataspollresult[$i]->text."</li>";
    }
       echo "</ul></fieldset>";
       $button_vote = "<input type=\"button\" value=\""._KUNENA_POLL_BUTTON_VOTE."\" onClick=\"javascript:ajax(".sizeof($dataspollresult).",".$id.",'results');\" />";
       echo "<div class=\"poll_center\" id=\"poll_buttons\">".$button_vote;
       if($dataspollusers[0]->userid == $kunena_my->id) {
       ?>
       <a href = <?php echo CKunenaLink::GetPollURL($kunena_config, 'changevote', $id, $catid); ?> /><?php echo _KUNENA_POLL_BUTTON_CHANGEVOTE; ?></a><?php
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
     	<tr><td><?php echo $row->text; ?></td><td><img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_JLIVEURL."components/com_kunena/template/default_ex/images/bar.gif"; ?>" height = "10" width = "<?php if(isset($row->votes)) { echo ($row->votes*25)/5; } else { echo "0"; }?>"/></td><td><?php if(isset($row->votes) && ($row->votes > 0)) { echo $row->votes; } else { echo _KUNENA_POLL_NO_VOTE; } ?></td><td><?php if($row->votes != "0") { echo round(($row->votes*100)/$nbvoters,1)."%"; } else { echo "0%"; } ?></td></tr>
     <?php
	 }?>
     <tr><td colspan="4"><?php if(empty($nbvoters)){$nbvoters = "0";} echo _KUNENA_POLL_VOTERS_TOTAL."<b>".$nbvoters."</b> "; if($kunena_config->pollresultsuserslist){ if(!empty($pollusersvoted)){ echo " ( "; foreach($pollusersvoted as $row){ echo CKunenaLink::GetProfileLink($kunena_config, $row->userid, ($kunena_config->username ? $row->username : $row->name))." "; } echo " ) "; } } ?></td></tr>
     <?php
     if ($kunena_my->id == "0")
     { ?>
     	<tr><td colspan="4"><?php echo _KUNENA_POLL_NOT_LOGGED; ?> </td></tr>
     <?php
	 }
	 else
	 {
						    if (!$kunena_config->pollallowvoteone)
						    {?>
						    	<tr><td colspan="4"><a href = "<?php echo CKunenaLink::GetPollURL($kunena_config, 'vote', $id, $catid);?>" /><?php echo _KUNENA_POLL_BUTTON_VOTE; ?></a></td></tr>
						  <?php
						    }else {
						    	?>
								<tr><td colspan="4"><a href = <?php echo CKunenaLink::GetPollURL($kunena_config, 'changevote', $id, $catid); ?> /><?php echo _KUNENA_POLL_BUTTON_CHANGEVOTE; ?></a></td></tr>
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