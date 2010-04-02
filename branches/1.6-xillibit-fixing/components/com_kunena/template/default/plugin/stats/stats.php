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

// Dont allow direct linking
defined( '_JEXEC' ) or die();


$kunena_config =& CKunenaConfig::getInstance();
$document =& JFactory::getDocument();

$document->setTitle(JText::_('COM_KUNENA_STAT_FORUMSTATS') . ' - ' . stripslashes($kunena_config->board_title));

if($kunena_config->showstats):

$loadAPIStats = new KunenaStatsAPI();

$forumurl = 'index.php?option=com_kunena';

$userlist1 = CKunenaLink::GetUserlistLink('', $loadAPIStats->getToTalMembers());

?>

        <!-- BEGIN: GENERAL STATS -->
<?php if($kunena_config->showgenstats): ?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
        <table  class = "kblocktable" id ="kmorestat" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <div class = "ktitle_cover km">
                            <span class="ktitle kl"><?php echo stripslashes($kunena_config->board_title); ?> <?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS'); ?></span>
                        </div>
                        <img id = "BoxSwitch__morestat_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                    </th>
                </tr>
            </thead>

            <tbody id = "morestat_tbody">
                <tr class = "ksth ks">
                    <th class = "th-1 ksectiontableheader" align="left" width="50%"><?php echo JText::_('COM_KUNENA_STAT_GENERAL_STATS'); ?>
                    </th>
                </tr>

                <tr class = "ksectiontableentry1">
                    <td class = "td-1" align="left">
<?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>:<b> <?php echo $userlist1;?></b>
                    &nbsp; <?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>:<b> <?php echo CKunenaLink::GetProfileLink($kunena_config, $loadAPIStats->getLastestMemberid(), $loadAPIStats->getLastestMember()); ?></b>

                <br/> <?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>: <b> <?php echo $loadAPIStats->getTotalMessages(); ?></b> &nbsp;
    <?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>: <b> <?php echo $loadAPIStats->getTotalTitles(); ?></b> &nbsp; <?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>: <b> <?php echo $loadAPIStats->getTotalCats(); ?></b> &nbsp; <?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>: <b> <?php echo $loadAPIStats->getTotalSections(); ?></b>

                <br/> <?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>: <b> <?php echo $loadAPIStats->getTodayOpen(); ?></b> &nbsp; <?php echo
    JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>: <b> <?php echo $loadAPIStats->getTodayOpen(); ?></b> &nbsp; <?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>: <b> <?php echo $loadAPIStats->getTodayOpen(); ?></b> &nbsp; <?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>: <b> <?php echo $loadAPIStats->getYesterdayAnswer(); ?></b>

                    </td>
                </tr>
            </tbody>
        </table>
        </div>
</div>
</div>
</div>
</div>
<?php endif; ?>
<!-- FINISH: GENERAL STATS -->

<?php
$tabclass = array
(
"sectiontableentry1",
"sectiontableentry2"
);
$k = 0;
?>


