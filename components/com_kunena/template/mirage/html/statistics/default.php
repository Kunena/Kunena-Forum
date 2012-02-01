<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Statistics
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
		<div id="statistics" class="block">
			<h2 class="header">
				<a rel="statistics-general" title="<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>" rel="statistics-detailsbox">
					<!-- span class="kstats-smicon"></span -->
					<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>
				</a>
			</h2>
			<div class="detailsbox statistics-details" id="statistics-general" >
				<div class="statistics-totals statistics-totals-col1">
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
		<div class="spacer"></div>

		<?php foreach ($this->top as $top) : ?>
		<div class="section popsubjstats">
			<h2 class="header"><a rel="popsubstats-tbody" title="Top 10 Most Popular Topics"><span><?php echo $top[0]->title ?></span></a></h2>
			<div id="popsubstats-tbody" class="container">
				<ul class="posthead">
					<li class="popsubstats-row">
						<ul class="clearfix">
							<li class="popsubstats-col popsubstats-col1"><?php echo $top[0]->titleName ?></li>
							<li class="popsubstats-col popsubstats-col2">&nbsp;</li>
							<li class="popsubstats-col popsubstats-col3"><?php echo $top[0]->titleCount ?></li>
						</ul>
					</li>
				</ul>
				<ul>
					<?php foreach ($top as $id=>$item) : ?>
					<li class="row-<?php echo $this->row(!$id) ?>">
						<ul class="clearfix">
							<li class="popsubstats-col popsubstats-col1"><?php echo $item->link ?></li>
							<li class="popsubstats-col popsubstats-col2">
								<img class="stats-bar" src="<?php echo $this->ktemplate->getImagePath('bar.png') ?>" alt="" height="10" width="<?php echo $item->percent ?>%" />
							</li>
							<li class="popsubstats-col popsubstats-col3"><?php echo $item->count ?></li>
						</ul>
					</li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
		<?php endforeach ?>