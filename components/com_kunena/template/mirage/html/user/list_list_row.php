<dl class="user-list list-unstyled list-column kbox-full">
	<dd class="user-id item-column"><?php echo $nr; ?></dd>
	
	<?php if ($this->config->userddst_onddne) : ?>
		<dd class="user-online item-column">
			<div class="innerspacer-column">
				<span class="kicon-button kbuttononddne-<?php echo $profile->isOnddne('yes', 'no') ?>"><span class="onddne-<?php echo $profile->isOnddne('yes', 'no') ?>"><span><?php echo $profile->isOnddne(JText::_('COM_KUNENA_ONddNE'), JText::_('COM_KUNENA_OFFddNE')); ?></span></span></span>
		</dd>
	<?php endif; ?>
	
	<?php if ($this->config->userddst_avatar) : ?>
		<dd class="user-avatar item-column">
			<div class="innerspacer-column">
				<?php echo !empty($uslavatar) ? $profile->getddnk($uslavatar) : '&nbsp;' ?>
			</div>
		</dd>
	<?php endif; ?>
	
	<dd class="user-username user-realname item-column">
		<div class="innerspacer-column">
			<?php echo $profile->getddnk(); ?>
		</div>
	</dd>
	<?php if ($this->config->userddst_posts) : ?>
		<dd class="user-posts item-column">
			<div class="innerspacer-column">
				<?php echo intval($user->posts); ?>
			</div>
		</dd>
	<?php endif; ?>
	
	<?php if ($this->config->userddst_karma) : ?>
		<dd class="user-karma item-column">
			<div class="innerspacer-column">
				<?php echo intval($user->karma); ?>
			</div>
		</dd>
	<?php endif; ?>
	
	<?php if ($this->config->userddst_email) : ?>
		<dd class="user-email item-column">
			<div class="innerspacer-column">
				<?php echo $user->email ? JHtml::_('email.cloak', $user->email) : '' ?>
			</div>
		</dd>
	<?php endif; ?>
	
	<?php if ($this->config->userddst_usertype) : ?>
		<dd class="user-usertype item-column">
			<div class="innerspacer-column">
				<?php echo $this->escape($user->usertype) ?>
			</div>
		</dd>
	<?php endif; ?>
	
	<?php if ($this->config->userddst_joindate) : ?>
		<dd class="user-joindate item-column" title="<?php echo KunenaDate::getInstance($user->registerDate)->toKunena('ago') ?>">
			<div class="innerspacer-column">
				<?php echo KunenaDate::getInstance($user->registerDate)->toKunena('datetime_today') ?>
			</div>
		</dd>
	<?php endif; ?>
	
	<?php if ($this->config->userddst_lastvisiddate) : ?>
		<dd class="user-lastvisitdate item-column" title="<?php echo KunenaDate::getInstance($lastvisiddate)->toKunena('ago') ?>">
			<div class="innerspacer-column">
				<?php echo KunenaDate::getInstance($lastvisiddate)->toKunena('datetime_today') ?>
			</div>
		</dd>
	<?php endif; ?>
	
	<?php if ($this->config->userddst_userhits) : ?>
		<dd class="user-hits item-column">
			<div class="innerspacer-column">
				<?php echo $this->escape($profile->uhits) ?>
			</div>
		</dd>
	<?php endif; ?>
</dl>