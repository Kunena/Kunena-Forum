<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule common-statistics">
	<div class="kbox-wrapper kbox-full">
		<div class="common-statistics-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header fl">
					<h2 class="header link-header2">
						<a class="section" href="<?php echo $this->statisticsUrl ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_STAT_LINK_TITLE') ?>" rel="statistics-detailsbox">
							<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>
						</a>
					</h2>
				</div>
				<div class="header fr">
					<a class="link" href="<?php echo $this->statisticsUrl ?>" rel="follow"><?php echo JText::_('COM_KUNENA_STAT_MORE_ABOUT_STATS') ?></a>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="statistics-details detailsbox kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
					<div class="statistics-smicon">
						<a href="<?php echo $this->statisticsUrl ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_STAT_LINK_TITLE') ?>" rel="nofollow">
							<span class="stats-smicon"></span>
						</a>
					</div>
					<div class="statistics-totals">
						<ul class="list-unstyled">
							<li class="statistics-totalmess"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES') ?>:<span><?php echo $this->messageCount ?></span></li>
							<li class="statistics-totalsubj"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS') ?>:<span><?php echo $this->topicCount ?></span></li>
							<li class="statistics-totalsect"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS') ?>:<span><?php echo $this->sectionCount ?></span></li>
							<li class="statistics-totalcats"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES') ?>:<span><?php echo $this->categoryCount ?></span></li>
						</ul>
					</div>
					<div class="statistics-smicon">
						<a href="<?php echo $this->statisticsUrl ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_STAT_LINK_TITLE') ?>" rel="nofollow">
							<span class="stats-smicon"></span>
						</a>
					</div>
					<div class="statistics-totals">
						<ul class="list-unstyled">
							<li class="statistics-todayopen"><?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD') ?>:<span><?php echo $this->todayTopicCount ?></span></li>
							<li class="statistics-yestopen"><?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD') ?>:<span><?php echo $this->yesterdayTopicCount ?></span></li>
							<li class="statistics-todayans"><?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER') ?>:<span><?php echo $this->todayReplyCount ?></span></li>
							<li class="statistics-yestans"><?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER') ?>:<span><?php echo $this->yesterdayReplyCount ?></span></li>
						</ul>
					</div>
					<div class="statistics-smicon">
						<a href="<?php echo $this->statisticsUrl ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_STAT_LINK_TITLE') ?>" rel="nofollow">
							<span class="stats-smicon"></span>
						</a>
					</div>
					<div class="statistics-totals">
						<ul class="list-unstyled">
							<li class="statistics-totalusers"><?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS') ?>:<span><?php echo $this->memberCount ?></span></li>
							<li class="statistics-latestmem"><?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS') ?>:<span><?php echo $this->latestMemberLink ?></span></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

