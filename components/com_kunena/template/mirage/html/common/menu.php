<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="box-module">
	<div class="box-wrapper box-full box-border box-border_radius box-shadow">
		<div class="menubox block">
				<?php echo $this->getMenu() ?>
				<ul class="list-unstyled menu fr">
					<?php if (!$this->me->exists()) : ?>
						<li class="dropdown">
							<a class="link-login" href="#"><?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME') ?> <?php echo JText::_('COM_KUNENA_PROFILEBOX_GUEST') ?>, <?php echo JText::_('Sign In'); ?></a>
							<?php $this->displayLoginBox (); ?>
						</li>
					<?php endif; ?>
					<?php if ($this->me->exists()) : ?>
						<li class="dropdown">
							<a class="link-logout" href="#">
								<div class="login-member">
									<div class="login-avatar"><span><?php echo $this->me->getAvatarImage('', 'welcome') ?></span></div>
									<div class="loginbox">
										<div class="login-welcome"><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_LOGOUT_WELCOME', $this->me->getName(null, JText::_('COM_KUNENA_VIEW_COMMON_LOGOUT_OWN_LINK_TITLE'))) ?></div>
									</div>
								</div>
							</a>
							<?php $this->displayLoginBox (); ?>
						</li>
					<?php endif; ?>
				</ul>
		</div>
	</div>
</div>
<div class="spacer"></div>