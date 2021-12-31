<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Statistics
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

?>
<div>

	<?php foreach ($this->top as $top)
		:
		?>
		<h3>
			<?php echo $top[0]->title; ?>
		</h3>

		<table class="table table-striped table-bordered">
			<thead>
			<tr>
				<th class="span1 center">#</th>
				<th class="span5"><?php echo $top[0]->titleName; ?></th>
				<th class="span6"><?php echo $top[0]->titleCount; ?></th>
			</tr>
			</thead>
			<tbody>

			<?php foreach ($top as $id => $item)
				:
				?>
				<tr>
					<td class="center">
						<?php echo $id + 1; ?>
					</td>
					<td>
						<?php echo $item->link; ?>
					</td>
					<td>
						<div class="progress progress-info">
							<div class="bar"
							     style="width: <?php echo $item->percent; ?>%;"><?php echo $item->count; ?></div>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>

			</tbody>
		</table>
	<?php endforeach; ?>
	<?php
	if ($this->config->showgenstats)
		:
		?>
		<h2>
			<?php echo Text::_('COM_KUNENA_STATISTICS'); ?>
		</h2>

		<div class="well well-small">
			<?php echo Text::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>:
			<b>

				<?php if ($this->userlistUrl)
					:
					?>
					<a href="<?php echo $this->userlistUrl; ?>"><?php echo $this->memberCount; ?></a>
				<?php else
					:
					?>
					<?php echo $this->memberCount; ?>
				<?php endif; ?>

			</b>

			<?php echo Text::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>:
			<b><?php echo $this->latestMemberLink ?></b>

			<br/>

			<?php echo Text::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>:
			<b><?php echo (int) $this->messageCount; ?></b>

			<?php echo Text::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>:
			<b><?php echo (int) $this->topicCount; ?></b>

			<?php echo Text::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>:
			<b><?php echo (int) $this->sectionCount; ?></b>

			<?php echo Text::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>:
			<b><?php echo (int) $this->categoryCount; ?></b>

			<br/>

			<?php echo Text::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>:
			<b><?php echo (int) $this->todayTopicCount; ?></b>

			<?php echo Text::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>:
			<b><?php echo (int) $this->yesterdayTopicCount; ?></b>

			<?php echo Text::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>:
			<b><?php echo (int) $this->todayReplyCount; ?></b>

			<?php echo Text::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>:
			<b><?php echo (int) $this->yesterdayReplyCount; ?></b>
		</div>
	<?php endif; ?>

</div>
