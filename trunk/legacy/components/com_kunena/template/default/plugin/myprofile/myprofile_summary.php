<?php
/**
* @version $Id: myprofile_summary.php 833 2008-07-15 04:16:46Z fxstein $
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
        $database->setQuery("select fbsignature from #__comprofiler where user_id=$my->id");
        $signature = $database->loadResult();
    }
    else
    {
        $signature = $userinfo->signature;
    }

    if ($signature)
    {
        $signature = stripslashes($signature);
        $signature = stripslashes(smile::smileReplace($signature, 0, $fbConfig->disemoticons, $smileyList));
        $signature = str_replace("\n", "<br />", $signature);
        $signature = str_replace("<P>&nbsp;</P><br />", "", $signature);
        $signature = str_replace("</P><br />", "</P>", $signature);
        $signature = str_replace("<P><br />", "<P>", $signature);
        //wordwrap:
        $signature = smile::htmlwrap($signature, $fbConfig->wrap);
        $signature = nl2br($signature);
        //restore the \n (were replaced with _CTRL_) occurences inside code tags, but only after we have striplslashes; otherwise they will be stripped again
        //$signature = stripslashes($signature);
        //$signature = str_replace("_CRLF_", "\\n", $signature);
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
      <th colspan = "2"> <div class = "fb_title_cover"> <span class = "fb_title"><?php echo _FB_MYPROFILE_SUMMARY; ?></span> </div></th>
    </tr>
  </thead>
  <tbody  class = "fb_myprofile_general">
    <tr class="fb_sth">
      <th colspan="2" > <center>
          <?php echo _FB_MYPROFILE_PERSONAL_INFO; ?>
        </center></th>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_NAME; ?></b> </td>
      <td><?php echo $juserinfo->name; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_USERNAME; ?></b> </td>
      <td><?php echo $juserinfo->username; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_EMAIL; ?></b> </td>
      <td><?php echo $juserinfo->email; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_USERTYPE; ?></b> </td>
      <td><?php echo $juserinfo->usertype; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_REGISTERDATE; ?></b> </td>
      <td><?php echo $juserinfo->registerDate; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_LASTVISITDATE; ?></b> </td>
      <td><?php echo $juserinfo->lastvisitDate; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_POSTS; ?></b> </td>
      <td><?php echo $numPosts; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_PROFILEVIEW; ?>:</b> </td>
      <td><?php echo $userinfo->uhits; ?></td>
    </tr>
    <tr class="fb_sth">
      <th colspan="2" > <center>
          <?php echo _FB_MYPROFILE_ADDITIONAL_INFO; ?>
        </center></th>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_PERSONALTEXT; ?></b> </td>
      <td><?php echo $userinfo->personalText; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_GENDER; ?></b> </td>
      <td><?php if( $userinfo->gender == 1 ) { echo _FB_MYPROFILE_MALE; } else if ( $userinfo->gender == 2 ) { echo _FB_MYPROFILE_FEMALE; }?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_BIRTHDATE; ?></b> </td>
      <td><?php echo $userinfo->birthdate; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_LOCATION; ?></b> </td>
      <td><?php echo $userinfo->location; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_ICQ; ?></b> </td>
      <td><?php echo $userinfo->ICQ; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_AIM; ?></b> </td>
      <td><?php echo $userinfo->AIM; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_YIM; ?></b> </td>
      <td><?php echo $userinfo->YIM; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_MSN; ?></b> </td>
      <td><?php echo $userinfo->MSN; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_SKYPE; ?></b> </td>
      <td><?php echo $userinfo->SKYPE; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_GTALK; ?></b> </td>
      <td><?php echo $userinfo->GTALK; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_WEBSITE_NAME; ?></b> </td>
      <td><?php echo $userinfo->websitename; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_WEBSITE_URL; ?></b> </td>
      <td><?php echo $userinfo->websiteurl; ?></td>
    </tr>
    <tr>
      <td><b><?php echo _FB_MYPROFILE_SIGNATURE; ?></b> </td>
      <td><?php echo $usr_signature; ?></td>
    </tr>
  </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
