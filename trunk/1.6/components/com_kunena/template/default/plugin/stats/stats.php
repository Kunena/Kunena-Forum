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

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$kunenaConfig =& CKunenaConfig::getInstance();
$document =& JFactory::getDocument();

$document->setTitle(_STAT_FORUMSTATS . ' - ' . stripslashes($kunenaConfig->board_title));

if($kunenaConfig->showstats):

$forumurl = 'index.php?option=com_kunena';

$kunenaProfile =& CKunenaProfile::getInstance();
$userlist = $kunenaProfile->getUserListURL();

?>

        <!-- BEGIN: GENERAL STATS -->
<?php if($kunenaConfig->showgenstats): ?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
        <table  class = "kunena_blocktable" id ="kunena_morestat" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <div class = "kunena_title_cover kunenam">
                            <a class="kunena_title kunenal" href = "<?php echo $statslink;?>"><?php echo stripslashes($kunenaConfig->board_title); ?> <?php echo _STAT_FORUMSTATS; ?></a>
                        </div>
                        <img id = "BoxSwitch__morestat_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                    </th>
                </tr>
            </thead>

            <tbody id = "morestat_tbody">
                <tr class = "kunena_sth kunenas">
                    <th class = "th-1 <?php echo $boardclass; ?>sectiontableheader" align="left" width="50%"><?php echo _STAT_GENERAL_STATS; ?>
                    </th>
                </tr>

                <tr class = "<?php echo $boardclass ;?>sectiontableentry1">
                    <td class = "td-1" align="left">
