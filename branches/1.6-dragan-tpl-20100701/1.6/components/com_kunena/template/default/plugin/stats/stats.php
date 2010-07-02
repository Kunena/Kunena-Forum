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


$kunena_config = KunenaFactory::getConfig ();
$document =& JFactory::getDocument();

$document->setTitle(JText::_('COM_KUNENA_STAT_FORUMSTATS') . ' - ' . $kunena_config->board_title);

if($kunena_config->showstats):

$this->loadGenStats();
$this->loadUserStats();
$this->loadTopicStats();
$this->loadPollStats();
$this->loadThanksStats();

$forumurl = 'index.php?option=com_kunena';

$userlist1 = CKunenaLink::GetUserlistLink('', $this->totalmembers);

?>

        <!-- BEGIN: GENERAL STATS -->
<?php if($kunena_config->showgenstats): ?>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close"  rel="morestat_tbody"></a></span>
		<h2><span><?php echo $kunena_config->board_title; ?> <?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS'); ?></span></h2>
	</div>
	<div class="kcontainer" id="frontstats_tbody">
		<div class="kbody">
        <table  class = "kblocktable" id ="kmorestat" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <tbody id = "morestat_tbody">
                <tr class = "ksth ks">
                    <th class = "th-1 ksectiontableheader" align="left" width="50%"><?php echo JText::_('COM_KUNENA_STAT_GENERAL_STATS'); ?>
                    </th>
                </tr>

                <tr class = "krow1">
                    <td class = "td-1" align="left">
<?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>:<b> <?php echo $userlist1;?></b>
                    &nbsp; <?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>:<b> <?php echo CKunenaLink::GetProfileLink($this->lastestmemberid, $this->lastestmember); ?></b>

                <br/> <?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>: <b> <?php echo $this->totalmsgs; ?></b> &nbsp;
    <?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>: <b> <?php echo $this->totaltitles; ?></b> &nbsp; <?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>: <b> <?php echo $this->totalcats; ?></b> &nbsp; <?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>: <b> <?php echo $this->totalsections; ?></b>

                <br/> <?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>: <b> <?php echo $this->todayopen; ?></b> &nbsp; <?php echo
    JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>: <b> <?php echo $this->yesterdayopen; ?></b> &nbsp; <?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>: <b> <?php echo $this->todayanswer; ?></b> &nbsp; <?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>: <b> <?php echo $this->yesterdayanswer; ?></b>

                    </td>
                </tr>
            </tbody>
        </table>
        </div>
	</div>
</div>
<?php endif; ?>
<!-- FINISH: GENERAL STATS -->

<?php
$tabclass = array
(
"row1",
"row2"
);
$k = 0;
?>


<!-- B: Pop Subject -->
<?php if($this->showpopsubjectstats): ?>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close"  rel="kpopsubstats-tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_STAT_TOP'); ?> <strong><?php echo $kunena_config->popsubjectcount; ?></strong> <?php echo JText::_('COM_KUNENA_STAT_POPULAR'); ?> <?php echo JText::_('COM_KUNENA_STAT_POPULAR_USER_KGSG'); ?></span></h2>
	</div>
	<div class="kcontainer" id="frontstats_tbody">
		<div class="kbody">
			<table class = "kblocktable " id="kpopsubmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
 				 <tbody id = "kpopsubstats-tbody">
  					 <tr  class = "ksth" >
     					 <th class = "th-1 ksectiontableheader" align="left" width="50%"> <?php echo JText::_('COM_KUNENA_GEN_SUBJECT') ;?></th>
						 <th class = "th-2 ksectiontableheader" width="40%">&nbsp;  </th>
     					 <th class = "th-3 ksectiontableheader" align="center" width="10%"></th>
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
					?>

    				<tr class = "k<?php echo $tabclass[$k]; ?>">
     				 	<td class="td-1" align="left">
      				 		<?php echo CKunenaLink::GetThreadLink( 'view', $toptitle->catid, $toptitle->id, KunenaParser::parseText ($toptitle->subject), '' ); ?>
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
<?php endif; ?>
<!-- F: Pop Subject -->


<!-- B: Pop Poll -->
<?php if($this->showpoppollstats): ?>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close"  rel="kpoppollstats_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_STAT_TOP'); ?> <strong><?php echo $kunena_config->poppollscount; ?></strong> <?php echo JText::_('COM_KUNENA_STAT_POPULAR'); ?> <?php echo JText::_('COM_KUNENA_STAT_POPULAR_POLLS_KGSG'); ?></span></h2>
	</div>
	<div class="kcontainer" id="frontstats_tbody">
		<div class="kbody">
