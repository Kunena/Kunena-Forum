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
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die('Restricted access');
?>
<style>
.fbwelcome {
	clear:both;
	margin-bottom:10px;
	padding:10px;
	font-size:12px;
	color:#536482;
	line-height:140%;
	border:1px solid #ddd;
}
.fbwelcome h3 {
	margin:0;
	padding:0;
}
table.thisform {
	width: 100%;
	padding: 10px;
	border-collapse: collapse;
}
table.thisform tr.row0 {
	background-color: #F7F8F9;
}
table.thisform tr.row1 {
	background-color: #eeeeee;
}
table.thisform th {
	font-size: 15px;
	font-weight: normal;
	font-variant: small-caps;
	padding-top: 6px;
	padding-bottom: 2px;
	padding-left: 4px;
	padding-right: 4px;
	text-align: left;
	height: 25px;
	color: #666666;
	background: url(../images/background.gif);
	background-repeat: repeat;
}
table.thisform td {
	padding: 3px;
	text-align: left;
}
.fbstatscover {
	padding:0px;
}
table.fbstat {
	background-color:#FFFFFF;
	border:1px solid #ddd;
	padding:1px;
	width:100%;
}
table.fbstat th {
	background:#EEE;
	border-bottom:1px solid #CCC;
	border-top:1px solid #EEE;
	color:#666;
	font-size:11px;
	padding:3px 4px;
	text-align:left;
}
table.fbstat td {
	font-size:11px;
	line-height:140%;
	padding:4px;
	text-align:left;
}
table.fbstat caption {
	clear:both;
	font-size:14px;
	font-weight:bold;
	margin:10px 0 2px 0;
	padding:2px;
	text-align:left;
}
table.fbstat .col1 {
	background-color:#F1F3F5;
}
table.fbstat .col2 {
	background-color: #FBFBFB;
}
</style>
<div class="fbwelcome">
  <h3><?php echo _KUNENA_WELCOME;?></h3>
  <p><?php echo _KUNENA_WELCOME_DESC;?></p>
</div>
<div style="border:1px solid #ddd; background:#FBFBFB;">
  <table class = "thisform">
    <tr class = "thisform">
      <td width = "100%" valign = "top" class = "thisform"><div id = "cpanel">
          <div style = "float:left;">
            <div class = "icon"> <a href = "index2.php?option=com_kunena&amp;task=showconfig" style = "text-decoration:none;" title = "<?php echo _COM_C_FBCONFIGDESC;?>"> <img src = "components/com_kunena/images/fbconfig.png"  align = "middle" border = "0"/> <span> <?php echo _COM_C_FBCONFIG; ?> </span></a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "index2.php?option=com_kunena&amp;task=showAdministration" style = "text-decoration:none;" title = "<?php echo _COM_C_FORUMDESC;?>"> <img src = "components/com_kunena/images/fbforumadm.png" align = "middle" border = "0"/> <span> <?php echo _COM_C_FORUM; ?> </span></a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "index2.php?option=com_kunena&amp;task=showprofiles" style = "text-decoration:none;" title = "<?php echo _COM_C_USERDESC;?>"> <img src = "components/com_kunena/images/fbuser.png"  align = "middle" border = "0"/> <span> <?php echo _COM_C_USER; ?> </span> </a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "index2.php?option=com_kunena&amp;task=browseFiles" style = "text-decoration:none;" title = "<?php echo _COM_C_FILESDESC;?>"> <img src = "components/com_kunena/images/fbfiles.png" align = "middle" border = "0"/> <span> <?php echo _COM_C_FILES; ?> </span></a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "index2.php?option=com_kunena&amp;task=browseImages" style = "text-decoration:none;" title = "<?php echo _COM_C_IMAGESDESC;?>"> <img src = "components/com_kunena/images/fbimages.png"  align = "middle" border = "0"/> <span> <?php echo _COM_C_IMAGES; ?> </span></a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "index2.php?option=com_kunena&amp;task=showCss" style = "text-decoration:none;" title = "<?php echo _COM_C_CSSDESC;?>"> <img src = "components/com_kunena/images/fbcss.png"  align = "middle" border = "0"/> <span> <?php echo _COM_C_CSS; ?> </span></a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "index2.php?option=com_kunena&amp;task=pruneforum" style = "text-decoration:none;" title = "<?php echo _COM_C_PRUNETABDESC;?>"> <img src = "components/com_kunena/images/fbtable.png"  align = "middle" border = "0"/> <span> <?php echo _COM_C_PRUNETAB; ?> </span></a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "index2.php?option=com_kunena&amp;task=syncusers" style = "text-decoration:none;" title = "<?php echo _COM_C_SYNCEUSERSDESC;?>"> <img src = "components/com_kunena/images/fbusers.png"  align = "middle" border = "0"/> <span> <?php echo _KUNENA_SYNC_USERS; ?> </span></a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "http://www.kunena.com" target = "_blank" style = "text-decoration:none;" title = "<?php echo _COM_C_SUPPORTDESC;?>"> <img src = "components/com_kunena/images/fbtechsupport.png"  align = "middle" border = "0"/> <span> <?php echo _COM_C_SUPPORT; ?> </span></a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "index2.php?option=com_kunena&amp;task=showsmilies" style = "text-decoration:none;" title = "<?php echo _KUNENA_EMOTICONS_EDIT_SMILIES;?>"> <img src = "components/com_kunena/images/fbsmiley.png"  align = "middle" border = "0"/> <span> <?php echo _KUNENA_EMOTICONS_EDIT_SMILIES;?> </span></a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "index3.php?option=com_kunena&amp;task=recount&no_html=1" style = "text-decoration:none;" title = "<?php echo _KUNENA_RECOUNTFORUMS;?>"> <img src = "components/com_kunena/images/fbupgrade.png" align = "middle" border = "0"/> <span> <?php echo _KUNENA_RECOUNTFORUMS; ?> </span></a> </div>
          </div>
          <div style = "float:left;">
            <div class = "icon"> <a href = "index2.php?option=com_kunena&amp;task=ranks" style = "text-decoration:none;" title = "<?php echo _KUNENA_RANKS_MANAGE;?>"> <img src = "components/com_kunena/images/fbranks.png"  align = "middle" border = "0"/> <span> <?php echo _KUNENA_RANKS_MANAGE; ?> </span></a> </div>
          </div>
        </div></td>
    </tr>
  </table>
