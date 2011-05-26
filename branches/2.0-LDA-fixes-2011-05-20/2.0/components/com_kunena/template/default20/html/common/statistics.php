<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<div id="kstatistics">
			<a href="<?php echo $this->statisticsURL ?>" class="kheader-link"><?php echo JText::_('COM_KUNENA_STAT_MORE_ABOUT_STATS') ?> &raquo;</a>
			<h2 class="kheader">
				<a href="<?php echo $this->statisticsURL ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_STAT_LINK_TITLE') ?>" rel="kstatistics-detailsbox">
					<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>
				</a>
			</h2>
			<div class="kdetailsbox kstatistics-details" id="kstatistics-detailsbox" >
				<div class="kstatistics-smicon">
					<a href="<?php echo $this->statisticsURL ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_STAT_LINK_TITLE') ?>">
						<span class="kstats-smicon"></span>
					</a>
				</div>
				<div class="kstatistics-totals">
					<ul>
						<li class="kstatistics-totalmess"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES') ?>:<span><?php echo $this->messageCount ?></span></li>
						<li class="kstatistics-totalsubj"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS') ?>:<span><?php echo $this->topicCount ?></span></li>
						<li class="kstatistics-totalsect"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS') ?>:<span><?php echo $this->sectionCount ?></span></li>
						<li class="kstatistics-totalcats"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES') ?>:<span><?php echo $this->categoryCount ?></span></li>
					</ul>
				</div>
				<div class="kstatistics-totals">
					<ul>
						<li class="kstatistics-todayopen"><?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD') ?>:<span><?php echo $this->todayTopicCount ?></span></li>
						<li class="kstatistics-yestopen"><?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD') ?>:<span><?php echo $this->yesterdayTopicCount ?></span></li>
						<li class="kstatistics-todayans"><?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER') ?>:<span><?php echo $this->todayReplyCount ?></span></li>
						<li class="kstatistics-yestans"><?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER') ?>:<span><?php echo $this->yesterdayReplyCount ?></span></li>
					</ul>
				</div>
				<div class="kstatistics-totals">
					<ul>
						<li class="kstatistics-totalusers"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS') ?>:<span><?php echo $this->memberCount ?></span></li>
						<li class="kstatistics-latestmem"><?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS') ?>:<span><?php echo $this->latestMemberLink ?></span></li>
					</ul>
				</div>
			</div>
			<div class="clr"></div>
		</div>