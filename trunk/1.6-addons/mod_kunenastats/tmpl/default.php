<?php
/**
* @version $Id$
* Kunena Stats Module
* @package Kunena Stats
*
* @Copyright (C)2010-2011 Kunena Team. All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$i=0;

//JString::substr ( htmlspecialchars ( $item->subject ), '0', $this->params->get ( 'titlelength' ) )

?>
<div class="kstats-module kstats-module<?php echo $this->params->get( 'moduleclass_sfx' ) ?>">
	<?php if ( $this->type == 'general' ) : ?>
	<ul class="kstats-items">
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALUSERS'); ?> <?php echo CKunenaTools::formatLargeNumber($this->api->getToTalMembers(), 4); ?></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_LATESTMEMBER'); ?> <?php echo CKunenaLink::GetProfileLink($this->api->getLastestMemberid(), JString::substr ( htmlspecialchars ($this->api->getLastestMember()), '0', $this->params->get ( 'titlelength' ) ) ); ?></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALPOSTS'); ?> <?php echo CKunenaTools::formatLargeNumber($this->api->getTotalMessages(), 3); ?></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALTOPICS'); ?> <?php echo CKunenaTools::formatLargeNumber($this->api->getTotalTitles(), 3); ?></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALSECTIONS'); ?> <?php echo CKunenaTools::formatLargeNumber($this->api->getTotalSections(), 3); ?></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALCATEGORIES'); ?> <?php echo CKunenaTools::formatLargeNumber($this->api->getTotalCats(), 3); ?></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TODAYOPEN'); ?> <?php echo CKunenaTools::formatLargeNumber($this->api->getTodayOpen(), 3); ?></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_YESTERDAYOPEN'); ?> <?php echo CKunenaTools::formatLargeNumber($this->api->getYesterdayOpen(), 3); ?></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TODAYTOTANSW'); ?> <?php echo CKunenaTools::formatLargeNumber($this->api->getTodayAnswer(), 3); ?></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_YESTERDAYTOTANSW'); ?> <?php echo CKunenaTools::formatLargeNumber($this->api->getYesterdayAnswer(), 3); ?></li>
	</ul>
	<?php else : ?>
	<table class="kstats-table">
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
			<td class="kcol-first"><?php echo CKunenaLink::GetThreadLink('view', $stat->catid, $stat->thread, JString::substr ( htmlspecialchars ($stat->subject), '0', $this->params->get ( 'titlelength' ) ), htmlspecialchars ($stat->subject) ); ?></td>
			<td class="kcol-last"><span class="kstats-hits-bg"><span class="kstats-hits" style="width:<?php echo $this->getBarWidth($stat->hits);?>%;"><?php echo CKunenaTools::formatLargeNumber($stat->hits, 3);?></span></span></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'polls' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first"><?php echo CKunenaLink::GetThreadLink('view', $stat->catid, $stat->threadid, JString::substr ( htmlspecialchars ($stat->title), '0', $this->params->get ( 'titlelength' ) ), $stat->title); ?></td>
			<td class="kcol-last"><span class="kstats-hits-bg"><span class="kstats-hits" style="width:<?php echo $this->getBarWidth($stat->total);?>%;"><?php echo CKunenaTools::formatLargeNumber($stat->total, 3);?></span></span></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'posters' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first"><?php echo CKunenaLink::GetProfileLink($stat->userid, JString::substr ( htmlspecialchars ($stat->username), '0', $this->params->get ( 'titlelength' ) )); ?></td>
			<td class="kcol-last"><span class="kstats-hits-bg"><span class="kstats-hits" style="width:<?php echo $this->getBarWidth($stat->posts);?>%;"><?php echo CKunenaTools::formatLargeNumber($stat->posts, 3);?></span></span></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'profiles' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first"><?php echo CKunenaLink::GetProfileLink($stat->user_id, JString::substr ( htmlspecialchars ($stat->user), '0', $this->params->get ( 'titlelength' ) )); ?></td>
			<td class="kcol-last"><span class="kstats-hits-bg"><span class="kstats-hits" style="width:<?php echo $this->getBarWidth($stat->hits);?>%;"><?php echo CKunenaTools::formatLargeNumber($stat->hits, 3);?></span></span></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'thanks' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first"><?php echo CKunenaLink::GetProfileLink($stat->id, JString::substr ( htmlspecialchars ($stat->username), '0', $this->params->get ( 'titlelength' ) )); ?></td>
			<td class="kcol-last"><span class="kstats-hits-bg"><span class="kstats-hits" style="width:<?php echo $this->getBarWidth($stat->receivedthanks);?>%;"><?php echo CKunenaTools::formatLargeNumber($stat->receivedthanks, 3);?></span></span></td>
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