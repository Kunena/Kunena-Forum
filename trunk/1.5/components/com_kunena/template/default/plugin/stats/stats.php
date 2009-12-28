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

$kunena_config =& CKunenaConfig::getInstance();
$document =& JFactory::getDocument();

$document->setTitle(_STAT_FORUMSTATS . ' - ' . stripslashes($kunena_config->board_title));

if($kunena_config->showstats):

$forumurl = 'index.php?option=com_kunena';

if ($kunena_config->fb_profile == "jomsocial")
{
	$userlist = JRoute::_('index.php?option=com_community&amp;view=search&amp;task=browse');
}
else if ($kunena_config->fb_profile == 'cb')
{
    $userlist = CKunenaCBProfile::getUserListURL();
}
else
{
    $userlist = JRoute::_(KUNENA_LIVEURLREL . '&amp;func=userlist');
}

?>

        <!-- BEGIN: GENERAL STATS -->
<?php if($kunena_config->showgenstats): ?>
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
        <table  class = "fb_blocktable" id ="fb_morestat" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <div class = "fb_title_cover fbm">
                            <span class="fb_title fbl"><?php echo stripslashes($kunena_config->board_title); ?> <?php echo _STAT_FORUMSTATS; ?></span>
                        </div>
                        <img id = "BoxSwitch__morestat_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                    </th>
                </tr>
            </thead>

            <tbody id = "morestat_tbody">
                <tr class = "fb_sth fbs">
                    <th class = "th-1 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="left" width="50%"><?php echo _STAT_GENERAL_STATS; ?>
                    </th>
                </tr>

                <tr class = "<?php echo KUNENA_BOARD_CLASS ;?>sectiontableentry1">
                    <td class = "td-1" align="left">
