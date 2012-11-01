<li class="topic-row">
	<dl class="list-unstyled">
		<dd class="kbox-border kbox-border_radius kbox-shadow">
			<ul class="list-unstyled message-list">
				<li class="header">
					<a id="<?php echo $this->displayMessageField('id') ?>"></a>
					<dl class="list-unstyled list-inline-block">
						<dd class="kposthead-replytitle"><h3>RE: <?php echo $this->displayMessageField('subject') ?></h3></dd>
						<dd class="kposthead-postid fr"><?php echo $this->numLink ?></dd>
						<?php if (!empty($this->ipLink)) : ?><dd class="kposthead-postip fr"><?php echo $this->ipLink ?></dd><?php endif ?>
						<dd class="kposthead-posttime fr">Posted [K=DATE:<?php echo $this->message->time ?>]</dd>
					</dl>
				</li>
			</ul>
			<div class="kpost-container kbox-full kbox-hover">
				<div class="kpost-container-bottom">
					<ul class="list-unstyled kpost-post-body">
						<?php $this->displayMessageContents() ?>
					</ul>
					<?php $this->displayMessageActions() ?>
				</div>
				<ul class="list-unstyled kpost-user_details-horizontal kbox-full kbox-width">
					<?php $this->displayMessageProfile('horizontal') ?>
				</ul>
			</div>
		</dd>
	</dl>
</li>