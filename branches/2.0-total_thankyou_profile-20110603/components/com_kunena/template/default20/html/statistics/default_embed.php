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
			<h2 class="kheader">
				<a rel="kstatistics-general" title="<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>" rel="kstatistics-detailsbox">
					<!-- span class="kstats-smicon"></span -->
					<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>
				</a>
			</h2>
			<div class="kdetailsbox kstatistics-details" id="kstatistics-general" >
				<div class="kstatistics-totals kstatistics-totals-col1">
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

		<?php foreach ($this->top as $top) : ?>
		<div class="ksection kpopsubjstats">
			<h2 class="kheader"><a rel="kpopsubstats-tbody" title="Top 10 Most Popular Topics"><span><?php echo $top[0]->title ?></span></a></h2>
			<div id="kpopsubstats-tbody" class="kcontainer">
				<ul class="kposthead">
					<li class="kpopsubstats-row">
						<ul class="clearfix">
							<li class="kpopsubstats-col kpopsubstats-col1"><?php echo $top[0]->titleName ?></li>
							<li class="kpopsubstats-col kpopsubstats-col2">&nbsp;</li>
							<li class="kpopsubstats-col kpopsubstats-col3"><?php echo $top[0]->titleCount ?></li>
						</ul>
					</li>
				</ul>
				<ul>
					<?php foreach ($top as $id=>$item) : ?>
					<li class="krow-<?php echo $this->row(!$id) ?>">
						<ul class="clearfix">
							<li class="kpopsubstats-col kpopsubstats-col1"><?php echo $item->link ?></li>
							<li class="kpopsubstats-col kpopsubstats-col2">
								<img class="kstats-bar" src="<?php echo $this->template->getImagePath('bar.png') ?>" alt="" height="10" width="<?php echo $item->percent ?>%" />
							</li>
							<li class="kpopsubstats-col kpopsubstats-col3"><?php echo $item->count ?></li>
						</ul>
					</li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
		<?php endforeach ?>