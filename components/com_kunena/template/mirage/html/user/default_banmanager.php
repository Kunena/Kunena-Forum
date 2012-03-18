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

JHTML::_('behavior.tooltip');
$i=1;
$j=0;
?>
<div class="kmodule user-default_banmanager">
	<div class="kbox-wrapper">
		<div class="user-default_banmanager-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a rel="kbanmanager-detailsbox"><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="banmanager banmanager-detailsbox detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="list-unstyled banmanager-list">
						<li class="header kbox-hover_header-row clear">
							<dl class="list-unstyled">
								<dd class="banmanager-id"><span class="bold">#</span></dd>
								<dd class="banmanager-user"><span class="bold"><?php echo JText::_('COM_KUNENA_BAN_BANNEDUSER'); ?></span></dd>
								<dd class="banmanager-from"><span class="bold"><?php echo JText::_('COM_KUNENA_BAN_BANNEDFROM'); ?></span></dd>
								<dd class="banmanager-start"><span class="bold"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></span></dd>
								<dd class="banmanager-expire"><span class="bold"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></span></dd>
							</dl>
						</li>
					</ul>
					<ul class="list-unstyled banmanger-list">
						<?php
						if ( $this->bannedusers ) :
							foreach ($this->bannedusers as $userban) :
								$bantext = $userban->blocked ? JText::_('COM_KUNENA_BAN_UNBLOCK_USER') : JText::_('COM_KUNENA_BAN_UNBAN_USER');
								$j++;
						?>
						<li class="banmanager-row kbox-hover kbox-hover_list-row">
							<dl class="list-unstyled">
								<dd class="banmanager-id">
									<?php echo $j; ?>
								</dd>
								<dd class="banmanager-user">
									<?php echo $userban->getUser()->getLink() ?>
								</dd>
								<dd class="banmanager-from">
									<span><?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA') : JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA') ?></span>
								</dd>
								<dd class="banmanager-start">
									<span><?php echo KunenaDate::getInstance($userban->created_time)->toKunena('datetime') ?></span>
								</dd>
								<dd class="banmanager-expire">
									<span><?php echo $userban->isLifetime() ? JText::_('COM_KUNENA_BAN_LIFETIME') : KunenaDate::getInstance($userban->expiration)->toKunena('datetime') ?></span>
								</dd>
							</dl>
						</li>
						<?php endforeach; ?>
						<?php else : ?>
						<li class="banmanager-row kbox-hover kbox-hover_list-row">
							<dl class="list-unstyled">
								<dd><?php echo JText::_('COM_KUNENA_BAN_NO_BANNED_USERS') ?></dd>
							</dl>
						</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
