<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmsg-header kmsg-header-right">
	<h2>
		<span class="kmsgtitle<?php echo $this->escape($this->msgsuffix) ?> kmsg-title-right">
			<?php echo $this->displayMessageField('subject') ?>
		</span>
		<span class="kmsgdate kmsgdate-right" title="<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat_hover') ?>">
			<?php echo KunenaDate::getInstance($this->message->time)->toKunena('config_post_dateformat') ?>
		</span>
		<span class="kmsg-id-right">
			<a id="<?php echo intval($this->message->id) ?>"></a>
			<?php echo $this->numLink ?>
		</span>
	</h2>
</div>
<table class="<?php echo $this->class ?>">
	<tbody>
		<tr>
			<td class="kmessage-right">
				<?php $this->displayMessageContents() ?>
			</td>
			<td rowspan="2" class="kprofile-right">
				<?php $this->displayMessageProfile('vertical') ?>
			</td>
		</tr>
		<tr>
			<td class="kbuttonbar-right">
				<?php $this->displayMessageActions() ?>
			</td>
		</tr>
	</tbody>
</table>

<!-- Begin: Message Module Position -->
<?php $this->displayModulePosition('kunena_msg_' . $this->mmm) ?>
<!-- Finish: Message Module Position -->
