<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kfrontend">
 <h3>
	<a href="<?php echo $this->statisticsUrl; ?>">
		<?php echo $this->config->board_title.' '.JText::_('COM_KUNENA_STAT_FORUMSTATS'); ?>
	</a>
	<div class="btn btn-small pull-right" data-toggle="collapse" data-target="#kstats">X</div>
 </h3>
 <div class="kstats" id="kstats">
  <div class="well well-small">
	 <div class="row-fluid">
		<ul class="unstyled span6">
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>:
				<strong><?php echo intval($this->messageCount); ?></strong>
				<span class="divider">|</span>
				<?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>:
				<strong><?php echo intval($this->topicCount); ?></strong>
			</li>
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>:
				<strong><?php echo intval($this->sectionCount); ?></strong>
				<span class="divider">|</span>
				<?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>:
				<strong><?php echo intval($this->categoryCount); ?></strong>
			</li>
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>:
				<strong><?php echo $this->todayTopicCount; ?></strong>
				<span class="divider">|</span>
				<?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>:
				<strong><?php echo intval($this->yesterdayTopicCount); ?></strong>
			</li>
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>:
				<strong><?php echo intval($this->todayReplyCount); ?></strong>
				<span class="divider">|</span>
				<?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>:
				<strong><?php echo intval($this->yesterdayReplyCount); ?></strong>
			</li>
		</ul>
		<ul class="unstyled span6">
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>:
				<strong><a href="<?php echo $this->userlistUrl; ?>"><?php echo $this->memberCount; ?></a></strong>
				<span class="divider">|</span>
				<?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>:
				<strong><?php echo $this->latestMemberLink ?></strong>
			</li>
			<li>
				&nbsp;
			</li>
			<li>
				<a href="<?php echo $this->userlistUrl; ?>"><?php echo JText::_('COM_KUNENA_STAT_USERLIST').' &raquo;'; ?></a>
			</li>
			<?php if ($this->config->showpopuserstats || $this->config->showpopsubjectstats) : ?>
			<li>
				<a href="<?php echo $this->statisticsUrl; ?>"><?php echo JText::_('COM_KUNENA_STAT_MORE_ABOUT_STATS').' &raquo;'; ?></a>
			</li>
			<?php endif; ?>
		</ul>
	 </div>
	</div>
 </div>
</div>