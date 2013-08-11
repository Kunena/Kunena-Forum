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
<div class="kmodule common-menu">
	<div class="kbox-wrapper kbox-full">
		<div class="common-menu-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-shadow kbox-animate innerspacer-horizontal">
				<?php echo $this->getMenu() ?>

				<ul class="list-unstyled menu fr">
					<?php if (!$this->me->exists()) : ?>
						<li class="dropdown">
							<a class="link-login link" href="#"><?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME') ?> <?php echo JText::_('COM_KUNENA_PROFILEBOX_GUEST') ?>, <?php echo JText::_('Sign In'); ?><b class="caret caret-down"></b></a>
							<?php $this->displayLoginBox (); ?>
						</li>
					<?php else : ?>
						<li class="dropdown">
							<a class="link-logout link" href="#">
								<span class="login-member">
									<span class="login-avatar"><?php echo $this->me->getAvatarImage('kavatar', 'welcome') ?></span>
									<span class="loginbox">
										<span class="login-welcome"><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_LOGOUT_WELCOME', $this->me->getName(null, JText::_('COM_KUNENA_VIEW_COMMON_LOGOUT_OWN_LINK_TITLE'))) ?></span>
									</span>
								</span>
								<b class="caret caret-down"></b>
							</a>
							<?php $this->displayLoginBox (); ?>
						</li>
					<?php endif; ?>
				</ul>
		</div>
	</div>
</div>
