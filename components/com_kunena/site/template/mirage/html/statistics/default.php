<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Statistics
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule common-statistics">
	<div class="kbox-wrapper kbox-full">
		<div class="common-statistics-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header header2">
						<a class="header" rel="statistics-general" title="<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>" rel="statistics-detailsbox">
							<!-- span class="kstats-smicon"></span -->
							<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>
						</a>
					</h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="statistics-details detailsbox kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
					<div class="statistics-smicon">
						<span class="stats-smicon"></span>
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
						<span class="stats-smicon"></span>
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
						<span class="stats-smicon"></span>
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


<?php foreach ($this->top as $top) : ?>
	<div class="kmodule statistics-default">
		<div class="kbox-wrapper kbox-full">
			<div class="statistics-default-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
				<div class="headerbox-wrapper kbox-full">
					<div class="header">
						<h2 class="header"><a rel="popsubstats-tbody" title="Top 10 Most Popular Topics"><span><?php echo $top[0]->title ?></span></a></h2>
					</div>
				</div>
				<div class="detailsbox-wrapper innerspacer">
					<div class="statistics-details detailsbox kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow" id="statistics-detailsbox">
						<ul class="popsubstat-list list-unstyled list-row">
							<li class="header kbox-hover_header-row item-row">
								<dl class="list-unstyled list-column">
									<dd class="popsubstats-col1 item-column">
										<div class="innerspacer-header">
											<span class="bold"><?php echo $top[0]->titleName ?></span>
										</div>
									</dd>
									<dd class="popsubstats-col2 item-column">
										<div class="innerspacer-header">
											<span class="bold">&nbsp;</span>
										</div>
									</dd>
									<dd class="popsubstats-col3 item-column">
										<div class="innerspacer-header">
											<span class="bold"><?php echo $top[0]->titleCount ?></span>
										</div>
									</dd>
								</dl>
							</li>
						</ul>
						<ul class="popsubstat-list list-unstyled list-row">
							<?php foreach ($top as $id=>$item) : ?>
								<li class="popsubstats-row kbox-hover kbox-hover_list-row item-row">
									<dl class="list-unstyled list-column">
										<dd class="popsubstats-col1 item-column">
											<div class="innerspacer-column">
												<?php echo $item->link ?>
											</div>
										</dd>
										<dd class="popsubstats-col2 item-column">
											<div class="innerspacer-column">
												<div class="stats-bar" style="width:<?php echo $item->percent ?>%"></div>
											</div>
										</dd>
										<dd class="popsubstats-col3 item-column">
											<div class="innerspacer-column">
												<?php echo $item->count ?>
											</div>
										</dd>
									</dl>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php endforeach ?>
