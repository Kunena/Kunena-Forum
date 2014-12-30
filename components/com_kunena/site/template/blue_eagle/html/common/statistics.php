<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>
<div class="kblock kfrontstats">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kfrontstats-tbody"></a></span>
		<h2><span><?php echo $this->config->statslink_allowed ? $this->getStatsLink($this->config->board_title.' '.JText::_('COM_KUNENA_STAT_FORUMSTATS'), '') : $this->config->board_title.' '.JText::_('COM_KUNENA_STAT_FORUMSTATS'); ?></span></h2>
	</div>
	<div class="kcontainer" id="kfrontstats-tbody">
		<div class="kbody">
			<table class="kblocktable" id="kfrontstats">
				<tr class="krow1">
					<td class="kcol-first">
						<div class="kstatsicon"></div>
					</td>
					<td class="kcol-mid km">
						<ul id="kstatslistright" class="fltrt kright">
							<li class="hidden-phone"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>: <strong><?php echo $this->getUserlistLink('', $this->memberCount) ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>: <strong><?php echo $this->latestMemberLink ?></strong></li>
							<li>&nbsp;</li>
							<li><?php echo $this->getUserlistLink('', JText::_('COM_KUNENA_STAT_USERLIST').' &raquo;') ?></li>
							<li><?php if ($this->config->showpopuserstats || $this->config->showpopsubjectstats) echo $this->getStatsLink(JText::_('COM_KUNENA_STAT_MORE_ABOUT_STATS').' &raquo;');?></li>
						</ul>
						<ul id="kstatslistleft" class="fltlft">
							<li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>: <strong> <?php echo intval($this->messageCount); ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>: <strong><?php echo intval($this->topicCount); ?></strong></li>
							<li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>: <strong><?php echo intval($this->sectionCount); ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>: <strong><?php echo intval($this->categoryCount); ?></strong></li>
							<li><?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>: <strong><?php echo $this->todayTopicCount; ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>: <strong><?php echo intval($this->yesterdayTopicCount); ?></strong></li>
							<li><?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>: <strong><?php echo intval($this->todayReplyCount); ?></strong> <span class="divider">|</span> <?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>: <strong><?php echo intval($this->yesterdayReplyCount); ?></strong></li>
						</ul>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
