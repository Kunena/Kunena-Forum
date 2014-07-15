<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<div class="kfrontend">
	<h2>
		<?php if ($this->statisticsUrl) : ?>
			<a href="<?php echo $this->statisticsUrl; ?>">
				<?php echo JText::_('COM_KUNENA_STATISTICS'); ?>
			</a>
		<?php else : ?>
			<?php echo JText::_('COM_KUNENA_STATISTICS'); ?>
		<?php endif; ?>

		<span class="btn btn-small pull-right" data-toggle="collapse" data-target="#kstats">&times;</span>
	</h2>
	<div class="collapse in" id="kstats">
	<div class="well well-small">
	<div class="row-fluid">
		<ul class="unstyled span4">
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>:
				<strong><?php echo (int) $this->messageCount; ?></strong>
			</li>
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>:
				<strong><?php echo (int) $this->sectionCount; ?></strong>
			</li>
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>:
				<strong><?php echo (int) $this->todayTopicCount; ?></strong>
			</li>
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>:
				<strong><?php echo (int) $this->todayReplyCount; ?></strong>
			</li>
		</ul>
		<ul class="unstyled span4">
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>:
				<strong><?php echo (int) $this->topicCount; ?></strong>
			</li>
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>:
				<strong><?php echo (int) $this->categoryCount; ?></strong>
			</li>
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>:
				<strong><?php echo (int) $this->yesterdayTopicCount; ?></strong>
			</li>
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>:
				<strong><?php echo (int) $this->yesterdayReplyCount; ?></strong>
			</li>
		</ul>
		<ul class="unstyled span4">
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>:
				<strong><?php echo $this->memberCount; ?></strong>
			</li>
			<li>
				<?php echo JText::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>:
				<strong><?php echo $this->latestMemberLink; ?></strong>
			</li>
		</ul>
	</div>
	</div>
</div>
</div>