<?php echo _STAT_TOTAL_USERS; ?>:<b> <a href = "<?php echo $userlist;?>"><?php echo $totalmembers; ?></a> </b>
                    &nbsp; <?php echo _STAT_LATEST_MEMBERS; ?>:<b> <?php echo CKunenaLink::GetProfileLink($kunenaConfig, $lastestmemberid, $lastestmember); ?></b>

                <br/> <?php echo _STAT_TOTAL_MESSAGES; ?>: <b> <?php echo $totalmsgs; ?></b> &nbsp;
    <?php echo _STAT_TOTAL_SUBJECTS; ?>: <b> <?php echo $totaltitles; ?></b> &nbsp; <?php echo _STAT_TOTAL_SECTIONS; ?>: <b> <?php echo $totalcats; ?></b> &nbsp; <?php echo _STAT_TOTAL_CATEGORIES; ?>: <b> <?php echo $totalsections; ?></b>

                <br/> <?php echo _STAT_TODAY_OPEN_THREAD; ?>: <b> <?php echo $todayopen; ?></b> &nbsp; <?php echo
    _STAT_YESTERDAY_OPEN_THREAD; ?>: <b> <?php echo $yesterdayopen; ?></b> &nbsp; <?php echo _STAT_TODAY_TOTAL_ANSWER; ?>: <b> <?php echo $todayanswer; ?></b> &nbsp; <?php echo _STAT_YESTERDAY_TOTAL_ANSWER; ?>: <b> <?php echo $yesterdayanswer; ?></b>

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
<?php if($kunenaConfig->showpopsubjectstats): ?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "kunena_blocktable " id="kunena_popsubmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <thead>
    <tr>
      <th colspan="3">
      <div class = "kunena_title_cover kunenam"> <span class="kunena_title kunenal"> <?php echo _STAT_POPULAR; ?> <b><?php echo $kunenaConfig->popsubjectcount; ?></b> <?php echo _STAT_POPULAR_USER_KGSG; ?></span> </div>
      <img id = "BoxSwitch__<?php echo $boardclass ;?>popsubstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody id = "<?php echo $boardclass ;?>popsubstats_tbody">
   <tr  class = "kunena_sth" >
      <th class = "th-1 <?php echo $boardclass; ?>sectiontableheader" align="left" width="50%"> <?php echo _GEN_SUBJECT ;?></th>
      <th class = "th-2 <?php echo $boardclass; ?>sectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 <?php echo $boardclass; ?>sectiontableheader" align="center" width="10%"> <?php echo _KUNENA_USRL_HITS ;?> </th>
    </tr>
 <?php foreach ($toptitles as $toptitle)
       {
	   $k = 1 - $k;
		   if ($toptitle->hits == $toptitlehits) {
		   $barwidth = 100;
		   }
		   else {
		   $barwidth = round(($toptitle->hits * 100) / $toptitlehits);
		   }
	  $link = JRoute::_(KUNENA_LIVEURLREL . '&amp;func=view&amp;id=' . $toptitle->id . '&amp;catid=' . $toptitle->catid);
?>

    <tr class = "<?php echo ''.$boardclass.''. $tabclass[$k] . ''; ?>">
      <td class="td-1" align="left">
       <a href = "<?php echo $link;?>"><?php echo kunena_htmlspecialchars(stripslashes($toptitle->subject)); ?></a>
      </td>
      <td  class="td-2">
       <img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_TMPLTMAINIMGURL.'/images/bar.gif';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/>
      </td>
      <td  class="td-3">
	  <?php echo $toptitle->hits; ?>
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






<!-- B: User Messages -->
<?php if($kunenaConfig->showpopuserstats): ?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "kunena_blocktable " id="kunena_popusermsgmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <thead>
    <tr>
      <th colspan="3">
      <div class = "kunena_title_cover kunenam"> <span class="kunena_title kunenal"> <?php echo _STAT_POPULAR; ?> <b><?php echo $kunenaConfig->popusercount; ?></b> <?php echo _STAT_POPULAR_USER_TMSG; ?></span> </div>
      <img id = "BoxSwitch__<?php echo $boardclass ;?>popusermsgstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody id = "<?php echo $boardclass ;?>popusermsgstats_tbody">
   <tr  class = "kunena_sth" >
      <th class = "th-1 <?php echo $boardclass; ?>sectiontableheader" align="left" width="50%"><?php echo _KUNENA_USRL_USERNAME ;?></th>
      <th class = "th-2 <?php echo $boardclass; ?>sectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 <?php echo $boardclass; ?>sectiontableheader" align="center" width="10%"> <?php echo _KUNENA_USRL_POSTS ;?></th>
    </tr>
<?php

	foreach ($topposters as $poster)
	{

	$k = 1 - $k;

	if ($poster->posts == $topmessage) {
	$barwidth = 100;
	}
	else {
	$barwidth = round(($poster->posts * 100) / $topmessage);
	}
?>

    <tr class = "<?php echo ''.$boardclass.''. $tabclass[$k] . ''; ?>">
      <td  class="td-1"  align="left">

         <?php echo CKunenaLink::GetProfileLink($kunenaConfig, $poster->userid, $poster->username); ?>

</td>
      <td  class="td-2">
         <img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_TMPLTMAINIMGURL.'/images/bar.gif';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/>
                                    </td>
      <td  class="td-3">
	  <?php echo $poster->posts; ?>
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
<?php if($kunenaConfig->showpopuserstats): ?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "kunena_blocktable " id="kunena_popuserhitmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <thead>
    <tr>
      <th colspan="3">
      <div class = "kunena_title_cover kunenam"> <span class="kunena_title kunenal"> <?php echo _STAT_POPULAR; ?> <b><?php echo $kunenaConfig->popusercount; ?></b> <?php echo _STAT_POPULAR_USER_GSG; ?></span> </div>
      <img id = "BoxSwitch__<?php echo $boardclass ;?>popuserhitstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody id = "<?php echo $boardclass ;?>popuserhitstats_tbody">
   <tr  class = "kunena_sth kunenas" >
      <th class = "th-1 <?php echo $boardclass; ?>sectiontableheader"  align="left" width="50%"> <?php echo _KUNENA_USRL_USERNAME ;?></th>
      <th class = "th-2 <?php echo $boardclass; ?>sectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 <?php echo $boardclass; ?>sectiontableheader" align="center" width="10%"><?php echo _KUNENA_USRL_HITS ;?></th>
    </tr>

<?php
foreach ($topprofiles as $topprofile)
{
$k = 1 - $k;
if ($topprofile->hits == $topprofil) {
$barwidth = 100;
}
else {
$barwidth = round(($topprofile->hits * 100) / $topprofil);
}
?>

    <tr class = "<?php echo ''.$boardclass.''. $tabclass[$k] . ''; ?>">
      <td  class="td-1"  align="left">
        <?php echo CKunenaLink::GetProfileLink($kunenaConfig, $topprofile->user_id, $topprofile->user); ?>
</td>
      <td  class="td-2">
         <img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_TMPLTMAINIMGURL.'/images/bar.gif';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/>
                                    </td>
      <td  class="td-3">
	  <?php echo $topprofile->hits; ?>
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
//(FB) BEGIN: WHOISONLINE
if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php')) {
    include(KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php');
}
else {
    include(KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/who/whoisonline.php');
}

//(FB) FINISH: WHOISONLINE

endif;

?>