<?php echo _STAT_TOTAL_USERS; ?>:<b> <a href = "<?php echo $userlist;?>"><?php echo $this->totalmembers; ?></a> </b>
                    &nbsp; <?php echo _STAT_LATEST_MEMBERS; ?>:<b> <?php echo CKunenaLink::GetProfileLink($kunena_config, $this->lastestmemberid, $this->lastestmember); ?></b>

                <br/> <?php echo _STAT_TOTAL_MESSAGES; ?>: <b> <?php echo $this->totalmsgs; ?></b> &nbsp;
    <?php echo _STAT_TOTAL_SUBJECTS; ?>: <b> <?php echo $this->totaltitles; ?></b> &nbsp; <?php echo _STAT_TOTAL_SECTIONS; ?>: <b> <?php echo $this->totalcats; ?></b> &nbsp; <?php echo _STAT_TOTAL_CATEGORIES; ?>: <b> <?php echo $this->totalsections; ?></b>

                <br/> <?php echo _STAT_TODAY_OPEN_THREAD; ?>: <b> <?php echo $this->todayopen; ?></b> &nbsp; <?php echo
    _STAT_YESTERDAY_OPEN_THREAD; ?>: <b> <?php echo $this->yesterdayopen; ?></b> &nbsp; <?php echo _STAT_TODAY_TOTAL_ANSWER; ?>: <b> <?php echo $this->todayanswer; ?></b> &nbsp; <?php echo _STAT_YESTERDAY_TOTAL_ANSWER; ?>: <b> <?php echo $this->yesterdayanswer; ?></b>

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
<?php if($kunena_config->showpopsubjectstats): ?>
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
<table class = "fb_blocktable " id="fb_popsubmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <thead>
    <tr>
      <th colspan="3">
      <div class = "fb_title_cover fbm"> <span class="fb_title fbl"> <?php echo _STAT_POPULAR; ?> <b><?php echo $kunena_config->popsubjectcount; ?></b> <?php echo _STAT_POPULAR_USER_KGSG; ?></span> </div>
      <img id = "BoxSwitch__<?php echo KUNENA_BOARD_CLASS ;?>popsubstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody id = "<?php echo KUNENA_BOARD_CLASS ;?>popsubstats_tbody">
   <tr  class = "fb_sth" >
      <th class = "th-1 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="left" width="50%"> <?php echo _GEN_SUBJECT ;?></th>
      <th class = "th-2 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center" width="10%"> <?php echo _KUNENA_USRL_HITS ;?> </th>
    </tr>
 <?php foreach ($this->toptitles as $toptitle)
       {
	   $k = 1 - $k;
		   if ($toptitle->hits == $this->toptitlehits) {
		   $barwidth = 100;
		   }
		   else {
		   $barwidth = round(($toptitle->hits * 100) / $this->toptitlehits);
		   }
	  $link = JRoute::_(KUNENA_LIVEURLREL . '&amp;func=view&amp;id=' . $toptitle->id . '&amp;catid=' . $toptitle->catid);
?>

    <tr class = "<?php echo ''.KUNENA_BOARD_CLASS.''. $tabclass[$k] . ''; ?>">
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
<?php if($kunena_config->showpopuserstats): ?>
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
<table class = "fb_blocktable " id="fb_popusermsgmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <thead>
    <tr>
      <th colspan="3">
      <div class = "fb_title_cover fbm"> <span class="fb_title fbl"> <?php echo _STAT_POPULAR; ?> <b><?php echo $kunena_config->popusercount; ?></b> <?php echo _STAT_POPULAR_USER_TMSG; ?></span> </div>
      <img id = "BoxSwitch__<?php echo KUNENA_BOARD_CLASS ;?>popusermsgstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody id = "<?php echo KUNENA_BOARD_CLASS ;?>popusermsgstats_tbody">
   <tr  class = "fb_sth" >
      <th class = "th-1 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="left" width="50%"><?php echo _KUNENA_USRL_USERNAME ;?></th>
      <th class = "th-2 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center" width="10%"> <?php echo _KUNENA_USRL_POSTS ;?></th>
    </tr>
<?php

	foreach ($this->topposters as $poster)
	{

	$k = 1 - $k;

	if ($poster->posts == $this->topmessage) {
	$barwidth = 100;
	}
	else {
	$barwidth = round(($poster->posts * 100) / $this->topmessage);
	}
?>

    <tr class = "<?php echo ''.KUNENA_BOARD_CLASS.''. $tabclass[$k] . ''; ?>">
      <td  class="td-1"  align="left">

         <?php echo CKunenaLink::GetProfileLink($kunena_config, $poster->userid, $poster->username); ?>

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
<?php if($kunena_config->showpopuserstats): ?>
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
<table class = "fb_blocktable " id="fb_popuserhitmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <thead>
    <tr>
      <th colspan="3">
      <div class = "fb_title_cover fbm"> <span class="fb_title fbl"> <?php echo _STAT_POPULAR; ?> <b><?php echo $kunena_config->popusercount; ?></b> <?php echo _STAT_POPULAR_USER_GSG; ?></span> </div>
      <img id = "BoxSwitch__<?php echo KUNENA_BOARD_CLASS ;?>popuserhitstats_tbody" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
      </th>
    </tr>
  </thead>
  <tbody id = "<?php echo KUNENA_BOARD_CLASS ;?>popuserhitstats_tbody">
   <tr  class = "fb_sth fbs" >
      <th class = "th-1 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader"  align="left" width="50%"> <?php echo _KUNENA_USRL_USERNAME ;?></th>
      <th class = "th-2 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center" width="10%"><?php echo _KUNENA_USRL_HITS ;?></th>
    </tr>

<?php
foreach ($this->topprofiles as $topprofile)
{
$k = 1 - $k;
if ($topprofile->hits == $this->topprofilehits) {
$barwidth = 100;
}
else {
$barwidth = round(($topprofile->hits * 100) / $this->topprofilehits);
}
?>

    <tr class = "<?php echo ''.KUNENA_BOARD_CLASS.''. $tabclass[$k] . ''; ?>">
      <td  class="td-1"  align="left">
        <?php echo CKunenaLink::GetProfileLink($kunena_config, $topprofile->user_id, $topprofile->user); ?>
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
