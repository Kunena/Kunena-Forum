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
defined( '_JEXEC' ) or die();


$kunena_config =& CKunenaConfig::getInstance();

$signature = $userinfo->signature;

$usr_signature = '';
if ($signature)
{
	$kunena_emoticons = smile::getEmoticons(0);
	$signature = stripslashes($signature);
	$signature = smile::smileReplace($signature, 0, $kunena_config->disemoticons, $kunena_emoticons);
	$signature = nl2br($signature);
	$signature = str_replace("<P>&nbsp;</P><br />", "", $signature);
	$signature = str_replace("</P><br />", "</P>", $signature);
	$signature = str_replace("<P><br />", "<P>", $signature);
	$usr_signature = $signature;
}
$registerDate = strftime(_KUNENA_DT_DATETIME_FMT, strtotime($userinfo->registerDate));
$lastvisitDate = strftime(_KUNENA_DT_DATETIME_FMT, strtotime($userinfo->lastvisitDate));
?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class = "kblocktable" id = "kforumprofile_sub" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
  <thead>
    <tr>
      <th colspan = "2"> <div class = "ktitle_cover km"> <span class = "ktitle kl"><?php echo _KUNENA_MYPROFILE_SUMMARY; ?></span> </div>
       <img id = "BoxSwitch_kusersummary__kusersummary_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody  class = "kmyprofile_general" id = "kusersummary_tbody">
    <tr class="ksth ks">
      <th colspan="2" > <center>
          <?php echo _KUNENA_MYPROFILE_PERSONAL_INFO; ?>
        </center></th>
    </tr>
    <?php  if ( $kunena_config->userlist_name ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_NAME; ?></b> </td>
      <td  class = "td-2 km"><?php echo $userinfo->name; ?></td>
    </tr>
    <?php } ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_USERNAME; ?></b> </td>
      <td  class = "td-2 km"><?php echo $userinfo->username; ?></td>
    </tr>
    <?php  if ( $kunena_config->showemail && $userinfo->hideEmail==0 ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_EMAIL; ?></b> </td>
      <td  class = "td-2 km"><?php echo $userinfo->email; ?></td>
    </tr>
    <?php } ?>
    <?php  if ( $kunena_config->userlist_usertype ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_USERTYPE; ?></b> </td>
      <td  class = "td-2 km"><?php echo $userinfo->usertype; ?></td>
    </tr>
   	<?php }
    if($kunena_config->userlist_joindate){ ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_REGISTERDATE; ?></b> </td>
      <td  class = "td-2 km"><?php echo $registerDate; ?></td>
    </tr>
    <?php }
    if($kunena_config->userlist_lastvisitdate){ ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_LASTVISITDATE; ?></b> </td>
      <td  class = "td-2 km"><?php echo $lastvisitDate; ?></td>
    </tr>
    <?php } ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_POSTS; ?></b> </td>
      <td  class = "td-2 km"><?php echo $userinfo->posts; ?></td>
    </tr>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_PROFILEVIEW; ?></b> </td>
      <td  class = "td-2 km"><?php echo $userinfo->uhits; ?></td>
    </tr>
    <tr class="ksth ks">
      <th colspan="2" > <center>
          <?php echo _KUNENA_MYPROFILE_ADDITIONAL_INFO; ?>
        </center></th>
    </tr>
     <?php  if ( $userinfo->personalText !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_PERSONALTEXT; ?></b> </td>
      <td  class = "td-2 km"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->personalText)); ?></td>
    </tr>
    <?php }?>
     <?php  if ( $userinfo->gender !=0 ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_GENDER; ?></b> </td>
      <td  class = "td-2 km"><?php if( $userinfo->gender == 1 ) { echo _KUNENA_MYPROFILE_MALE; } else if ( $userinfo->gender == 2 ) { echo _KUNENA_MYPROFILE_FEMALE; } else { }  ?></td>
    </tr>
    <?php } ?>
    <?php  if ($userinfo->birthdate !='0001-01-01' AND $userinfo->birthdate !='0000-00-00') {
		$birthday = strftime(_KUNENA_DT_MONTHDAY_FMT, strtotime($userinfo->birthdate));
    ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_PROFILE_BIRTHDAY; ?></b> </td>
      <td  class = "td-2 km"><?php echo $birthday; ?></td>
    </tr>
    <?php }?>
    <?php  if ( $userinfo->location !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_LOCATION; ?></b> </td>
      <td  class = "td-2 km"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->location)); ?></td>
    </tr>
    <?php }?>
    <?php  if ( $userinfo->ICQ !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_ICQ; ?></b> </td>
      <td  class = "td-2 km"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->ICQ)); ?></td>
    </tr>
    <?php }?>
    <?php  if ( $userinfo->AIM !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_AIM; ?></b> </td>
      <td  class = "td-2 km"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->AIM)); ?></td>
    </tr>
     <?php }?>
    <?php  if ( $userinfo->YIM !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_YIM; ?></b> </td>
      <td  class = "td-2 km"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->YIM)); ?></td>
    </tr>
      <?php }?>
    <?php  if ( $userinfo->MSN !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_MSN; ?></b> </td>
      <td  class = "td-2 km"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->MSN)); ?></td>
    </tr>
      <?php }?>
    <?php  if ( $userinfo->SKYPE !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_SKYPE; ?></b> </td>
      <td  class = "td-2 km"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->SKYPE)); ?></td>
    </tr>
    <?php }?>
    <?php  if ( $userinfo->TWITTER !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_TWITTER; ?></b> </td>
      <td  class = "td-2 km"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->TWITTER)); ?></td>
    </tr>
    <?php }?>
    <?php  if ( $userinfo->FACEBOOK !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_FACEBOOK; ?></b> </td>
      <td  class = "td-2 km"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->FACEBOOK)); ?></td>
    </tr>
    <?php }?>
     <?php  if ( $userinfo->GTALK !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_GTALK; ?></b> </td>
      <td  class = "td-2 km"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->GTALK)); ?></td>
    </tr>
    <?php }?>
     <?php  if ( $userinfo->websiteurl !='' ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_WEBSITE; ?></b> </td>
      <td  class = "td-2 km"><a href="http://<?php echo kunena_htmlspecialchars(stripslashes($userinfo->websiteurl)); ?>" target="_blank"><?php echo kunena_htmlspecialchars(stripslashes($userinfo->websitename)); ?></a></td>
    </tr>
    <?php }?>
    <?php  if ( !empty($usr_signature) ) { ?>
    <tr class ="ksectiontableentry1">
      <td  class = "td-1 km"><b><?php echo _KUNENA_MYPROFILE_SIGNATURE; ?></b> </td>
      <td  class = "td-2 km"><div class="msgsignature"><div><?php echo $usr_signature; ?></div></div></td>
    </tr>
     <?php }?>
  </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
