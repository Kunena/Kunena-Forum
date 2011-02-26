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
<div class="kmsg-header kmsg-header-bottom">
	<h2>
		<span class="kmsgtitle<?php echo $this->escape($this->messagesuffix) ?> kmsg-title-bottom">
			<?php echo $this->escape($this->message->subject) ?>
		</span>
		<span class="kmsgdate kmsgdate-bottom" title="<?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat_hover') ?>">
			<?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat') ?>
		</span>
		<span class="kmsg-id-bottom">
			<a name="<?php echo intval($this->id) ?>"></a>
			<?php echo $this->numLink ?>
		</span>
	</h2>
</div>
<table class="<?php echo $this->class ?>">
	<tbody>
		<tr>
			<td class="kmessage-bottom">
				<?php $this->displayMessageContents() ?>
			</td>
		</tr>
		<tr>
			<td class="kbuttonbar-bottom">
				<?php $this->displayMessageActions() ?>
			</td>
		</tr>
		<tr>
			<td class="kprofile-bottom">
				<?php $this->displayMessageProfile('horizontal') ?>
			</td>
		</tr>
	</tbody>
</table>

<!-- Begin: Message Module Position -->
<?php $this->getModulePosition('kunena_msg_' . $this->mmm) ?>
<!-- Finish: Message Module Position -->