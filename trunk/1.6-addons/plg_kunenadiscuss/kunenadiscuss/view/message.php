<?php
/**
 * @version $Id$
 * Kunena Discuss Plug-in
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010-2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die ( '' );

$msgurl=CKunenaLink::GetMessageURL ( $this->id );
?>
<div class="kdiscuss-item kdiscuss-item<?php echo $this->mmm & 1 ? 1 : 2 ?>">
	<a name="<?php echo intval($this->id) ?>" id="id<?php echo intval($this->id) ?>" > </a>
	<div class="kdiscuss-reply-header">
		<span class="kdiscuss-date" title="<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat_hover'); ?>">
			<?php echo JText::_ ( 'PLG_KUNENADISCUSS_POSTED' )?> <?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat'); ?>
		</span>
		<span class="kdiscuss-username">
			<?php echo JText::_ ( 'PLG_KUNENADISCUSS_BY' ) . ' ' . CKunenaLink::GetProfileLink ( $this->profile->userid, $this->escape($this->username) ) ?>
		</span>
		<span class="kdiscuss-id">
			<a href="<?php echo $msgurl; ?>" title="#<?php echo $this->id; ?>">#<?php echo $this->id; ?></a>
		</span>
	</div>
	<div class="kdiscuss-reply-body">
	<?php if ($this->config->allowavatar) : ?>
		<div class="kdiscuss-avatar">
			<?php echo CKunenaLink::GetProfileLink ( $this->profile->userid, $this->avatar ) ?>
		</div>
	<?php endif; ?>
		<div class="kdiscuss-text">
			<?php echo $this->escape($this->subject); ?>
			<?php echo $this->messageHtml; ?>
		</div>
	</div>
</div>



