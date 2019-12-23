<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Statistics
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

?>

<div class="kfrontend">
	<div class="btn-toolbar pull-right">
		<div class="btn-group">
			<div class="btn btn-default btn-sm" data-toggle="collapse"
			     data-target="#kwho"><?php echo KunenaIcons::collapse(); ?></div>
		</div>
	</div>
	<h2 class="btn-link">
		<?php if ($this->usersUrl)
			:
			?>
			<a href="<?php echo $this->usersUrl; ?>">
				<?php echo Text::_('COM_KUNENA_MEMBERS'); ?>
			</a>
		<?php else

			:
			?>
			<?php echo Text::_('COM_KUNENA_MEMBERS'); ?>
		<?php endif; ?>
	</h2>

	<div class="collapse in" id="kwho">
		<div class="well well-sm">
			<div class="container">
				<div class="row">

					<div class="col-md-1">
						<ul class="list-unstyled">
							<li class="btn-link">
								<?php echo KunenaIcons::members(); ?>
							</li>
						</ul>
					</div>

					<div class="col-md-11">
						<ul class="list-unstyled">
							<span>
								<?php echo Text::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline); ?>
							</span>
							<?php
							$template  = KunenaTemplate::getInstance();
							$direction = $template->params->get('whoisonlineName');

							if ($direction == 'both')
								:
								?>
								<div><?php echo $this->setLayout('both'); ?></div>
							<?php
							elseif ($direction == 'avatar')
								:
								?>
								<div><?php echo $this->setLayout('avatar'); ?></div>
							<?php else

								:
								?>
								<div><?php echo $this->setLayout('name'); ?></div>
							<?php
							endif;
							?>

							<?php if (!empty($this->onlineList))
								:
								?>
								<div>
									<span><?php echo Text::_('COM_KUNENA_LEGEND'); ?>:</span>
									<span class="kwho-admin">
										<?php echo KunenaIcons::user(); ?><?php echo Text::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?>
									</span>
									<span class="kwho-globalmoderator">
										<?php echo KunenaIcons::user(); ?><?php echo Text::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?>
									</span>
									<span class="kwho-moderator">
										<?php echo KunenaIcons::user(); ?><?php echo Text::_('COM_KUNENA_COLOR_MODERATOR'); ?>
									</span>
									<span class="kwho-banned">
										<?php echo KunenaIcons::user(); ?><?php echo Text::_('COM_KUNENA_COLOR_BANNED'); ?>
									</span>
									<span class="kwho-user">
										<?php echo KunenaIcons::user(); ?><?php echo Text::_('COM_KUNENA_COLOR_USER'); ?>
									</span>
									<span class="kwho-guest">
										<?php echo KunenaIcons::user(); ?><?php echo Text::_('COM_KUNENA_COLOR_GUEST'); ?>
									</span>
								</div>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
