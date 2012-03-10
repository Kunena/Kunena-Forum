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
	<div class="box-wrapper ">
		<div class="menubox-kbox kbox box-color box-full box-border box-border_radius box-shadow">
				<?php echo $this->getMenu() ?>

				<ul class="list-unstyled menu fr">
					<?php if (!$this->me->exists()) : ?>
						<li class="dropdown">
							<a class="link-login" href="#"><?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME') ?> <?php echo JText::_('COM_KUNENA_PROFILEBOX_GUEST') ?>, <?php echo JText::_('Sign In'); ?></a>
							<?php $this->displayLoginBox (); ?>
						</li>
					<?php else : ?>
						<li class="dropdown">
							<a class="link-logout" href="#">
								<span class="login-member">
									<span class="login-avatar"><span><?php echo $this->me->getAvatarImage('', 'welcome') ?></span></span>
									<span class="loginbox">
										<span class="login-welcome"><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_LOGOUT_WELCOME', $this->me->getName(null, JText::_('COM_KUNENA_VIEW_COMMON_LOGOUT_OWN_LINK_TITLE'))) ?></span>
									</span>
								</span>
							</a>
							<?php $this->displayLoginBox (); ?>
						</li>
					<?php endif; ?>
				</ul>
		</div>
	</div>
</div>
<div class="spacer"></div>