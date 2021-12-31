<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Statistics
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
			     data-target="#kwho"><?php echo KunenaIcons::collapse(); ?></div>
		</div>
	</div>
	<h2 class="card-header">
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

	<div class="shadow-lg rounded " id="kwho">
		<div class="card-body">
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
							<li>
								<?php echo Text::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline); ?>
							</li>
							<?php
							$template  = KunenaTemplate::getInstance();
							$direction = $template->params->get('whoisonlineName');

							if ($direction == 'both')
								:
								?>
								<li><?php echo $this->setLayout('both'); ?></li>
							<?php
							elseif ($direction == 'avatar')
								:
								?>
								<li><?php echo $this->setLayout('avatar'); ?></li>
							<?php else

								:
								?>
								<li><?php echo $this->setLayout('name'); ?></li>
							<?php
							endif;
							?>

							<?php if (!empty($this->onlineList))
								:
								?>
								<li>
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
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
