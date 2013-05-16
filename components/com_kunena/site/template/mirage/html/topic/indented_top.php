<li class="topic-row">
	<div class="post-indent" style="padding-left: <?php echo (max(0,count($this->message->indent)-3)*2) ?>%">
		<dl class="list-unstyled">
			<dd class="kbox-border kbox-border_radius kbox-shadow">
				<ul class="list-unstyled message-list">
					<li class="header">
						<a id="<?php echo $this->displayMessageField('id') ?>"></a>
						<dl class="list-unstyled list-inline-block">
							<dd class="kposthead-replytitle"><h3>RE: <?php echo $this->displayMessageField('subject') ?></h3></dd>
							<dd class="kposthead-postid" ><?php echo $this->numLink ?></dd>
							<?php if (!empty($this->ipLink)) : ?><dd class="kposthead-postip"><?php echo $this->ipLink ?></dd><?php endif ?>
							<dd class="kposthead-posttime">Posted [K=DATE:<?php echo $this->message->time ?>]</dd>
						</dl>
					</li>
				</ul>
				<div class="kpost-container kbox-full kbox-hover">
					<div class="kpost-user_details-horizontal kbox-full kbox-width">
						<?php $this->displayMessageProfile('horizontal') ?>
					</div>
					<div class="kpost-container-top">
						<ul class="list-unstyled kpost-post-body">
							<?php $this->displayMessageContents() ?>
						</ul>
						<?php $this->displayMessageActions() ?>
					</div>
				</div>
			</dd>
		</dl>
	</div>
</li>