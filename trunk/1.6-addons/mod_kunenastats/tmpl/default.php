<?php
/**
* @version $Id$
* KunenaStats Module
* @package Kunena Stats
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$i=0;
?>
<div class="mod-kunenastats mod-kunenastats<?php echo $this->params->get( 'moduleclass_sfx' ) ?>">
	<?php if ( $this->type == 'general' ) : ?>
	<ul>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALUSERS'); ?> <b><?php echo CKunenaTools::formatLargeNumber($this->api->getToTalMembers(), 4); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_LATESTMEMBER'); ?> <b><?php echo CKunenaLink::GetProfileLink($this->api->getLastestMemberid(), $this->api->getLastestMember()); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALPOSTS'); ?> <b><?php echo CKunenaTools::formatLargeNumber($this->api->getTotalMessages(), 3); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALTOPICS'); ?> <b><?php echo CKunenaTools::formatLargeNumber($this->api->getTotalTitles(), 3); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALSECTIONS'); ?> <b><?php echo CKunenaTools::formatLargeNumber($this->api->getTotalSections(), 3); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALCATEGORIES'); ?> <b><?php echo CKunenaTools::formatLargeNumber($this->api->getTotalCats(), 3); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TODAYOPEN'); ?> <b><?php echo CKunenaTools::formatLargeNumber($this->api->getTodayOpen(), 3); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_YESTERDAYOPEN'); ?> <b><?php echo CKunenaTools::formatLargeNumber($this->api->getYesterdayOpen(), 3); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TODAYTOTANSW'); ?> <b><?php echo CKunenaTools::formatLargeNumber($this->api->getTodayAnswer(), 3); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_YESTERDAYTOTANSW'); ?> <b><?php echo CKunenaTools::formatLargeNumber($this->api->getYesterdayAnswer(), 3); ?></b></li>
	</ul>
	<?php else : ?>
	<table>
		<tr>
			<th><?php echo $this->titleHeader ?></th>
			<th><?php echo $this->valueHeader ?></th>
		</tr>
		<?php if (empty($this->stats)) : ?>
		<tr class="krow<?php echo ($i^=1)+1;?>"><td><?php echo JText::_('MOD_KUNENASTATS_NO_ITEMS'); ?></td></tr>
		<?php else : ?>
	<?php if ( $this->type == 'topics' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first"><?php echo CKunenaLink::GetThreadLink('view', $stat->catid, $stat->thread, $stat->subject, $stat->subject); ?></td>
			<td class="kcol-last"><span class="kstats-hits" style="width:<?php echo $this->getBarWidth($stat->hits);?>%;"><?php echo CKunenaTools::formatLargeNumber($stat->hits, 3);?></span></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'polls' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first"><?php echo CKunenaLink::GetThreadLink('view', $stat->catid, $stat->threadid, $stat->title, $stat->title); ?></td>
			<td class="kcol-last"><span class="kstats-hits" style="width:<?php echo $this->getBarWidth($stat->total);?>%;"><?php echo CKunenaTools::formatLargeNumber($stat->total, 3);?></span></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'posters' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first"><?php echo CKunenaLink::GetProfileLink($stat->userid, $stat->username); ?></td>
			<td class="kcol-last"><span class="kstats-hits" style="width:<?php echo $this->getBarWidth($stat->posts);?>%;"><?php echo CKunenaTools::formatLargeNumber($stat->posts, 3);?></span></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'profiles' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first"><?php echo CKunenaLink::GetProfileLink($stat->user_id, $stat->user); ?></td>
			<td class="kcol-last"><span class="kstats-hits" style="width:<?php echo $this->getBarWidth($stat->hits);?>%;"><?php echo CKunenaTools::formatLargeNumber($stat->hits, 3);?></span></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'thanks' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first"><?php echo CKunenaLink::GetProfileLink($stat->userid, $stat->username); ?></td>
			<td class="kcol-last"><span class="kstats-hits" style="width:<?php echo $this->getBarWidth($stat->receivedthanks);?>%;"><?php echo CKunenaTools::formatLargeNumber($stat->receivedthanks, 3);?></span></td>
		</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php endif; ?>
	</table>
	<?php endif; ?>
	<?php if ($this->params->get( 'sh_statslink' )) : ?>
	<div class="kstats-all"><?php echo CKunenaLink::GetStatsLink(JText::_('MOD_KUNENASTATS_LINK')); ?></div>
	<?php endif; ?>
</div>