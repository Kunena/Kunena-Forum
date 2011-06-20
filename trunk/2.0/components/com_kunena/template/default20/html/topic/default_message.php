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
							<?php if (!empty($this->ipLink)) : ?><li class="kposthead-postip"><?php echo $this->ipLink ?></li><?php endif ?>
							<li class="kposthead-posttime">Posted [K=DATE:<?php echo $this->message->time ?>]</li>
						</ul>
						[K=MESSAGE_PROFILE]
						<div class="kpost-container">
							<ul class="kpost-post-body">
								<li class="kpost-body">
								<?php echo $this->parse($this->message->message) ?>
								</li>
								<li class="clr"></li>
								<?php if (!empty($this->attachments)) : ?>
								<li class="kpost-body-attach">
									<span class="kattach-title"><?php echo JText::_('COM_KUNENA_ATTACHMENTS') ?></span>
									<ul>
										<?php foreach($this->attachments as $attachment) : ?>
										<li class="kattach-details">
											<?php echo $attachment->getThumbnailLink(); ?> <span><?php echo $attachment->getTextLink(); ?></span>
										</li>
										<?php endforeach; ?>
									</ul>
									<div class="clr"></div>
								</li>
								<?php endif; ?>
								<?php if ( $this->topic->authorise('reply') ) : ?>
								<li id="kreply<?php echo intval($this->message->id) ?>_form" class="kreply-form" style="display: none">
									<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="postform" enctype="multipart/form-data">
										<?php if (KunenaFactory::getUser()->exists() && $this->category->allow_anonymous): ?>
											<input type="text" name="authorname" size="35" class="kinputbox postinput" maxlength="35" value="<?php echo $this->escape($this->profile->getName()) ?>" /><br />
											<input type="checkbox" id="kanonymous<?php echo intval($this->message->id) ?>" name="anonymous" value="1" class="kinputbox postinput" <?php if ($this->category->post_anonymous) echo 'checked="checked"'; ?> /> <label for="kanonymous<?php echo intval($this->message->id) ?>"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC') ?></label><br />
										<?php else: ?>
											<input type="hidden" name="authorname" value="<?php echo $this->escape($this->profile->getName()) ?>" />
										<?php endif; ?>
										<input type="text" name="subject" size="35" class="inputbox" maxlength="<?php echo intval($this->config->maxsubject); ?>" value="<?php echo  $this->escape($this->message->subject) ?>" /><br />
										<textarea class="inputbox" name="message" rows="6" cols="60"></textarea><br />
										<?php if ($this->topic->authorise('subscribe')) : ?>
											<input type="checkbox" name="subscribeMe" value="1" <?php echo ($this->config->subscriptionschecked == 1) ? 'checked="checked"' : '' ?> />
											<i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i>
											<br />
										<?php endif; ?>
										<input type="submit" class="kbutton kreply-submit" name="submit" value="<?php echo JText::_('COM_KUNENA_GEN_CONTINUE') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'));?>" />
										<input type="reset" class="kbutton kreply-cancel" name="cancel" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" />
										<small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE') ?></small>
										<input type="hidden" name="view" value="topic" />
										<input type="hidden" name="task" value="post" />
										<input type="hidden" name="parentid" value="<?php echo intval($this->message->id) ?>" />
										<input type="hidden" name="catid" value="<?php echo intval($this->category->id) ?>" />
										<?php echo JHTML::_( 'form.token' ) ?>
									</form>
								</li>
								<?php endif ?>
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