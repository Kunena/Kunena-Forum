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
?>
<div class="mod-kunenastats mod-kunenastats<?php echo $this->params->get( 'moduleclass_sfx' ) ?>">
	<?php if ( $this->type == 'general' ) : ?>
	<ul>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALUSERS'); ?> <b><?php echo $this->api->getToTalMembers(); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_LATESTMEMBER'); ?> <b><?php echo CKunenaLink::GetProfileLink($this->api->getLastestMemberid(), $this->api->getLastestMember()); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALPOSTS'); ?> <b><?php echo $this->api->getTotalMessages(); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALTOPICS'); ?> <b><?php echo $this->api->getTotalTitles(); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALSECTIONS'); ?> <b><?php echo $this->api->getTotalSections(); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TOTALCATEGORIES'); ?> <b><?php echo $this->api->getTotalCats(); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TODAYOPEN'); ?> <b><?php echo $this->api->getTodayOpen(); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_YESTERDAYOPEN'); ?> <b><?php echo $this->api->getYesterdayOpen(); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_TODAYTOTANSW'); ?> <b><?php echo $this->api->getTodayAnswer(); ?></b></li>
		<li><?php echo JText::_('MOD_KUNENASTATS_YESTERDAYTOTANSW'); ?> <b><?php echo $this->api->getYesterdayAnswer(); ?></b></li>
	</ul>
	<?php else : ?>
	<table>
		<tr>
			<th><?php echo $this->titleHeader ?></th>
			<th><?php echo $this->valueHeader ?></th>
		</tr>
		<?php if (empty($this->stats)) : ?>
		<tr><td><?php echo JText::_('MOD_KUNENASTATS_NO_ITEMS'); ?></td></tr>
		<?php else : ?>
	<?php if ( $this->type == 'topics' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr>
			<td><?php echo CKunenaLink::GetThreadLink('view', $stat->catid, $stat->thread, $stat->subject, $stat->subject); ?></td>
			<td><img class = "jr-forum-stat-bar" src = "<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png';?>" alt = "" height = "10" width = "<?php echo $this->getBarWidth($stat->hits);?>%"/></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'polls' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr>
			<td><?php echo CKunenaLink::GetThreadLink('view', $stat->catid, $stat->threadid, $stat->title, $stat->title); ?></td>
			<td><img class = "jr-forum-stat-bar" src = "<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png';?>" alt = "" height = "10" width = "<?php echo $this->getBarWidth($stat->total);?>%"/></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'posters' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr>
			<td><?php echo CKunenaLink::GetProfileLink($stat->userid, $stat->username); ?></td>
			<td><img class = "jr-forum-stat-bar" src = "<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png';?>" alt = "" height = "10" width = "<?php echo $this->getBarWidth($stat->posts);?>%"/></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'profiles' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr>
			<td><?php echo CKunenaLink::GetProfileLink($stat->user_id, $stat->user); ?></td>
			<td><img class = "jr-forum-stat-bar" src = "<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png';?>" alt = "" height = "10" width = "<?php echo $this->getBarWidth($stat->hits);?>%"/></td>
		</tr>
		<?php endforeach; ?>

	<?php elseif ( $this->type == 'thanks' ) : ?>
		<?php foreach ( $this->stats as $stat) : ?>
		<tr>
			<td><?php echo CKunenaLink::GetProfileLink($stat->userid, $stat->username); ?></td>
			<td><img class = "jr-forum-stat-bar" src = "<?php echo JURI::Root().'components/com_kunena/template/default/images/bar.png';?>" alt = "" height = "10" width = "<?php echo $this->getBarWidth($stat->hits);?>%"/></td>
		</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php endif; ?>
	</table>
	<?php endif; ?>
	<?php if ($this->params->get( 'sh_statslink' )) : ?>
	<div><?php echo CKunenaLink::GetStatsLink(JText::_('MOD_KUNENASTATS_LINK')); ?></div>
	<?php endif; ?>
</div>