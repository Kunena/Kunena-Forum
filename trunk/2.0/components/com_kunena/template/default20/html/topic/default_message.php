<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
					<li class="kposts-row krow-[K=ROW] krow-[K=NEW]">
						<a name="<?php echo intval($this->message->id) ?>"></a>
						<ul class="kposthead">
							<li class="kposthead-replytitle"><h3>RE: <?php echo $this->escape($this->message->subject) ?></h3></li>
							<li class="kposthead-postid" ><?php echo $this->numLink ?></li>
							<?php if (!empty($this->ipLink)) : ?><li class="kposthead-postip">IP: <?php echo $this->ipLink ?></li><?php endif ?>
							<li class="kposthead-posttime">Posted [K=DATE:<?php echo $this->message->time ?>]</li>
						</ul>
						[K=MESSAGE_PROFILE]
						<div class="kpost-container">
							<ul class="kpost-post-body">
								<li class="kpost-body">
								<?php echo $this->parse($this->message->message) ?>
								</li>
								<?php if (!empty($this->attachments)) : ?>
								<li class="kpost-body-attach">
									<span class="kattach-title"><?php echo JText::_('COM_KUNENA_ATTACHMENTS') ?></span>
									<ul>
										<?php foreach($this->attachments as $attachment) : ?>
										<!-- Loop this LI for each attachment  -->
										<li class="kattach-details">
											<?php echo $attachment->getThumbnailLink(); ?>
											<span>
												<?php echo $attachment->getTextLink(); ?>
											</span>
										</li>
										<?php endforeach; ?>
									</ul>
									<div class="clr"></div>
								</li>
								<?php endif; ?>
								<?php if ($this->message->modified_by && $this->config->editmarkup) : ?>
								<li class="kpost-body-lastedit">
									<?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ": [K=DATE:{$this->message->modified_time}] " . JText::_('COM_KUNENA_BY') . ' ' . CKunenaLink::getProfileLink( $this->message->modified_by ) . '.'; ?>
								</li>
								<?php if ($this->message->modified_reason) : ?>
								<li class="kpost-body-editreason">
									<?php echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape ( $this->message->modified_reason ); ?>
								</li>
								<?php endif ?>
								<?php endif ?>
								<?php if (!empty($this->signatureHtml)) : ?>
								<li class="kpost-body-sig"><?php echo $this->signatureHtml ?></li>
								<?php endif ?>
							</ul>
							[K=MESSAGE_ACTIONS]
							<div class="clr"></div>
						</div>
					</li>
					<?php if ($this->isModulePosition('kunena_msg_' . $this->mmm)) : ?><li class="kmodules"><?php $this->getModulePosition('kunena_msg_' . $this->mmm) ?></li><?php endif ?>