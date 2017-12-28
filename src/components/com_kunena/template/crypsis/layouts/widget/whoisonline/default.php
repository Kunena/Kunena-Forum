<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Statistics
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>

<div class="kfrontend">
	<div class="btn-toolbar pull-right">
		<div class="btn-group">
			<div class="btn btn-small" data-toggle="collapse" data-target="#kwho"></div>
		</div>
	</div>
	<h2 class="btn-link">
		<?php if ($this->usersUrl) : ?>
			<a href="<?php echo $this->usersUrl; ?>">
				<?php echo JText::_('COM_KUNENA_MEMBERS'); ?>
			</a>
		<?php else : ?>
			<?php echo JText::_('COM_KUNENA_MEMBERS'); ?>
		<?php endif; ?>
	</h2>

	<div class="row-fluid collapse in" id="kwho">
		<div class="well-small">
			<ul class="unstyled span1 btn-link">
				<?php echo KunenaIcons::members(); ?>
			</ul>
			<ul class="unstyled span11">
			<span>
				<?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline); ?>
			</span>
				<?php
				$template = KunenaTemplate::getInstance();
				$direction = $template->params->get('whoisonlineName');

				if ($direction == 'both') : ?>
					<div><?php echo $this->setLayout('both'); ?></div>
				<?php
				elseif ($direction == 'avatar') : ?>
					<div><?php echo $this->setLayout('avatar'); ?></div>
				<?php else : ?>
					<div><?php echo $this->setLayout('name'); ?></div>
				<?php
					endif;
				?>

				<?php if (!empty($this->onlineList)) : ?>
				<div>
					<span><?php echo JText::_('COM_KUNENA_LEGEND'); ?>:</span>
					<span class="kwho-admin">
						<?php echo KunenaIcons::user(); ?> <?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?>
					</span>
					<span class="kwho-globalmoderator">
						<?php echo KunenaIcons::user(); ?> <?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?>
					</span>
					<span class="kwho-moderator">
						<?php echo KunenaIcons::user(); ?> <?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?>
					</span>
					<span class="kwho-banned">
						<?php echo KunenaIcons::user(); ?> <?php echo JText::_('COM_KUNENA_COLOR_BANNED'); ?>
					</span>
					<span class="kwho-user">
						<?php echo KunenaIcons::user(); ?> <?php echo JText::_('COM_KUNENA_COLOR_USER'); ?>
					</span>
					<span class="kwho-guest">
						<?php echo KunenaIcons::user(); ?> <?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?>
					</span>
				</div>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
