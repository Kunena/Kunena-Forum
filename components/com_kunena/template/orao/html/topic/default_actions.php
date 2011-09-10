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
?>
<div class="forumlist tk-forum-action tk-clear">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist forums">
				<li class="rowfull tk-fixed">
					<dl class="icon">
						<dt></dt>
						<dd class="tk-thread-action" style="width:75%">
							<span class="klist-actions-goto tk-goto">
								<?php echo $this->goto ?>
							</span>

							<div class="tk-tools">
								<div class="tk-tools-options">
									<a class="kicon-button kbuttonuser btn-left">
										<span class="user-tools"><span style="">User Actions</span></span>
									</a>
									<div style="padding-top:3px;">
									<ul class="tk-tools-container tk-tools-container-user">
										<?php if (!empty ( $this->topic_new )) : ?>
										<li class="">
											<span>
												<?php echo $this->topic_new; ?>
											</span>
										</li>
										<?php endif;?>
										<?php if (!empty($this->topic_reply)) : ?>
										<li class="">
											<span>
												<?php echo $this->topic_reply ?>
											</span>
										</li>
										<?php endif;?>
										<?php if ( (!empty($this->topic_subscribe)) || (!empty($this->topic_favorite)) ) : ?>
										<li class="">
											<span>
												<?php echo $this->topic_subscribe ?>
											</span>
										</li>
										<li class="">
											<span class="">
												<?php echo $this->topic_favorite ?>
											</span>
										</li>
										<?php endif;?>
										<?php if (!empty($this->layout_buttons)) : ?>
										<li class="">
											<span class="tk-tools-layout">
												<?php echo implode(' ', $this->layout_buttons) ?>
											</span>
										</li>
										<?php endif;?>
									</ul>
									</div>
								</div>
							</div>
							<?php if (!empty($this->topic_moderate)) :?>
							<div class="tk-tools">
								<div class="tk-tools-options">
									<a class="kicon-button kbuttonmod btn-left">
										<span class="moderator-tools"><span>Moderator Tools</span></span>
									</a>
									<div style="padding-top:3px;">
									<ul class="tk-tools-container tk-tools-container-moderator">
										<?php if (!empty ( $this->topic_moderate )) : ?>
										<li class="">
											<span>
												<?php echo $this->topic_moderate; ?>
											</span>
										</li>
										<?php endif;?>
										<?php if ((!empty($this->topic_delete)) || (!empty($this->topic_sticky)) || (!empty($this->topic_lock))) : ?>
										<li class="">
											<span>
												<?php echo $this->topic_sticky ?>
											</span>
										</li>
										<li>
											<span>
												<?php echo $this->topic_lock ?>
											</span>
										</li>
										<li>
											<span>
												<?php echo $this->topic_delete ?>
											</span>
										</li>
										<?php endif ?>
									</ul>
									</div>
								</div>
							</div>
							<?php endif;?>
						</dd>
						<dd class="tk-pagination">
						<?php echo $this->getPagination(4) ?>
						</dd>
					</dl>
				</li>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>