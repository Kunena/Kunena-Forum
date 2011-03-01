<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<tr class="krow<?php echo $this->mmm % 2 ?> <?php echo $this->msgclass ?>">
	<td class="kcol-first kmsgsubject kmsgsubject<?php echo $this->escape($this->msgsuffix) ?>">
		<span class="<?php echo implode('"></span><span class="ktree ktree-', $this->msg->indent)?>"></span>
		<?php if ($this->msg->id == $this->mesid) : ?>
		<?php echo $this->subjectHtml ?>
		<?php else : ?>
		<?php echo CKunenaLink::GetThreadLayoutLink(null, $this->catid, $this->msg->thread, $this->msg->id, $this->subjectHtml, $this->limitstart, $this->limit) ?>
		<?php endif; ?>
	</td>
	<td class="kcol-mid kprofile kprofile-list"><?php echo CKunenaLink::GetProfileLink($this->profile->userid, $this->username) ?></td>
	<td class="kcol-last kmsgdate kmsgdate-list" title="<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat_hover') ?>">
		<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat') ?>
	</td>
</tr>
<!-- Begin: Message Module Position -->
<?php //CKunenaTools::showModulePosition('kunena_tmsg_' . $this->mmm) ?>
<!-- Finish: Message Module Position -->
