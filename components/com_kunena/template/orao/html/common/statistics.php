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
<div class="forumlist">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon frontstats">
						<dt>
							<span class="ktitle"><?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS'); ?></span>
						</dt>
						<dd class="tk-toggler"><a class="ktoggler close" rel="frontstats-body"></a></dd>
					</dl>
				</li>
			</ul>
			<ul id="frontstats-body" class="topiclist forums">
				<li class="rowfull">
					<dl class="icon caticon-stats">
						<dt></dt>
						<dd class="first">
								<ul id="statslistleft" class="fltlft">
									<li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>: <strong> <?php echo $this->messageCount ?></strong></li>
									<li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>: <strong><?php echo $this->sectionCount ?></strong></li>
									<li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>: <strong><?php echo $this->topicCount ?></strong></li>
									<li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>: <strong><?php echo $this->categoryCount ?></strong></li>
								</ul>
						</dd>
						<dd class="tk-usersactivity-total">
								<ul id="tk-statslistright"  class="fltlft">
									<li><?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>: <strong><?php echo $this->todayTopicCount ?></strong></li>
									<li><?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>: <strong><?php echo $this->todayReplyCount ?></strong></li>
									<li><?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>: <strong><?php echo $this->yesterdayTopicCount ?></strong></li>
									<li><?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>: <strong><?php echo $this->yesterdayReplyCount ?></strong></li>
								</ul>
						</dd>
						<dd class="tk-users-total">
								<ul id="statslistright"  class="fltlft">
									<li><?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>: <strong><?php echo $this->memberCount ?></strong></li>
									<li><?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>:<strong> <?php echo $this->latestMemberLink ?></strong></li>
									<li><?php //echo $this->memberCount ?>&nbsp;</li>
									<li><a href="<?php echo $this->statisticsURL ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_STAT_LINK_TITLE') ?>" rel="kstatistics-detailsbox">
											<?php echo JText::_('COM_KUNENA_STAT_MORE_ABOUT_STATS') ?>
										</a>
									</li>
								</ul>
						</dd>
					</dl>
				</li>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>
