<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>

<div class="kfrontend shadow-lg rounded mt-4 border">
	<div class="btn-toolbar float-right">
		<div class="btn-group">
			<div class="btn btn-outline-primary border btn-sm" data-toggle="collapse"
			     data-target="#kstats"><?php echo KunenaIcons::collapse(); ?></div>
		</div>
	</div>
	<h2 class="card-header">
		<?php if ($this->statisticsUrl)
			:
			?>
			<a href="<?php echo $this->statisticsUrl; ?>">
				<?php echo Text::_('COM_KUNENA_STATISTICS'); ?>
			</a>
		<?php else

			:
			?>
			<?php echo Text::_('COM_KUNENA_STATISTICS'); ?>
		<?php endif; ?>
	</h2>
	<div class="shadow-lg rounded " id="kstats">
		<div class="card-body">
			<div class="container">
				<div class="row">
					<div class="col-md-1">
						<ul class="list-unstyled">
							<li class="btn-link text-center"><?php echo KunenaIcons::stats(); ?></li>
						</ul>
					</div>

					<div class="col-md-3">
						<ul class="list-unstyled">
							<li>
								<?php echo Text::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>:
								<strong><?php echo (int) $this->messageCount; ?></strong>
							</li>
							<li>
								<?php echo Text::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>:
								<strong><?php echo (int) $this->sectionCount; ?></strong>
							</li>
							<li>
								<?php echo Text::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>:
								<strong><?php echo (int) $this->todayTopicCount; ?></strong>
							</li>
							<li>
								<?php echo Text::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>:
								<strong><?php echo (int) $this->todayReplyCount; ?></strong>
							</li>
						</ul>
					</div>

					<div class="col-md-3">
						<ul class="list-unstyled">
							<li>
								<?php echo Text::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>:
								<strong><?php echo (int) $this->topicCount; ?></strong>
							</li>
							<li>
								<?php echo Text::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>:
								<strong><?php echo (int) $this->categoryCount; ?></strong>
							</li>
							<li>
								<?php echo Text::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>:
								<strong><?php echo (int) $this->yesterdayTopicCount; ?></strong>
							</li>
							<li>
								<?php echo Text::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>:
								<strong><?php echo (int) $this->yesterdayReplyCount; ?></strong>
							</li>
						</ul>
					</div>

					<div class="col-md-3">
						<ul class="list-unstyled">
							<li>
								<?php echo Text::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>:
								<strong><?php echo $this->memberCount; ?></strong>
							</li>
							<li>
								<?php echo Text::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?>:
								<strong><?php echo $this->latestMemberLink; ?></strong>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
