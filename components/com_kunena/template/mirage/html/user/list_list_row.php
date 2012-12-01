<dl class="user-list list-unstyled list-column kbox-full">
	<dd class="user-id item-column"><?php echo $nr; ?></dd>

	<?php if ($this->config->userlist_online) : ?>
		<dd class="user-online item-column">
			<div class="innerspacer-column">
				<span class="kicon-button kbuttononline-<?php echo $this->profile->isOnline('yes', 'no') ?>"><span class="online-<?php echo $this->profile->isOnline('yes', 'no') ?>"><span><?php echo $this->profile->isOnline(JText::_('COM_KUNENA_ONLINE'), JText::_('COM_KUNENA_OFFLINE')); ?></span></span></span>
		</dd>
	<?php endif; ?>

	<?php if ($this->config->userlist_avatar) : ?>
		<dd class="user-avatar item-column">
			<div class="innerspacer-column">
				<?php echo !empty($uslavatar) ? $this->profile->getLink($uslavatar) : '&nbsp;' ?>
			</div>
		</dd>
	<?php endif; ?>

	<dd class="user-username user-realname item-column">
		<div class="innerspacer-column">
			<?php echo $this->profile->getLink(); ?>
		</div>
	</dd>
	<?php if ($this->config->userlist_posts) : ?>
		<dd class="user-posts item-column">
			<div class="innerspacer-column">
				<?php echo intval($user->posts); ?>
			</div>
		</dd>
	<?php endif; ?>

	<?php if ($this->config->userlist_karma) : ?>
		<dd class="user-karma item-column">
			<div class="innerspacer-column">
				<?php echo intval($user->karma); ?>
			</div>
		</dd>
	<?php endif; ?>

	<?php if ($this->config->userlist_email) : ?>
		<dd class="user-email item-column">
			<div class="innerspacer-column">
				<?php echo $user->email ? JHtml::_('email.cloak', $user->email) : '' ?>
			</div>
		</dd>
	<?php endif; ?>

	<?php if ($this->config->userlist_usertype) : ?>
		<dd class="user-usertype item-column">
			<div class="innerspacer-column">
				<?php echo JText::_($user->getType()) ?>
			</div>
		</dd>
	<?php endif; ?>

	<?php if ($this->config->userlist_joindate) : ?>
		<dd class="user-joindate item-column" title="<?php echo KunenaDate::getInstance($user->registerDate)->toKunena('ago') ?>">
			<div class="innerspacer-column">
				<?php echo KunenaDate::getInstance($user->registerDate)->toKunena('datetime_today') ?>
			</div>
		</dd>
	<?php endif; ?>

	<?php if ($this->config->userlist_lastvisitdate) : ?>
		<dd class="user-lastvisitdate item-column" title="<?php echo KunenaDate::getInstance($user->lastvisitDate)->toKunena('ago') ?>">
			<div class="innerspacer-column">
				<?php echo KunenaDate::getInstance($user->lastvisitDate)->toKunena('datetime_today') ?>
			</div>
		</dd>
	<?php endif; ?>

	<?php if ($this->config->userlist_userhits) : ?>
		<dd class="user-hits item-column">
			<div class="innerspacer-column">
				<?php echo $this->escape($this->profile->uhits) ?>
			</div>
		</dd>
	<?php endif; ?>
</dl>