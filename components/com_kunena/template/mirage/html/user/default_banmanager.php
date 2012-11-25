<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.tooltip');
$i=1;
$j=0;
?>
<div class="kmodule user-default_banmanager">
	<div class="kbox-wrapper">
		<div class="user-default_banmanager-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a rel="kbanmanager-detailsbox"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="banmanager banmanager-detailsbox detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="banmanager-list list-unstyled list-row">
						<li class="header kbox-hover_header-row kbox-full item-row">
							<dl class="list-unstyled list-column">
								<dd class="banmanager-id item-column">
									<div class="innerspacer-header">
										<span class="bold">#</span>
									</div>
								</dd>
								<dd class="banmanager-user item-column">
									<div class="innerspacer-header">
										<span class="bold"><?php echo JText::_('COM_KUNENA_BAN_BANNEDUSER'); ?></span>
									</div>
								</dd>
								<dd class="banmanager-from item-column">
									<div class="innerspacer-header">
										<span class="bold"><?php echo JText::_('COM_KUNENA_BAN_BANNEDFROM'); ?></span>
									</div>
								</dd>
								<dd class="banmanager-start item-column">
									<div class="innerspacer-header">
										<span class="bold"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></span>
									</div>
								</dd>
								<dd class="banmanager-expire item-column">
									<div class="innerspacer-header">
										<span class="bold"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></span>
									</div>
								</dd>
							</dl>
						</li>
					</ul>
					<ul class="banmanger-list list-unstyled list-row">
						<?php
						if ( $this->bannedusers ) :
							foreach ($this->bannedusers as $userban) :
								$bantext = $userban->blocked ? JText::_('COM_KUNENA_BAN_UNBLOCK_USER') : JText::_('COM_KUNENA_BAN_UNBAN_USER');
								$j++;
						?>
						<li class="banmanager-row kbox-hover kbox-hover_list-row item-row">
							<dl class="list-unstyled list-column">
								<dd class="banmanager-id item-column">
									<div class="innerspacer-column">
										<?php echo $j; ?>
									</div>
								</dd>
								<dd class="banmanager-user item-column">
									<div class="innerspacer-column">
										<?php echo $userban->getUser()->getLink() ?>
									</div>
								</dd>
								<dd class="banmanager-from item-column">
									<div class="innerspacer-column">
										<span><?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA') : JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA') ?></span>
									</div>
								</dd>
								<dd class="banmanager-start item-column">
									<div class="innerspacer-column">
										<span><?php echo KunenaDate::getInstance($userban->created_time)->toKunena('datetime') ?></span>
									</div>
								</dd>
								<dd class="banmanager-expire item-column">
									<div class="innerspacer-column">
										<span><?php echo $userban->isLifetime() ? JText::_('COM_KUNENA_BAN_LIFETIME') : KunenaDate::getInstance($userban->expiration)->toKunena('datetime') ?></span>
									</div>
								</dd>
							</dl>
						</li>
						<?php endforeach; ?>
						<?php else : ?>
						<li class="banmanager-row kbox-hover kbox-hover_list-row item-row">
							<dl class="list-unstyled">
								<dd class="banmanager-none">
									<div class="innerspacer-column">
										<?php echo JText::_('COM_KUNENA_BAN_NO_BANNED_USERS') ?>
									</div>
								</dd>
							</dl>
						</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

