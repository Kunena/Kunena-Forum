<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule">
	<div class="box-wrapper">
		<div class="whosonline-kbox kbox box-color box-border box-border_radius box-border_radius-child box-shadow">
			<div class="headerbox-wrapper box-full">
				<div class="header fl">
					<h2 class="header link-header2">
						<a class="section" href="<?php echo $this->usersUrl ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_LINK_TITLE') ?>" rel="whosonline-detailsbox" >
							<?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_TITLE') ?>
						</a>
					</h2>
				</div>
				<div class="header fr">
					<?php if ($this->usersUrl) : ?>
					<a class="link" href="<?php echo $this->usersUrl ?>" rel="follow"><?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_LINK') ?></a>
					<?php endif ?>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="detailsbox whosonline-details box-full box-hover box-border box-border_radius box-shadow" id="whosonline-detailsbox" >
					<div class="whosonline-smicon">
						<?php if ($this->usersUrl) : ?>
						<a href="<?php echo $this->usersUrl ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_LINK_TITLE') ?>" rel="nofollow">
							<span class="kwho-smicon"></span>
						</a>
						<?php else : ?>
						<span class="kwho-smicon"></span>
						<?php endif ?>
					</div>
					<div class="whosonline-users">
						<ul class="list-unstyled">
							<li class="whosonline-subtitle"><p><?php  echo JText::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline) ?></p></li>
							<li class="whosonline-usernames">
								<ul class="list-unstyled">
									<?php foreach ($this->onlineList as $user) : ?>
									<li><?php echo $user->getLink() ?></li>
									<?php endforeach ?>
								</ul>
							</li>
							<?php if ($this->hiddenList) : ?>
								<li class="whosonline-usernames">
									<ul class="list-unstyled">
										<li class="legend-title"><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>:</li>
										<?php foreach ($this->hiddenList as $user) : ?>
										<li><?php echo $user->getLink() ?></li>
										<?php endforeach ?>
									</ul>
								</li>
							<?php endif ?>
							<li class="whosonline-legend">
								<ul class="list-unstyled">
									<li class="legend-title"><?php echo JText::_('COM_KUNENA_LEGEND'); ?>:</li>
									<!-- Loop this LI for each group -->
									<!-- Can each of these link to a memberlist of that group only?  -->
									<li class="user-admin"><?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?></li>
									<li class="user-globalmod"><?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?></li>
									<li class="user-moderator"><?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?></li>
									<li class="user-user"><?php echo JText::_('COM_KUNENA_COLOR_USER'); ?></li>
									<li class="user-guest"><?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>