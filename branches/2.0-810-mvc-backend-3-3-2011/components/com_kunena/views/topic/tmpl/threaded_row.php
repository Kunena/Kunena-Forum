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
<tr class="krow<?php echo $this->mmm % 2 ?> <?php echo $this->class ?>">
	<td class="kcol-first kmsgsubject kmsgsubject<?php echo $this->escape($this->msgsuffix) ?>">
		<span class="<?php echo implode('"></span><span class="ktree ktree-', $this->message->indent)?>"></span>
		<?php if ($this->message->id == $this->state->get('item.mesid')) : ?>
		<?php echo $this->escape($this->message->subject) ?>
		<?php else : ?>
		<?php echo CKunenaLink::GetThreadLayoutLink(null, $this->message->catid, $this->message->thread, $this->message->id, $this->escape($this->message->subject), $this->state->get('list.start'), $this->state->get('list.limit')) ?>
		<?php endif; ?>
	</td>
	<td class="kcol-mid kprofile kprofile-list"><?php echo CKunenaLink::GetProfileLink($this->profile->userid, $this->message->name) ?></td>
	<td class="kcol-last kmsgdate kmsgdate-list" title="<?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat_hover') ?>">
		<?php echo CKunenaTimeformat::showDate($this->message->time, 'config_post_dateformat') ?>
	</td>
</tr>
<!-- Begin: Message Module Position -->
<?php //CKunenaTools::showModulePosition('kunena_tmsg_' . $this->mmm) ?>
<!-- Finish: Message Module Position -->
