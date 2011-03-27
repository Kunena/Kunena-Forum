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
		<div id="kwhosonline">
			<a href="<?php echo $this->usersURL ?>" class="kheader-link"><?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_LINK') ?> &raquo;</a>
			<h2 class="kheader">
				<a href="<?php echo $this->usersURL ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_LINK_TITLE') ?>" rel="kwhosonline-detailsbox" >
					<?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_TITLE') ?>
				</a>
			</h2>
			<div class="kdetailsbox kwhosonline-details" id="kwhosonline-detailsbox" >
				<div class="kwhosonline-smicon"><a href="<?php echo $this->usersURL ?>" title="<?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_LINK_TITLE') ?>"><span class="kwho-smicon"></span></a></div>
				<div class="kwhosonline-users">
					<ul>
						<li class="kwhosonline-subtitle"><p><?php  echo JText::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline) ?></p></li>
						<li class="kwhosonline-usernames">
							<ul>
								<?php foreach ($this->onlineList as $user) : ?>
								<li><?php echo $user->getLink() ?></li>
								<?php endforeach ?>
							</ul>
						</li>
						<?php if ($this->hiddenList) : ?>
						<li class="kwhosonline-usernames">
							<ul>
								<li class="klegend-title"><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>:</li>
								<?php foreach ($this->hiddenList as $user) : ?>
								<li><?php echo $user->getLink() ?></li>
								<?php endforeach ?>
							</ul>
						</li>
						<?php endif ?>
						<li class="kwhosonline-legend">
							<ul>
								<li class="klegend-title"><?php echo JText::_('COM_KUNENA_LEGEND'); ?>:</li>
								<!-- Loop this LI for each group -->
								<!-- Can each of these link to a memberlist of that group only?  -->
								<li class="kuser-admin"><?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?></li>
								<li class="kuser-globalmod"><?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?></li>
								<li class="kuser-moderator"><?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?></li>
								<li class="kuser-user"><?php echo JText::_('COM_KUNENA_COLOR_USER'); ?></li>
								<li class="kuser-guest"><?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
			<div class="clr"></div>
		</div>