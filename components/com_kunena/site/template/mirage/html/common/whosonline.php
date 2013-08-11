<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule common-whosonline">
	<div class="kbox-wrapper kbox-full">
		<div class="common-whosonline-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
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
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox whosonline-details kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow" id="whosonline-detailsbox" >
					<?php if ($this->usersUrl) : ?>
					<a class="fl" href="<?php echo $this->usersUrl ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_LINK_TITLE') ?>" rel="nofollow">
						<span class="kwho-smicon"></span>
					</a>
					<?php else : ?>
					<span class="kwho-smicon"></span>
					<?php endif ?>
					<div class="kcontent-48 whosonline-users">
						<ul class="list-unstyled">
							<li class="whosonline-subtitle"><?php  echo JText::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline) ?></li>
							<li class="divider"></li>
							<?php if($this->onlineList) : ?>
							<li class="whosonline-usernames">
								<ul class="list-unstyled">
									<?php foreach ($this->onlineList as $user) : ?>
									<li><?php echo $user->getLink() ?></li>
									<?php endforeach ?>
								</ul>
							</li>
							<li class="divider"></li>
							<?php endif ?>
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
									<li><span class="kuser-admin"><?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?></span></li>
									<li><span class="kuser-globalmod"><?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?></span></li>
									<li><span class="kuser-moderator"><?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?></span></li>
									<li><span class="kuser-banned"><?php echo JText::_('COM_KUNENA_COLOR_BANNED'); ?></span></li>
									<li><span class="kuser-user"><?php echo JText::_('COM_KUNENA_COLOR_USER'); ?></span></li>
									<li><span class="kuser-guest"><?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?></span></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

