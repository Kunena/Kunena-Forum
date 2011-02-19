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
<div class="kmsg-header kmsg-header-top">
	<h2>
		<span class="kmsgtitle<?php echo $this->escape($this->messagesuffix) ?> kmsg-title-top">
			<?php echo $this->escape($this->message->subject) ?>
		</span>
		<span class="kmsgdate kmsgdate-top" title="<?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat_hover') ?>">
			<?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat') ?>
		</span>
		<span class="kmsg-id-top">
			<a name="<?php echo intval($this->id) ?>"></a>
			<?php echo $this->numLink ?>
		</span>
	</h2>
</div>
<table class="<?php echo $this->class ?>">
	<tbody>
		<tr>
			<td class="kprofile-top">
				<?php $this->displayMessageProfile('horizontal') ?>
			</td>
		</tr>
		<tr>
			<td class="kmessage-top">
				<?php $this->displayMessageContents() ?>
			</td>
		</tr>
		<tr>
			<td class="kbuttonbar-top">
				<?php $this->displayMessageActions() ?>
			</td>
		</tr>
	</tbody>
</table>

<!-- Begin: Message Module Position -->
<?php $this->getModulePosition('kunena_msg_' . $this->mmm) ?>
<!-- Finish: Message Module Position -->