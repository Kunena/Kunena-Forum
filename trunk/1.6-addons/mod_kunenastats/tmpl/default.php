<?php
/**
* @version $Id$
* KunenaStats Module
* @package Kunena Stats
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*/

 // no direct access
defined('_JEXEC') or die('Restricted access');
 ?>
<div class="kstatistics<?php echo $params->get( 'moduleclass_sfx' ) ?>">
  <?php if ( $this->statsType == '0' ) { ?>
  <table>
    <tr>
      <th><?php echo JText::_('MOD_STATS_SUBJECT'); ?></th>
      <th><?php echo JText::_('MOD_STATS_HIT'); ?></th>
    </tr>
    
      <?php 
      foreach ( $stats as $stat) { 
        if ($stat->hits == modKStatisticsHelper::getTopTitlesHits($this->nbItems)) {
		      $barwidth = 100;
		    } else {
		      $barwidth = round(($stat->hits * 100) / modKStatisticsHelper::getTopTitlesHits($this->nbItems));
		    }
      ?>
      <tr>
      <td><?php echo $klink->GetThreadLink('view', $stat->catid, $stat->thread,  stripslashes($stat->subject), stripslashes($stat->subject));  ?></td>
      <td><img class = "jr-forum-stat-bar" src = "<?php echo JURI::Root().'components/com_kunena/template/default/images/backgrounds/bar.png';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/></td>
      </tr>
      <?php } ?>
    
  </table>
  <?php } elseif ( $this->statsType == '1' ) { ?>
  <table>
    <tr>
      <th><?php echo JText::_('MOD_STATS_POLL'); ?></th>
      <th><?php echo JText::_('MOD_STATS_VOTES'); ?></th>
    </tr>
    
      <?php 
      foreach ( $stats as $stat) { 
        if ($stat->total == modKStatisticsHelper::getTopPollVotesStats($this->nbItems)) {
		      $barwidth = 100;
		    } else {
		      $barwidth = round(($stat->total * 100) / modKStatisticsHelper::getTopPollVotesStats($this->nbItems));
		    }
      ?>
      <tr>
      <td><?php echo $klink->GetThreadLink('view', $stat->catid, $stat->threadid, $stat->title, $stat->title);  ?></td>
      <td><img class = "jr-forum-stat-bar" src = "<?php echo JURI::Root().'components/com_kunena/template/default/images/backgrounds/bar.png';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/></td>
      </tr>
      <?php } ?>
    
  </table>
  <?php } elseif ( $this->statsType == '2' ) { ?>  
  <table>
    <tr>
      <th><?php echo JText::_('MOD_STATS_USER'); ?></th>
      <th><?php echo JText::_('MOD_STATS_HIT'); ?></th>
    </tr>
    
      <?php 
      foreach ( $stats as $stat) {       
        if ($stat->posts == modKStatisticsHelper::getTopMessage($this->nbItems)) {
	       $barwidth = 100;
	     } else {
	       $barwidth = round(($stat->posts * 100) / modKStatisticsHelper::getTopMessage($this->nbItems));
	     }	     
      ?>
      <tr>
      <td><?php echo $klink->GetProfileLink($kconfig, $stat->userid, stripslashes($stat->username));  ?></td>
      <td><img class = "jr-forum-stat-bar" src = "<?php echo JURI::Root().'components/com_kunena/template/default/images/backgrounds/bar.png';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/></td>
      </tr>
      <?php } ?>
    
  </table>
  <?php } elseif ( $this->statsType == '3' ) { ?>  
  <table>
    <tr>
      <th><?php echo JText::_('MOD_STATS_USERPROFILE'); ?></th>
      <th><?php echo JText::_('MOD_STATS_HIT'); ?></th>
    </tr>
    
      <?php 
      foreach ( $stats as $stat) { 
        if ($stat->hits == modKStatisticsHelper::getTopProfileHits($this->nbItems)) {
          $barwidth = 100;
        } else {
          $barwidth = round(($stat->hits * 100) / modKStatisticsHelper::getTopProfileHits($this->nbItems));
        }
      ?>
      <tr>
      <td><?php echo $klink->GetProfileLink($kconfig, $stat->user_id, stripslashes($stat->user));  ?></td>
      <td><img class = "jr-forum-stat-bar" src = "<?php echo JURI::Root().'components/com_kunena/template/default/images/backgrounds/bar.png';?>" alt = "" height = "10" width = "<?php echo $barwidth;?>%"/></td>
      </tr>      
      <?php }  ?>
    
  </table>
  <?php } elseif ( $this->statsType == '4' ) { ?>
  <table>
    <tr>
      <th><?php echo JText::_('MOD_STATS_GENERALSTATS'); ?></th>      
    </tr>      
      <tr>
      <td><?php echo JText::_('MOD_STATS_TOTALUSERS'); ?><b><?php echo $model->getToTalMembers(); ?></b></td>
      <td><?php echo JText::_('MOD_STATS_LATESTMEMBER'); ?><b><?php echo $klink->GetProfileLink($kconfig, $model->getLastestMemberid(), $model->getLastestMember()); ?></b></td>
      </tr><tr> 
      <td><?php echo JText::_('MOD_STATS_TOTALMESSAGES'); ?><b><?php echo $model->getTotalMessages(); ?></b></td>
      <td><?php echo JText::_('MOD_STATS_TOTALSUBJECTS'); ?><b><?php echo $model->getTotalTitles(); ?></b></td>
      </tr><tr> 
      <td><?php echo JText::_('MOD_STATS_TOTALSECTIONS'); ?><b><?php echo $model->getTotalSections(); ?></b></td>
      <td><?php echo JText::_('MOD_STATS_TOTALCATS'); ?><b><?php echo $model->getTotalCats(); ?></b></td>
       </tr><tr>
      <td><?php echo JText::_('MOD_STATS_TODAYOPEN'); ?><b><?php echo $model->getTodayOpen(); ?></b></td>
      <td><?php echo JText::_('MOD_STATS_YESTERDAYOPEN'); ?><b><?php echo $model->getYesterdayOpen(); ?></b></td>
       </tr><tr>
      <td><?php echo JText::_('MOD_STATS_TODAYTOTANSW'); ?><b><?php echo $model->getTodayAnswer(); ?></b></td>
      <td><?php echo JText::_('MOD_STATS_YESTERDAYTOTANSW'); ?><b><?php echo $model->getYesterdayAnswer(); ?></b></td>
      </tr>    
  </table>
  <?php }  
    if ($params->get( 'sh_statslink' )) { 
      echo $klink->GetStatsLink('See full Stats details');  
   } ?>
</div>