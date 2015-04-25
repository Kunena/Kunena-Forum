<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Statistics
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$template = KunenaTemplate::getInstance();
?>
<!-- BEGIN: GENERAL STATS -->
<?php if($this->config->showgenstats): ?>
<div class="kblock kgenstats">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kgenstats_tbody"></a></span>
		<h1><span><?php echo $this->escape($this->config->board_title); ?> <?php echo JText::_('COM_KUNENA_STAT_FORUMSTATS'); ?></span></h1>
	</div>
	<div class="kcontainer" id="kgenstats_tbody">
		<div class="kbody">
	<table  class = "kblocktable">
		<tbody>
			<tr class = "ksth">
				<th colspan="2"><?php echo JText::_('COM_KUNENA_STAT_GENERAL_STATS'); ?></th>
			</tr>
			<tr class = "krow1">
				<td class = "kcol-first">
					<div class="kstatsicon"></div>
				</td>
				<td class = "kcol-mid">
					<?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>:<b> <?php echo $this->userlist;?></b> &nbsp;
					<?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>:<b> <?php echo $this->latestMemberLink ?></b>

					<br /> <?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>: <b> <?php echo intval($this->messageCount); ?></b> &nbsp;
					<?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>: <b> <?php echo intval($this->topicCount); ?></b> &nbsp;
					<?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>: <b> <?php echo intval($this->sectionCount); ?></b> &nbsp;
					<?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>: <b> <?php echo intval($this->categoryCount); ?></b>

					<br /> <?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>: <b> <?php echo intval($this->todayTopicCount); ?></b> &nbsp;
					<?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>: <b> <?php echo intval($this->yesterdayTopicCount); ?></b> &nbsp;
					<?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>: <b> <?php echo intval($this->todayReplyCount); ?></b> &nbsp;
					<?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>: <b> <?php echo intval($this->yesterdayReplyCount); ?></b>

				</td>
			</tr>
		</tbody>
	</table>
</div>
</div>
</div>
<?php endif; ?>
<!-- FINISH: GENERAL STATS -->

<?php
$tabclass = array("row1","row2");
$k = 0;
?>
<?php foreach ($this->top as $top) : ?>
<div class="kblock kpopsubjstats">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kpopsubstats-tbody"></a></span>
		<h2><span><?php echo $top[0]->title ?></span></h2>
	</div>
	<div class="kcontainer" id="kpopsubstats-tbody">
		<div class="kbody">
			<table class="kblocktable">
				<tbody>
					<tr class="ksth" >
						<th>#</th>
						<th class="kname"><?php echo $top[0]->titleName ?></th>
						<th class="kbar">&nbsp;</th>
						<th class="kname"><?php echo $top[0]->titleCount ?></th>
					</tr>
					<?php foreach ($top as $id=>$item) : ?>
					<tr class="k<?php echo $this->escape($tabclass[$id & 1]); ?>">
						<td class="kcol-first"><?php echo $id+1 ?></td>
						<td class="kcol-mid">
							<?php echo $item->link ?>
						</td>
						<td class="kcol-mid">
							<img class="kstats-bar" src="<?php echo $template->getImagePath('bar.png') ?>" alt="" height="10" width="<?php echo $item->percent ?>%" />
						</td>
						<td class="kcol-last">
							<?php echo $item->count ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php endforeach; ?>
<?php $this->displayWhoIsOnline(); ?>
