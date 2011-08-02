<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
$template = KunenaFactory::getTemplate();
$this->params = $template->params;
?>
<div class="forumlist">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon whoisonline">
						<dt>
							<?php echo JText::_('COM_KUNENA_VIEW_COMMON_WHO_TITLE') ?>
						</dt>
						<dd class="tk-toggler"><a class="ktoggler close" rel="whoisonline-body"></a></dd>
					</dl>
				</li>
			</ul>
			<ul id="whoisonline-body" class="topiclist forums">
				<li class="rowfull">
					<dl class="icon whoisonline">
					<dt></dt>
					<dd class="first body">
					<div class="whoonline km">
						<?php  echo JText::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline) ?>
					</div>
					<div>
						<?php
							foreach ($this->onlineList as $user) :
							if ( $this->params->get('showWoisonlineavatar') == '1' ): ?>
							<div class="online_hor">
								<div class="online_avatar" style="position:relateive;">
									<?php echo $user->getLink($user->getAvatarImage('klist-avatar', 'list')) ?>
								</div>
							</div>
							<?php else : ?>
							<div class="online_hor">
							<div class="online_username">
								<?php echo $user->getLink() ?>
							</div>
							</div>
							<?php endif; endforeach;?>
							<div class="clr"></div>
							<?php if ($this->hiddenList) : ?>
							<div class="who-hidenusers">
								<span class="km"><?php echo JText::_('COM_KUNENA_HIDDEN_USERS'); ?>: </span><br />
								<?php foreach ($this->hiddenList as $user) : ?>
									<?php echo $user->getLink() ?>
								<?php endforeach; ?>
							</div>
							<?php endif ?>
					</div>
					<div class="clr"></div>
					<div class="wholegend km">
						<span><?php echo JText::_('COM_KUNENA_LEGEND'); ?> :: </span>&nbsp;
						<span class = "kuser-admin" title = "<?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_ADMINISTRATOR'); ?></span>,&nbsp;
						<span class = "kuser-globalmod" title = "<?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_GLOBAL_MODERATOR'); ?></span>,&nbsp;
						<span class = "kuser-moderator" title = "<?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_MODERATOR'); ?></span>,&nbsp;
						<span class = "kuser-user" title = "<?php echo JText::_('COM_KUNENA_COLOR_USER'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_USER'); ?></span>,&nbsp;
						<span class = "kuser-guest" title = "<?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?>"> <?php echo JText::_('COM_KUNENA_COLOR_GUEST'); ?></span>
					</div>
					</dd>
					</dl>
				</li>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>