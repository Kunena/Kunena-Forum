<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
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
defined( '_JEXEC' ) or die();

kimport ( 'thankyou' );
?>
<style>
.kadmin-welcome {
	clear:both;
	margin:10px 0;
	padding:10px;
	font-size:12px;
	color:#536482;
	line-height:140%;
	border:1px solid #ddd;
}
.kadmin-welcome h3 {
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
.kadmin-statscover {
	padding:0px;
}
table.kadmin-stat {
	background-color:#FFFFFF;
	border:1px solid #ddd;
	padding:1px;
	width:100%;
}
table.kadmin-stat th {
	background:#EEE;
	border-bottom:1px solid #CCC;
	border-top:1px solid #EEE;
	color:#666;
	font-size:11px;
	padding:3px 4px;
	text-align:left;
}
table.kadmin-stat td {
	font-size:11px;
	line-height:140%;
	padding:4px;
	text-align:left;
}
table.kadmin-stat caption {
	clear:both;
	font-size:14px;
	font-weight:bold;
	margin:10px 0 2px 0;
	padding:2px;
	text-align:left;
}
table.kadmin-stat .col1 {
	background-color:#F1F3F5;
}
table.kadmin-stat .col2 {
	background-color: #FBFBFB;
}
div.icon a {
	text-decoration:none;
}
div.icon-container {
	float:left;
}

</style>
<div class="kadmin-welcome">
	<h3><?php echo JText::_('COM_KUNENA_WELCOME');?></h3>
	<p><?php echo JText::_('COM_KUNENA_WELCOME_DESC');?></p>
</div>
<div style="border:1px solid #ddd; background:#FBFBFB;">
	<table class = "thisform">
		<tr class = "thisform">
			<td width = "100%" valign = "top" class = "thisform">
				<div id = "cpanel">
					  <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showconfig" title = "<?php echo JText::_('COM_KUNENA_C_FBCONFIGDESC');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/kconfig.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_C_FBCONFIG'); ?> </span></a> </div>
					  </div>
					  <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showAdministration" title = "<?php echo JText::_('COM_KUNENA_C_FORUMDESC');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/kforumadm.png" align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_C_FORUM'); ?> </span></a> </div>
					  </div>
					  <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showTemplates" title = "<?php echo JText::_('COM_KUNENA_REPORT_SYSTEM');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/templatemanager.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?> </span></a> </div>
					  </div>
					  <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showprofiles" title = "<?php echo JText::_('COM_KUNENA_C_USERDESC');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/kuser.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_C_USER'); ?> </span> </a> </div>
					  </div>
					  <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=browseFiles" title = "<?php echo JText::_('COM_KUNENA_C_FILESDESC');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/kfiles.png" align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_C_FILES'); ?> </span></a> </div>
					  </div>
					  <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=browseImages" title = "<?php echo JText::_('COM_KUNENA_C_IMAGESDESC');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/kimages.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_C_IMAGES'); ?> </span></a> </div>
					  </div>
					   <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=pruneforum" title = "<?php echo JText::_('COM_KUNENA_C_PRUNETABDESC');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/ktable.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?> </span></a> </div>
					  </div>
					   <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=syncusers" title = "<?php echo JText::_('COM_KUNENA_C_SYNCEUSERSDESC');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/kusers.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?> </span></a> </div>
					  </div>
					   <div class="icon-container">
						<div class = "icon"> <a href = "http://www.kunena.com" target = "_blank" title = "<?php echo JText::_('COM_KUNENA_C_SUPPORTDESC');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/ktechsupport.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_C_SUPPORT'); ?> </span></a> </div>
					  </div>
					   <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showsmilies" title = "<?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/ksmiley.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER');?> </span></a> </div>
					  </div>
					   <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=recount&no_html=1" title = "<?php echo JText::_('COM_KUNENA_RECOUNTFORUMS');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/kupgrade.png" align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_RECOUNTFORUMS'); ?> </span></a> </div>
					  </div>
					   <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=ranks" title = "<?php echo JText::_('COM_KUNENA_RANK_MANAGER');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/kranks.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_RANK_MANAGER'); ?> </span></a> </div>
					  </div>
					   <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=createmenu" title = "<?php echo JText::_('COM_KUNENA_CREATE_MENU');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/menu.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_CREATE_MENU'); ?> </span></a> </div>
					  </div>
					   <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showtrashview" title = "<?php echo JText::_('COM_KUNENA_TRASH_VIEW');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/trash.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?> </span></a> </div>
					  </div>
					  <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showsystemreport" title = "<?php echo JText::_('COM_KUNENA_REPORT_SYSTEM');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/report_conf.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?> </span></a> </div>
					  </div>
					  <?php
						 if (JString::strpos ( KUNENA_VERSION, 'SVN' ) !== false) {
					  ?>
					  <div class="icon-container">
						<div class = "icon"> <a href = "<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=install" title = "<?php echo JText::_('COM_KUNENA_SVN_INSTALL');?>"> <img src = "<?php echo JURI::base(); ?>components/com_kunena/images/install.png"  align = "middle" border = "0"/> <span> <?php echo JText::_('COM_KUNENA_SVN_INSTALL'); ?> </span></a> </div>
					  </div>
					  <?php
						}
					  ?>
				</div>
       		</td>
   		 </tr>
  	</table>
</div>
<?php if ($kunena_config->version_check) : ?>
<div class="kadmin-welcome">
	<?php echo checkLatestVersion(); ?>
</div>
<?php endif; ?>
<!-- BEGIN: STATS -->
<div class="kadmin-statscover">
  <?php include_once (JPATH_COMPONENT_ADMINISTRATOR .'/lib/kunena.stats.class.php');
  jimport ( 'joomla.utilities.date' );
  $datem = new JDate(date("Y-m-d 00:00:01"));
  $datee = new JDate(date("Y-m-d 23:59:59"));?>
  <table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
    <caption><?php echo JText::_('COM_KUNENA_STATS_GEN_STATS'); ?></caption>
    <col class="col1">
    <col class="col2">
    <col class="col1">
    <col class="col2">
    <thead>
      <tr>
        <th><?php echo JText::_('COM_KUNENA_STATISTIC');?></th>
        <th><?php echo JText::_('COM_KUNENA_VALUE');?></th>
        <th><?php echo JText::_('COM_KUNENA_STATISTIC');?></th>
        <th><?php echo JText::_('COM_KUNENA_VALUE');?></th>
      </tr>
    </thead>
    <?php $yesterday = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")); ?>
    <tbody>
      <tr>
        <td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_MEMBERS'); ?> </td>
        <td><strong><?php echo jbStats::get_total_members(); ?></strong></td>
        <td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_SECTIONS'); ?> </td>
        <td><strong><?php echo jbStats::get_total_sections();?></strong></td>
      </tr>
      <tr>
        <td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_REPLIES'); ?></td>
        <td><strong><?php echo jbStats::get_total_messages() ;?></strong></td>
        <td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_CATEGORIES'); ?> </td>
        <td><strong><?php echo jbStats::get_total_categories() ;?></strong></td>
      </tr>
      <tr>
        <td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_TOPICS'); ?></td>
        <td><strong><?php echo jbStats::get_total_topics() ;?></strong></td>
        <td><?php echo JText::_('COM_KUNENA_STATS_LATEST_MEMBER'); ?> </td>
        <td><strong><?php echo jbStats::get_latest_member() ;?></strong></td>
      </tr>
      <tr>
      	<td><?php echo JText::_('COM_KUNENA_STATS_TOTAL_THANKYOU'); ?></td>
        <td><strong><?php echo KunenaThankYou::getTotalThankYou();?></strong></td>
        <td><?php echo JText::_('COM_KUNENA_STATS_TODAY_THANKYOU'); ?> </td>
        <td><strong><?php echo KunenaThankYou::getTotalThankYou($datem->toMySQL(),$datee->toMySQL()) ;?></strong></td>
      </tr>
      <tr>
        <td><?php echo JText::_('COM_KUNENA_STATS_TODAY_TOPICS'); ?></td>
        <td><strong><?php echo jbStats::get_total_topics(date("Y-m-d 00:00:01"),date("Y-m-d 23:59:59")) ;?></strong></td>
        <td><?php echo JText::_('COM_KUNENA_STATS_YESTERDAY_TOPICS'); ?> </td>
        <td><strong><?php echo jbStats::get_total_topics(date("Y-m-d 00:00:01",$yesterday),date("Y-m-d 23:59:59",$yesterday)) ;?></strong></td>
      </tr>
      <tr>
        <td><?php echo JText::_('COM_KUNENA_STATS_TODAY_REPLIES'); ?></td>
        <td><strong><?php echo jbStats::get_total_messages(date("Y-m-d 00:00:01"),date("Y-m-d 23:59:59")) ;?></strong></td>
        <td><?php echo JText::_('COM_KUNENA_STATS_YESTERDAY_REPLIES'); ?></td>
        <td><strong><?php echo jbStats::get_total_messages(date("Y-m-d 00:00:01",$yesterday),date("Y-m-d 23:59:59",$yesterday)) ; ?></strong></td>
      </tr>
    </tbody>
  </table>
  <!-- B: UserStat -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="49%" valign="top">
        <table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
          <caption><?php echo JText::_('COM_KUNENA_STATS_TOP_POSTERS'); ?></caption>
          <col class="col1">
          <col class="col2">
          <col class="col2">
          <thead>
            <tr>
              <th><?php echo JText::_('COM_KUNENA_USRL_USERNAME');?></th>
              <th></th>
              <th><?php echo JText::_('COM_KUNENA_USRL_HITS');?></th>
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
              <td ><img style="margin-bottom:1px" src="<?php echo KUNENA_DIRECTURL.'/template/default/images/backgrounds/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>"> </td>
              <td ><?php echo $KUNENA_poster->posts;?></td>
            </tr>
            <?php
				}
				?>
          </tbody>
        </table>
      </td>
      <td width="1%">&nbsp;</td>
      <td width="49%" valign="top">
        <table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
          <caption><?php echo  JText::_('COM_KUNENA_STATS_POPULAR_PROFILE'); ?></caption>
          <col class="col1">
          <col class="col2">
          <col class="col2">
          <thead>
            <tr>
              <th><?php echo JText::_('COM_KUNENA_USRL_USERNAME');?></th>
              <th></th>
              <th><?php echo JText::_('COM_KUNENA_USRL_HITS');?></th>
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
              <td ><img style="margin-bottom:1px" src="<?php echo KUNENA_DIRECTURL.'/template/default/images/backgrounds/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>"> </td>
              <td ><?php echo $fb_profile->uhits;?></td>
            </tr>
            <?php
				}
			?>
          </tbody>
        </table>
      </td>
    </tr>
  </table>
  <!-- F: UserStat -->
  <!-- Thank you stat -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="49%" valign="top">
        <table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
          <caption><?php echo JText::_('COM_KUNENA_STATS_TOP_GOT_THANKYOU'); ?></caption>
          <col class="col1">
          <col class="col2">
          <col class="col2">
          <thead>
            <tr>
              <th><?php echo JText::_('COM_KUNENA_USRL_USERNAME');?></th>
              <th></th>
              <th><?php echo JText::_('COM_KUNENA_USRL_THANKYOU');?></th>
            </tr>
          </thead>
          <tbody>
            <?php
				$KUNENA_top_thankyous=KunenaThankYou::getMostThankYou();
				foreach ($KUNENA_top_thankyous as $KUNENA_thankyou) {
					if ($KUNENA_thankyou->countid == $KUNENA_top_thankyous[0]->countid) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($KUNENA_thankyou->countid * 100) / $KUNENA_top_thankyous[0]->countid);
					}
			?>
            <tr>
              <td><?php echo $KUNENA_thankyou->username;?> </td>
              <td ><img style="margin-bottom:1px" src="<?php echo KUNENA_DIRECTURL.'/template/default/images/backgrounds/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>"> </td>
              <td ><?php echo $KUNENA_thankyou->countid;?></td>
            </tr>
            <?php
				}
				?>
          </tbody>
        </table>
      </td>
      <td width="1%">&nbsp;</td>
      <td width="49%" valign="top">
        <table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
          <caption><?php echo  JText::_('COM_KUNENA_STATS_TOP_SAID_THANKYOU'); ?></caption>
          <col class="col1">
          <col class="col2">
          <col class="col2">
          <thead>
            <tr>
              <th><?php echo JText::_('COM_KUNENA_USRL_USERNAME');?></th>
              <th></th>
              <th><?php echo JText::_('COM_KUNENA_USRL_THANKYOU');?></th>
            </tr>
          </thead>
          <tbody>
            <?php
		$KUNENA_top_said_thankyous=KunenaThankYou::getMostThankYou('said');
				foreach ($KUNENA_top_said_thankyous as $KUNENA_said_thankyou) {
					if ($KUNENA_said_thankyou->countid == $KUNENA_top_said_thankyous[0]->countid) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($KUNENA_said_thankyou->countid * 100) / $KUNENA_top_said_thankyous[0]->countid);
					}
			?>
            <tr>
              <td><?php echo $KUNENA_said_thankyou->username;?> </td>
              <td ><img style="margin-bottom:1px" src="<?php echo KUNENA_DIRECTURL.'/template/default/images/backgrounds/bar.png'; ?>" alt="" height="15" width="<?php echo $barwidth;?>"> </td>
              <td ><?php echo $KUNENA_said_thankyou->countid;?></td>
            </tr>
            <?php
				}
			?>
          </tbody>
        </table>
      </td>
    </tr>
  </table>
  <!-- Thank you stat -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="49%" valign="top">
		  <!-- Begin : Top popular topics -->
		  <table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
		    <caption><?php echo JText::_('COM_KUNENA_STATS_POPULAR_TOPICS'); ?></caption>
		    <col class="col1">
		    <col class="col2">
		    <col class="col2">
		    <thead>
		      <tr>
		        <th><?php echo JText::_('COM_KUNENA_USERPROFILE_TOPICS');?></th>
		        <th></th>
		        <th><?php echo JText::_('COM_KUNENA_USRL_HITS');?></th>
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
		        <td ><img src="<?php echo KUNENA_DIRECTURL.'/template/default/images/backgrounds/bar.png'; ?>" alt="" style="margin-bottom:1px" height="15" width="<?php echo $barwidth;?>"> </td>
		        <td ><?php echo $KUNENA_post->hits;?></td>
		      </tr>
		      <?php } ?>
		    </tbody>
		  </table>
		  <!-- Finish : Top popular topics -->
		</td>
		<td width="1%">&nbsp;</td>
      	<td width="49%" valign="top">
      		<!-- Begin: Top Thank you topics -->
      		<table cellspacing="1"  border="0" width="100%" class="kadmin-stat">
		    <caption><?php echo JText::_('COM_KUNENA_STATS_THANKYOU_TOPICS'); ?></caption>
		    <col class="col1">
		    <col class="col2">
		    <col class="col2">
		    <thead>
		      <tr>
		        <th><?php echo JText::_('COM_KUNENA_USERPROFILE_TOPICS');?></th>
		        <th></th>
		        <th><?php echo JText::_('COM_KUNENA_USRL_THANKYOU');?></th>
		      </tr>
		    </thead>
		    <tbody>
		      <?php
				$KUNENA_top_posts=KunenaThankYou::getTopThankYouTopics();
				foreach ($KUNENA_top_posts as $KUNENA_post) {
					if ($KUNENA_post->countid == $KUNENA_top_posts[0]->countid) {
						$barwidth = 100;
					}
					else {
						$barwidth = round(($KUNENA_post->countid * 100) / $KUNENA_top_posts[0]->countid);
					}
					$link = KUNENA_LIVEURL.'&func=view&id='.$KUNENA_post->postid.'&catid='.$KUNENA_post->catid;
				?>
		      <tr>
		        <td ><a href="<?php echo $link;?>"><?php echo $KUNENA_post->subject;?></a> </td>
		        <td ><img src="<?php echo KUNENA_DIRECTURL.'/template/default/images/backgrounds/bar.png'; ?>" alt="" style="margin-bottom:1px" height="15" width="<?php echo $barwidth;?>"> </td>
		        <td ><?php echo $KUNENA_post->countid;?></td>
		      </tr>
		      <?php } ?>
		    </tbody>
		  </table>
		  <!-- Fnish : Top Thank you topics  -->
      	</td>
	</tr>
</table>

</div>
<!-- FINISH: STATS -->
