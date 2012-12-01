<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
?>
<div class="kmodule topic-moderate">
	<div class="kbox-wrapper kbox-full">
		<div class="topic-moderate-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="kheader"><a rel="kmod-detailsbox"><?php echo !isset($this->mesid) ? JText::_('COM_KUNENA_BUTTON_MODERATE_MESSAGE') : JText::_('COM_KUNENA_TITLE_MODERATE_TOPIC') ?></a></h2>
					<p class="kheader-desc">Category: <strong><?php echo $this->escape($this->category->name) ?></strong></p>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox kbox-border kbox-border_radius kbox-shadow">
					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" name="myform" method="post">
						<input type="hidden" name="view" value="topic" />
						<input type="hidden" name="task" value="move" />
						<input type="hidden" name="catid" value="<?php echo $this->category->id; ?>" />
						<input type="hidden" name="id" value="<?php echo $this->topic->id; ?>" />
						<?php if (isset($this->message)) : ?>
						<input type="hidden" name="mesid" value="<?php echo $this->message->id; ?>" />
						<?php endif; ?>
						<?php echo JHtml::_( 'form.token' ); ?>

						<ul class="kmod-postlist">
							<li>
								<ul class="kposthead">
									<li class="kposthead-replytitle"><h3><?php echo !isset($this->mesid) ? $this->escape($this->message->subject) : $this->escape($this->topic->subject)  ?></h3></li>
								</ul>
								<div class="kmod-container">
								<?php if (isset($this->message)) : ?>
									<p><?php echo JText::_('COM_KUNENA_MODERATION_TITLE_SELECTED') ?>:</p>
									<div class="kmoderate-message">
										<h4><?php echo $this->message->subject ?></h4>
										<div class="kmessage-timeby">
											<span class="kmessage-time" title="<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat_hover'); ?>">
												<?php echo JText::_('COM_KUNENA_POSTED_AT')?> <?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat'); ?>
											</span>
										<span class="kmessage-by"><?php echo JText::_('COM_KUNENA_BY')?> <?php echo $this->message->getAuthor()->getLink() ?></span></div>
										<div class="kmessage-avatar"><?php echo KunenaFactory::getAvatarIntegration()->getLink(KunenaFactory::getUser($this->message->userid)); ?></div>
										<div class="kmessage-msgtext"><?php echo KunenaHtmlParser::stripBBCode ($this->message->message, 300) ?></div>
										<div class="clr"></div>
									</div>
									<p>
										<?php if ($this->userLink) :
										echo JText::_('COM_KUNENA_MODERATE_THIS_USER'); ?>:
										<strong>
											<?php echo $this->userLink; ?>
										</strong>
										<?php endif; ?>
									</p>
									<ul>
										<li>
											<label for="kmoderate-mode-selected" class="hasTip" title="<?php echo JText::_('COM_KUNENA_MODERATION_MOVE_SELECTED') ?> :: ">
												<input type="radio" value="0" checked="checked" name="mode" id="kmoderate-mode-selected" />
												<?php echo JText::_('COM_KUNENA_MODERATION_MOVE_SELECTED') ?>
											</label>
										</li>
										<li>
											<label for="kmoderate-mode-newer" class="hasTip" title="<?php echo JText::sprintf ( 'COM_KUNENA_MODERATION_MOVE_NEWER', $this->escape($this->replies) ) ?> :: ">
												<input type="radio" value="2" name="mode" id="kmoderate-mode-newer" />
												<?php echo JText::sprintf ( 'COM_KUNENA_MODERATION_MOVE_NEWER', $this->escape($this->replies) ) ?>
											</label>
										</li>
									</ul>
									<br/>
									<?php else: ?>
									 <label><?php echo JText::_('COM_KUNENA_MODERATION_DEST') ?>:</label>
									<?php endif; ?>
									<div class="modcategorieslist">
										<label for="kmod_categories"><?php echo JText::_('COM_KUNENA_MODERATION_DEST_CATEGORY') ?>:</label>
										<?php echo $this->categorylist ?>
									</div>
									<div class="modtopicslist">
										<label for="kmod_topics"><?php echo JText::_('COM_KUNENA_MODERATION_DEST_TOPIC') ?>:</label>
										<input id="kmod_targetid" class="input hasTip" type="text" size="7" name="targetid" value="" style="display: none" />
										<?php echo $this->topiclist ?>
									</div>
									<div id="kmod_subject" class="kmod_subject">
										<label for="kmod_topicsubject"><?php echo JText::_('COM_KUNENA_MODERATION_TITLE_DEST_SUBJECT') ?>:</label>
										<input type="text" value="<?php echo !isset($this->mesid) ? $this->escape($this->message->subject) : $this->escape($this->topic->subject)  ?>" name="subject" class="input hasTip" size="50" title="<?php echo JText::_('COM_KUNENA_MODERATION_TITLE_DEST_SUBJECT') ?> :: <?php echo JText::_('COM_KUNENA_MODERATION_TITLE_DEST_ENTER_SUBJECT') ?>" />
									</div>
									<label for="kmod_replieschangesubject"><?php echo JText::_('COM_KUNENA_MODERATION_CHANGE_SUBJECT_ON_REPLIES') ?>:</label>
									<input id="kmod_replieschangesubject" type="checkbox" name="changesubject" value="1" title="<?php echo JText::_('COM_KUNENA_MODERATION_CHANGE_SUBJECT_ON_REPLIES') ?> :: <?php echo JText::_('COM_KUNENA_MODERATION_CHANGE_SUBJECT_ON_REPLIES') ?>" />
									<div class="clr"></div>
									<?php if (!isset($this->message)) : ?>
										<label for="kmod_shadow" class="hasTip" title="<?php echo JText::_('COM_KUNENA_MODERATION_TOPIC_SHADOW') ?> :: "><input id="kmod_shadow" type="checkbox" value="1" name="shadow"><?php echo JText::_('COM_KUNENA_MODERATION_TOPIC_SHADOW') ?></label>
									<?php endif ?>
								</div>
							</li>
						</ul>
						<div class="kpost-buttons">
							<button title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'));?>" type="submit" class="kbutton"> <?php echo JText::_('COM_KUNENA_SUBMIT') ?> </button>
							<button onclick="javascript:window.history.back();" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" type="button" class="kbutton"> <?php echo JText::_('COM_KUNENA_CANCEL') ?> </button>
						</div>
						<div class="clr"></div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