<table class = "kblocktable " id="kpoppollbmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <tbody id = "kpoppollstats_tbody">
   <tr  class = "ksth" >
      <th class = "th-1 ksectiontableheader" align="left" width="50%"> <?php echo JText::_('COM_KUNENA_POLL_NAME');?></th>
      <th class = "th-2 ksectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 ksectiontableheader" align="center" width="10%"></th>
    </tr>
 <?php foreach($this->toppolls as $toppoll)
       {
       if($toppoll->total != "0")
       {
	   $k = 1 - $k;
		   if ($toppoll->total == $this->toppollvotes) {
          $barwidth = 100;
		   }
		   else {
		    if($toppoll->total== null){
          $toppoll->total = "0";
        }
		    $barwidth = round(($toppoll->total * 100) / $this->toppollvotes);
		   }
?>

    <tr class = "k<?php echo $tabclass[$k]; ?>">
      <td class="td-1" align="left">
       <?php echo CKunenaLink::GetThreadLink( 'view', $toppoll->catid, $toppoll->threadid, kunena_htmlspecialchars($toppoll->title), '' ); ?>
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
<?php endif; ?>
<!-- F: Pop Polls -->


<!-- B: User Messages -->
<?php if($this->showpopuserstats): ?>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close"  rel="kpopusermsgstats_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_STAT_TOP'); ?> <strong><?php echo $kunena_config->popusercount; ?></strong> <?php echo JText::_('COM_KUNENA_STAT_POPULAR'); ?> <?php echo JText::_('COM_KUNENA_STAT_POPULAR_USER_TMSG'); ?></span></h2>
	</div>
	<div class="kcontainer" id="frontstats_tbody">
		<div class="kbody">
<table class = "kblocktable " id="kpopusermsgmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <tbody id = "kpopusermsgstats_tbody">
   <tr  class = "ksth" >
      <th class = "th-1 ksectiontableheader" align="left" width="50%"><?php echo JText::_('COM_KUNENA_USRL_USERNAME') ;?></th>
      <th class = "th-2 ksectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 ksectiontableheader" align="center" width="10%"></th>
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

    <tr class = "k<?php echo $tabclass[$k]; ?>">
      <td  class="td-1"  align="left">

         <?php echo CKunenaLink::GetProfileLink($poster->userid, $poster->username); ?>

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
<?php endif; ?>
<!-- F: User Messages -->


<!-- B: Pop User  -->
<?php if($this->showpopuserstats): ?>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close"  rel="kpopuserhitstats_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_STAT_TOP'); ?> <strong><?php echo $kunena_config->popusercount; ?></strong> <?php echo JText::_('COM_KUNENA_STAT_POPULAR'); ?> <?php echo JText::_('COM_KUNENA_STAT_POPULAR_USER_GSG'); ?></span></h2>
	</div>
	<div class="kcontainer" id="frontstats_tbody">
		<div class="kbody">
<table class = "kblocktable " id="kpopuserhitmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <tbody id = "kpopuserhitstats_tbody">
   <tr  class = "ksth ks" >
      <th class = "th-1 ksectiontableheader"  align="left" width="50%"> <?php echo JText::_('COM_KUNENA_USRL_USERNAME') ;?></th>
      <th class = "th-2 ksectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 ksectiontableheader" align="center" width="10%"></th>
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

    <tr class = "k<?php echo $tabclass[$k]; ?>">
      <td  class="td-1"  align="left">
        <?php echo CKunenaLink::GetProfileLink($topprofile->user_id, $topprofile->user); ?>
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
<?php endif; ?>
<!-- F: User User -->

<!-- B: Pop Thank you  -->
<?php if($this->showpopthankysoustats): ?>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close"  rel="kpopthankyou_tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_STAT_TOP'); ?> <strong><?php echo $kunena_config->popthankscount; ?></strong> <?php echo JText::_('COM_KUNENA_STAT_POPULAR'); ?> <?php echo JText::_('COM_KUNENA_STAT_POPULAR_USER_THANKS_YOU'); ?></span></h2>
	</div>
	<div class="kcontainer" id="frontstats_tbody">
		<div class="kbody">
<table class = "kblocktable " id="kpopuserhitmorestat"  cellpadding = "0" cellspacing = "0" border = "0" width = "100%">
  <tbody id = "kpopthankyou_tbody">
   <tr  class = "ksth ks" >
      <th class = "th-1 ksectiontableheader"  align="left" width="50%"> <?php echo JText::_('COM_KUNENA_USRL_USERNAME') ;?></th>
      <th class = "th-2 ksectiontableheader" width="40%">&nbsp;  </th>
      <th class = "th-3 ksectiontableheader" align="center" width="10%"></th>
    </tr>

<?php
foreach ($this->topuserthanks as $topthanks)
{
$k = 1 - $k;
if ($topthanks->receivedthanks == $this->topthanks) {
$barwidth = 100;
}
else {
$barwidth = round(($topthanks->receivedthanks * 100) / $this->topthanks);
}
?>

    <tr class = "k<?php echo $tabclass[$k]; ?>">
      <td  class="td-1"  align="left">
        <?php echo CKunenaLink::GetProfileLink($topthanks->id, $topthanks->username); ?>
</td>
      <td  class="td-2">
         <img class = "jr-forum-stat-bar" src = "<?php echo KUNENA_TMPLTMAINIMGURL.'/images/backgrounds/bar.png';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/>
                                    </td>
      <td  class="td-3">
	  <?php echo $topthanks->receivedthanks; ?> <?php echo JText::_('COM_KUNENA_STAT_THANKS_YOU_RECEIVED') ;?>
       </td>
    </tr>
<?php }   ?>
  </tbody>
</table>
        </div>
	</div>
</div>
<?php endif; ?>
<!-- F: Thank you -->


<?php
// WHOISONLINE
require_once (KUNENA_PATH_LIB .DS. 'kunena.who.class.php');
$online =& CKunenaWhoIsOnline::getInstance();
$online->displayWhoIsOnline();
// /WHOISONLINE

endif;
