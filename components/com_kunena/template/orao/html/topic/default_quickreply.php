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
<div id="kreply<?php echo intval($this->message->id) ?>_form" class="kreply-form forumlist qreply" style="display: none">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon quickreply">
						<dt class="body"><span class="ktitle"><?php echo JText::_('COM_KUNENA_BUTTON_QUICKREPLY'); ?></span></dt>
					</dl>
				</li>
			</ul>
			<ul class="topiclist forums">
				<li class="rowfull">
					<dl class="icon quickreply">
						<dt>
						</dt>
						<dd class="first tk-quick-reply">
							<div class="" style="display: block !important;">
								<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="postform" enctype="multipart/form-data">
								<ul>
									<?php if (KunenaUserHelper::getMyself()->exists() && $this->category->allow_anonymous): ?>
									<li>
										<?php // FIXME : Language string ?>
										<span class="tk-quick-title"><?php echo JText::_('Author Name') ?></span>
										<span class="tk-quick-form">
											<input type="text" name="authorname" size="35" class="kinputbox postinput" maxlength="35" value="<?php echo $this->escape($this->profile->getName()) ?>" />
											<input type="checkbox" id="kanonymous<?php echo intval($this->message->id) ?>" name="anonymous" value="1" class="kinputbox postinput" <?php if ($this->category->post_anonymous) echo 'checked="checked"'; ?> /> <label for="kanonymous<?php echo intval($this->message->id) ?>"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC') ?></label>
										</span>
									</li>
										<?php else: ?>
										<li>
											<input type="hidden" name="authorname" value="<?php echo $this->escape($this->profile->getName()) ?>" />
										</li>
										<?php endif; ?>
									<li>
										<span class="tk-quick-title"><?php echo JText::_('COM_KUNENA_GEN_SUBJECT') ?></span>
										<span class="tk-quick-form">
										<input type="hidden" name="view" value="topic" />
										<input type="hidden" name="task" value="post" />
										<input type="hidden" name="parentid" value="<?php echo intval($this->message->id) ?>" />
										<input type="hidden" name="catid" value="<?php echo intval($this->category->id) ?>" />
										<?php echo JHTML::_( 'form.token' ) ?>
											<input type="text" name="subject" size="55" class="inputbox" maxlength="<?php echo intval($this->config->maxsubject); ?>" value="<?php echo  $this->escape($this->message->subject) ?>" /><br />
										</span>
									</li>
									<li>
										<span class="tk-quick-title"><?php echo JText::_('COM_KUNENA_GEN_MESSAGE') ?></span>
										<span class="tk-quick-form">
											<textarea class="inputbox" name="message" rows="6" cols="100"></textarea><br />
										</span>
									</li>
										<?php if ($this->topic->authorise('subscribe')) : ?>
									<li>
										<span class="tk-quick-title">&nbsp;</span>
										<span class="tk-quick-form">
											<input type="checkbox" name="subscribeMe" value="1" <?php echo ($this->config->subscriptionschecked == 1) ? 'checked="checked"' : '' ?> />
											<i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i>
										</span>
									</li>
										<?php endif; ?>
									<li>
										<span class="tk-quick-title">&nbsp;</span>
										<span class="tk-quick-form">
											<input type="submit" class="tk-submit-button kreply-submit" name="submit" value="<?php echo JText::_('COM_KUNENA_GEN_CONTINUE') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'));?>" />
											<input type="reset" class="tk-cancel-button kreply-cancel" name="cancel" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" />
											<small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE') ?></small>
										</span>
									</li>
								</ul>
								</form>
							</div>
						</dd>
					</dl>
				</li>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>