<!-- B: Pop Subject -->
<?php if($this->showpopsubjectstats): ?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class = "kblocktable " id="kpopsubmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <thead>
    <tr>
      <th colspan="3">
      <div class = "ktitle_cover km"> <span class="ktitle kl"><?php echo JText::_('COM_KUNENA_STAT_TOP'); ?> <strong><?php echo $kunena_config->popsubjectcount; ?></strong> <?php echo JText::_('COM_KUNENA_STAT_POPULAR'); ?> <?php echo JText::_('COM_KUNENA_STAT_POPULAR_USER_KGSG'); ?></span> </div>
      <img id = "BoxSwitch__kpopsubstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody id = "kpopsubstats_tbody">
   <tr  class = "ksth" >
      <th class = "th-1 ksectiontableheader" align="left" width="50%"> <?php echo JText::_('COM_KUNENA_GEN_SUBJECT') ;?></th>
      <th class = "th-2 ksectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 ksectiontableheader" align="center" width="10%"></th>
    </tr>
 <?php
 foreach ($loadAPIStats->getTopicsStats($kunena_config->popsubjectcount) as $toptitle)
       {
	   $k = 1 - $k;
		   if ($toptitle->hits == $loadAPIStats->getTopTitlesHits($kunena_config->popsubjectcount)) {
		   $barwidth = 100;
		   }
		   else {
		   $barwidth = round(($toptitle->hits * 100) / $loadAPIStats->getTopTitlesHits($kunena_config->popsubjectcount));
		   }
?>

    <tr class = "k<?php echo $tabclass[$k]; ?>">
      <td class="td-1" align="left">
       <?php echo CKunenaLink::GetThreadLink( 'view', $toptitle->catid, $toptitle->id, kunena_htmlspecialchars(stripslashes($toptitle->subject)), '' ); ?>
      </td>
      <td  class="td-2">
       <img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_TMPLTMAINIMGURL.'/images/backgrounds/bar.png';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/>
      </td>
      <td  class="td-3">
	  <?php echo $toptitle->hits; ?> <?php echo JText::_('COM_KUNENA_USRL_HITS') ;?>
       </td>
    </tr>
<?php }   ?>
  </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php endif; ?>
<!-- F: Pop Subject -->


<!-- B: Pop Poll -->
<?php if($this->showpoppollstats): ?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class = "kblocktable " id="kpopsubmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <thead>
    <tr>
      <th colspan="3">
      <div class = "ktitle_cover km"> <span class="ktitle kl"><?php echo JText::_('COM_KUNENA_STAT_TOP'); ?> <strong><?php echo $kunena_config->poppollscount; ?></strong> <?php echo JText::_('COM_KUNENA_STAT_POPULAR'); ?> <?php echo JText::_('COM_KUNENA_STAT_POPULAR_POLLS_KGSG'); ?></span> </div>
      <img id = "BoxSwitch__kpopsubstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody id = "kpopsubstats_tbody">
   <tr  class = "ksth" >
      <th class = "th-1 ksectiontableheader" align="left" width="50%"> <?php echo JText::_('COM_KUNENA_POLL_NAME');?></th>
      <th class = "th-2 ksectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 ksectiontableheader" align="center" width="10%"></th>
    </tr>
 <?php foreach($loadAPIStats->getTopPollStats($kunena_config->poppollscount) as $toppoll)
       {
       if($toppoll->total != "0")
       {
	   $k = 1 - $k;
		   if ($toppoll->total == $loadAPIStats->getTopPollVotesStats($kunena_config->poppollscount)) {
          $barwidth = 100;
		   }
		   else {
		    if($toppoll->total== null){
          $toppoll->total = "0";
        }
		    $barwidth = round(($toppoll->total * 100) / $loadAPIStats->getTopPollVotesStats($kunena_config->poppollscount));
		   }
?>

    <tr class = "k<?php echo $tabclass[$k]; ?>">
      <td class="td-1" align="left">
       <?php echo CKunenaLink::GetThreadLink( 'view', $toppoll->catid, $toppoll->threadid, kunena_htmlspecialchars(stripslashes($toppoll->title)), '' ); ?>
      </td>
      <td  class="td-2">
       <img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_TMPLTMAINIMGURL.'/images/backgrounds/bar.png';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/>
      </td>
      <td  class="td-3">
	  <?php echo $toppoll->total; ?> <?php echo JText::_('COM_KUNENA_USRL_VOTES') ;?>
       </td>
    </tr>
<?php }
}  ?>
  </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php endif; ?>
<!-- F: Pop Polls -->


