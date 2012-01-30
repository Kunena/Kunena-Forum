<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
	<div class="block-wrapper box-color box-border box-border_radius">
		<div id="statistics" class="block">
			<div class="headerbox-wrapper">
			<div class="header">
			<h2 class="header">
				<a href="<?php echo $this->statisticsURL ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_STAT_LINK_TITLE') ?>" rel="statistics-detailsbox">
					<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>
				</a>
			</h2>
			</div>
			</div>
			<div class="detailsbox-wrapper">
			<div class="detailsbox statistics-details box-hover" id="statistics-detailsbox" >
				<div class="statistics-smicon">
					<a href="<?php echo $this->statisticsURL ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_STAT_LINK_TITLE') ?>">
						<span class="stats-smicon"></span>
					</a>
				</div>
				<div class="statistics-totals">
					<ul>
						<li class="statistics-totalmess"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES') ?>:<span><?php echo $this->messageCount ?></span></li>
						<li class="statistics-totalsubj"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS') ?>:<span><?php echo $this->topicCount ?></span></li>
						<li class="statistics-totalsect"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS') ?>:<span><?php echo $this->sectionCount ?></span></li>
						<li class="statistics-totalcats"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES') ?>:<span><?php echo $this->categoryCount ?></span></li>
					</ul>
				</div>
				<div class="statistics-totals">
					<ul>
						<li class="statistics-todayopen"><?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD') ?>:<span><?php echo $this->todayTopicCount ?></span></li>
						<li class="statistics-yestopen"><?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD') ?>:<span><?php echo $this->yesterdayTopicCount ?></span></li>
						<li class="statistics-todayans"><?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER') ?>:<span><?php echo $this->todayReplyCount ?></span></li>
						<li class="statistics-yestans"><?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER') ?>:<span><?php echo $this->yesterdayReplyCount ?></span></li>
					</ul>
				</div>
				<div class="statistics-totals">
					<ul>
						<li class="statistics-totalusers"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS') ?>:<span><?php echo $this->memberCount ?></span></li>
						<li class="statistics-latestmem"><?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS') ?>:<span><?php echo $this->latestMemberLink ?></span></li>
					</ul>
				</div>
			</div>
			</div>
		</div>
	</div>
	<div class="spacer"></div>