</div>
<!-- BEGIN: STATS -->
<div class="fbstatscover">
  <?php

   include_once (JPATH_COMPONENT_ADMINISTRATOR .'/lib/kunena.stats.class.php');
    ?>
  <table cellspacing="1"  border="0" width="100%" class="fbstat">
    <caption>
    <?php echo _STATS_GEN_STATS; ?>
    </caption>
    <col class="col1">
    <col class="col2">
    <col class="col1">
    <col class="col2">
    <thead>
      <tr>
        <th><?php echo _KUNENA_STATISTIC;?></th>
        <th><?php echo _KUNENA_VALUE;?></th>
        <th><?php echo _KUNENA_STATISTIC;?></th>
        <th><?php echo _KUNENA_VALUE;?></th>
      </tr>
    </thead>
    <?php
	$yesterday = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
	?>
    <tbody>
      <tr>
        <td><?php echo _STATS_TOTAL_MEMBERS; ?> </td>
        <td><strong><?php echo jbStats::get_total_members(); ?></strong></td>
        <td><?php echo _STATS_TOTAL_CATEGORIES; ?> </td>
        <td><strong><?php echo jbStats::get_total_categories();?></strong></td>
      </tr>
      <tr>
        <td><?php echo _STATS_TOTAL_REPLIES; ?></td>
        <td><strong><?php echo jbStats::get_total_messages() ;?></strong></td>
        <td><?php echo _STATS_TOTAL_SECTIONS; ?> </td>
        <td><strong><?php echo jbStats::get_total_sections() ;?></strong></td>
      </tr>
      <tr>
        <td><?php echo _STATS_TOTAL_TOPICS; ?></td>
        <td><strong><?php echo jbStats::get_total_topics() ;?></strong></td>
        <td><?php echo _STATS_LATEST_MEMBER; ?> </td>
        <td><strong><?php echo jbStats::get_latest_member() ;?></strong></td>
      </tr>
      <tr>
        <td><?php echo _STATS_TODAY_TOPICS; ?></td>
        <td><strong><?php echo jbStats::get_total_topics(date("Y-m-d 00:00:01"),date("Y-m-d 23:59:59")) ;?></strong></td>
        <td><?php echo _STATS_YESTERDAY_TOPICS; ?> </td>
        <td><strong><?php echo jbStats::get_total_topics(date("Y-m-d 00:00:01",$yesterday),date("Y-m-d 23:59:59",$yesterday)) ;?></strong></td>
      </tr>
      <tr>
        <td><?php echo _STATS_TODAY_REPLIES; ?></td>
        <td><strong><?php echo jbStats::get_total_messages(date("Y-m-d 00:00:01"),date("Y-m-d 23:59:59")) ;?></strong></td>
        <td><?php echo _STATS_YESTERDAY_REPLIES; ?></td>
        <td><strong>
          <?php
	echo jbStats::get_total_messages(date("Y-m-d 00:00:01",$yesterday),date("Y-m-d 23:59:59",$yesterday)) ;
	?>
          </strong></td>
      </tr>
    </tbody>
  </table>
  <!-- B: UserStat -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="49%" valign="top"><!-- -->
        <table cellspacing="1"  border="0" width="100%" class="fbstat">
          <caption>
          <?php echo _STATS_TOP_POSTERS; ?>
          </caption>
          <col class="col1">
          <col class="col2">
          <col class="col2">
          <thead>
            <tr>
              <th><?php echo _KUNENA_USRL_USERNAME;?></th>
              <th></th>
              <th><?php echo _KUNENA_USRL_HITS;?></th>
            </tr>
          </thead>
          <tbody>
            <?php
	$KUNENA_top_posters=jbStats::get_top_posters();
	foreach ($KUNENA_top_posters as $KUNENA_poster) {
		if ($KUNENA_poster->posts == $KUNENA_top_posters[0]->posts) {
			$barwidth = 100;
		}
		else {
			$barwidth = round(($KUNENA_poster->posts * 100) / $KUNENA_top_posters[0]->posts);
		}
	?>
            <tr>
              <td><?php echo $KUNENA_poster->username;?> </td>
              <td ><img style="margin-bottom:1px" src="<?php echo KUNENA_DIRECTURL.'/template/default/images/bar.gif'; ?>" alt="" height="15" width="<?php echo $barwidth;?>"> </td>
              <td ><?php echo $KUNENA_poster->posts;?></td>
            </tr>
            <?php
	}
	?>
          </tbody>
        </table>
        <!-- / -->
      </td>
      <td width="1%">&nbsp;</td>
      <td width="49%" valign="top"><!--  -->
        <table cellspacing="1"  border="0" width="100%" class="fbstat">
          <caption>
          <?php echo  _STATS_POPULAR_PROFILE; ?>
          </caption>
          <col class="col1">
          <col class="col2">
          <col class="col2">
          <thead>
            <tr>
              <th><?php echo _KUNENA_USRL_USERNAME;?></th>
              <th></th>
              <th><?php echo _KUNENA_USRL_HITS;?></th>
            </tr>
          </thead>
          <tbody>
            <?php
		$fb_top_profiles=jbStats::get_top_profiles();
		foreach ($fb_top_profiles as $fb_profile) {
			if ($fb_profile->uhits == $fb_top_profiles[0]->uhits)
				$barwidth = 100;
			else
				$barwidth = round(($fb_profile->uhits * 100) / $fb_top_profiles[0]->uhits);
	?>
            <tr>
              <td><?php echo $fb_profile->username; ?></td>
              <td ><img style="margin-bottom:1px" src="<?php echo KUNENA_DIRECTURL.'/template/default/images/bar.gif'; ?>" alt="" height="15" width="<?php echo $barwidth;?>"> </td>
              <td ><?php echo $fb_profile->uhits;?></td>
            </tr>
            <?php
		}
	?>
          </tbody>
        </table>
        <!-- / -->
      </td>
    </tr>
  </table>
  <!-- F: UserStat -->
  <!-- Begin : Top popular topics -->
  <table cellspacing="1"  border="0" width="100%" class="fbstat">
    <caption>
    <?php echo _STATS_POPULAR_TOPICS; ?>
    </caption>
    <col class="col1">
    <col class="col2">
    <col class="col2">
    <thead>
      <tr>
        <th><?php echo _KUNENA_USERPROFILE_TOPICS;?></th>
        <th></th>
        <th><?php echo _KUNENA_USRL_HITS;?></th>
      </tr>
    </thead>
    <tbody>
      <?php
	$KUNENA_top_posts=jbStats::get_top_topics();
	foreach ($KUNENA_top_posts as $KUNENA_post) {
		if ($KUNENA_post->hits == $KUNENA_top_posts[0]->hits) {
			$barwidth = 100;
		}
		else {
			$barwidth = round(($KUNENA_post->hits * 100) / $KUNENA_top_posts[0]->hits);
		}
		$link = KUNENA_LIVEURL.'&func=view&id='.$KUNENA_post->id.'&catid='.$KUNENA_post->catid;
	?>
      <tr>
        <td ><a href="<?php echo $link;?>"><?php echo $KUNENA_post->subject;?></a> </td>
        <td ><img src="<?php echo KUNENA_DIRECTURL.'/template/default/images/bar.gif'; ?>" alt="" style="margin-bottom:1px" height="15" width="<?php echo $barwidth;?>"> </td>
        <td ><?php echo $KUNENA_post->hits;?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <!-- Finish : Top popular topics -->
</div>
<!-- FINISH: STATS -->