<!-- B: User Messages -->
<?php if($this->showpopuserstats): ?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class = "kblocktable " id="kpopusermsgmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <thead>
    <tr>
      <th colspan="3">
      <div class = "ktitle_cover km"> <span class="ktitle kl"><?php echo JText::_('COM_KUNENA_STAT_TOP'); ?> <strong><?php echo $kunena_config->popusercount; ?></strong> <?php echo JText::_('COM_KUNENA_STAT_POPULAR'); ?> <?php echo JText::_('COM_KUNENA_STAT_POPULAR_USER_TMSG'); ?></span></div>
      <img id = "BoxSwitch__kpopusermsgstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody id = "kpopusermsgstats_tbody">
   <tr  class = "ksth" >
      <th class = "th-1 ksectiontableheader" align="left" width="50%"><?php echo JText::_('COM_KUNENA_USRL_USERNAME') ;?></th>
      <th class = "th-2 ksectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 ksectiontableheader" align="center" width="10%"></th>
    </tr>
<?php

	foreach ($loadAPIStats->getPostersStats($kunena_config->popusercount) as $poster)
	{

	$k = 1 - $k;

	if ($poster->posts == $loadAPIStats->getTopMessage($kunena_config->popusercount)) {
	$barwidth = 100;
	}
	else {
	$barwidth = round(($poster->posts * 100) / $loadAPIStats->getTopMessage($kunena_config->popusercount));
	}
?>

    <tr class = "k<?php echo $tabclass[$k]; ?>">
      <td  class="td-1"  align="left">

         <?php echo CKunenaLink::GetProfileLink($kunena_config, $poster->userid, $poster->username); ?>

</td>
      <td  class="td-2">
         <img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_TMPLTMAINIMGURL.'/images/backgrounds/bar.png';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/>
                                    </td>
      <td  class="td-3">
	  <?php echo $poster->posts; ?> <?php echo JText::_('COM_KUNENA_USRL_POSTS') ;?>
       </td>
    </tr>
<?php }   ?>
  </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php endif; ?>
<!-- F: User Messages -->


<!-- B: Pop User  -->
<?php if($this->showpopuserstats): ?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class = "kblocktable " id="kpopuserhitmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <thead>
    <tr>
      <th colspan="3">
      <div class = "ktitle_cover km"> <span class="ktitle kl"><?php echo JText::_('COM_KUNENA_STAT_TOP'); ?> <strong><?php echo $kunena_config->popusercount; ?></strong> <?php echo JText::_('COM_KUNENA_STAT_POPULAR'); ?> <?php echo JText::_('COM_KUNENA_STAT_POPULAR_USER_GSG'); ?></span> </div>
      <img id = "BoxSwitch__kpopuserhitstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody id = "kpopuserhitstats_tbody">
   <tr  class = "ksth ks" >
      <th class = "th-1 ksectiontableheader"  align="left" width="50%"> <?php echo JText::_('COM_KUNENA_USRL_USERNAME') ;?></th>
      <th class = "th-2 ksectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 ksectiontableheader" align="center" width="10%"></th>
    </tr>

<?php
foreach ($loadAPIStats->getProfileStats($kunena_config->popusercount) as $topprofile)
{
$k = 1 - $k;
if ($topprofile->hits == $loadAPIStats->getTopTitlesHits($kunena_config->popusercount)) {
$barwidth = 100;
}
else {
$barwidth = round(($topprofile->hits * 100) / $loadAPIStats->getTopTitlesHits($kunena_config->popusercount));
}
?>

    <tr class = "k<?php echo $tabclass[$k]; ?>">
      <td  class="td-1"  align="left">
        <?php echo CKunenaLink::GetProfileLink($kunena_config, $topprofile->user_id, $topprofile->user); ?>
</td>
      <td  class="td-2">
         <img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_TMPLTMAINIMGURL.'/images/backgrounds/bar.png';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/>
                                    </td>
      <td  class="td-3">
	  <?php echo $topprofile->hits; ?> <?php echo JText::_('COM_KUNENA_USRL_HITS') ;?>
       </td>
    </tr>
<?php }   ?>
  </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php endif; ?>
<!-- F: User User -->


<?php
// WHOISONLINE
if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php')) {
    include(KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php');
}
else {
    include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/who/whoisonline.php');
}
// /WHOISONLINE

endif;
