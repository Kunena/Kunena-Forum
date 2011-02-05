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

$this->document->addScriptDeclaration("// <![CDATA[
kunena_url_ajax= '".CKunenaLink::GetJsonURL()."';
// ]]>");
?>
<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo !isset($this->message) ? JText::_('COM_KUNENA_TITLE_MODERATE_TOPIC') : JText::_('COM_KUNENA_TITLE_MODERATE_MESSAGE'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody" id="kmod-container">
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="myform">
				<input type="hidden" name="view" value="topic" />
				<input type="hidden" name="task" value="move" />
				<input type="hidden" name="catid" value="<?php echo $this->category->id; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->topic->id; ?>" />
<?php if (isset($this->message)) : ?>
				<input type="hidden" name="mesid" value="<?php echo $this->message->id; ?>" />
<?php endif; ?>
				<?php echo JHTML::_( 'form.token' ); ?>

				<div>
					<?php echo JText::_('COM_KUNENA_GEN_TOPIC'); ?>:
					<strong><?php echo $this->escape( $this->topic->subject ); ?></strong>
				</div>
				<div>
					<?php echo JText::_('COM_KUNENA_POST_IN_CATEGORY'); ?>:
					<strong><?php echo $this->escape( $this->category->name ) ?></strong>
				</div>

				<br />
				<?php if (isset($this->message)) : ?>
				<div><?php echo JText::_('COM_KUNENA_MODERATION_TITLE_SELECTED'); ?>:</div>
				<div class="kmoderate-message">
					<h4><?php echo $this->escape( $this->message->subject ); ?></h4>
					<div class="kmessage-timeby">
						<span class="kmessage-time" title="<?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat_hover'); ?>">
							<?php echo JText::_('COM_KUNENA_POSTED_AT')?> <?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat'); ?>
						</span>
						<span class="kmessage-by">
							<?php echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($this->message->userid), $this->escape($this->message->name) ); ?>
						</span>
					</div>
					<div class="kmessage-avatar"><?php echo $this->user->getAvatarLink('', 'list'); ?></div>
					<div class="kmessage-msgtext"><?php echo KunenaHtmlParser::stripBBCode ($this->message->message, 300) ?></div>
				</div>
				<div>
					<?php echo JText::_('COM_KUNENA_MODERATE_THIS_USER');
					if ( $this->message->userid) : ?>:
					<strong>
						<?php echo CKunenaLink::GetModerateUserLink( intval($this->message->userid), $this->escape($this->message->name).' ('.intval($this->message->userid).')' ); ?>
					</strong>
					<?php else : ?>:
					<strong><?php echo JText::_('COM_KUNENA_USERNAME_ANONYMOUS'); ?></strong>
					<?php endif; ?>
				</div>
				<?php if (!empty($this->replies)) : ?>
				<ul>
					<li><input id="kmoderate-mode-selected" type="radio" name="mode" checked="checked" value="selected" /><?php echo JText::_ ( 'COM_KUNENA_MODERATION_MOVE_SELECTED' ); ?></li>
					<li><input id="kmoderate-mode-newer" type="radio" name="mode" value="newer" ><?php echo JText::sprintf ( 'COM_KUNENA_MODERATION_MOVE_NEWER', $this->escape($this->replies) ); ?></li>
				</ul>
				<?php endif; ?>
				<br />
				<?php endif; ?>

				<div><?php echo JText::_ ( 'COM_KUNENA_MODERATION_DEST' );?>:
				<div id="modcategorieslist">
					<?php echo JText::_ ( 'COM_KUNENA_MODERATION_DEST_CATEGORY' );?>:
					<?php echo $this->categorylist ?>
				</div>

				<div id="modtopicslist">
					<?php echo JText::_ ( 'COM_KUNENA_MODERATION_DEST_TOPIC' ); ?>:
					<input id="kmod_targetid" type="text" size="7" name="targetid" value="" style="display: none"/>
					<?php echo $this->topiclist ?>
				</div>

				<div id="kmod_subject">
					<?php echo JText::_ ( 'COM_KUNENA_MODERATION_TITLE_DEST_SUBJECT' ); ?>:
					<input type="text" name="subject" value="<?php echo $this->escape( !isset($this->message) ? $this->topic->subject : $this->message->subject ); ?>" />
				</div>
				<div>
					<input type="checkbox" name="changesubject" value="1" />
					<?php echo JText::_ ( 'COM_KUNENA_MODERATION_CHANGE_SUBJECT_ON_REPLIES' ); ?>
				</div>
				<?php if (!isset($this->message)) : ?>
				<div>
					<input type="checkbox" <?php if ($this->config->boxghostmessage): ?> checked="checked" <?php endif; ?> name="shadow" value="1" />
					<?php echo JText::_ ( 'COM_KUNENA_MODERATION_TOPIC_SHADOW' ); ?>
				</div>
				<?php endif ?>
				</div>
				<div>
					<input type="submit" class="button" value="<?php echo JText::_ ( 'COM_KUNENA_POST_MODERATION_PROCEED' ); ?>" />
					<a href="javascript:history.back();" class="button" ><span class="kbutton-back"><?php echo JText::_ ( 'COM_KUNENA_BACK' ); ?></span></a>
				</div>
			</form>
		</div>
	</div>
</div>