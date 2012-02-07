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
<div class="box-module">
	<div class="box-wrapper box-color box-border box-border_radius box-shadow">
		<div class="statistics block">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<h2 class="header">
						<a rel="statistics-general" title="<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>" rel="statistics-detailsbox">
							<!-- span class="kstats-smicon"></span -->
							<?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS') ?>
						</a>
					</h2>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="statistics-details detailsbox box-full box-hover box-border box-border_radius box-shadow">
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
		</div>
	</div>
</div>
<div class="spacer"></div>

<?php foreach ($this->top as $top) : ?>
	<div class="box-module">
		<div class="box-wrapper box-color box-border box-border_radius box-shadow">
			<div class="section popsubjstats block">
				<div class="headerbox-wrapper box-full">
					<div class="header">
						<h2 class="header"><a rel="popsubstats-tbody" title="Top 10 Most Popular Topics"><span><?php echo $top[0]->title ?></span></a></h2>
					</div>
				</div>
				<div class="detailsbox-wrapper">
					<div class="statistics-details detailsbox innerspacer box-full box-hover box-border box-border_radius box-shadow" id="statistics-detailsbox">
						<ul class="popsubstat-list">
							<li class="header box-hover_header-row">
								<dl>
									<dd class="popsubstats-col1"><?php echo $top[0]->titleName ?></dd>
									<dd class="popsubstats-col2">&nbsp;</dd>
									<dd class="popsubstats-col3"><?php echo $top[0]->titleCount ?></dd>
								</dl>
							</li>
						</ul>
						<ul class="popsubstat-list">
							<?php foreach ($top as $id=>$item) : ?>
								<li class="popsubstats-row box-hover box-hover_list-row">
									<dl>
										<dd class="popsubstats-col1"><?php echo $item->link ?></dd>
										<dd class="popsubstats-col2">
											<img class="stats-bar" src="<?php echo $this->ktemplate->getImagePath('bar.png') ?>" alt="" height="10" width="<?php echo $item->percent ?>%" />
										</dd>
										<dd class="popsubstats-col3"><?php echo $item->count ?></dd>
									</dl>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="spacer"></div>
<?php endforeach ?>