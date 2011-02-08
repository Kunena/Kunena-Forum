<?php
/**
 * @version $Id: message.php 4062 2010-12-23 07:15:10Z severdia $
 * Kunena Discuss Plug-in
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010-2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die ( '' );

// Do not display first message
if ($this->message->id == $this->topic->first_post_id) return;
$msgurl=CKunenaLink::GetMessageURL ( $this->message->id );
?>
<div class="kdiscuss-item kdiscuss-item<?php echo $this->mmm & 1 ? 1 : 2 ?>">
	<a name="<?php echo intval($this->id) ?>" id="id<?php echo intval($this->id) ?>" > </a>
	<div class="kdiscuss-reply-header">
		<span class="kdiscuss-date" title="<?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat_hover'); ?>">
			<?php echo JText::_ ( 'PLG_KUNENADISCUSS_POSTED' )?> <?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat'); ?>
		</span>
		<span class="kdiscuss-username">
			<?php echo JText::_ ( 'PLG_KUNENADISCUSS_BY' ) . ' ' . CKunenaLink::GetProfileLink ( $this->profile->userid, $this->escape($this->message->name) ) ?>
		</span>
		<span class="kdiscuss-id">
			<a href="<?php echo $msgurl; ?>" title="#<?php echo $this->message->id; ?>">#<?php echo $this->message->id; ?></a>
		</span>
	</div>
	<div class="kdiscuss-reply-body">
		<?php $avatar = $this->profile->getAvatarLink ('kavatar', 'welcome'); if ($avatar) : ?>
		<div class="kdiscuss-avatar">
			<?php echo CKunenaLink::GetProfileLink ( intval($this->profile->userid), $avatar ); ?>
		</div>
		<?php endif; ?>
		<div class="kdiscuss-text">
			<?php echo $this->escape($this->message->subject) ?>
			<?php echo KunenaHtmlParser::parseBBCode ($this->message->message, $this) ?>
		</div>
	</div>
</div>