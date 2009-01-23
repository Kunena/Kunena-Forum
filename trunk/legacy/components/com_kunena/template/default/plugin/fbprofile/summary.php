<?php
/**
* @version $Id: summary.php 895 2008-08-03 06:15:11Z fxstein $
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
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $fbConfig;
    if ($fbConfig->cb_profile)
    {
        $database->setQuery("select fbsignature from #__comprofiler where user_id=$userid");
        $signature = $database->loadResult();
    }
    else
    {
        $signature = $userinfo->signature;
    }

    if ($signature)
    {
        $signature = stripslashes($signature);
        $signature = smile::smileReplace($signature, 0, $fbConfig->disemoticons, $smileyList);
        $signature = nl2br($signature);
        $signature = str_replace("<P>&nbsp;</P><br />", "", $signature);
        $signature = str_replace("</P><br />", "</P>", $signature);
        $signature = str_replace("<P><br />", "<P>", $signature);
        //wordwrap:
        $signature = smile::htmlwrap($signature, $fbConfig->wrap);
        //restore the \n (were replaced with _CTRL_) occurences inside code tags, but only after we have striplslashes; otherwise they will be stripped again
        //$signature = str_replace("_CRLF_", "\\n", stripslashes($signature));
        $usr_signature = $signature;
    }
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id = "fb_forumprofile_sub" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
  <thead>
    <tr>
      <th colspan = "2"> <div class = "fb_title_cover fbm"> <span class = "fb_title fbl"><?php echo _FB_MYPROFILE_SUMMARY; ?></span> </div>
       <img id = "BoxSwitch_fbusersummary__<?php echo $boardclass ;?>fbusersummary_tbody" class = "hideshow" src = "<?php echo JB_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody  class = "fb_myprofile_general" id = "<?php echo $boardclass ;?>fbusersummary_tbody">
    <tr class="fb_sth fbs">
      <th colspan="2" > <center>
          <?php echo _FB_MYPROFILE_PERSONAL_INFO; ?>
        </center></th>
    </tr>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_NAME; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->name; ?></td>
    </tr>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_USERNAME; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->username; ?></td>
    </tr>
    <?php  if ( $fbConfig->showemail && $userinfo->hideEmail==0 ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_EMAIL; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->email; ?></td>
    </tr>
    <?php } ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_USERTYPE; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->usertype; ?></td>
    </tr>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_REGISTERDATE; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->registerDate; ?></td>
    </tr>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_LASTVISITDATE; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->lastvisitDate; ?></td>
    </tr>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_POSTS; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $numPosts; ?></td>
    </tr>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_PROFILEVIEW; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->uhits; ?></td>
    </tr>
    <tr class="fb_sth fbs">
      <th colspan="2" > <center>
          <?php echo _FB_MYPROFILE_ADDITIONAL_INFO; ?>
        </center></th>
    </tr>
     <?php  if ( $userinfo->personalText !='' ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_PERSONALTEXT; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->personalText; ?></td>
    </tr>
    <?php }?>
     <?php  if ( $userinfo->gender !=0 ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_GENDER; ?></b> </td>
      <td  class = "td-2 fbm"><?php if( $userinfo->gender == 1 ) { echo _FB_MYPROFILE_MALE; } else if ( $userinfo->gender == 2 ) { echo _FB_MYPROFILE_FEMALE; } else { }  ?></td>
    </tr>
    <?php } ?>
    <?php  if ($userinfo->birthdate !='0001-01-01' AND $userinfo->birthdate !='0000-00-00') {?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_BIRTHDATE; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->birthdate; ?></td>
    </tr>
    <?php }?>
    <?php  if ( $userinfo->location !='' ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_LOCATION; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->location; ?></td>
    </tr>
    <?php }?>
    <?php  if ( $userinfo->ICQ !='' ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_ICQ; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->ICQ; ?></td>
    </tr>
    <?php }?>
    <?php  if ( $userinfo->AIM !='' ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_AIM; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->AIM; ?></td>
    </tr>
     <?php }?>
    <?php  if ( $userinfo->YIM !='' ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_YIM; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->YIM; ?></td>
    </tr>
      <?php }?>
    <?php  if ( $userinfo->MSN !='' ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_MSN; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->MSN; ?></td>
    </tr>
      <?php }?>
    <?php  if ( $userinfo->SKYPE !='' ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_SKYPE; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->SKYPE; ?></td>
    </tr>
    <?php }?>
     <?php  if ( $userinfo->GTALK !='' ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_GTALK; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $userinfo->GTALK; ?></td>
    </tr>
    <?php }?>
     <?php  if ( $userinfo->websiteurl !='' ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_WEBSITE; ?></b> </td>
      <td  class = "td-2 fbm"><a href="http://<?php echo $userinfo->websiteurl; ?>" target="_blank"><?php echo $userinfo->websitename; ?></a></td>
    </tr>
    <?php }?>
    <?php  if ( $usr_signature !='' ) { ?>
    <tr class ="<?php echo $boardclass; ?>sectiontableentry1">
      <td  class = "td-1 fbm"><b><?php echo _FB_MYPROFILE_SIGNATURE; ?></b> </td>
      <td  class = "td-2 fbm"><?php echo $usr_signature; ?></td>
    </tr>
     <?php }?>
  </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
