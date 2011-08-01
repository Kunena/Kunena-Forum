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

<li class="row krow<?php echo $this->mmm % 2 ?> <?php echo $this->class ?>">
	<dl class="">
		<dt style="width:65%;">
			<span class="<?php echo implode('"></span><span class="ktree ktree-', $this->message->indent)?>"></span>
			<?php if ($this->message->id == $this->state->get('item.mesid')) : ?>
			<?php echo $this->escape($this->message->subject) ?>
			<?php else : ?>
			<?php echo CKunenaLink::GetThreadLayoutLink(null, $this->message->catid, $this->message->thread, $this->message->id, $this->escape($this->message->subject), $this->state->get('list.start'), $this->state->get('list.limit')) ?>
			<?php endif; ?>
		</dt>
		<dd class="tk-poll-bar" style="text-align:left; width:13%">
		<?php //$avatar = $this->profile->getAvatarImage ('kavatar', 'list'); if ($avatar) : ?>
		<?php //echo $this->profile->getLink($avatar); ?>
		<?php //endif?>
			<?php echo CKunenaLink::GetProfileLink($this->profile->userid, $this->message->name) ?>
		</dd>
		<dd class="posts" style="text-align:left; width:17%" title="<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat_hover') ?>">
			<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat') ?>
		</dd>
	</dl>
</li>