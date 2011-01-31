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
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
$this->document->addScriptDeclaration("// <![CDATA[
kunena_url_ajax= '".CKunenaLink::GetJsonURL()."';
// ]]>");
?>
<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo $this->moderateTopic ? JText::_('COM_KUNENA_TITLE_MODERATE_TOPIC') : JText::_('COM_KUNENA_TITLE_MODERATE_MESSAGE'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody" id="kmod-container">
	<form action="<?php echo CKunenaLink::GetPostURL (); ?>" method="post" name="myform"><input type="hidden" name="do" value="domoderate" />
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="catid" value="<?php echo $this->catid; ?>" />

	<div>
		<?php echo JText::_('COM_KUNENA_GEN_TOPIC'); ?>:
		<strong><?php echo $this->escape( $this->threadmsg->subject ); ?></strong>
	</div>
	<div>
		<?php echo JText::_('COM_KUNENA_POST_IN_CATEGORY'); ?>:
		<strong><?php echo $this->escape( $this->message->catname ) ?></strong>
	</div>

	<br />
	<?php if (!$this->moderateTopic) : ?>
	<div><?php echo JText::_('COM_KUNENA_MODERATION_TITLE_SELECTED'); ?>:</div>
	<div class="kmoderate-message">
		<h4><?php echo $this->escape( $this->message->subject ); ?></h4>
		<div class="kmessage-timeby"><span class="kmessage-time" title="<?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat_hover'); ?>">
		<?php echo JText::_('COM_KUNENA_POSTED_AT')?> <?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat'); ?></span>
		<span class="kmessage-by"><?php echo JText::_('COM_KUNENA_GEN_BY') . ' ' . CKunenaLink::GetProfileLink ( intval($this->message->userid), $this->escape($this->message->name) ); ?></span></div>
		<div class="kmessage-avatar"><?php echo $this->user->getAvatarLink('', 'list'); ?></div>
		<div class="kmessage-msgtext"><?php echo KunenaParser::stripBBCode ($this->message->message, 300) ?></div>
	</div>
	<div>
		<?php echo JText::_('COM_KUNENA_MODERATE_THIS_USER');
		if ( $this->message->userid != 0){ ?>:
		<strong><?php echo CKunenaLink::GetModerateUserLink( intval($this->message->userid),
		$this->escape($this->message->name).' ('.intval($this->message->userid).')' ); ?></strong>
		<?php }else{ ?>:
		<strong><?php echo JText::_('COM_KUNENA_USERNAME_ANONYMOUS'); ?></strong>
		<?php } ?>
	</div>
	<?php if ($this->threadmsg->replies) : ?>
	<ul>
		<li><input id="kmoderate-mode-selected" type="radio" name="mode" checked="checked" value="<?php echo KN_MOVE_MESSAGE ?>" /><?php echo JText::_ ( 'COM_KUNENA_MODERATION_MOVE_SELECTED' ); ?></li>
		<li><input id="kmoderate-mode-newer" type="radio" name="mode" value="<?php echo KN_MOVE_NEWER ?>" ><?php echo JText::sprintf ( 'COM_KUNENA_MODERATION_MOVE_NEWER', $this->escape($this->threadmsg->replies) ); ?></li>
	</ul>
	<?php endif; ?>
	<br />
<?php else : ?>
	<input id="kmoderate-mode-topic" type="hidden" name="mode" value="<?php echo KN_MOVE_THREAD ?>" />
<?php endif; ?>

	<div><?php echo JText::_ ( 'COM_KUNENA_MODERATION_DEST' );?>:
	<div id="modcategorieslist">
		<?php echo JText::_ ( 'COM_KUNENA_MODERATION_DEST_CATEGORY' );?>:
		<?php echo $this->categorylist ?>
	</div>

	<div id="modtopicslist">
		<?php echo JText::_ ( 'COM_KUNENA_MODERATION_DEST_TOPIC' ); ?>:
		<input id="kmod_targetid" type="text" size="7" name="targetid" value="" style="display: none"/>
		<?php echo $this->messagelist ?>
	</div>

	<div id="kmod_subject">
		<?php echo JText::_ ( 'COM_KUNENA_MODERATION_TITLE_DEST_SUBJECT' ); ?>:
		<input type="text" name="subject" value="<?php echo $this->escape( $this->message->subject ); ?>" />
	</div>

	<div>
		<input type="checkbox" name="changesubject" value="1" />
		<?php echo JText::_ ( 'COM_KUNENA_MODERATION_CHANGE_SUBJECT_ON_REPLIES' ); ?>
	</div>
<?php if ($this->moderateTopic) : ?>
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
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
        </div>
	</div>
</div>